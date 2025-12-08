<?php
namespace App\Controllers;

use App\Models\Comment;
use App\Models\Ticket;
use App\Models\User;
use App\Models\ActivityLog;
use App\Helpers\Security;
use App\Services\EmailService;

class CommentController {
    private $commentModel;
    private $ticketModel;
    private $userModel;
    private $activityLog;
    private $emailService;
    
    public function __construct() {
        $this->commentModel = new Comment();
        $this->ticketModel = new Ticket();
        $this->userModel = new User();
        $this->activityLog = new ActivityLog();
        $this->emailService = new EmailService();
    }
    
    /**
     * Store new comment
     */
    public function store() {
        if (!Security::isAuthenticated()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }
        
        // CSRF Protection
        if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Invalid security token']);
            exit;
        }
        
        $ticketId = (int)($_POST['ticket_id'] ?? 0);
        $comment = Security::sanitize($_POST['comment'] ?? '');
        $isInternal = isset($_POST['is_internal']) && $_POST['is_internal'] === '1';
        
        // Validation
        if (empty($comment) || strlen(strip_tags($comment)) < 3) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Comment must be at least 3 characters']);
            exit;
        }
        
        // Check if ticket exists
        $ticket = $this->ticketModel->findById($ticketId);
        
        if (!$ticket) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Ticket not found']);
            exit;
        }
        
        // IMPORTANT: Check if ticket is closed
        if ($ticket['status'] === 'closed') {
            $_SESSION['error'] = 'Cannot add comment to a closed ticket';
            
            if ($this->isAjaxRequest()) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Cannot add comment to a closed ticket']);
                exit;
            }
            
            header('Location: /tickets/' . $ticketId);
            exit;
        }
        
        // Check permission
        if ($_SESSION['user_role'] === 'customer') {
            // Customer can only comment on their own tickets
            if ($ticket['user_id'] != $_SESSION['user_id']) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Access denied']);
                exit;
            }
            // Customer cannot create internal comments
            $isInternal = false;
        }
        
        // Rate limiting
        if (!Security::checkRateLimit('comment_' . $_SESSION['user_id'], 10, 60)) {
            http_response_code(429);
            echo json_encode(['success' => false, 'message' => 'Too many comments. Please slow down.']);
            exit;
        }
        
        // Create comment
        $commentId = $this->commentModel->create([
            'ticket_id' => $ticketId,
            'user_id' => $_SESSION['user_id'],
            'comment' => $comment,
            'is_internal' => $isInternal
        ]);
        
        if ($commentId) {
            // Update ticket updated_at timestamp
            $this->ticketModel->update($ticketId, ['updated_at' => date('Y-m-d H:i:s')]);
            
            // Log activity
            $this->activityLog->log([
                'ticket_id' => $ticketId,
                'user_id' => $_SESSION['user_id'],
                'action' => 'commented',
                'description' => $isInternal ? 'Added internal note' : 'Added comment'
            ]);
            
            // Queue email notifications (skip for internal notes)
            if (!$isInternal) {
                $commentData = $this->commentModel->findById($commentId);
                
                // Notify ticket owner if commenter is not the owner
                if ($ticket['user_id'] != $_SESSION['user_id']) {
                    $ticketOwner = $this->userModel->findById($ticket['user_id']);
                    if ($ticketOwner) {
                        $this->emailService->queueCommentAdded($ticket, $commentData, $ticketOwner);
                    }
                }
                
                // Notify assigned admin if exists and not the commenter
                if ($ticket['assigned_to'] && $ticket['assigned_to'] != $_SESSION['user_id']) {
                    $assignedUser = $this->userModel->findById($ticket['assigned_to']);
                    if ($assignedUser) {
                        $this->emailService->queueCommentAdded($ticket, $commentData, $assignedUser);
                    }
                }
            }
            
            $_SESSION['success'] = $isInternal ? 'Internal note added' : 'Comment added successfully';
            
            // Return JSON for AJAX requests
            if ($this->isAjaxRequest()) {
                echo json_encode([
                    'success' => true, 
                    'message' => $isInternal ? 'Internal note added' : 'Comment added successfully',
                    'comment' => [
                        'id' => $commentId,
                        'user_name' => $_SESSION['user_name'],
                        'comment' => Security::escape($comment),
                        'created_at' => date('Y-m-d H:i:s'),
                        'is_internal' => $isInternal
                    ]
                ]);
            } else {
                header('Location: /tickets/' . $ticketId . '#comment-' . $commentId);
            }
        } else {
            if ($this->isAjaxRequest()) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to add comment']);
            } else {
                $_SESSION['error'] = 'Failed to add comment';
                header('Location: /tickets/' . $ticketId);
            }
        }
        exit;
    }
    
    /**
     * Update comment
     */
    public function update($id) {
        if (!Security::isAuthenticated()) {
            $this->jsonResponse(['success' => false, 'message' => 'Unauthorized'], 401);
        }
        
        $comment = $this->commentModel->findById($id);
        
        if (!$comment) {
            $this->jsonResponse(['success' => false, 'message' => 'Comment not found'], 404);
        }
        
        // Check permission (only owner or admin can edit)
        if ($comment['user_id'] != $_SESSION['user_id'] && !Security::isAdmin()) {
            $this->jsonResponse(['success' => false, 'message' => 'Access denied'], 403);
        }
        
        // Check if ticket is closed
        $ticket = $this->ticketModel->findById($comment['ticket_id']);
        if ($ticket && $ticket['status'] === 'closed') {
            $this->jsonResponse(['success' => false, 'message' => 'Cannot edit comments on closed tickets'], 400);
        }
        
        $newComment = Security::sanitize($_POST['comment'] ?? '');
        
        if (strlen(strip_tags($newComment)) < 3) {
            $this->jsonResponse(['success' => false, 'message' => 'Comment must be at least 3 characters'], 400);
        }
        
        $result = $this->commentModel->update($id, ['comment' => $newComment]);
        
        if ($result) {
            // Log activity
            $this->activityLog->log([
                'ticket_id' => $comment['ticket_id'],
                'user_id' => $_SESSION['user_id'],
                'action' => 'updated',
                'description' => 'Updated comment'
            ]);
            
            $this->jsonResponse(['success' => true, 'message' => 'Comment updated successfully']);
        }
        
        $this->jsonResponse(['success' => false, 'message' => 'Failed to update comment'], 500);
    }
    
    /**
     * Delete comment
     */
    public function delete($id) {
        if (!Security::isAuthenticated()) {
            $this->jsonResponse(['success' => false, 'message' => 'Unauthorized'], 401);
        }
        
        $comment = $this->commentModel->findById($id);
        
        if (!$comment) {
            $this->jsonResponse(['success' => false, 'message' => 'Comment not found'], 404);
        }
        
        // Check permission (only owner or admin can delete)
        if ($comment['user_id'] != $_SESSION['user_id'] && !Security::isAdmin()) {
            $this->jsonResponse(['success' => false, 'message' => 'Access denied'], 403);
        }
        
        // Check if ticket is closed
        $ticket = $this->ticketModel->findById($comment['ticket_id']);
        if ($ticket && $ticket['status'] === 'closed') {
            $this->jsonResponse(['success' => false, 'message' => 'Cannot delete comments on closed tickets'], 400);
        }
        
        // Log before deletion
        $this->activityLog->log([
            'ticket_id' => $comment['ticket_id'],
            'user_id' => $_SESSION['user_id'],
            'action' => 'updated',
            'description' => 'Deleted comment'
        ]);
        
        $result = $this->commentModel->delete($id);
        
        if ($result) {
            $this->jsonResponse(['success' => true, 'message' => 'Comment deleted successfully']);
        }
        
        $this->jsonResponse(['success' => false, 'message' => 'Failed to delete comment'], 500);
    }
    
    // ==================== PRIVATE HELPER METHODS ====================
    
    /**
     * Check if request is AJAX
     */
    private function isAjaxRequest() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    
    /**
     * Send JSON response
     */
    private function jsonResponse($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}