<?php
use App\Helpers\Security;

$title = 'Notification Preferences - Helpdesk System';
$pageTitle = 'Notification Preferences';
$isAdmin = Security::isAdmin();
ob_start();
?>

<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-900">Notification Preferences</h2>
        <p class="mt-1 text-sm text-slate-600">Manage how you receive notifications about ticket updates</p>
    </div>

    <!-- Navigation Tabs -->
    <div class="mb-6 border-b border-slate-200">
        <nav class="flex gap-4">
            <a href="/notifications/preferences" 
               class="px-4 py-3 border-b-2 border-emerald-600 text-emerald-600 font-medium text-sm">
                Preferences
            </a>
            <a href="/notifications/history" 
               class="px-4 py-3 border-b-2 border-transparent text-slate-600 hover:text-slate-900 hover:border-slate-300 font-medium text-sm transition">
                History
            </a>
        </nav>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Email Preferences Card -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-slate-200">
                    <h3 class="text-lg font-semibold text-slate-900">Email Notifications</h3>
                    <p class="mt-1 text-sm text-slate-600">Choose which events trigger email notifications</p>
                </div>
                
                <form method="POST" action="/notifications/preferences" class="p-6">
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                    
                    <div class="space-y-4">
                        <!-- Ticket Created -->
                        <label class="flex items-start cursor-pointer group">
                            <div class="flex items-center h-6">
                                <input type="checkbox" 
                                       name="ticket_created" 
                                       value="1"
                                       <?= $preferences['ticket_created'] ? 'checked' : '' ?>
                                       class="w-5 h-5 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500 focus:ring-2">
                            </div>
                            <div class="ml-3">
                                <span class="font-medium text-slate-900 group-hover:text-emerald-600 transition">New Ticket Created</span>
                                <p class="text-sm text-slate-500">Get notified when a new ticket is submitted</p>
                            </div>
                        </label>

                        <!-- Ticket Updated -->
                        <label class="flex items-start cursor-pointer group">
                            <div class="flex items-center h-6">
                                <input type="checkbox" 
                                       name="ticket_updated" 
                                       value="1"
                                       <?= $preferences['ticket_updated'] ? 'checked' : '' ?>
                                       class="w-5 h-5 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500 focus:ring-2">
                            </div>
                            <div class="ml-3">
                                <span class="font-medium text-slate-900 group-hover:text-emerald-600 transition">Ticket Updated</span>
                                <p class="text-sm text-slate-500">Get notified when ticket details are changed</p>
                            </div>
                        </label>

                        <!-- Ticket Assigned -->
                        <label class="flex items-start cursor-pointer group">
                            <div class="flex items-center h-6">
                                <input type="checkbox" 
                                       name="ticket_assigned" 
                                       value="1"
                                       <?= $preferences['ticket_assigned'] ? 'checked' : '' ?>
                                       class="w-5 h-5 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500 focus:ring-2">
                            </div>
                            <div class="ml-3">
                                <span class="font-medium text-slate-900 group-hover:text-emerald-600 transition">Ticket Assigned</span>
                                <p class="text-sm text-slate-500">Get notified when a ticket is assigned to you</p>
                            </div>
                        </label>

                        <!-- Comment Added -->
                        <label class="flex items-start cursor-pointer group">
                            <div class="flex items-center h-6">
                                <input type="checkbox" 
                                       name="comment_added" 
                                       value="1"
                                       <?= $preferences['comment_added'] ? 'checked' : '' ?>
                                       class="w-5 h-5 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500 focus:ring-2">
                            </div>
                            <div class="ml-3">
                                <span class="font-medium text-slate-900 group-hover:text-emerald-600 transition">New Comment Added</span>
                                <p class="text-sm text-slate-500">Get notified when someone comments on your tickets</p>
                            </div>
                        </label>

                        <!-- Status Changed -->
                        <label class="flex items-start cursor-pointer group">
                            <div class="flex items-center h-6">
                                <input type="checkbox" 
                                       name="status_changed" 
                                       value="1"
                                       <?= $preferences['status_changed'] ? 'checked' : '' ?>
                                       class="w-5 h-5 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500 focus:ring-2">
                            </div>
                            <div class="ml-3">
                                <span class="font-medium text-slate-900 group-hover:text-emerald-600 transition">Status Changed</span>
                                <p class="text-sm text-slate-500">Get notified when ticket status is updated</p>
                            </div>
                        </label>
                    </div>

                    <!-- Save Button -->
                    <div class="mt-6 pt-6 border-t border-slate-200 flex gap-3">
                        <button type="submit" 
                                class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-medium transition">
                            Save Preferences
                        </button>
                        <a href="/dashboard" 
                           class="px-6 py-2.5 border border-slate-300 text-slate-700 hover:bg-slate-50 rounded-lg font-medium transition">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Quick Actions & Info -->
        <div class="space-y-6">
            <!-- Test Email Card -->
            <?php if ($isAdmin): ?>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="bg-blue-100 rounded-lg p-2">
                        <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="font-semibold text-slate-900">Test Email</h3>
                </div>
                <p class="text-sm text-slate-600 mb-4">Send a test email to verify your notification settings</p>
                <form method="POST" action="/notifications/test-email">
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                    <button type="submit" 
                            class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition">
                        Send Test Email
                    </button>
                </form>
            </div>
            <?php endif; ?>

            <!-- Info Card -->
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-6">
                <div class="flex items-start gap-3">
                    <svg class="h-6 w-6 text-amber-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <h4 class="font-semibold text-amber-900 mb-2">About Notifications</h4>
                        <ul class="text-sm text-amber-800 space-y-1.5">
                            <li class="flex items-start gap-2">
                                <span class="text-amber-600 mt-1">•</span>
                                <span>Email notifications are sent in real-time</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-amber-600 mt-1">•</span>
                                <span>Check your spam folder if you don't receive emails</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-amber-600 mt-1">•</span>
                                <span>You can disable specific notification types anytime</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Stats Card -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-semibold text-slate-900 mb-4">Your Activity</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-600">Emails Sent (30 days)</span>
                        <span class="font-semibold text-slate-900">--</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-600">Last Email</span>
                        <span class="font-semibold text-slate-900">--</span>
                    </div>
                </div>
                <a href="/notifications/history" 
                   class="mt-4 block text-center text-sm text-emerald-600 hover:text-emerald-700 font-medium">
                    View Full History →
                </a>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>