<?php
namespace App\Models;

use App\Config\Database;
use PDO;

class EmailNotification {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Create email notification
     */
    public function create($data) {
        $sql = "INSERT INTO email_notifications 
                (user_id, ticket_id, type, subject, body, status) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['user_id'],
            $data['ticket_id'] ?? null,
            $data['type'],
            $data['subject'],
            $data['body'],
            $data['status'] ?? 'pending'
        ]);
        
        return $this->db->lastInsertId();
    }
    
    /**
     * Get pending notifications
     */
    public function getPending($limit = 10) {
        $sql = "SELECT en.*, u.email as user_email, u.name as user_name
                FROM email_notifications en
                JOIN users u ON en.user_id = u.id
                WHERE en.status = 'pending'
                ORDER BY en.created_at ASC
                LIMIT ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * Mark as sent
     */
    public function markAsSent($id) {
        $stmt = $this->db->prepare(
            "UPDATE email_notifications 
             SET status = 'sent', sent_at = NOW() 
             WHERE id = ?"
        );
        return $stmt->execute([$id]);
    }
    
    /**
     * Mark as failed
     */
    public function markAsFailed($id, $errorMessage) {
        $stmt = $this->db->prepare(
            "UPDATE email_notifications 
             SET status = 'failed', error_message = ? 
             WHERE id = ?"
        );
        return $stmt->execute([$errorMessage, $id]);
    }
    
    /**
     * Get user notification history
     */
    public function getUserHistory($userId, $limit = 50) {
        $sql = "SELECT * FROM email_notifications 
                WHERE user_id = ? 
                ORDER BY created_at DESC 
                LIMIT ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll();
    }
}
