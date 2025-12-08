<?php
namespace App\Controllers;

use App\Models\ActivityLog;
use App\Helpers\Security;

class ActivityLogController {
    private $activityLog;
    
    public function __construct() {
        $this->activityLog = new ActivityLog();
    }
    
    /**
     * Display recent activities
     */
    public function index() {
        $this->requireAuth();
        
        $userId = $_SESSION['user_id'];
        $role = $_SESSION['user_role'];
        $limit = (int)($_GET['limit'] ?? 50);
        
        // Get activities based on role
        $activities = $this->activityLog->getRecent($limit, $userId, $role);
        
        require_once __DIR__ . '/../../views/activities/index.php';
    }
    
    /**
     * Get activities for specific ticket (AJAX)
     */
    public function getByTicket($ticketId) {
        $this->requireAuth();
        
        $limit = (int)($_GET['limit'] ?? 20);
        $activities = $this->activityLog->getByTicketId($ticketId, $limit);
        
        // Format activities for display
        $formatted = array_map(function($activity) {
            return [
                'id' => $activity['id'],
                'description' => ActivityLog::formatActivity($activity),
                'created_at' => $activity['created_at'],
                'user_name' => $activity['user_name'],
                'user_role' => $activity['user_role'],
                'action' => $activity['action']
            ];
        }, $activities);
        
        $this->jsonResponse(['activities' => $formatted]);
    }
    
    /**
     * Get user's own activities
     */
    public function getUserActivities() {
        $this->requireAuth();
        
        $userId = $_SESSION['user_id'];
        $limit = (int)($_GET['limit'] ?? 50);
        
        $activities = $this->activityLog->getByUserId($userId, $limit);
        
        require_once __DIR__ . '/../../views/activities/user.php';
    }
    
    /**
     * Export activity log to CSV (Admin only)
     */
    public function export() {
        $this->requireAuth();
        $this->requireAdmin();
        
        $userId = null;
        $role = 'admin';
        $limit = 1000;
        
        $activities = $this->activityLog->getRecent($limit, $userId, $role);
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="activity_log_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // CSV headers
        fputcsv($output, ['ID', 'Ticket ID', 'User', 'Action', 'Field', 'Old Value', 'New Value', 'Description', 'IP Address', 'Created At']);
        
        // CSV rows
        foreach ($activities as $activity) {
            fputcsv($output, [
                $activity['id'],
                $activity['ticket_id'],
                $activity['user_name'],
                $activity['action'],
                $activity['field_name'] ?? '',
                $activity['old_value'] ?? '',
                $activity['new_value'] ?? '',
                $activity['description'] ?? '',
                $activity['ip_address'] ?? '',
                $activity['created_at']
            ]);
        }
        
        fclose($output);
        exit;
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
            $this->jsonResponse(['success' => false, 'message' => 'Access denied'], 403);
        }
    }
    
    private function jsonResponse($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}