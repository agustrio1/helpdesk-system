<?php

use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\TicketController;
use App\Controllers\CommentController;
use App\Controllers\AttachmentController;
use App\Controllers\CategoryController;
use App\Controllers\ActivityLogController;
use App\Controllers\NotificationController;
use App\Controllers\UserController;
use App\Controllers\ProfileController;

// Simple Router
class Router {
    private $routes = [];
    
    public function get($path, $callback) {
        $this->routes['GET'][$path] = $callback;
    }
    
    public function post($path, $callback) {
        $this->routes['POST'][$path] = $callback;
    }
    
    public function delete($path, $callback) {
        $this->routes['DELETE'][$path] = $callback;
    }
    
    public function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        
        // Support DELETE method via POST with _method=DELETE
        if ($method === 'POST' && isset($_POST['_method']) && $_POST['_method'] === 'DELETE') {
            $method = 'DELETE';
        }
        
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Remove base path if exists
        $scriptName = dirname($_SERVER['SCRIPT_NAME']);
        if ($scriptName !== '/') {
            $path = str_replace($scriptName, '', $path);
        }
        
        $path = $path === '' ? '/' : $path;
        
        // Match exact routes first (prioritize specific routes)
        if (isset($this->routes[$method][$path])) {
            return call_user_func($this->routes[$method][$path]);
        }
        
        // Then match dynamic routes
        foreach ($this->routes[$method] ?? [] as $route => $callback) {
            $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([a-zA-Z0-9_-]+)', $route);
            $pattern = '#^' . $pattern . '$#';
            
            if (preg_match($pattern, $path, $matches)) {
                array_shift($matches);
                return call_user_func_array($callback, $matches);
            }
        }
        
        // 404 Not Found
        http_response_code(404);
        echo '<h1>404 - Page Not Found</h1>';
    }
}

$router = new Router();

// ==================== AUTHENTICATION ROUTES ====================
$router->get('/', function() {
    header('Location: /login');
    exit;
});

$router->get('/login', function() {
    $controller = new AuthController();
    $controller->showLoginForm();
});

$router->post('/login', function() {
    $controller = new AuthController();
    $controller->login();
});

$router->get('/register', function() {
    $controller = new AuthController();
    $controller->showRegisterForm();
});

$router->post('/register', function() {
    $controller = new AuthController();
    $controller->register();
});

$router->get('/logout', function() {
    $controller = new AuthController();
    $controller->logout();
});

// ==================== DASHBOARD ====================
$router->get('/dashboard', function() {
    $controller = new DashboardController();
    $controller->index();
});

// ==================== TICKET ROUTES ====================
$router->get('/tickets', function() {
    $controller = new TicketController();
    $controller->index();
});

$router->get('/tickets/create', function() {
    $controller = new TicketController();
    $controller->create();
});

$router->post('/tickets', function() {
    $controller = new TicketController();
    $controller->store();
});

$router->get('/tickets/search', function() {
    $controller = new TicketController();
    $controller->search();
});

$router->get('/tickets/export', function() {
    $controller = new TicketController();
    $controller->export();
});

$router->get('/tickets/{id}', function($id) {
    $controller = new TicketController();
    $controller->show($id);
});

$router->post('/tickets/{id}', function($id) {
    $controller = new TicketController();
    $controller->update($id);
});

$router->delete('/tickets/{id}', function($id) {
    $controller = new TicketController();
    $controller->delete($id);
});

// ==================== COMMENT ROUTES ====================
$router->post('/comments', function() {
    $controller = new CommentController();
    $controller->store();
});

$router->post('/comments/{id}/update', function($id) {
    $controller = new CommentController();
    $controller->update($id);
});

$router->delete('/comments/{id}', function($id) {
    $controller = new CommentController();
    $controller->delete($id);
});

// ==================== ATTACHMENT ROUTES ====================
$router->get('/attachments/{id}/download', function($id) {
    $controller = new AttachmentController();
    $controller->download($id);
});

