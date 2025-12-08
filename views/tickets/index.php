<?php
use App\Helpers\Security;

$title = 'All Tickets - Helpdesk System';
$pageTitle = 'All Tickets';
$isAdmin = Security::isAdmin();
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
    <div>
        <h2 class="text-2xl font-bold text-slate-900">All Tickets</h2>
        <p class="mt-1 text-sm text-slate-600">Manage and track all support tickets</p>
    </div>
    <div class="flex gap-2">
        <?php if ($isAdmin): ?>
        <a href="/tickets/export<?= !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '' ?>" 
           class="inline-flex items-center justify-center px-4 py-2 border border-slate-300 rounded-md shadow-sm text-sm font-medium text-slate-700 bg-white hover:bg-slate-50 transition">
            <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            Export CSV
        </a>
        <?php endif; ?>
        <a href="/tickets/create" 
           class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 transition">
            <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            New Ticket
        </a>
    </div>
</div>

<!-- Statistics Cards -->
<?php if (!empty($stats)): ?>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-slate-600">Total Tickets</p>
                <p class="text-2xl font-bold text-slate-900 mt-1"><?= $stats['total'] ?? 0 ?></p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-slate-600">Open</p>
                <p class="text-2xl font-bold text-amber-600 mt-1"><?= $stats['open'] ?? 0 ?></p>
            </div>
            <div class="bg-amber-100 rounded-full p-3">
                <svg class="h-6 w-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-slate-600">In Progress</p>
                <p class="text-2xl font-bold text-sky-600 mt-1"><?= $stats['progress'] ?? 0 ?></p>
            </div>
            <div class="bg-sky-100 rounded-full p-3">
                <svg class="h-6 w-6 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-slate-600">Closed</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1"><?= $stats['closed'] ?? 0 ?></p>
            </div>
            <div class="bg-emerald-100 rounded-full p-3">
                <svg class="h-6 w-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Filters -->
