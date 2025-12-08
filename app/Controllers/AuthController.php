<?php
namespace App\Controllers;

use App\Models\User;
use App\Helpers\Security;

class AuthController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    public function showLoginForm() {
        if (Security::isAuthenticated()) {
            header('Location: /dashboard');
            exit;
        }
        
        $csrfToken = Security::generateCSRFToken();
        require_once __DIR__ . '/../../views/auth/login.php';
    }
    
    public function login() {
        // Rate limiting
        if (!Security::checkRateLimit('login_' . $_SERVER['REMOTE_ADDR'], 5, 300)) {
            $_SESSION['error'] = 'Too many login attempts. Please try again later.';
            header('Location: /login');
            exit;
        }
        
        // CSRF Protection
        if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Invalid security token';
            header('Location: /login');
            exit;
        }
        
        $email = Security::sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        // Validation
        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Email and password are required';
            header('Location: /login');
            exit;
        }
        
        // Check user
        $user = $this->userModel->findByEmail($email);
        
        if (!$user || !$this->userModel->verifyPassword($password, $user['password'])) {
            $_SESSION['error'] = 'Invalid email or password';
            header('Location: /login');
            exit;
        }
        
        // Set session
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['last_activity'] = time();
        
        $_SESSION['success'] = 'Welcome back, ' . Security::escape($user['name']);
        header('Location: /dashboard');
        exit;
    }
    
    public function showRegisterForm() {
        if (Security::isAuthenticated()) {
            header('Location: /dashboard');
            exit;
        }
        
        $csrfToken = Security::generateCSRFToken();
        require_once __DIR__ . '/../../views/auth/register.php';
    }
    
    public function register() {
        // Rate limiting
        if (!Security::checkRateLimit('register_' . $_SERVER['REMOTE_ADDR'], 3, 600)) {
            $_SESSION['error'] = 'Too many registration attempts. Please try again later.';
            header('Location: /register');
            exit;
        }
        
        // CSRF Protection
        if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Invalid security token';
            header('Location: /register');
            exit;
        }
        
        $name = Security::sanitize($_POST['name'] ?? '');
        $email = Security::sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Validation
        $errors = [];
        
        if (empty($name) || strlen($name) < 3) {
            $errors[] = 'Name must be at least 3 characters';
        }
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Valid email is required';
        }
        
        if (empty($password) || strlen($password) < 6) {
            $errors[] = 'Password must be at least 6 characters';
        }
        
        if ($password !== $confirmPassword) {
            $errors[] = 'Passwords do not match';
        }
        
        // Check if email exists
        if ($this->userModel->findByEmail($email)) {
            $errors[] = 'Email already registered';
        }
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode(', ', $errors);
            $_SESSION['old_input'] = ['name' => $name, 'email' => $email];
            header('Location: /register');
            exit;
        }
        
        // Create user
        $result = $this->userModel->create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => 'customer'
        ]);
        
        if ($result) {
            $_SESSION['success'] = 'Registration successful! Please login.';
            header('Location: /login');
        } else {
            $_SESSION['error'] = 'Registration failed. Please try again.';
            header('Location: /register');
        }
        exit;
    }
    
    public function logout() {
        // Clear session
        $_SESSION = [];
        
        // Destroy session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        session_destroy();
        
        header('Location: /login');
        exit;
    }
}