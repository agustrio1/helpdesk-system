<?php
namespace App\Controllers;

use App\Models\NotificationPreference;
use App\Models\EmailNotification;
use App\Helpers\Security;
use App\Services\EmailService;

class NotificationController {
    private $prefModel;
    private $emailModel;
    
    public function __construct() {
        $this->prefModel = new NotificationPreference();
        $this->emailModel = new EmailNotification();
    }
    
    /**
     * Show notification preferences
     */
    public function preferences() {
        $this->requireAuth();
        
        $userId = $_SESSION['user_id'];
        $preferences = $this->prefModel->getOrCreate($userId);
        $csrfToken = Security::generateCSRFToken();
        
        require_once __DIR__ . '/../../views/notifications/preferences.php';
    }
    
    /**
     * Update notification preferences
     */
    public function updatePreferences() {
        $this->requireAuth();
        $this->validateCSRF();
        
        $userId = $_SESSION['user_id'];
        
        $data = [
            'ticket_created' => isset($_POST['ticket_created']) ? 1 : 0,
            'ticket_updated' => isset($_POST['ticket_updated']) ? 1 : 0,
            'ticket_assigned' => isset($_POST['ticket_assigned']) ? 1 : 0,
            'comment_added' => isset($_POST['comment_added']) ? 1 : 0,
            'status_changed' => isset($_POST['status_changed']) ? 1 : 0
        ];
        
        $result = $this->prefModel->update($userId, $data);
        
        if ($result) {
            $_SESSION['success'] = 'Notification preferences updated successfully';
        } else {
            $_SESSION['error'] = 'Failed to update preferences';
        }
        
        header('Location: /notifications/preferences');
        exit;
    }
    
    /**
     * Show notification history
     */
    public function history() {
        $this->requireAuth();
        
        $userId = $_SESSION['user_id'];
        $limit = (int)($_GET['limit'] ?? 50);
        
        $notifications = $this->emailModel->getUserHistory($userId, $limit);
        $csrfToken = Security::generateCSRFToken();
        
        // Get pending count for admin
        $pendingCount = 0;
        if (Security::isAdmin()) {
            $pending = $this->emailModel->getPending(1000);
            $pendingCount = count($pending);
        }
        
        // Pass variables to view
        $title = 'Notification History - Helpdesk System';
        $pageTitle = 'Notification History';
        
        require_once __DIR__ . '/../../views/notifications/history.php';
    }
    
    /**
     * Process pending emails manually (Admin only)
     */
    public function processPendingEmails() {
        $this->requireAuth();
        $this->requireAdmin();
        
        try {
            $emailService = new EmailService();
            $results = $emailService->processPendingNotifications(20);
            
            if ($results['sent'] > 0) {
                $_SESSION['success'] = "✓ Successfully sent {$results['sent']} email(s)!";
            }
            
            if ($results['failed'] > 0) {
                $_SESSION['warning'] = "⚠ {$results['failed']} email(s) failed to send. Check error logs.";
            }
            
            if ($results['total'] === 0) {
                $_SESSION['info'] = "No pending emails to process.";
            }
            
        } catch (\Exception $e) {
            $_SESSION['error'] = "Error: " . $e->getMessage();
        }
        
        header('Location: /notifications/history');
        exit;
    }
    
    /**
     * Get pending notifications count (AJAX)
     */
    public function getPendingCount() {
        $this->requireAuth();
        $this->requireAdmin();
        
        $pending = $this->emailModel->getPending(1000);
        
        $this->jsonResponse([
            'count' => count($pending),
            'status' => 'success'
        ]);
    }
    
    /**
     * Test email notification (Admin only)
     */
    public function testEmail() {
        $this->requireAuth();
        $this->requireAdmin();
        $this->validateCSRF();
        
        $userId = $_SESSION['user_id'];
        
        $testNotificationId = $this->emailModel->create([
            'user_id' => $userId,
            'type' => 'test',
            'subject' => 'Test Email from Helpdesk System',
            'body' => '<h2>Test Email</h2><p>This is a test email to verify your notification settings are working correctly.</p><p>If you receive this, your email notifications are configured properly!</p>',
            'status' => 'pending'
        ]);
        
        if ($testNotificationId) {
            $_SESSION['success'] = 'Test email queued successfully. Check your inbox shortly.';
        } else {
            $_SESSION['error'] = 'Failed to queue test email';
        }
        
        header('Location: /notifications/preferences');
        exit;
    }
    
    /**
     * Resend failed notification (Admin only)
     */
    public function resend($notificationId) {
        $this->requireAuth();
        $this->requireAdmin();
        
        // Reset status to pending for retry
        $sql = "UPDATE email_notifications SET status = 'pending', error_message = NULL WHERE id = ?";
        $stmt = $this->emailModel->db->prepare($sql);
        $result = $stmt->execute([$notificationId]);
        
        if ($result) {
            $this->jsonResponse([
                'success' => true,
                'message' => 'Notification queued for resend'
            ]);
        }
        
        $this->jsonResponse([
            'success' => false,
            'message' => 'Failed to resend notification'
        ], 500);
    }
    
    // ==================== PRIVATE HELPER METHODS ====================
    
    private function requireAuth() {
        if (!Security::isAuthenticated()) {
            header('Location: /login');
            exit;
        }
    }
    
    private function requireAdmin() {
        if (!Security::isAdmin()) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            exit;
        }
    }
    
    private function validateCSRF() {
        if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Invalid security token';
            header('Location: /notifications/preferences');
            exit;
        }
    }
    
    private function jsonResponse($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}