<?php
namespace App\Models;

use App\Config\Database;
use PDO;

class Comment {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Create new comment
     */
    public function create($data) {
        $sql = "INSERT INTO comments (ticket_id, user_id, comment, is_internal) 
                VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        
        // Convert boolean to integer (0 or 1)
        $isInternal = isset($data['is_internal']) ? (int)$data['is_internal'] : 0;
        
        $result = $stmt->execute([
            $data['ticket_id'],
            $data['user_id'],
            $data['comment'],
            $isInternal
        ]);
        
        return $result ? $this->db->lastInsertId() : false;
    }
    
    /**
     * Find comment by ID
     */
    public function findById($id) {
        $sql = "SELECT c.*, u.name as user_name, u.role as user_role
                FROM comments c
                LEFT JOIN users u ON c.user_id = u.id
                WHERE c.id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Get comments by ticket ID
     */
    public function getByTicketId($ticketId, $isAdmin = false) {
        // Optimized query with JOIN to prevent N+1
        if ($isAdmin) {
            $sql = "SELECT c.*, u.name as user_name, u.role as user_role
                    FROM comments c
                    LEFT JOIN users u ON c.user_id = u.id
                    WHERE c.ticket_id = ?
                    ORDER BY c.created_at ASC";
        } else {
            $sql = "SELECT c.*, u.name as user_name, u.role as user_role
                    FROM comments c
                    LEFT JOIN users u ON c.user_id = u.id
                    WHERE c.ticket_id = ? AND c.is_internal = 0
                    ORDER BY c.created_at ASC";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$ticketId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Update comment
     */
    public function update($id, $data) {
        $sql = "UPDATE comments SET comment = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$data['comment'], $id]);
    }
    
    /**
     * Delete comment
     */
    public function delete($id) {
        $sql = "DELETE FROM comments WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
    
    /**
     * Count comments by ticket ID
     */
    public function countByTicketId($ticketId, $includeInternal = false) {
        if ($includeInternal) {
            $sql = "SELECT COUNT(*) FROM comments WHERE ticket_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$ticketId]);
        } else {
            $sql = "SELECT COUNT(*) FROM comments WHERE ticket_id = ? AND is_internal = 0";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$ticketId]);
        }
        
        return $stmt->fetchColumn();
    }
    
    /**
     * Get latest comments (for dashboard)
     */
    public function getLatest($limit = 5, $userId = null, $isAdmin = false) {
        if ($userId && !$isAdmin) {
            // Customer: only their tickets' comments
            $sql = "SELECT c.*, u.name as user_name, t.title as ticket_title
                    FROM comments c
                    LEFT JOIN users u ON c.user_id = u.id
                    LEFT JOIN tickets t ON c.ticket_id = t.id
                    WHERE t.user_id = ? AND c.is_internal = 0
                    ORDER BY c.created_at DESC
                    LIMIT ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId, $limit]);
        } else {
            // Admin: all comments
            $sql = "SELECT c.*, u.name as user_name, t.title as ticket_title
                    FROM comments c
                    LEFT JOIN users u ON c.user_id = u.id
                    LEFT JOIN tickets t ON c.ticket_id = t.id
                    ORDER BY c.created_at DESC
                    LIMIT ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$limit]);
        }
        
        return $stmt->fetchAll();
    }
}