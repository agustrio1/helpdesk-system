<?php
namespace App\Models;

use App\Config\Database;
use PDO;

class Attachment {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function create($data) {
        $sql = "INSERT INTO attachments (ticket_id, user_id, filename, original_filename, file_path, file_size, mime_type) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            $data['ticket_id'],
            $data['user_id'],
            $data['filename'],
            $data['original_filename'],
            $data['file_path'],
            $data['file_size'],
            $data['mime_type']
        ]);
    }
    
    public function getByTicketId($ticketId) {
        $sql = "SELECT * FROM attachments WHERE ticket_id = ? ORDER BY created_at ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$ticketId]);
        return $stmt->fetchAll();
    }
    
    public function findById($id) {
        $sql = "SELECT * FROM attachments WHERE id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function delete($id) {
        $sql = "DELETE FROM attachments WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}