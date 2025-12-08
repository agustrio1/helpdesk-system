<?php
use App\Helpers\Security;

$title = 'Notification Preferences - Helpdesk System';
$pageTitle = 'Notification Settings';
ob_start();
?>

<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-900">Notification Preferences</h2>
        <p class="mt-1 text-sm text-slate-600">Manage your email notification settings</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Settings -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-5 border-b border-slate-200">
                    <h3 class="text-lg font-medium text-slate-900">Email Notifications</h3>
                    <p class="mt-1 text-sm text-slate-600">Choose when you want to receive email notifications</p>
                </div>

                <form method="POST" action="/notifications/preferences" class="px-6 py-5">
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

                    <div class="space-y-4">
                        <!-- Ticket Created -->
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input 
                                    type="checkbox" 
                                    name="ticket_created" 
                                    id="ticket_created"
                                    value="1"
                                    <?= $preferences['ticket_created'] ? 'checked' : '' ?>
                                    class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-slate-300 rounded"
                                >
                            </div>
                            <div class="ml-3">
                                <label for="ticket_created" class="text-sm font-medium text-slate-700">
                                    Ticket Created
                                </label>
                                <p class="text-sm text-slate-500">
                                    Receive notifications when a new ticket is created
                                </p>
                            </div>
                        </div>

                        <!-- Ticket Updated -->
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input 
                                    type="checkbox" 
                                    name="ticket_updated" 
                                    id="ticket_updated"
                                    value="1"
                                    <?= $preferences['ticket_updated'] ? 'checked' : '' ?>
                                    class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-slate-300 rounded"
                                >
                            </div>
                            <div class="ml-3">
                                <label for="ticket_updated" class="text-sm font-medium text-slate-700">
                                    Ticket Updated
                                </label>
                                <p class="text-sm text-slate-500">
                                    Receive notifications when ticket status, priority, or assignment changes
                                </p>
                            </div>
                        </div>

                        <!-- Ticket Assigned -->
                        <?php if (Security::isAdmin()): ?>
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input 
                                    type="checkbox" 
                                    name="ticket_assigned" 
                                    id="ticket_assigned"
                                    value="1"
                                    <?= $preferences['ticket_assigned'] ? 'checked' : '' ?>
                                    class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-slate-300 rounded"
                                >
                            </div>
                            <div class="ml-3">
                                <label for="ticket_assigned" class="text-sm font-medium text-slate-700">
                                    Ticket Assigned to Me
                                </label>
                                <p class="text-sm text-slate-500">
                                    Receive notifications when a ticket is assigned to you
                                </p>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Comment Added -->
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input 
                                    type="checkbox" 
                                    name="comment_added" 
                                    id="comment_added"
                                    value="1"
                                    <?= $preferences['comment_added'] ? 'checked' : '' ?>
                                    class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-slate-300 rounded"
                                >
                            </div>
                            <div class="ml-3">
                                <label for="comment_added" class="text-sm font-medium text-slate-700">
                                    New Comments
                                </label>
                                <p class="text-sm text-slate-500">
                                    Receive notifications when someone comments on your tickets
                                </p>
                            </div>
                        </div>

                        <!-- Status Changed -->
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input 
                                    type="checkbox" 
                                    name="status_changed" 
                                    id="status_changed"
                                    value="1"
                                    <?= $preferences['status_changed'] ? 'checked' : '' ?>
                                    class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-slate-300 rounded"
                                >
                            </div>
                            <div class="ml-3">
                                <label for="status_changed" class="text-sm font-medium text-slate-700">
                                    Status Changed
                                </label>
                                <p class="text-sm text-slate-500">
                                    Receive notifications when ticket status changes
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end space-x-4 pt-4 border-t border-slate-200">
                        <a href="/dashboard" class="px-4 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                            Cancel
                        </a>
                        <button 
                            type="submit"
                            class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium transition"
                        >
                            Save Preferences
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="lg:col-span-1">
            <div class="bg-white shadow rounded-lg sticky top-20">
                <div class="px-6 py-5">
                    <h3 class="text-lg font-medium text-slate-900 mb-4">About Notifications</h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <svg class="h-5 w-5 text-emerald-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="ml-3">
                                <p class="text-sm text-slate-700">
                                    Notifications are sent to your registered email address
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <svg class="h-5 w-5 text-emerald-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="ml-3">
                                <p class="text-sm text-slate-700">
                                    Email notifications are processed every 5 minutes
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <svg class="h-5 w-5 text-emerald-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <div class="ml-3">
                                <p class="text-sm text-slate-700">
                                    You can update your preferences at any time
                                </p>
                            </div>
                        </div>
                    </div>

                    <?php if (Security::isAdmin()): ?>
                    <div class="mt-6 pt-6 border-t border-slate-200">
                        <h4 class="text-sm font-medium text-slate-900 mb-2">Test Notifications</h4>
                        <form method="POST" action="/notifications/test-email">
                            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                            <button 
                                type="submit"
                                class="w-full px-4 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition"
                            >
                                Send Test Email
                            </button>
                        </form>
                        <p class="mt-2 text-xs text-slate-500">
                            Send a test email to verify your notification settings
                        </p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Notification History Link -->
            <div class="mt-4">
                <a href="/notifications/history" 
                   class="block px-4 py-3 bg-white shadow rounded-lg hover:bg-slate-50 transition text-center">
                    <div class="flex items-center justify-center">
                        <svg class="h-5 w-5 text-slate-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-sm font-medium text-slate-700">View Notification History</span>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>