$router->delete('/attachments/{id}', function($id) {
    $controller = new AttachmentController();
    $controller->delete($id);
});

// ==================== CATEGORY ROUTES (Admin Only) ====================
$router->get('/categories', function() {
    $controller = new CategoryController();
    $controller->index();
});

$router->get('/categories/create', function() {
    $controller = new CategoryController();
    $controller->create();
});

$router->get('/categories/active', function() {
    $controller = new CategoryController();
    $controller->getActive();
});

$router->post('/categories/store', function() {
    $controller = new CategoryController();
    $controller->store();
});

$router->get('/categories/{id}/edit', function($id) {
    $controller = new CategoryController();
    $controller->edit($id);
});

$router->post('/categories/{id}/update', function($id) {
    $controller = new CategoryController();
    $controller->update($id);
});

$router->delete('/categories/{id}', function($id) {
    $controller = new CategoryController();
    $controller->delete($id);
});

$router->post('/categories/{id}/toggle', function($id) {
    $controller = new CategoryController();
    $controller->toggleStatus($id);
});

// ==================== ACTIVITY LOG ROUTES ====================
$router->get('/activities', function() {
    $controller = new ActivityLogController();
    $controller->index();
});

$router->get('/activities/user', function() {
    $controller = new ActivityLogController();
    $controller->getUserActivities();
});

$router->get('/activities/export', function() {
    $controller = new ActivityLogController();
    $controller->export();
});

$router->get('/activities/ticket/{id}', function($id) {
    $controller = new ActivityLogController();
    $controller->getByTicket($id);
});

// ==================== NOTIFICATION ROUTES ====================
// Route spesifik HARUS di atas route dengan parameter dinamis!

$router->get('/notifications/preferences', function() {
    $controller = new NotificationController();
    $controller->preferences();
});

$router->post('/notifications/preferences', function() {
    $controller = new NotificationController();
    $controller->updatePreferences();
});

$router->get('/notifications/history', function() {
    $controller = new NotificationController();
    $controller->history();
});

$router->get('/notifications/pending-count', function() {
    $controller = new NotificationController();
    $controller->getPendingCount();
});

$router->post('/notifications/test-email', function() {
    $controller = new NotificationController();
    $controller->testEmail();
});

// ✅ ROUTE BARU: Process pending emails manually (Admin only)
$router->post('/notifications/process-pending', function() {
    $controller = new NotificationController();
    $controller->processPendingEmails();
});

$router->post('/notifications/{id}/resend', function($id) {
    $controller = new NotificationController();
    $controller->resend($id);
});

// ==================== USER PROFILE ROUTES ====================
$router->get('/profile', function() {
    $controller = new ProfileController();
    $controller->show();
});

$router->post('/profile', function() {
    $controller = new ProfileController();
    $controller->updateProfile();
});

$router->post('/profile/password', function() {
    $controller = new ProfileController();
    $controller->updatePassword();
});

$router->post('/profile/avatar', function() {
    $controller = new ProfileController();
    $controller->uploadAvatar();
});

$router->post('/profile/notifications', function() {
    $controller = new ProfileController();
    $controller->updateNotifications();
});

// ==================== ADMIN: USER MANAGEMENT ====================
$router->get('/users', function() {
    $controller = new UserController();
    $controller->index();
});

$router->get('/users/create', function() {
    $controller = new UserController();
    $controller->create();
});

$router->post('/users', function() {
    $controller = new UserController();
    $controller->store();
});

$router->get('/users/{id}/edit', function($id) {
    $controller = new UserController();
    $controller->edit($id);
});

$router->post('/users/{id}', function($id) {
    $controller = new UserController();
    $controller->update($id);
});

$router->delete('/users/{id}', function($id) {
    $controller = new UserController();
    $controller->delete($id);
});

$router->post('/users/{id}/toggle', function($id) {
    $controller = new UserController();
    $controller->toggleStatus($id);
});

return $router;