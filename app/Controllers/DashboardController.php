<?php
namespace App\Controllers;

use App\Models\Ticket;
use App\Helpers\Security;

class DashboardController {
    private $ticketModel;
    
    public function __construct() {
        $this->ticketModel = new Ticket();
    }
    
    public function index() {
        if (!Security::isAuthenticated()) {
            header('Location: /login');
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        $role = $_SESSION['user_role'];
        
        // Get statistics
        $stats = $this->ticketModel->getStatistics($userId, $role);
        
        // Get recent tickets (optimized query with JOIN)
        $tickets = $this->ticketModel->getAll($userId, $role);
        $recentTickets = array_slice($tickets, 0, 5);
        
        // Calculate additional stats
        $todayTickets = 0;
        $urgentTickets = 0;
        $today = date('Y-m-d');
        
        foreach ($tickets as $ticket) {
            if (date('Y-m-d', strtotime($ticket['created_at'])) === $today) {
                $todayTickets++;
            }
            if ($ticket['priority'] === 'urgent' && $ticket['status'] !== 'closed') {
                $urgentTickets++;
            }
        }
        
        // Get priority distribution
        $priorityStats = [
            'low' => 0,
            'medium' => 0,
            'high' => 0,
            'urgent' => 0
        ];
        
        foreach ($tickets as $ticket) {
            if (isset($priorityStats[$ticket['priority']])) {
                $priorityStats[$ticket['priority']]++;
            }
        }
        
        require_once __DIR__ . '/../../views/dashboard/index.php';
    }
}