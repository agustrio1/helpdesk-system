<?php
namespace App\Models;

use App\Config\Database;
use PDO;

class Ticket {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Create ticket (updated with category support)
     */
    public function create($data) {
        $sql = "INSERT INTO tickets (user_id, title, description, priority, status, category_id) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        
        $stmt->execute([
            $data['user_id'],
            $data['title'],
            $data['description'],
            $data['priority'] ?? 'medium',
            $data['status'] ?? 'open',
            $data['category_id'] ?? null  // NEW: Category support
        ]);
        
        return $this->db->lastInsertId();
    }
    
    /**
     * Find ticket by ID (updated with category)
     */
    public function findById($id) {
        // Optimized query to prevent N+1
        $sql = "SELECT t.*, 
                       u.name as user_name, u.email as user_email,
                       a.name as assigned_name, a.email as assigned_email,
                       c.name as category_name, c.color as category_color, c.icon as category_icon
                FROM tickets t
                LEFT JOIN users u ON t.user_id = u.id
                LEFT JOIN users a ON t.assigned_to = a.id
                LEFT JOIN categories c ON t.category_id = c.id
                WHERE t.id = ?
                LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Get all tickets (updated with advanced filtering)
     */
    public function getAll($userId = null, $role = 'customer', $filters = []) {
        // Base query with category
        $sql = "SELECT t.*, 
                       u.name as user_name, 
                       a.name as assigned_name,
                       c.name as category_name,
                       c.color as category_color,
                       c.icon as category_icon
                FROM tickets t
                LEFT JOIN users u ON t.user_id = u.id
                LEFT JOIN users a ON t.assigned_to = a.id
                LEFT JOIN categories c ON t.category_id = c.id
                WHERE 1=1";
        
        $params = [];
        
        // PENTING: Role-based filtering (original logic)
        if ($role === 'customer' && $userId) {
            // Customer HANYA bisa lihat ticket sendiri
            $sql .= " AND t.user_id = ?";
            $params[] = $userId;
        }
        
        // NEW: Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $sql .= " AND (t.title LIKE ? OR t.description LIKE ? OR t.id = ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            $params[] = is_numeric($search) ? $search : 0;
        }
        
        // NEW: Status filter
        if (!empty($filters['status'])) {
            $sql .= " AND t.status = ?";
            $params[] = $filters['status'];
        }
        
        // NEW: Priority filter
        if (!empty($filters['priority'])) {
            $sql .= " AND t.priority = ?";
            $params[] = $filters['priority'];
        }
        
        // NEW: Category filter
        if (!empty($filters['category_id'])) {
            $sql .= " AND t.category_id = ?";
            $params[] = $filters['category_id'];
        }
        
        // NEW: Assigned filter
        if (isset($filters['assigned_to'])) {
            if ($filters['assigned_to'] === 'unassigned') {
                $sql .= " AND t.assigned_to IS NULL";
            } elseif ($filters['assigned_to'] === 'me' && $userId) {
                $sql .= " AND t.assigned_to = ?";
                $params[] = $userId;
            } elseif (is_numeric($filters['assigned_to'])) {
                $sql .= " AND t.assigned_to = ?";
                $params[] = $filters['assigned_to'];
            }
        }
        
        // NEW: Date range filter
        if (!empty($filters['date_from'])) {
            $sql .= " AND DATE(t.created_at) >= ?";
            $params[] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $sql .= " AND DATE(t.created_at) <= ?";
            $params[] = $filters['date_to'];
        }
        
        // NEW: Sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'DESC';
        
        $allowedSort = ['id', 'created_at', 'updated_at', 'priority', 'status', 'title'];
        if (!in_array($sortBy, $allowedSort)) {
            $sortBy = 'created_at';
        }
        
        $sortOrder = strtoupper($sortOrder) === 'ASC' ? 'ASC' : 'DESC';
        $sql .= " ORDER BY t.{$sortBy} {$sortOrder}";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Update ticket
     */
    public function update($id, $data) {
        $fields = [];
        $values = [];
        
        // Whitelist allowed fields
        $allowedFields = ['title', 'description', 'priority', 'status', 'category_id', 'assigned_to', 'updated_at'];
        
        foreach ($data as $key => $value) {
            if (in_array($key, $allowedFields)) {
                $fields[] = "$key = ?";
                $values[] = $value;
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $values[] = $id;
        
        $sql = "UPDATE tickets SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute($values);
    }
    
    /**
     * Delete ticket
     */
    public function delete($id) {
        // Cascade delete akan hapus comments dan attachments otomatis (foreign key)
        $sql = "DELETE FROM tickets WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
    
    /**
     * Get statistics (updated)
     */
    public function getStatistics($userId = null, $role = 'customer') {
        // PENTING: Pisahkan statistik berdasarkan role
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'open' THEN 1 ELSE 0 END) as open,
                    SUM(CASE WHEN status = 'progress' THEN 1 ELSE 0 END) as progress,
                    SUM(CASE WHEN status = 'closed' THEN 1 ELSE 0 END) as closed,
                    SUM(CASE WHEN priority = 'urgent' THEN 1 ELSE 0 END) as urgent,
                    SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as today
                FROM tickets";
        
        $params = [];
        
        if ($role === 'customer' && $userId) {
            // Customer HANYA lihat statistik ticket sendiri
            $sql .= " WHERE user_id = ?";
            $params[] = $userId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }
    
    /**
     * Get tickets by status
     */
    public function getByStatus($status, $userId = null, $role = 'customer') {
        $sql = "SELECT t.*, 
                       u.name as user_name, 
                       a.name as assigned_name,
                       c.name as category_name,
                       c.color as category_color
                FROM tickets t
                LEFT JOIN users u ON t.user_id = u.id
                LEFT JOIN users a ON t.assigned_to = a.id
                LEFT JOIN categories c ON t.category_id = c.id
                WHERE t.status = ?";
        
        $params = [$status];
        
        if ($role === 'customer' && $userId) {
            $sql .= " AND t.user_id = ?";
            $params[] = $userId;
        }
        
        $sql .= " ORDER BY t.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Search tickets (kept original, works with or without full-text index)
     */
    public function search($keyword, $userId = null, $role = 'customer') {
        $searchTerm = '%' . $keyword . '%';
        
        $sql = "SELECT t.*, 
                       u.name as user_name, 
                       a.name as assigned_name,
                       c.name as category_name,
                       c.color as category_color
                FROM tickets t
                LEFT JOIN users u ON t.user_id = u.id
                LEFT JOIN users a ON t.assigned_to = a.id
                LEFT JOIN categories c ON t.category_id = c.id
                WHERE (t.title LIKE ? OR t.description LIKE ? OR t.id LIKE ?)";
        
        $params = [$searchTerm, $searchTerm, $searchTerm];
        
        if ($role === 'customer' && $userId) {
            $sql .= " AND t.user_id = ?";
            $params[] = $userId;
        }
        
        $sql .= " ORDER BY t.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    // ========== NEW PHASE 1 METHODS ==========
    
    /**
     * NEW: Search with full-text (requires FULLTEXT index)
     */
    public function searchFullText($query, $userId = null, $role = 'customer') {
        $sql = "SELECT t.*, 
                       u.name as user_name, 
                       u.email as user_email,
                       a.name as assigned_name,
                       c.name as category_name,
                       c.color as category_color,
                       MATCH(t.title, t.description) AGAINST(? IN NATURAL LANGUAGE MODE) as relevance
                FROM tickets t
                LEFT JOIN users u ON t.user_id = u.id
                LEFT JOIN users a ON t.assigned_to = a.id
                LEFT JOIN categories c ON t.category_id = c.id
                WHERE MATCH(t.title, t.description) AGAINST(? IN NATURAL LANGUAGE MODE)";
        
        $params = [$query, $query];
        
        if ($role === 'customer' && $userId) {
            $sql .= " AND t.user_id = ?";
            $params[] = $userId;
        }
        
        $sql .= " ORDER BY relevance DESC, t.created_at DESC";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            // Fallback to LIKE search if FULLTEXT index not available
            return $this->search($query, $userId, $role);
        }
    }
    
    /**
     * NEW: Get tickets by category
     */
    public function getByCategory($categoryId, $userId = null, $role = 'customer') {
        $sql = "SELECT t.*, 
                       u.name as user_name, 
                       a.name as assigned_name,
                       c.name as category_name,
                       c.color as category_color
                FROM tickets t
                LEFT JOIN users u ON t.user_id = u.id
                LEFT JOIN users a ON t.assigned_to = a.id
                LEFT JOIN categories c ON t.category_id = c.id
                WHERE t.category_id = ?";
        
        $params = [$categoryId];
        
        if ($role === 'customer' && $userId) {
            $sql .= " AND t.user_id = ?";
            $params[] = $userId;
        }
        
        $sql .= " ORDER BY t.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * NEW: Get tickets assigned to specific user
     */
    public function getAssignedTo($userId) {
        $sql = "SELECT t.*, 
                       u.name as user_name, 
                       c.name as category_name, 
                       c.color as category_color
                FROM tickets t
                LEFT JOIN users u ON t.user_id = u.id
                LEFT JOIN categories c ON t.category_id = c.id
                WHERE t.assigned_to = ? AND t.status != 'closed'
                ORDER BY 
                    CASE t.priority 
                        WHEN 'urgent' THEN 1
                        WHEN 'high' THEN 2
                        WHEN 'medium' THEN 3
                        WHEN 'low' THEN 4
                    END,
                    t.created_at ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    /**
     * NEW: Get overdue tickets (no activity in 24 hours)
     */
    public function getOverdue() {
        $sql = "SELECT t.*, 
                       u.name as user_name, 
                       a.name as assigned_name,
                       c.name as category_name
                FROM tickets t
                LEFT JOIN users u ON t.user_id = u.id
                LEFT JOIN users a ON t.assigned_to = a.id
                LEFT JOIN categories c ON t.category_id = c.id
                WHERE t.status IN ('open', 'progress')
                AND t.updated_at < DATE_SUB(NOW(), INTERVAL 24 HOUR)
                ORDER BY t.priority DESC, t.created_at ASC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * NEW: Get priority distribution
     */
    public function getPriorityDistribution($userId = null, $role = 'customer') {
        $sql = "SELECT priority, COUNT(*) as count
                FROM tickets";
        
        $params = [];
        
        if ($role === 'customer' && $userId) {
            $sql .= " WHERE user_id = ?";
            $params[] = $userId;
        }
        
        $sql .= " GROUP BY priority";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        // Format result as associative array
        $result = [
            'low' => 0,
            'medium' => 0,
            'high' => 0,
            'urgent' => 0
        ];
        
        foreach ($stmt->fetchAll() as $row) {
            $result[$row['priority']] = (int)$row['count'];
        }
        
        return $result;
    }
    
    /**
     * NEW: Get category distribution
     */
    public function getCategoryDistribution($userId = null, $role = 'customer') {
        $sql = "SELECT c.id, c.name, c.color, COUNT(t.id) as count
                FROM categories c
                LEFT JOIN tickets t ON c.id = t.category_id";
        
        $params = [];
        
        if ($role === 'customer' && $userId) {
            $sql .= " AND t.user_id = ?";
            $params[] = $userId;
        }
        
        $sql .= " WHERE c.is_active = 1
                  GROUP BY c.id, c.name, c.color
                  ORDER BY count DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}