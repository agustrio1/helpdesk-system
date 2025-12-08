<?php
namespace App\Models;

use App\Config\Database;
use PDO;

class NotificationPreference {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Get or create user preferences
     */
    public function getOrCreate($userId) {
        $prefs = $this->get($userId);
        
        if (!$prefs) {
            // Create default preferences
            $this->create($userId);
            $prefs = $this->get($userId);
        }
        
        return $prefs;
    }
    
    /**
     * Get user preferences
     */
    public function get($userId) {
        $stmt = $this->db->prepare("SELECT * FROM notification_preferences WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }
    
    /**
     * Create default preferences
     */
    public function create($userId) {
        $sql = "INSERT INTO notification_preferences 
                (user_id, ticket_created, ticket_updated, ticket_assigned, comment_added, status_changed) 
                VALUES (?, 1, 1, 1, 1, 1)
                ON DUPLICATE KEY UPDATE id=id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId]);
    }
    
    /**
     * Update preferences
     */
    public function update($userId, $data) {
        $fields = [];
        $values = [];
        
        $allowedFields = ['ticket_created', 'ticket_updated', 'ticket_assigned', 'comment_added', 'status_changed'];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = ?";
                // Convert explicitly to integer (0 or 1)
                $values[] = (int)(bool)$data[$field];
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $values[] = $userId;
        $sql = "UPDATE notification_preferences SET " . implode(', ', $fields) . " WHERE user_id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }
    
    /**
     * Check if user should be notified
     */
    public function shouldNotify($userId, $type) {
        $prefs = $this->getOrCreate($userId);
        
        if (!$prefs) {
            return true; // Default to notify if no preferences
        }
        
        return (bool)($prefs[$type] ?? true);
    }
}