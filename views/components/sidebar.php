<?php
use App\Helpers\Security;
$currentPath = $_SERVER['REQUEST_URI'] ?? '/';
$isAdmin = Security::isAdmin();
$userName = Security::escape($_SESSION['user_name'] ?? 'User');
$userAvatar = $_SESSION['user_avatar'] ?? null;

// Cek apakah function sudah ada (karena sidebar di-include 2x)
if (!function_exists('isActive')) {
    function isActive($path) {
        global $currentPath;
        return strpos($currentPath ?? '', $path) === 0 ? 'bg-emerald-50 border-emerald-500 text-emerald-700' : 'border-transparent text-slate-600 hover:bg-slate-50 hover:text-slate-900';
    }
}
?>

<div class="flex flex-col h-full">
    <!-- Logo -->
    <div class="flex items-center justify-center h-16 px-4 bg-emerald-600">
        <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
        <span class="ml-2 text-xl font-bold text-white">Helpdesk</span>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-2 py-4 space-y-1 overflow-y-auto">
        <!-- Dashboard -->
        <a href="/dashboard" 
           class="<?= isActive('/dashboard') ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md border-l-4 transition-colors">
            <svg class="mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            Dashboard
        </a>

        <!-- Tickets -->
        <a href="/tickets" 
           class="<?= isActive('/tickets') ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md border-l-4 transition-colors">
            <svg class="mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            All Tickets
        </a>

        <!-- New Ticket -->
        <a href="/tickets/create" 
           class="<?= isActive('/tickets/create') ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md border-l-4 transition-colors">
            <svg class="mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            New Ticket
        </a>

        <!-- Divider -->
        <div class="pt-4 pb-2">
            <div class="border-t border-slate-200"></div>
        </div>

        <!-- Admin Only Section -->
        <?php if ($isAdmin): ?>
        <div class="pt-2">
            <p class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                Admin Tools
            </p>
        </div>

        <!-- Categories -->
        <a href="/categories" 
           class="<?= isActive('/categories') ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md border-l-4 transition-colors">
            <svg class="mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
            </svg>
            Categories
        </a>

        <!-- Activity Log -->
        <a href="/activities" 
           class="<?= isActive('/activities') ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md border-l-4 transition-colors">
            <svg class="mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Activity Log
        </a>

        <!-- Users (if you have user management) -->
        <!-- <a href="/users" 
           class="<?= isActive('/users') ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md border-l-4 transition-colors">
            <svg class="mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            Users
        </a> -->
        
                <!-- Divider -->
        <div class="pt-4 pb-2">
            <div class="border-t border-slate-200"></div>
        </div>
        
        <?php endif; ?>

        <!-- Settings Section -->
        <div class="pt-2">
            <p class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                Settings
            </p>
        </div>

        <!-- Profile -->
        <a href="/profile" 
           class="<?= isActive('/profile') ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md border-l-4 transition-colors">
            <svg class="mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            My Profile
        </a>

        <!-- Notifications -->
        <a href="/notifications/preferences" 
           class="<?= isActive('/notifications') ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md border-l-4 transition-colors">
            <svg class="mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            Notifications
        </a>
    </nav>

    <!-- User Info & Logout -->
    <div class="flex-shrink-0 border-t border-slate-200">
        <div class="flex items-center p-4">
            <div class="flex-shrink-0">
                <?php if (!empty($userAvatar) && file_exists(__DIR__ . '/../../public/' . $userAvatar)): ?>
                    <!-- Show uploaded avatar -->
                    <img src="/<?= Security::escape($userAvatar) ?>" 
                         alt="<?= $userName ?>"
                         class="h-10 w-10 rounded-full object-cover border-2 border-emerald-100">
                <?php else: ?>
                    <!-- Show default avatar with initial -->
                    <div class="h-10 w-10 rounded-full bg-emerald-100 flex items-center justify-center">
                        <span class="text-emerald-600 font-medium text-lg">
                            <?= strtoupper(substr($userName, 0, 1)) ?>
                        </span>
                    </div>
                <?php endif; ?>
            </div>
            <div class="ml-3 flex-1 min-w-0">
                <p class="text-sm font-medium text-slate-700 truncate">
                    <?= $userName ?>
                </p>
                <p class="text-xs text-slate-500">
                    <?= ucfirst($_SESSION['user_role'] ?? 'customer') ?>
                </p>
            </div>
            <div>
                <a href="/logout" 
                   class="text-slate-400 hover:text-slate-600 transition-colors"
                   title="Logout">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>