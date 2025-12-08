<?php
namespace App\Models;

use App\Config\Database;
use PDO;

class Category {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Get all active categories (tanpa ticket count)
     */
    public function getAll($includeInactive = false) {
        $sql = "SELECT * FROM categories";
        
        if (!$includeInactive) {
            $sql .= " WHERE is_active = 1";
        }
        
        $sql .= " ORDER BY name ASC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Get all categories WITH ticket count (untuk index page)
     */
    public function getAllWithTicketCount($includeInactive = true) {
        $sql = "SELECT c.*, COUNT(t.id) as ticket_count
                FROM categories c
                LEFT JOIN tickets t ON c.id = t.category_id";
        
        if (!$includeInactive) {
            $sql .= " WHERE c.is_active = 1";
        }
        
        $sql .= " GROUP BY c.id ORDER BY c.name ASC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Get active categories only (untuk dropdown)
     */
    public function getActive() {
        $sql = "SELECT * FROM categories WHERE is_active = 1 ORDER BY name ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Get category by ID (tanpa ticket count)
     */
    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Get category by ID WITH ticket count (untuk edit/delete)
     */
    public function findByIdWithTicketCount($id) {
        $sql = "SELECT c.*, COUNT(t.id) as ticket_count
                FROM categories c
                LEFT JOIN tickets t ON c.id = t.category_id
                WHERE c.id = ?
                GROUP BY c.id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Get category by slug
     */
    public function findBySlug($slug) {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE slug = ?");
        $stmt->execute([$slug]);
        return $stmt->fetch();
    }
    
    /**
     * Create new category
     */
    public function create($data) {
        $sql = "INSERT INTO categories (name, slug, description, color, icon, is_active) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            $data['name'],
            $data['slug'],
            $data['description'] ?? null,
            $data['color'] ?? '#3B82F6',
            $data['icon'] ?? 'folder',
            $data['is_active'] ?? 1
        ]);
        
        if ($result) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Update category
     */
    public function update($id, $data) {
        $fields = [];
        $values = [];
        
        $allowedFields = ['name', 'slug', 'description', 'color', 'icon', 'is_active'];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = ?";
                $values[] = $data[$field];
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $values[] = $id;
        $sql = "UPDATE categories SET " . implode(', ', $fields) . " WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }
    
    /**
     * Delete category
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM categories WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Get category with ticket count (DEPRECATED - use getAllWithTicketCount)
     */
    public function getWithTicketCount() {
        return $this->getAllWithTicketCount(false); // only active
    }
    
    /**
     * Generate slug from name
     */
    public static function generateSlug($name) {
        $slug = strtolower($name);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');
        return $slug;
    }
    
    /**
     * Get category statistics
     */
    public function getStatistics() {
        try {
            $stmt = $this->db->query("
                SELECT 
                    COUNT(DISTINCT c.id) as total_categories,
                    SUM(CASE WHEN c.is_active = 1 THEN 1 ELSE 0 END) as active_categories,
                    SUM(CASE WHEN c.is_active = 0 THEN 1 ELSE 0 END) as inactive_categories,
                    COUNT(t.id) as total_tickets
                FROM categories c
                LEFT JOIN tickets t ON c.id = t.category_id
            ");
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting category statistics: " . $e->getMessage());
            return [
                'total_categories' => 0,
                'active_categories' => 0,
                'inactive_categories' => 0,
                'total_tickets' => 0
            ];
        }
    }
}