<?php
namespace App\Models;

use App\Config\Database;
use PDO;

class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function create($data) {
        $sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        
        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
        
        return $stmt->execute([
            $data['name'],
            $data['email'],
            $hashedPassword,
            $data['role'] ?? 'customer'
        ]);
    }
    
    public function findByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch();
    }
    
    public function findById($id) {
        $sql = "SELECT id, name, email, role, avatar, created_at FROM users WHERE id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    public function getAllAdmins() {
        $sql = "SELECT id, name, email FROM users WHERE role = 'admin'";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Update user data
     * INI METHOD YANG KURANG!
     */
    public function update($id, $data) {
        // Build dynamic UPDATE query based on provided data
        $fields = [];
        $params = [];
        
        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $params[] = $value;
        }
        
        // Add updated_at timestamp
        $fields[] = "updated_at = NOW()";
        
        // Add user ID for WHERE clause
        $params[] = $id;
        
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute($params);
    }
    
    /**
     * Get all users with filters
     */
    public function getAll($filters = []) {
        $sql = "SELECT id, name, email, role, avatar, created_at FROM users WHERE 1=1";
        $params = [];
        
        // Filter by role
        if (!empty($filters['role'])) {
            $sql .= " AND role = ?";
            $params[] = $filters['role'];
        }
        
        // Search
        if (!empty($filters['search'])) {
            $sql .= " AND (name LIKE ? OR email LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Delete user
     */
    public function delete($id) {
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}