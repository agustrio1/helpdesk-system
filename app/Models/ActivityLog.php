<?php
namespace App\Models;

use App\Config\Database;
use PDO;

class ActivityLog {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Log activity
     */
    public function log($data) {
        $sql = "INSERT INTO activity_logs 
                (ticket_id, user_id, action, field_name, old_value, new_value, description, ip_address, user_agent) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['ticket_id'],
            $data['user_id'],
            $data['action'],
            $data['field_name'] ?? null,
            $data['old_value'] ?? null,
            $data['new_value'] ?? null,
            $data['description'] ?? null,
            $data['ip_address'] ?? $_SERVER['REMOTE_ADDR'] ?? null,
            $data['user_agent'] ?? $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    }
    
    /**
     * Get activities by ticket ID
     */
    public function getByTicketId($ticketId, $limit = null) {
        $sql = "SELECT al.*, u.name as user_name, u.role as user_role
                FROM activity_logs al
                JOIN users u ON al.user_id = u.id
                WHERE al.ticket_id = ?
                ORDER BY al.created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$ticketId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get recent activities
     */
    public function getRecent($limit = 50, $userId = null, $role = null) {
        $sql = "SELECT al.*, u.name as user_name, u.role as user_role,
                       t.title as ticket_title, t.status as ticket_status
                FROM activity_logs al
                JOIN users u ON al.user_id = u.id
                JOIN tickets t ON al.ticket_id = t.id
                WHERE 1=1";
        
        $params = [];
        
        // If customer, only show their tickets
        if ($role === 'customer' && $userId) {
            $sql .= " AND t.user_id = ?";
            $params[] = $userId;
        }
        
        $sql .= " ORDER BY al.created_at DESC LIMIT ?";
        $params[] = $limit;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Get activities by user ID
     */
    public function getByUserId($userId, $limit = 50) {
        $sql = "SELECT al.*, t.title as ticket_title, t.status as ticket_status
                FROM activity_logs al
                JOIN tickets t ON al.ticket_id = t.id
                WHERE al.user_id = ?
                ORDER BY al.created_at DESC
                LIMIT ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * Format activity description for display
     */
    public static function formatActivity($activity) {
        $action = $activity['action'];
        $fieldName = $activity['field_name'];
        $oldValue = $activity['old_value'];
        $newValue = $activity['new_value'];
        $userName = $activity['user_name'];
        
        switch ($action) {
            case 'created':
                return "<strong>{$userName}</strong> created the ticket";
            
            case 'updated':
                if ($fieldName === 'status') {
                    return "<strong>{$userName}</strong> changed status from <span class='badge'>{$oldValue}</span> to <span class='badge'>{$newValue}</span>";
                }
                if ($fieldName === 'priority') {
                    return "<strong>{$userName}</strong> changed priority from <span class='badge'>{$oldValue}</span> to <span class='badge'>{$newValue}</span>";
                }
                if ($fieldName === 'assigned_to') {
                    $oldName = $oldValue ?: 'Unassigned';
                    return "<strong>{$userName}</strong> assigned ticket from <strong>{$oldName}</strong> to <strong>{$newValue}</strong>";
                }
                if ($fieldName === 'category') {
                    return "<strong>{$userName}</strong> changed category to <strong>{$newValue}</strong>";
                }
                return "<strong>{$userName}</strong> updated {$fieldName}";
            
            case 'commented':
                return "<strong>{$userName}</strong> added a comment";
            
            case 'closed':
                return "<strong>{$userName}</strong> closed the ticket";
            
            case 'reopened':
                return "<strong>{$userName}</strong> reopened the ticket";
            
            case 'attachment_added':
                return "<strong>{$userName}</strong> added an attachment";
            
            default:
                return $activity['description'] ?? "<strong>{$userName}</strong> performed {$action}";
        }
    }
}