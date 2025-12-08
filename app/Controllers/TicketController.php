<?php
namespace App\Controllers;

use App\Models\Ticket;
use App\Models\User;
use App\Models\Attachment;
use App\Models\Comment;
use App\Models\Category;
use App\Models\ActivityLog;  
use App\Helpers\Security;
use App\Services\EmailService;

class TicketController {
    private $ticketModel;
    private $userModel;
    private $attachmentModel;
    private $commentModel;
    private $categoryModel;
    private $activityLog;
    private $emailService;
    
    public function __construct() {
        $this->ticketModel = new Ticket();
        $this->userModel = new User();
        $this->attachmentModel = new Attachment();
        $this->commentModel = new Comment();
        $this->categoryModel = new Category();
        $this->activityLog = new ActivityLog();
        $this->emailService = new EmailService();
    }
    
    /**
     * Display tickets with filters and pagination
     */
    public function index() {
        $this->requireAuth();
        
        $userId = $_SESSION['user_id'];
        $role = $_SESSION['user_role'];
        
        // Pagination settings
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 20; // Items per page
        $offset = ($page - 1) * $perPage;
        
        // Build filters from query params
        $filters = [
            'search' => $_GET['search'] ?? '',
            'status' => $_GET['status'] ?? '',
            'priority' => $_GET['priority'] ?? '',
            'category_id' => $_GET['category_id'] ?? '',
            'assigned_to' => $_GET['assigned_to'] ?? '',
            'date_from' => $_GET['date_from'] ?? '',
            'date_to' => $_GET['date_to'] ?? '',
            'sort_by' => $_GET['sort_by'] ?? 'created_at',
            'sort_order' => $_GET['sort_order'] ?? 'DESC'
        ];
        
        // Get all tickets with filters first (untuk menghitung total)
        $allTickets = $this->ticketModel->getAll($userId, $role, $filters);
        $totalTickets = count($allTickets);
        $totalPages = ceil($totalTickets / $perPage);
        
        // Slice tickets for current page
        $tickets = array_slice($allTickets, $offset, $perPage);
        
        // Get statistics
        $stats = $this->ticketModel->getStatistics($userId, $role);
        
        // Get categories for filter dropdown
        $categories = $this->categoryModel->getActive();
        
        // Get admins for assigned filter (admin only)
        $admins = Security::isAdmin() ? $this->userModel->getAllAdmins() : [];
        
        // Pagination data
        $pagination = [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'per_page' => $perPage,
            'total_items' => $totalTickets,
            'has_prev' => $page > 1,
            'has_next' => $page < $totalPages,
            'prev_page' => $page - 1,
            'next_page' => $page + 1,
            'start_item' => $totalTickets > 0 ? $offset + 1 : 0,
            'end_item' => min($offset + $perPage, $totalTickets)
        ];
        
        require_once __DIR__ . '/../../views/tickets/index.php';
    }
    
    /**
     * Show create form
     */
    public function create() {
        $this->requireAuth();
        
        $categories = $this->categoryModel->getActive();
        $csrfToken = Security::generateCSRFToken();
        
        require_once __DIR__ . '/../../views/tickets/create.php';
    }
    
    /**
     * Store new ticket
     */
    public function store() {
        $this->requireAuth();
        $this->validateCSRF();
        
        $data = $this->validateTicketInput($_POST);
        
        // Create ticket
        $ticketId = $this->ticketModel->create([
            'user_id' => $_SESSION['user_id'],
            'title' => $data['title'],
            'description' => $data['description'],
            'priority' => $data['priority'],
            'category_id' => $data['category_id'] ?? null,
            'status' => 'open'
        ]);
        
        if (!$ticketId) {
            $this->redirectWithError('/tickets/create', 'Failed to create ticket');
        }
        
        // Log activity
        $this->activityLog->log([
            'ticket_id' => $ticketId,
            'user_id' => $_SESSION['user_id'],
            'action' => 'created',
            'description' => 'Ticket created'
        ]);
        
        // Handle file uploads
        $this->handleFileUploads($ticketId, $_FILES['attachments'] ?? null);
        
        // Queue email notification
        $ticket = $this->ticketModel->findById($ticketId);
        $user = $this->userModel->findById($_SESSION['user_id']);
        $this->emailService->queueTicketCreated($ticket, $user);
        
        $this->redirectWithSuccess('/tickets/' . $ticketId, 'Ticket created successfully!');
    }
    
