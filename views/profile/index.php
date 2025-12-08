<?php
use App\Helpers\Security;

$title = 'My Profile - Helpdesk System';
$pageTitle = 'My Profile';

// Get user data from session
$userId = $_SESSION['user_id'];
$userName = $_SESSION['user_name'];
$userEmail = $_SESSION['user_email'];
$userRole = $_SESSION['user_role'];
$avatar = $_SESSION['user_avatar'] ?? null;

// Get notification preferences (passed from controller)
$notifyPrefs = $notificationPrefs ?? [];

// Get active tab from URL parameter
$activeTab = $_GET['tab'] ?? 'profile';

ob_start();
?>

<div class="max-w-4xl mx-auto">
    <!-- Profile Header Card -->
    <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
        <div class="h-32 bg-gradient-to-r from-emerald-500 to-emerald-600"></div>
        <div class="px-6 pb-6">
            <div class="flex items-center -mt-16 mb-4">
                <div class="relative">
                    <?php if (!empty($avatar) && file_exists(__DIR__ . '/../../public/' . $avatar)): ?>
                        <img src="/<?= Security::escape($avatar) ?>" 
                             alt="<?= Security::escape($userName) ?>"
                             class="h-32 w-32 rounded-full border-4 border-white bg-white object-cover">
                    <?php else: ?>
                        <div class="h-32 w-32 rounded-full border-4 border-white bg-emerald-100 flex items-center justify-center">
                            <span class="text-4xl font-bold text-emerald-600">
                                <?= strtoupper(substr($userName, 0, 1)) ?>
                            </span>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="ml-6 flex-1">
                    <h1 class="text-2xl font-bold text-slate-900"><?= Security::escape($userName) ?></h1>
                    <p class="text-slate-600"><?= Security::escape($userEmail) ?></p>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?= $userRole === 'admin' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-800' ?>">
                    <?= ucfirst($userRole) ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white shadow rounded-lg mb-6">
        <div class="border-b border-slate-200">
            <nav class="flex -mb-px">
                <button onclick="showTab('profile')" id="tab-profile" class="tab-button px-6 py-3 text-sm font-medium border-b-2 <?= $activeTab === 'profile' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' ?>">
                    <svg class="inline-block mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Profile Information
                </button>
                <button onclick="showTab('security')" id="tab-security" class="tab-button px-6 py-3 text-sm font-medium border-b-2 <?= $activeTab === 'security' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' ?>">
                    <svg class="inline-block mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    Security
                </button>
                <button onclick="showTab('notifications')" id="tab-notifications" class="tab-button px-6 py-3 text-sm font-medium border-b-2 <?= $activeTab === 'notifications' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' ?>">
                    <svg class="inline-block mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    Notifications
                </button>
            </nav>
        </div>
    </div>

    <!-- Profile Information Tab -->
    <div id="content-profile" class="tab-content <?= $activeTab !== 'profile' ? 'hidden' : '' ?>">
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200">
                <h3 class="text-lg font-medium text-slate-900">Profile Information</h3>
                <p class="text-sm text-slate-500 mt-1">Update your account's profile information</p>
            </div>

            <div class="p-6 space-y-6">
                <!-- Avatar Upload - SEPARATE FORM -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Profile Photo
                    </label>
                    <div class="flex items-center gap-4">
                        <?php if (!empty($avatar) && file_exists(__DIR__ . '/../../public/' . $avatar)): ?>
                            <img src="/<?= Security::escape($avatar) ?>" 
                                 alt="Current avatar"
                                 id="avatar-preview"
                                 class="h-20 w-20 rounded-full object-cover border-2 border-slate-200">
                        <?php else: ?>
                            <div id="avatar-preview" class="h-20 w-20 rounded-full bg-emerald-100 flex items-center justify-center border-2 border-slate-200">
                                <span class="text-2xl font-bold text-emerald-600">
                                    <?= strtoupper(substr($userName, 0, 1)) ?>
                                </span>
                            </div>
                        <?php endif; ?>
                        <div class="flex-1">
                            <form method="POST" action="/profile/avatar" enctype="multipart/form-data" id="avatar-form">
                                <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                                <input type="file" 
                                       name="avatar" 
                                       id="avatar-upload"
                                       accept="image/jpeg,image/jpg,image/png,image/gif"
                                       onchange="handleAvatarChange(this)"
                                       class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer">
                            </form>
                            <p class="mt-1 text-xs text-slate-500">JPG, PNG or GIF. Max 2MB.</p>
                        </div>
                    </div>
                </div>

                <!-- Profile Update Form -->
                <form method="POST" action="/profile" id="profile-form" onsubmit="return validateProfileForm()">
                    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">

                    <!-- Full Name -->
                    <div class="mb-6">
                        <label for="name" class="block text-sm font-medium text-slate-700 mb-2">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            value="<?= Security::escape($userName) ?>"
                            required
                            minlength="3"
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                    </div>

                    <!-- Email -->
                    <div class="mb-6">
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-2">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            value="<?= Security::escape($userEmail) ?>"
                            required
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end pt-4 border-t border-slate-200">
                        <button 
                            type="submit"
                            class="inline-flex items-center px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition">
                            <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Security Tab -->
    <div id="content-security" class="tab-content <?= $activeTab !== 'security' ? 'hidden' : '' ?>">
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200">
                <h3 class="text-lg font-medium text-slate-900">Change Password</h3>
                <p class="text-sm text-slate-500 mt-1">Ensure your account is using a secure password</p>
            </div>

            <form method="POST" action="/profile/password" class="p-6 space-y-6" onsubmit="return validatePasswordForm()">
                <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">

                <!-- Current Password -->
                <div>
                    <label for="current_password" class="block text-sm font-medium text-slate-700 mb-2">
                        Current Password <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="password" 
                        id="current_password" 
                        name="current_password" 
                        required
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                </div>

                <!-- New Password -->
                <div>
                    <label for="new_password" class="block text-sm font-medium text-slate-700 mb-2">
                        New Password <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="password" 
                        id="new_password" 
                        name="new_password" 
                        required
                        minlength="8"
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                    <p class="mt-1 text-xs text-slate-500">Minimum 8 characters</p>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-slate-700 mb-2">
                        Confirm New Password <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="password" 
                        id="confirm_password" 
                        name="confirm_password" 
                        required
                        minlength="8"
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end pt-4 border-t border-slate-200">
                    <button 
                        type="submit"
                        class="inline-flex items-center px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition">
                        <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Notifications Tab -->
    <div id="content-notifications" class="tab-content <?= $activeTab !== 'notifications' ? 'hidden' : '' ?>">
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200">
                <h3 class="text-lg font-medium text-slate-900">Notification Preferences</h3>
                <p class="text-sm text-slate-500 mt-1">Manage how you receive notifications</p>
            </div>

            <form method="POST" action="/profile/notifications" class="p-6 space-y-6">
                <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">

                <!-- Email Notifications -->
                <div class="space-y-4">
                    <h4 class="text-sm font-medium text-slate-900">Email Notifications</h4>
                    
                    <label class="flex items-start">
                        <input type="checkbox" name="ticket_created" value="1" <?= ($notifyPrefs['ticket_created'] ?? 1) ? 'checked' : '' ?> class="mt-1 h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-slate-300 rounded">
                        <span class="ml-3">
                            <span class="block text-sm font-medium text-slate-700">New Ticket Created</span>
                            <span class="block text-sm text-slate-500">Receive email when a new ticket is created</span>
                        </span>
                    </label>

                    <label class="flex items-start">
                        <input type="checkbox" name="ticket_updated" value="1" <?= ($notifyPrefs['ticket_updated'] ?? 1) ? 'checked' : '' ?> class="mt-1 h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-slate-300 rounded">
                        <span class="ml-3">
                            <span class="block text-sm font-medium text-slate-700">Ticket Updated</span>
                            <span class="block text-sm text-slate-500">Receive email when your ticket is updated</span>
                        </span>
                    </label>

                    <label class="flex items-start">
                        <input type="checkbox" name="ticket_assigned" value="1" <?= ($notifyPrefs['ticket_assigned'] ?? 1) ? 'checked' : '' ?> class="mt-1 h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-slate-300 rounded">
                        <span class="ml-3">
                            <span class="block text-sm font-medium text-slate-700">Ticket Assigned</span>
                            <span class="block text-sm text-slate-500">Receive email when a ticket is assigned to you</span>
                        </span>
                    </label>

                    <label class="flex items-start">
                        <input type="checkbox" name="comment_added" value="1" <?= ($notifyPrefs['comment_added'] ?? 1) ? 'checked' : '' ?> class="mt-1 h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-slate-300 rounded">
                        <span class="ml-3">
                            <span class="block text-sm font-medium text-slate-700">New Comment</span>
                            <span class="block text-sm text-slate-500">Receive email when someone comments on your ticket</span>
                        </span>
                    </label>

                    <label class="flex items-start">
                        <input type="checkbox" name="status_changed" value="1" <?= ($notifyPrefs['status_changed'] ?? 1) ? 'checked' : '' ?> class="mt-1 h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-slate-300 rounded">
                        <span class="ml-3">
                            <span class="block text-sm font-medium text-slate-700">Status Changed</span>
                            <span class="block text-sm text-slate-500">Receive email when ticket status is changed</span>
                        </span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end pt-4 border-t border-slate-200">
                    <button 
                        type="submit"
                        class="inline-flex items-center px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition">
                        <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Save Preferences
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Tab switching
function showTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('border-emerald-500', 'text-emerald-600');
        button.classList.add('border-transparent', 'text-slate-500');
    });
    
    document.getElementById('content-' + tabName).classList.remove('hidden');
    
    const activeTab = document.getElementById('tab-' + tabName);
    activeTab.classList.add('border-emerald-500', 'text-emerald-600');
    activeTab.classList.remove('border-transparent', 'text-slate-500');
    
    // Update URL without reload
    const url = new URL(window.location);
    url.searchParams.set('tab', tabName);
    window.history.pushState({}, '', url);
}

