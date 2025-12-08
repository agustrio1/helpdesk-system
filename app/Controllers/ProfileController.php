<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\NotificationPreference;
use App\Helpers\Security;

class ProfileController {
    private $userModel;
    private $notificationModel;
    
    public function __construct() {
        $this->userModel = new User();
        $this->notificationModel = new NotificationPreference();
    }
    
    /**
     * Show profile page
     */
    public function show() {
        $this->requireAuth();
        
        $userId = $_SESSION['user_id'];
        $user = $this->userModel->findById($userId);
        
        if (!$user) {
            $_SESSION['error'] = 'User not found';
            header('Location: /dashboard');
            exit;
        }
        
        // Update session data
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_avatar'] = $user['avatar'] ?? null;
        
        // Get notification preferences
        $notificationPrefs = $this->notificationModel->getOrCreate($userId);
        
        // Get active tab from query string (default: profile)
        $activeTab = $_GET['tab'] ?? 'profile';
        
        require_once __DIR__ . '/../../views/profile/index.php';
    }
    
    /**
     * Update profile information (name, email)
     */
    public function updateProfile() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /profile');
            exit;
        }
        
        $this->validateCSRF();
        
        $userId = $_SESSION['user_id'];
        $name = Security::sanitize($_POST['name'] ?? '');
        $email = Security::sanitize($_POST['email'] ?? '');
        
        // Validation
        $errors = [];
        
        if (strlen($name) < 3) {
            $errors[] = 'Name must be at least 3 characters';
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format';
        }
        
        // Check if email already exists (excluding current user)
        $existingUser = $this->userModel->findByEmail($email);
        if ($existingUser && $existingUser['id'] != $userId) {
            $errors[] = 'Email already exists';
        }
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode(', ', $errors);
            header('Location: /profile?tab=profile');
            exit;
        }
        
        // Update user
        $result = $this->userModel->update($userId, [
            'name' => $name,
            'email' => $email
        ]);
        
        if ($result) {
            // Update session
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            
            $_SESSION['success'] = 'Profile updated successfully';
        } else {
            $_SESSION['error'] = 'Failed to update profile';
        }
        
        header('Location: /profile?tab=profile');
        exit;
    }
    
    /**
     * Upload avatar (separate route)
     */
    public function uploadAvatar() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /profile');
            exit;
        }
        
        $this->validateCSRF();
        
        $userId = $_SESSION['user_id'];
        
        // Check if avatar file is uploaded
        if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = 'Please select an image to upload';
            header('Location: /profile?tab=profile');
            exit;
        }
        
        // Handle avatar upload
        $avatarResult = $this->handleAvatarUpload($_FILES['avatar'], $userId);
        
        if (!$avatarResult['success']) {
            $_SESSION['error'] = $avatarResult['message'];
            header('Location: /profile?tab=profile');
            exit;
        }
        
        // Delete old avatar if exists
        $currentUser = $this->userModel->findById($userId);
        if (!empty($currentUser['avatar'])) {
            $oldAvatarPath = __DIR__ . '/../../public/' . $currentUser['avatar'];
            if (file_exists($oldAvatarPath)) {
                @unlink($oldAvatarPath);
            }
        }
        
        // Update avatar in database
        $result = $this->userModel->update($userId, [
            'avatar' => $avatarResult['path']
        ]);
        
        if ($result) {
            $_SESSION['user_avatar'] = $avatarResult['path'];
            $_SESSION['success'] = 'Avatar updated successfully';
        } else {
            $_SESSION['error'] = 'Failed to update avatar';
        }
        
        header('Location: /profile?tab=profile');
        exit;
    }
    
    /**
     * Change password
     */
    public function updatePassword() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /profile');
            exit;
        }
        
        $this->validateCSRF();
        
        $userId = $_SESSION['user_id'];
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Validation
        $errors = [];
        
        if (empty($currentPassword)) {
            $errors[] = 'Current password is required';
        }
        
        if (strlen($newPassword) < 8) {
            $errors[] = 'New password must be at least 8 characters';
        }
        
        if ($newPassword !== $confirmPassword) {
            $errors[] = 'Passwords do not match';
        }
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode(', ', $errors);
            header('Location: /profile?tab=security');
            exit;
        }
        
        // Verify current password
        $user = $this->userModel->findById($userId);
        
        if (!password_verify($currentPassword, $user['password'])) {
            $_SESSION['error'] = 'Current password is incorrect';
            header('Location: /profile?tab=security');
            exit;
        }
        
        // Update password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $result = $this->userModel->update($userId, ['password' => $hashedPassword]);
        
        if ($result) {
            $_SESSION['success'] = 'Password changed successfully';
        } else {
            $_SESSION['error'] = 'Failed to change password';
        }
        
        header('Location: /profile?tab=security');
        exit;
    }
    
    /**
     * Update notification preferences
     */
    public function updateNotifications() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /profile');
            exit;
        }
        
        $this->validateCSRF();
        
        $userId = $_SESSION['user_id'];
        
        // Get notification preferences (checkbox yang tidak di-check tidak akan di-POST)
        $preferences = [
            'ticket_created' => isset($_POST['ticket_created']) ? 1 : 0,
            'ticket_updated' => isset($_POST['ticket_updated']) ? 1 : 0,
            'ticket_assigned' => isset($_POST['ticket_assigned']) ? 1 : 0,
            'comment_added' => isset($_POST['comment_added']) ? 1 : 0,
            'status_changed' => isset($_POST['status_changed']) ? 1 : 0
        ];
        
        // Update preferences
        $result = $this->notificationModel->update($userId, $preferences);
        
        if ($result) {
            $_SESSION['success'] = 'Notification preferences updated successfully';
        } else {
            $_SESSION['error'] = 'Failed to update notification preferences';
        }
        
        // Redirect kembali ke tab notifications
        header('Location: /profile?tab=notifications');
        exit;
    }
    
    // ==================== PRIVATE HELPER METHODS ====================
    
    private function requireAuth() {
        if (!Security::isAuthenticated()) {
            header('Location: /login');
            exit;
        }
    }
    
    private function validateCSRF() {
        if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Invalid security token';
            header('Location: /profile');
            exit;
        }
    }
    
    private function handleAvatarUpload($file, $userId) {
        // Validate file
        $validation = Security::validateFileUpload($file);
        
        if (!$validation['success']) {
            return [
                'success' => false,
                'message' => $validation['message']
            ];
        }
        
        // Check if it's an image
        $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!in_array($validation['mime_type'], $allowedMimes)) {
            return [
                'success' => false,
                'message' => 'Only JPG, PNG, and GIF images are allowed'
            ];
        }
        
        // Check file size (max 2MB)
        if ($file['size'] > 2 * 1024 * 1024) {
            return [
                'success' => false,
                'message' => 'Avatar must be less than 2MB'
            ];
        }
        
        // Create uploads directory if not exists
        $uploadPath = __DIR__ . '/../../public/uploads/avatars/';
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }
        
        // Generate secure filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'avatar_' . $userId . '_' . time() . '.' . $extension;
        $filePath = $uploadPath . $filename;
        
        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            return [
                'success' => false,
                'message' => 'Failed to upload avatar'
            ];
        }
        
        return [
            'success' => true,
            'path' => 'uploads/avatars/' . $filename
        ];
    }
}