    /**
     * Show ticket details
     */
    public function show($id) {
        $this->requireAuth();
        
        $ticket = $this->getTicketOrFail($id);
        $this->checkTicketViewPermission($ticket);
        
        $isAdmin = Security::isAdmin();
        
        // Get related data
        $comments = $this->commentModel->getByTicketId($id, $isAdmin);
        $attachments = $this->attachmentModel->getByTicketId($id);
        $activities = $this->activityLog->getByTicketId($id, 20);
        $categories = $isAdmin ? $this->categoryModel->getActive() : [];
        $admins = $isAdmin ? $this->userModel->getAllAdmins() : [];
        
        $csrfToken = Security::generateCSRFToken();
        
        require_once __DIR__ . '/../../views/tickets/show.php';
    }
    
    /**
     * Update ticket
     */
    public function update($id) {
        $this->requireAuth();
        $this->validateCSRF();
        
        $ticket = $this->getTicketOrFail($id);
        
        // Prevent updates to closed tickets
        if ($ticket['status'] === 'closed') {
            $this->redirectWithError('/tickets/' . $id, 'Cannot modify a closed ticket');
        }
        
        $updateData = $this->prepareUpdateData($ticket, $_POST);
        
        if (empty($updateData)) {
            $this->redirectWithError('/tickets/' . $id, 'No valid updates provided');
        }
        
        // Track changes for activity log
        $changes = $this->trackChanges($ticket, $updateData);
        
        // Update ticket
        $updateData['updated_at'] = date('Y-m-d H:i:s');
        $this->ticketModel->update($id, $updateData);
        
        // Log activities
        foreach ($changes as $field => $change) {
            $this->activityLog->log([
                'ticket_id' => $id,
                'user_id' => $_SESSION['user_id'],
                'action' => 'updated',
                'field_name' => $field,
                'old_value' => $change['old'],
                'new_value' => $change['new'],
                'description' => ucfirst($field) . ' changed'
            ]);
        }
        
        // Queue email notification for updates
        $updatedTicket = $this->ticketModel->findById($id);
        $user = $this->userModel->findById($ticket['user_id']);
        if ($user) {
            $this->emailService->queueTicketUpdated($updatedTicket, $user, $changes);
        }
        
        // If assigned, notify assignee
        if (isset($updateData['assigned_to']) && $updateData['assigned_to']) {
            $assignedUser = $this->userModel->findById($updateData['assigned_to']);
            if ($assignedUser) {
                $this->activityLog->log([
                    'ticket_id' => $id,
                    'user_id' => $_SESSION['user_id'],
                    'action' => 'updated',
                    'field_name' => 'assigned_to',
                    'old_value' => $ticket['assigned_name'] ?? 'Unassigned',
                    'new_value' => $assignedUser['name'],
                    'description' => 'Ticket assigned'
                ]);
                
                $this->emailService->queueTicketAssigned($updatedTicket, $assignedUser);
            }
        }
        
        $this->redirectWithSuccess('/tickets/' . $id, 'Ticket updated successfully');
    }
    
    /**
     * Delete ticket
     */
    public function delete($id) {
        $this->requireAuth();
        $this->requireAdmin();
        
        $ticket = $this->getTicketOrFail($id);
        
        // Log before deletion
        $this->activityLog->log([
            'ticket_id' => $id,
            'user_id' => $_SESSION['user_id'],
            'action' => 'deleted',
            'description' => "Ticket #{$id} deleted"
        ]);
        
        $result = $this->ticketModel->delete($id);
        
        if ($result) {
            $this->jsonResponse(['success' => true, 'message' => 'Ticket deleted successfully']);
        }
        
        $this->jsonResponse(['success' => false, 'message' => 'Failed to delete ticket'], 500);
    }
    
