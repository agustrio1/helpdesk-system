<?php
namespace App\Controllers;

use App\Models\Category;
use App\Models\ActivityLog;
use App\Helpers\Security;

class CategoryController {
    private $categoryModel;
    private $activityLog;
    
    public function __construct() {
        $this->categoryModel = new Category();
        $this->activityLog = new ActivityLog();
    }
    
    /**
     * Display all categories (Admin only)
     */
    public function index() {
        $this->requireAuth();
        $this->requireAdmin();
        
        try {
            // Ambil semua category dengan ticket count
            $categories = $this->categoryModel->getAllWithTicketCount();
            
            // Hitung statistik
            $stats = [
                'total' => count($categories),
                'active' => count(array_filter($categories, fn($c) => $c['is_active'] == 1)),
                'inactive' => count(array_filter($categories, fn($c) => $c['is_active'] == 0)),
                'total_tickets' => array_sum(array_column($categories, 'ticket_count'))
            ];
            
            $csrfToken = Security::generateCSRFToken();
            
            require_once __DIR__ . '/../../views/categories/index.php';
        } catch (\Exception $e) {
            error_log("Error in category index: " . $e->getMessage());
            $_SESSION['error'] = 'Failed to load categories';
            header('Location: /dashboard');
            exit;
        }
    }
    
    /**
     * Show create form
     */
    public function create() {
        $this->requireAuth();
        $this->requireAdmin();
        
        $csrfToken = Security::generateCSRFToken();
        require_once __DIR__ . '/../../views/categories/create.php';
    }
    