<div class="mb-6 bg-white shadow rounded-lg p-4">
    <form method="GET" action="/tickets" x-data="ticketFilters()" @submit.prevent="applyFilters">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-<?= $isAdmin ? '5' : '4' ?> gap-4">
            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Search</label>
                <input type="text" 
                       name="search"
                       x-model="search"
                       value="<?= Security::escape($_GET['search'] ?? '') ?>"
                       placeholder="Search tickets..."
                       class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            
            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Status</label>
                <select name="status" 
                        x-model="status"
                        class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">All Status</option>
                    <option value="open" <?= ($_GET['status'] ?? '') === 'open' ? 'selected' : '' ?>>Open</option>
                    <option value="progress" <?= ($_GET['status'] ?? '') === 'progress' ? 'selected' : '' ?>>In Progress</option>
                    <option value="closed" <?= ($_GET['status'] ?? '') === 'closed' ? 'selected' : '' ?>>Closed</option>
                </select>
            </div>
            
            <!-- Priority Filter -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Priority</label>
                <select name="priority" 
                        x-model="priority"
                        class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">All Priority</option>
                    <option value="low" <?= ($_GET['priority'] ?? '') === 'low' ? 'selected' : '' ?>>Low</option>
                    <option value="medium" <?= ($_GET['priority'] ?? '') === 'medium' ? 'selected' : '' ?>>Medium</option>
                    <option value="high" <?= ($_GET['priority'] ?? '') === 'high' ? 'selected' : '' ?>>High</option>
                    <option value="urgent" <?= ($_GET['priority'] ?? '') === 'urgent' ? 'selected' : '' ?>>Urgent</option>
                </select>
            </div>
            
            <!-- Category Filter -->
            <?php if (!empty($categories)): ?>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Category</label>
                <select name="category_id" 
                        x-model="category_id"
                        class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id'] ?>" <?= ($_GET['category_id'] ?? '') == $category['id'] ? 'selected' : '' ?>>
                        <?= Security::escape($category['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>
            
            <!-- Assigned Filter (Admin Only) -->
            <?php if ($isAdmin && !empty($admins)): ?>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Assigned To</label>
                <select name="assigned_to" 
                        x-model="assigned_to"
                        class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">All</option>
                    <option value="unassigned" <?= ($_GET['assigned_to'] ?? '') === 'unassigned' ? 'selected' : '' ?>>Unassigned</option>
                    <option value="me" <?= ($_GET['assigned_to'] ?? '') === 'me' ? 'selected' : '' ?>>Assigned to Me</option>
                    <?php foreach ($admins as $admin): ?>
                    <option value="<?= $admin['id'] ?>" <?= ($_GET['assigned_to'] ?? '') == $admin['id'] ? 'selected' : '' ?>>
                        <?= Security::escape($admin['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>
            
            <!-- Actions -->
            <div class="flex items-end gap-2">
                <button type="submit" 
                        class="flex-1 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-md text-sm font-medium transition">
                    Apply
                </button>
                <a href="/tickets" 
                   class="px-4 py-2 border border-slate-300 rounded-md text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                    Reset
                </a>
            </div>
        </div>
    </form>

    <!-- Tickets List - Desktop Table -->
    <div class="mt-6 hidden md:block">
        <div class="overflow-hidden rounded-lg border border-slate-200">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Ticket</th>
                        <?php if (!empty($categories)): ?>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Category</th>
                        <?php endif; ?>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Priority</th>
                        <?php if ($isAdmin): ?>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Assigned</th>
                        <?php endif; ?>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-700 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    <?php if (empty($tickets)): ?>
                    <tr>
                        <td colspan="<?= $isAdmin ? '7' : '6' ?>" class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="mt-2 text-sm text-slate-500">No tickets found</p>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($tickets as $ticket): ?>
                        <tr class="hover:bg-slate-50 cursor-pointer" onclick="window.location.href='/tickets/<?= $ticket['id'] ?>'">
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-slate-900"><?= Security::escape($ticket['title']) ?></p>
                                <p class="text-xs text-slate-500 mt-1"><?= Security::escape($ticket['user_name']) ?></p>
                            </td>
                            <?php if (!empty($categories)): ?>
                            <td class="px-6 py-4">
                                <?php if ($ticket['category_name']): ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
                                      style="background-color: <?= $ticket['category_color'] ?>20; color: <?= $ticket['category_color'] ?>">
                                    <?= Security::escape($ticket['category_name']) ?>
                                </span>
                                <?php else: ?>
                                <span class="text-xs text-slate-400">-</span>
                                <?php endif; ?>
                            </td>
                            <?php endif; ?>
                            <td class="px-6 py-4">
                                <?php
                                $statusColors = [
                                    'open' => 'bg-amber-100 text-amber-800',
                                    'progress' => 'bg-sky-100 text-sky-800',
                                    'closed' => 'bg-emerald-100 text-emerald-800'
                                ];
                                ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $statusColors[$ticket['status']] ?>">
                                    <?= ucfirst($ticket['status']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <?php
                                $priorityColors = [
                                    'low' => 'text-slate-600',
                                    'medium' => 'text-amber-600',
                                    'high' => 'text-orange-600',
                                    'urgent' => 'text-red-600'
                                ];
                                ?>
                                <span class="inline-flex items-center text-sm font-medium <?= $priorityColors[$ticket['priority']] ?>">
                                    <svg class="mr-1.5 h-2 w-2 fill-current" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3" />
                                    </svg>
                                    <?= ucfirst($ticket['priority']) ?>
                                </span>
                            </td>
                            <?php if ($isAdmin): ?>
                            <td class="px-6 py-4">
                                <span class="text-sm text-slate-700">
                                    <?= $ticket['assigned_name'] ? Security::escape($ticket['assigned_name']) : '-' ?>
                                </span>
                            </td>
                            <?php endif; ?>
                            <td class="px-6 py-4 text-sm text-slate-500">
                                <?= date('M d, Y', strtotime($ticket['created_at'])) ?>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <svg class="inline h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tickets List - Mobile Cards -->
    <div class="mt-6 md:hidden space-y-4">
        <?php if (empty($tickets)): ?>
        <div class="bg-white rounded-lg border border-slate-200 p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="mt-2 text-sm text-slate-500">No tickets found</p>
        </div>
        <?php else: ?>
            <?php foreach ($tickets as $ticket): ?>
            <a href="/tickets/<?= $ticket['id'] ?>" class="block">
                <div class="bg-white rounded-lg border border-slate-200 p-4 hover:shadow-md transition">
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-medium text-slate-900 truncate"><?= Security::escape($ticket['title']) ?></h3>
                            <p class="text-xs text-slate-500 mt-1"><?= Security::escape($ticket['user_name']) ?></p>
                        </div>
                        <svg class="h-5 w-5 text-slate-400 ml-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                    
                    <div class="flex flex-wrap gap-2 items-center">
                        <?php
                        $statusColors = [
                            'open' => 'bg-amber-100 text-amber-800',
                            'progress' => 'bg-sky-100 text-sky-800',
                            'closed' => 'bg-emerald-100 text-emerald-800'
                        ];
                        $priorityColors = [
                            'low' => 'text-slate-600',
                            'medium' => 'text-amber-600',
                            'high' => 'text-orange-600',
                            'urgent' => 'text-red-600'
                        ];
                        ?>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $statusColors[$ticket['status']] ?>">
                            <?= ucfirst($ticket['status']) ?>
                        </span>
                        
                        <span class="inline-flex items-center text-xs font-medium <?= $priorityColors[$ticket['priority']] ?>">
                            <svg class="mr-1 h-2 w-2 fill-current" viewBox="0 0 8 8">
                                <circle cx="4" cy="4" r="3" />
                            </svg>
                            <?= ucfirst($ticket['priority']) ?>
                        </span>
                        
                        <span class="text-xs text-slate-500 ml-auto">
                            <?= date('M d', strtotime($ticket['created_at'])) ?>
                        </span>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if (!empty($tickets) && $pagination['total_pages'] > 1): ?>
    <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
        <!-- Results Info -->
        <div class="text-sm text-slate-600">
            Showing <span class="font-medium"><?= $pagination['start_item'] ?></span> 
            to <span class="font-medium"><?= $pagination['end_item'] ?></span> 
            of <span class="font-medium"><?= $pagination['total_items'] ?></span> results
        </div>

        <!-- Pagination Controls -->
        <nav class="flex items-center gap-2">
            <!-- Previous Button -->
            <?php if ($pagination['has_prev']): ?>
                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $pagination['prev_page']])) ?>" 
                   class="px-3 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-md hover:bg-slate-50 transition">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
            <?php else: ?>
                <span class="px-3 py-2 text-sm font-medium text-slate-400 bg-slate-100 border border-slate-200 rounded-md cursor-not-allowed">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </span>
            <?php endif; ?>

            <!-- Page Numbers -->
            <div class="hidden sm:flex gap-1">
                <?php
                $start = max(1, $pagination['current_page'] - 2);
                $end = min($pagination['total_pages'], $pagination['current_page'] + 2);
                
                if ($start > 1): ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => 1])) ?>" 
                       class="px-3 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-md hover:bg-slate-50 transition">
                        1
                    </a>
                    <?php if ($start > 2): ?>
                        <span class="px-3 py-2 text-sm font-medium text-slate-400">...</span>
                    <?php endif; ?>
                <?php endif; ?>

                <?php for ($i = $start; $i <= $end; $i++): ?>
                    <?php if ($i == $pagination['current_page']): ?>
                        <span class="px-3 py-2 text-sm font-medium text-white bg-emerald-600 border border-emerald-600 rounded-md">
                            <?= $i ?>
                        </span>
                    <?php else: ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" 
                           class="px-3 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-md hover:bg-slate-50 transition">
                            <?= $i ?>
                        </a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($end < $pagination['total_pages']): ?>
                    <?php if ($end < $pagination['total_pages'] - 1): ?>
                        <span class="px-3 py-2 text-sm font-medium text-slate-400">...</span>
                    <?php endif; ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $pagination['total_pages']])) ?>" 
                       class="px-3 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-md hover:bg-slate-50 transition">
                        <?= $pagination['total_pages'] ?>
                    </a>
                <?php endif; ?>
            </div>

            <!-- Mobile Page Info -->
            <div class="sm:hidden px-3 py-2 text-sm font-medium text-slate-700">
                Page <?= $pagination['current_page'] ?> of <?= $pagination['total_pages'] ?>
            </div>

            <!-- Next Button -->
            <?php if ($pagination['has_next']): ?>
                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $pagination['next_page']])) ?>" 
                   class="px-3 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-md hover:bg-slate-50 transition">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            <?php else: ?>
                <span class="px-3 py-2 text-sm font-medium text-slate-400 bg-slate-100 border border-slate-200 rounded-md cursor-not-allowed">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </span>
            <?php endif; ?>
        </nav>
    </div>
    <?php elseif (!empty($tickets)): ?>
    <!-- Result Count for single page -->
    <div class="mt-4 text-sm text-slate-600">
        Showing <?= count($tickets) ?> ticket(s)
    </div>
    <?php endif; ?>
</div>

<script>
function ticketFilters() {
    return {
        search: '<?= Security::escape($_GET['search'] ?? '') ?>',
        status: '<?= Security::escape($_GET['status'] ?? '') ?>',
        priority: '<?= Security::escape($_GET['priority'] ?? '') ?>',
        category_id: '<?= Security::escape($_GET['category_id'] ?? '') ?>',
        assigned_to: '<?= Security::escape($_GET['assigned_to'] ?? '') ?>',
        
        applyFilters() {
            this.$el.submit();
        }
    }
}
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>