    /**
     * Search tickets (AJAX endpoint)
     */
    public function search() {
        $this->requireAuth();
        
        $query = $_GET['q'] ?? '';
        $userId = $_SESSION['user_id'];
        $role = $_SESSION['user_role'];
        
        if (strlen($query) < 2) {
            $this->jsonResponse(['results' => []]);
        }
        
        $results = $this->ticketModel->search($query, $userId, $role);
        
        $this->jsonResponse(['results' => $results]);
    }
    
    /**
     * Export tickets to CSV
     */
    public function export() {
        $this->requireAuth();
        $this->requireAdmin();
        
        // Disable error reporting untuk export
        error_reporting(0);
        ini_set('display_errors', 0);
        
        // Clean any output buffers
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        $userId = $_SESSION['user_id'];
        $role = $_SESSION['user_role'];
        
        // Build filters from query params (sama seperti di index)
        $filters = [
            'search' => $_GET['search'] ?? '',
            'status' => $_GET['status'] ?? '',
            'priority' => $_GET['priority'] ?? '',
            'category_id' => $_GET['category_id'] ?? '',
            'assigned_to' => $_GET['assigned_to'] ?? '',
        ];
        
        $tickets = $this->ticketModel->getAll($userId, $role, $filters);
        
        // Set headers
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="tickets_' . date('Y-m-d') . '.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        $output = fopen('php://output', 'w');
        
        // CSV headers - No pakai nomor urut
        fputcsv($output, ['No', 'Title', 'Status', 'Priority', 'Category', 'User', 'Assigned To', 'Created', 'Updated'], ',', '"');
        
        // CSV rows dengan nomor urut
        $no = 1;
        foreach ($tickets as $ticket) {
            fputcsv($output, [
                $no++, // Nomor urut
                $ticket['title'] ?? '',
                ucfirst($ticket['status'] ?? ''),
                ucfirst($ticket['priority'] ?? ''),
                $ticket['category_name'] ?? 'N/A',
                $ticket['user_name'] ?? '',
                $ticket['assigned_name'] ?? 'Unassigned',
                isset($ticket['created_at']) ? date('Y-m-d H:i:s', strtotime($ticket['created_at'])) : '',
                isset($ticket['updated_at']) ? date('Y-m-d H:i:s', strtotime($ticket['updated_at'])) : ''
            ], ',', '"');
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
    
    private function validateCSRF() {
        if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Invalid security token';
            header('Location: /tickets');
            exit;
        }
    }
    
    private function getTicketOrFail($id) {
        $ticket = $this->ticketModel->findById($id);
        
        if (!$ticket) {
            $_SESSION['error'] = 'Ticket not found';
            header('Location: /tickets');
            exit;
        }
        
        return $ticket;
    }
    
    private function checkTicketViewPermission($ticket) {
        $isCustomer = $_SESSION['user_role'] === 'customer';
        $isOwner = $ticket['user_id'] == $_SESSION['user_id'];
        
        if ($isCustomer && !$isOwner) {
            $_SESSION['error'] = 'Access denied - You can only view your own tickets';
            header('Location: /tickets');
            exit;
        }
    }
    
    private function validateTicketInput($input) {
        $title = Security::sanitize($input['title'] ?? '');
        $description = $input['description'] ?? '';
        $priority = Security::sanitize($input['priority'] ?? 'medium');
        $category_id = isset($input['category_id']) && $input['category_id'] !== '' 
            ? (int)$input['category_id'] 
            : null;
        
        $errors = [];
        
        if (strlen($title) < 5) {
            $errors[] = 'Title must be at least 5 characters';
        }
        
        if (strlen(strip_tags($description)) < 10) {
            $errors[] = 'Description must be at least 10 characters';
        }
        
        if (!in_array($priority, ['low', 'medium', 'high', 'urgent'])) {
            $errors[] = 'Invalid priority';
        }
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode(', ', $errors);
            $_SESSION['old_input'] = compact('title', 'description', 'priority', 'category_id');
            header('Location: /tickets/create');
            exit;
        }
        
        return compact('title', 'description', 'priority', 'category_id');
    }
    
