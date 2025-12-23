<?php

namespace App\Services;

use App\Models\EmailNotification;
use App\Models\NotificationPreference;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService {
    private $emailModel;
    private $prefModel;
    
    public function __construct() {
        $this->emailModel = new EmailNotification();
        $this->prefModel = new NotificationPreference();
    }
    
    /**
     * Queue ticket created notification
     */
    public function queueTicketCreated($ticket, $user) {
        // Check user preferences
        if (!$this->prefModel->shouldNotify($user['id'], 'ticket_created')) {
            return false;
        }
        
        $subject = "New Ticket Created: #{$ticket['id']} - {$ticket['title']}";
        $body = $this->renderTicketCreatedEmail($ticket, $user);
        
        return $this->emailModel->create([
            'user_id' => $user['id'],
            'ticket_id' => $ticket['id'],
            'type' => 'ticket_created',
            'subject' => $subject,
            'body' => $body,
            'status' => 'pending'
        ]);
    }
    
    /**
     * Queue ticket updated notification
     */
    public function queueTicketUpdated($ticket, $user, $changes) {
        if (!$this->prefModel->shouldNotify($user['id'], 'ticket_updated')) {
            return false;
        }
        
        $subject = "Ticket Updated: #{$ticket['id']} - {$ticket['title']}";
        $body = $this->renderTicketUpdatedEmail($ticket, $user, $changes);
        
        return $this->emailModel->create([
            'user_id' => $user['id'],
            'ticket_id' => $ticket['id'],
            'type' => 'ticket_updated',
            'subject' => $subject,
            'body' => $body,
            'status' => 'pending'
        ]);
    }
    
    /**
     * Queue comment added notification
     */
    public function queueCommentAdded($ticket, $comment, $user) {
        if (!$this->prefModel->shouldNotify($user['id'], 'comment_added')) {
            return false;
        }
        
        $subject = "New Comment on Ticket #{$ticket['id']}: {$ticket['title']}";
        $body = $this->renderCommentAddedEmail($ticket, $comment, $user);
        
        return $this->emailModel->create([
            'user_id' => $user['id'],
            'ticket_id' => $ticket['id'],
            'type' => 'comment_added',
            'subject' => $subject,
            'body' => $body,
            'status' => 'pending'
        ]);
    }
    
    /**
     * Queue ticket assigned notification
     */
    public function queueTicketAssigned($ticket, $assignedUser) {
        if (!$this->prefModel->shouldNotify($assignedUser['id'], 'ticket_assigned')) {
            return false;
        }
        
        $subject = "Ticket Assigned to You: #{$ticket['id']} - {$ticket['title']}";
        $body = $this->renderTicketAssignedEmail($ticket, $assignedUser);
        
        return $this->emailModel->create([
            'user_id' => $assignedUser['id'],
            'ticket_id' => $ticket['id'],
            'type' => 'ticket_assigned',
            'subject' => $subject,
            'body' => $body,
            'status' => 'pending'
        ]);
    }
    
    /**
     * Process pending notifications
     */
    public function processPendingNotifications($limit = 10) {
        $notifications = $this->emailModel->getPending($limit);
        $sent = 0;
        $failed = 0;
        
        foreach ($notifications as $notification) {
            try {
                $this->sendEmail(
                    $notification['user_email'],
                    $notification['user_name'],
                    $notification['subject'],
                    $notification['body']
                );
                
                $this->emailModel->markAsSent($notification['id']);
                $sent++;
            } catch (\Exception $e) {
                $this->emailModel->markAsFailed($notification['id'], $e->getMessage());
                $failed++;
            }
        }
        
        return [
            'total' => count($notifications),
            'sent' => $sent,
            'failed' => $failed
        ];
    }
    
    /**
     * Send email using PHPMailer
     */
    private function sendEmail($to, $toName, $subject, $body)
{
    $payload = [
        'from' => $_ENV['MAIL_FROM_NAME'] . ' <' . $_ENV['MAIL_FROM_ADDRESS'] . '>',
        'to' => [$to],
        'subject' => $subject,
        'html' => $body,
    ];

    $ch = curl_init('https://api.resend.com/emails');

    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $_ENV['RESEND_API_KEY'],
            'Content-Type: application/json',
        ],
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_TIMEOUT => 15,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        throw new \Exception('Resend CURL error: ' . curl_error($ch));
    }

    curl_close($ch);

    if ($httpCode !== 200) {
        error_log('RESEND ERROR: ' . $response);
        throw new \Exception('Email gagal dikirim via Resend');
    }

    return true;
}
    
    /**
     * Email templates
     */
    private function renderTicketCreatedEmail($ticket, $user) {
        return "
        <h2>Your ticket has been created</h2>
        <p>Hello {$user['name']},</p>
        <p>Your support ticket has been successfully created.</p>
        
        <div style='background: #f3f4f6; padding: 15px; border-radius: 5px; margin: 20px 0;'>
            <strong>Ticket #: </strong>{$ticket['id']}<br>
            <strong>Title: </strong>{$ticket['title']}<br>
            <strong>Priority: </strong>{$ticket['priority']}<br>
            <strong>Status: </strong>{$ticket['status']}
        </div>
        
        <p>We'll get back to you as soon as possible.</p>
        <p><a href='" . $_ENV['APP_URL'] . "/tickets/{$ticket['id']}' style='background: #3B82F6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>View Ticket</a></p>
        
        <p>Best regards,<br>Helpdesk Support Team</p>
        ";
    }
    
    private function renderTicketUpdatedEmail($ticket, $user, $changes) {
        $changesList = '';
        foreach ($changes as $field => $change) {
            $changesList .= "<li><strong>{$field}:</strong> {$change['old']} → {$change['new']}</li>";
        }
        
        return "
        <h2>Ticket Updated</h2>
        <p>Hello {$user['name']},</p>
        <p>Your ticket has been updated:</p>
        
        <div style='background: #f3f4f6; padding: 15px; border-radius: 5px; margin: 20px 0;'>
            <strong>Ticket #: </strong>{$ticket['id']}<br>
            <strong>Title: </strong>{$ticket['title']}
        </div>
        
        <p><strong>Changes:</strong></p>
        <ul>{$changesList}</ul>
        
        <p><a href='" . $_ENV['APP_URL'] . "/tickets/{$ticket['id']}' style='background: #3B82F6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>View Ticket</a></p>
        
        <p>Best regards,<br>Helpdesk Support Team</p>
        ";
    }
    
    private function renderCommentAddedEmail($ticket, $comment, $user) {
        return "
        <h2>New Comment on Your Ticket</h2>
        <p>Hello {$user['name']},</p>
        <p>A new comment has been added to your ticket:</p>
        
        <div style='background: #f3f4f6; padding: 15px; border-radius: 5px; margin: 20px 0;'>
            <strong>Ticket #: </strong>{$ticket['id']}<br>
            <strong>Title: </strong>{$ticket['title']}
        </div>
        
        <div style='background: white; border-left: 4px solid #3B82F6; padding: 15px; margin: 20px 0;'>
            <strong>{$comment['user_name']}</strong> wrote:<br><br>
            {$comment['comment']}
        </div>
        
        <p><a href='" . $_ENV['APP_URL'] . "/tickets/{$ticket['id']}' style='background: #3B82F6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>View & Reply</a></p>
        
        <p>Best regards,<br>Helpdesk Support Team</p>
        ";
    }
    
    private function renderTicketAssignedEmail($ticket, $user) {
        return "
        <h2>Ticket Assigned to You</h2>
        <p>Hello {$user['name']},</p>
        <p>A ticket has been assigned to you:</p>
        
        <div style='background: #f3f4f6; padding: 15px; border-radius: 5px; margin: 20px 0;'>
            <strong>Ticket #: </strong>{$ticket['id']}<br>
            <strong>Title: </strong>{$ticket['title']}<br>
            <strong>Priority: </strong>{$ticket['priority']}<br>
            <strong>Status: </strong>{$ticket['status']}
        </div>
        
        <p><a href='" . $_ENV['APP_URL'] . "/tickets/{$ticket['id']}' style='background: #3B82F6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>View Ticket</a></p>
        
        <p>Best regards,<br>Helpdesk Support Team</p>
        ";
    }
}