    /**
     * Store new category
     */
    public function store() {
        $this->requireAuth();
        $this->requireAdmin();
        $this->validateCSRF();
        
        try {
            $data = $this->validateInput($_POST);
            
            // Generate slug dari nama
            $slug = Category::generateSlug($data['name']);
            
            // Debug log
            error_log("Creating category with data: " . json_encode([
                'name' => $data['name'],
                'slug' => $slug,
                'description' => $data['description'],
                'color' => $data['color'],
                'icon' => $data['icon'],
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ]));
            
            $categoryId = $this->categoryModel->create([
                'name' => $data['name'],
                'slug' => $slug,
                'description' => $data['description'],
                'color' => $data['color'],
                'icon' => $data['icon'],
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ]);
            
            if ($categoryId) {
                error_log("Category created successfully with ID: " . $categoryId);
                $_SESSION['success'] = 'Category created successfully';
            } else {
                error_log("Failed to create category - no ID returned");
                $_SESSION['error'] = 'Failed to create category';
            }
            
        } catch (\Exception $e) {
            error_log("Exception creating category: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $_SESSION['error'] = 'Error: ' . $e->getMessage();
        }
        
        header('Location: /categories');
        exit;
    }
    
    /**
     * Show edit form
     */
    public function edit($id) {
        $this->requireAuth();
        $this->requireAdmin();
        
        try {
            $category = $this->categoryModel->findByIdWithTicketCount($id);
            
            if (!$category) {
                error_log("Category not found with ID: " . $id);
                $_SESSION['error'] = 'Category not found';
                header('Location: /categories');
                exit;
            }
            
            $csrfToken = Security::generateCSRFToken();
            require_once __DIR__ . '/../../views/categories/edit.php';
        } catch (\Exception $e) {
            error_log("Error loading category for edit: " . $e->getMessage());
            $_SESSION['error'] = 'Failed to load category';
            header('Location: /categories');
            exit;
        }
    }
    
    /**
     * Update category
     */
    public function update($id) {
        $this->requireAuth();
        $this->requireAdmin();
        $this->validateCSRF();
        
        try {
            $category = $this->categoryModel->findById($id);
            
            if (!$category) {
                $_SESSION['error'] = 'Category not found';
                header('Location: /categories');
                exit;
            }
            
            $data = $this->validateInput($_POST);
            
            // Generate slug dari nama jika nama berubah
            $slug = ($data['name'] !== $category['name']) 
                ? Category::generateSlug($data['name']) 
                : $category['slug'];
            
            $result = $this->categoryModel->update($id, [
                'name' => $data['name'],
                'slug' => $slug,
                'description' => $data['description'],
                'color' => $data['color'],
                'icon' => $data['icon'],
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ]);
            
            if ($result) {
                $_SESSION['success'] = 'Category updated successfully';
            } else {
                $_SESSION['error'] = 'Failed to update category';
            }
        } catch (\Exception $e) {
            error_log("Error updating category: " . $e->getMessage());
            $_SESSION['error'] = 'Error: ' . $e->getMessage();
        }
        
        header('Location: /categories');
        exit;
    }
    
    /**
     * Delete category
     */
    public function delete($id) {
        $this->requireAuth();
        $this->requireAdmin();
        
        try {
            $category = $this->categoryModel->findByIdWithTicketCount($id);
            
            if (!$category) {
                $this->jsonResponse(['success' => false, 'message' => 'Category not found'], 404);
            }
            
            // Check if category has tickets
            if ($category['ticket_count'] > 0) {
                $this->jsonResponse([
                    'success' => false, 
                    'message' => "Cannot delete category with {$category['ticket_count']} active tickets. Please reassign tickets first."
                ], 400);
            }
            
            $result = $this->categoryModel->delete($id);
            
            if ($result) {
                $this->jsonResponse(['success' => true, 'message' => 'Category deleted successfully']);
            }
            
            $this->jsonResponse(['success' => false, 'message' => 'Failed to delete category'], 500);
        } catch (\Exception $e) {
            error_log("Error deleting category: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Toggle category status
     */
    public function toggleStatus($id) {
        $this->requireAuth();
        $this->requireAdmin();
        
        try {
            $category = $this->categoryModel->findById($id);
            
            if (!$category) {
                $this->jsonResponse(['success' => false, 'message' => 'Category not found'], 404);
            }
            
            $newStatus = $category['is_active'] ? 0 : 1;
            $result = $this->categoryModel->update($id, ['is_active' => $newStatus]);
            
            if ($result) {
                $statusText = $newStatus ? 'activated' : 'deactivated';
                $this->jsonResponse([
                    'success' => true, 
                    'message' => "Category {$statusText} successfully",
                    'is_active' => $newStatus
                ]);
            }
            
            $this->jsonResponse(['success' => false, 'message' => 'Failed to update status'], 500);
        } catch (\Exception $e) {
            error_log("Error toggling category status: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Get active categories (AJAX)
     */
    public function getActive() {
        $this->requireAuth();
        
        try {
            $categories = $this->categoryModel->getActive();
            $this->jsonResponse(['categories' => $categories]);
        } catch (\Exception $e) {
            error_log("Error getting active categories: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
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
            $_SESSION['error'] = 'Access denied';
            header('Location: /dashboard');
            exit;
        }
    }
    
    private function validateCSRF() {
        if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Invalid security token';
            header('Location: /categories');
            exit;
        }
    }
    
    private function validateInput($input) {
        $name = Security::sanitize($input['name'] ?? '');
        $description = Security::sanitize($input['description'] ?? '');
        $color = Security::sanitize($input['color'] ?? '#3B82F6');
        $icon = Security::sanitize($input['icon'] ?? 'folder');
        
        $errors = [];
        
        if (strlen($name) < 2) {
            $errors[] = 'Category name must be at least 2 characters';
        }
        
        if (!preg_match('/^#[0-9A-F]{6}$/i', $color)) {
            $errors[] = 'Invalid color format';
        }
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode(', ', $errors);
            $_SESSION['old_input'] = compact('name', 'description', 'color', 'icon');
            throw new \Exception(implode(', ', $errors));
        }
        
        return compact('name', 'description', 'color', 'icon');
    }
    
    private function jsonResponse($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}