    private function prepareUpdateData($ticket, $input) {
        $isAdmin = Security::isAdmin();
        $isOwner = $ticket['user_id'] == $_SESSION['user_id'];
        $updateData = [];
        
        // Customer can only close their own ticket
        if (!$isAdmin && $isOwner) {
            $status = Security::sanitize($input['status'] ?? '');
            
            if ($status === 'closed') {
                $updateData['status'] = 'closed';
            } elseif ($status) {
                $_SESSION['error'] = 'You can only close your own tickets';
            }
            
            return $updateData;
        }
        
        // Admin can update everything
        if (!$isAdmin) {
            $_SESSION['error'] = 'Access denied';
            header('Location: /tickets/' . $ticket['id']);
            exit;
        }
        
        // Admin updates
        $status = Security::sanitize($input['status'] ?? '');
        if (in_array($status, ['open', 'progress', 'closed'])) {
            $updateData['status'] = $status;
        }
        
        $priority = Security::sanitize($input['priority'] ?? '');
        if (in_array($priority, ['low', 'medium', 'high', 'urgent'])) {
            $updateData['priority'] = $priority;
        }
        
        if (isset($input['category_id'])) {
            $category_id = $input['category_id'] !== '' ? (int)$input['category_id'] : null;
            $updateData['category_id'] = $category_id;
        }
        
        if (isset($input['assigned_to'])) {
            $assignedTo = (int)$input['assigned_to'];
            $updateData['assigned_to'] = $assignedTo > 0 ? $assignedTo : null;
        }
        
        return $updateData;
    }
    
    private function trackChanges($oldTicket, $newData) {
        $changes = [];
        
        foreach ($newData as $field => $newValue) {
            $oldValue = $oldTicket[$field] ?? null;
            
            if ($oldValue != $newValue) {
                $changes[$field] = [
                    'old' => $oldValue,
                    'new' => $newValue
                ];
            }
        }
        
        return $changes;
    }
    
    private function handleFileUploads($ticketId, $files) {
        if (!$files || empty($files['name'][0])) {
            return;
        }
        
        $uploadPath = __DIR__ . '/../../public/uploads/';
        
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }
        
        $fileCount = count($files['name']);
        
        for ($i = 0; $i < $fileCount; $i++) {
            if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                continue;
            }
            
            $file = [
                'name' => $files['name'][$i],
                'type' => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error' => $files['error'][$i],
                'size' => $files['size'][$i]
            ];
            
            $this->processFileUpload($ticketId, $file, $uploadPath);
        }
    }
    
    private function processFileUpload($ticketId, $file, $uploadPath) {
        $validation = Security::validateFileUpload($file);
        
        if (!$validation['success']) {
            return;
        }
        
        $secureFilename = Security::generateSecureFilename($file['name']);
        $filePath = $uploadPath . $secureFilename;
        
        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            return;
        }
        
        $this->attachmentModel->create([
            'ticket_id' => $ticketId,
            'user_id' => $_SESSION['user_id'],
            'filename' => $secureFilename,
            'original_filename' => $file['name'],
            'file_path' => 'uploads/' . $secureFilename,
            'file_size' => $file['size'],
            'mime_type' => $validation['mime_type']
        ]);
        
        // Log attachment
        $this->activityLog->log([
            'ticket_id' => $ticketId,
            'user_id' => $_SESSION['user_id'],
            'action' => 'attachment_added',
            'description' => 'Attachment added: ' . $file['name']
        ]);
    }
    
    private function redirectWithSuccess($url, $message) {
        $_SESSION['success'] = $message;
        header('Location: ' . $url);
        exit;
    }
    
    private function redirectWithError($url, $message) {
        $_SESSION['error'] = $message;
        header('Location: ' . $url);
        exit;
    }
    
    private function jsonResponse($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}