// Avatar upload handler
function handleAvatarChange(input) {
    if (!input.files || !input.files[0]) {
        return;
    }
    
    const file = input.files[0];
    
    // Validate file type
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    if (!allowedTypes.includes(file.type)) {
        alert('Only JPG, PNG, and GIF images are allowed');
        input.value = '';
        return;
    }
    
    // Validate file size (2MB)
    if (file.size > 2 * 1024 * 1024) {
        alert('Image must be less than 2MB');
        input.value = '';
        return;
    }
    
    // Show preview
    const reader = new FileReader();
    reader.onload = function(e) {
        const preview = document.getElementById('avatar-preview');
        if (preview.tagName === 'IMG') {
            preview.src = e.target.result;
        } else {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'h-20 w-20 rounded-full object-cover border-2 border-slate-200';
            img.id = 'avatar-preview';
            preview.replaceWith(img);
        }
    };
    reader.readAsDataURL(file);
    
    // Submit form
    const form = document.getElementById('avatar-form');
    if (form && confirm('Upload this image as your profile picture?')) {
        form.submit();
    }
}

// Profile form validation
function validateProfileForm() {
    const name = document.getElementById('name').value.trim();
    const email = document.getElementById('email').value.trim();
    
    if (name.length < 3) {
        alert('Name must be at least 3 characters');
        return false;
    }
    
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        alert('Please enter a valid email address');
        return false;
    }
    
    return true;
}

// Password form validation
function validatePasswordForm() {
    const currentPassword = document.getElementById('current_password').value;
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (currentPassword.length < 1) {
        alert('Please enter your current password');
        return false;
    }
    
    if (newPassword.length < 8) {
        alert('New password must be at least 8 characters');
        return false;
    }
    
    if (newPassword !== confirmPassword) {
        alert('Passwords do not match');
        return false;
    }
    
    return true;
}
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>