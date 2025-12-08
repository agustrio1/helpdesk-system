<?php
use App\Helpers\Security;

$isAdmin = Security::isAdmin();
ob_start();
?>

<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-900">Notification History</h2>
            <p class="mt-1 text-sm text-slate-600">View all email notifications sent to you</p>
        </div>
        
        <!-- Process Pending Button (Admin Only) -->
        <?php if ($isAdmin && isset($pendingCount) && $pendingCount > 0): ?>
        <form method="POST" action="/notifications/process-pending">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
            <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-md font-medium transition flex items-center gap-2">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                </svg>
                Send Pending (<?= $pendingCount ?>)
            </button>
        </form>
        <?php endif; ?>
    </div>

    <!-- Navigation Tabs -->
    <div class="mb-6 border-b border-slate-200">
        <nav class="flex gap-4">
            <a href="/notifications/preferences" 
               class="px-4 py-3 border-b-2 border-transparent text-slate-600 hover:text-slate-900 hover:border-slate-300 font-medium text-sm transition">
                Preferences
            </a>
            <a href="/notifications/history" 
               class="px-4 py-3 border-b-2 border-emerald-600 text-emerald-600 font-medium text-sm">
                History
            </a>
        </nav>
    </div>

    <!-- Alert Messages -->
    <?php if (isset($_SESSION['success'])): ?>
    <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-md flex items-start gap-3">
        <svg class="h-5 w-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        <span><?= $_SESSION['success'] ?></span>
    </div>
    <?php unset($_SESSION['success']); endif; ?>

    <?php if (isset($_SESSION['warning'])): ?>
    <div class="mb-6 bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 rounded-md flex items-start gap-3">
        <svg class="h-5 w-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
        </svg>
        <span><?= $_SESSION['warning'] ?></span>
    </div>
    <?php unset($_SESSION['warning']); endif; ?>

    <?php if (isset($_SESSION['info'])): ?>
    <div class="mb-6 bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-md flex items-start gap-3">
        <svg class="h-5 w-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
        </svg>
        <span><?= $_SESSION['info'] ?></span>
    </div>
    <?php unset($_SESSION['info']); endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
    <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-md flex items-start gap-3">
        <svg class="h-5 w-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
        </svg>
        <span><?= $_SESSION['error'] ?></span>
    </div>
    <?php unset($_SESSION['error']); endif; ?>

    <!-- Filters -->
    <div class="mb-6 bg-white rounded-lg shadow p-4">
        <form method="GET" action="/notifications/history" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-slate-700 mb-2">Limit</label>
                <select name="limit" 
                        class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="50" <?= ($_GET['limit'] ?? 50) == 50 ? 'selected' : '' ?>>Last 50</option>
                    <option value="100" <?= ($_GET['limit'] ?? 50) == 100 ? 'selected' : '' ?>>Last 100</option>
                    <option value="200" <?= ($_GET['limit'] ?? 50) == 200 ? 'selected' : '' ?>>Last 200</option>
                    <option value="500" <?= ($_GET['limit'] ?? 50) == 500 ? 'selected' : '' ?>>Last 500</option>
                </select>
            </div>
            <button type="submit" 
                    class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-md font-medium transition">
                Apply
            </button>
        </form>
    </div>

    <!-- Notifications List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <?php if (empty($notifications)): ?>
        <!-- Empty State -->
        <div class="p-12 text-center">
            <svg class="mx-auto h-16 w-16 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            <h3 class="mt-4 text-lg font-medium text-slate-900">No notifications yet</h3>
            <p class="mt-2 text-sm text-slate-500">You haven't received any email notifications</p>
        </div>
        <?php else: ?>
        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Subject</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Sent At</th>
                        <?php if ($isAdmin): ?>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-700 uppercase tracking-wider">Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    <?php foreach ($notifications as $notification): ?>
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 mt-1">
                                    <?php
                                    $iconColor = match($notification['status']) {
                                        'sent' => 'text-emerald-600',
                                        'failed' => 'text-red-600',
                                        'pending' => 'text-amber-600',
                                        default => 'text-slate-600'
                                    };
                                    ?>
                                    <svg class="h-5 w-5 <?= $iconColor ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-slate-900 truncate">
                                        <?= Security::escape($notification['subject']) ?>
                                    </p>
                                    <?php if (!empty($notification['error_message'])): ?>
                                    <p class="text-xs text-red-600 mt-1">
                                        <?= Security::escape($notification['error_message']) ?>
                                    </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <?= ucfirst($notification['type']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php
                            $statusColors = [
                                'sent' => 'bg-emerald-100 text-emerald-800',
                                'failed' => 'bg-red-100 text-red-800',
                                'pending' => 'bg-amber-100 text-amber-800'
                            ];
                            ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $statusColors[$notification['status']] ?? 'bg-slate-100 text-slate-800' ?>">
                                <?= ucfirst($notification['status']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                            <?php if ($notification['sent_at']): ?>
                                <?= date('M d, Y H:i', strtotime($notification['sent_at'])) ?>
                            <?php else: ?>
                                <span class="text-slate-400">Not sent</span>
                            <?php endif; ?>
                        </td>
                        <?php if ($isAdmin && $notification['status'] === 'failed'): ?>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                            <button onclick="resendNotification(<?= $notification['id'] ?>)" 
                                    class="text-emerald-600 hover:text-emerald-900 font-medium">
                                Resend
                            </button>
                        </td>
                        <?php elseif ($isAdmin): ?>
                        <td class="px-6 py-4"></td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div class="md:hidden divide-y divide-slate-200">
            <?php foreach ($notifications as $notification): ?>
            <div class="p-4">
                <div class="flex items-start gap-3 mb-3">
                    <?php
                    $iconColor = match($notification['status']) {
                        'sent' => 'text-emerald-600',
                        'failed' => 'text-red-600',
                        'pending' => 'text-amber-600',
                        default => 'text-slate-600'
                    };
                    ?>
                    <svg class="h-5 w-5 <?= $iconColor ?> flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-900">
                            <?= Security::escape($notification['subject']) ?>
                        </p>
                        <?php if (!empty($notification['error_message'])): ?>
                        <p class="text-xs text-red-600 mt-1">
                            <?= Security::escape($notification['error_message']) ?>
                        </p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="flex flex-wrap gap-2 items-center">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        <?= ucfirst($notification['type']) ?>
                    </span>
                    
                    <?php
                    $statusColors = [
                        'sent' => 'bg-emerald-100 text-emerald-800',
                        'failed' => 'bg-red-100 text-red-800',
                        'pending' => 'bg-amber-100 text-amber-800'
                    ];
                    ?>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $statusColors[$notification['status']] ?? 'bg-slate-100 text-slate-800' ?>">
                        <?= ucfirst($notification['status']) ?>
                    </span>
                    
                    <span class="text-xs text-slate-500 ml-auto">
                        <?php if ($notification['sent_at']): ?>
                            <?= date('M d, H:i', strtotime($notification['sent_at'])) ?>
                        <?php else: ?>
                            Not sent
                        <?php endif; ?>
                    </span>
                </div>

                <?php if ($isAdmin && $notification['status'] === 'failed'): ?>
                <button onclick="resendNotification(<?= $notification['id'] ?>)" 
                        class="mt-3 w-full px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm rounded-md font-medium transition">
                    Resend Email
                </button>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Results Info -->
    <?php if (!empty($notifications)): ?>
    <div class="mt-4 text-sm text-slate-600">
        Showing <?= count($notifications) ?> notification(s)
    </div>
    <?php endif; ?>
</div>

<?php if ($isAdmin): ?>
<script>
async function resendNotification(id) {
    if (!confirm('Resend this email notification?')) return;
    
    try {
        const response = await fetch(`/notifications/${id}/resend`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('Notification queued for resend');
            window.location.reload();
        } else {
            alert('Failed to resend: ' + data.message);
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }
}
</script>
<?php endif; ?>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>