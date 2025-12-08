<?php
use App\Helpers\Security;

$title = 'Dashboard - Helpdesk System';
$pageTitle = 'Dashboard';
ob_start();
?>

<!-- Stats Grid -->
<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
    <!-- Total Tickets -->
    <div class="bg-white overflow-hidden shadow rounded-lg hover:shadow-md transition">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="rounded-md bg-slate-100 p-3">
                        <svg class="h-6 w-6 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-slate-500 truncate">Total Tickets</dt>
                        <dd class="flex items-baseline">
                            <div class="text-2xl font-semibold text-slate-900"><?= $stats['total'] ?></div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Open Tickets -->
    <div class="bg-white overflow-hidden shadow rounded-lg hover:shadow-md transition">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="rounded-md bg-amber-100 p-3">
                        <svg class="h-6 w-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-slate-500 truncate">Open</dt>
                        <dd class="flex items-baseline">
                            <div class="text-2xl font-semibold text-slate-900"><?= $stats['open'] ?></div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- In Progress -->
    <div class="bg-white overflow-hidden shadow rounded-lg hover:shadow-md transition">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="rounded-md bg-sky-100 p-3">
                        <svg class="h-6 w-6 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-slate-500 truncate">In Progress</dt>
                        <dd class="flex items-baseline">
                            <div class="text-2xl font-semibold text-slate-900"><?= $stats['progress'] ?></div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Closed -->
    <div class="bg-white overflow-hidden shadow rounded-lg hover:shadow-md transition">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="rounded-md bg-emerald-100 p-3">
                        <svg class="h-6 w-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-slate-500 truncate">Closed</dt>
                        <dd class="flex items-baseline">
                            <div class="text-2xl font-semibold text-slate-900"><?= $stats['closed'] ?></div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
    <!-- Recent Tickets -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-5 border-b border-slate-200">
            <h3 class="text-lg font-medium text-slate-900">Recent Tickets</h3>
        </div>
        <div class="divide-y divide-slate-200">
            <?php if (empty($recentTickets)): ?>
            <div class="px-6 py-8 text-center">
                <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <p class="mt-2 text-sm text-slate-500">No tickets yet</p>
                <a href="/tickets/create" class="mt-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700">
                    Create your first ticket
                </a>
            </div>
            <?php else: ?>
                <?php foreach ($recentTickets as $ticket): ?>
                <a href="/tickets/<?= $ticket['id'] ?>" class="block px-6 py-4 hover:bg-slate-50 transition">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-900 truncate">
                                <?= Security::escape($ticket['title']) ?>
                            </p>
                            <p class="text-sm text-slate-500 mt-1">
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
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium <?= $statusColors[$ticket['status']] ?>">
                                    <?= ucfirst($ticket['status']) ?>
                                </span>
                                <span class="ml-2 <?= $priorityColors[$ticket['priority']] ?>">
                                    <?= ucfirst($ticket['priority']) ?>
                                </span>
                            </p>
                        </div>
                        <div class="ml-4 flex-shrink-0 text-sm text-slate-500">
                            <?= date('M d, Y', strtotime($ticket['created_at'])) ?>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="px-6 py-3 bg-slate-50 border-t border-slate-200">
            <a href="/tickets" class="text-sm font-medium text-emerald-600 hover:text-emerald-700">
                View all tickets →
            </a>
        </div>
    </div>

    <!-- Priority Distribution -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-5 border-b border-slate-200">
            <h3 class="text-lg font-medium text-slate-900">Priority Distribution</h3>
        </div>
        <div class="px-6 py-5 space-y-4">
            <?php
            $priorities = [
                'urgent' => ['label' => 'Urgent', 'color' => 'bg-red-500'],
                'high' => ['label' => 'High', 'color' => 'bg-orange-500'],
                'medium' => ['label' => 'Medium', 'color' => 'bg-amber-500'],
                'low' => ['label' => 'Low', 'color' => 'bg-slate-500']
            ];
            
            $total = array_sum($priorityStats);
            
            foreach ($priorities as $key => $priority):
                $count = $priorityStats[$key];
                $percentage = $total > 0 ? ($count / $total) * 100 : 0;
            ?>
            <div>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-sm font-medium text-slate-700"><?= $priority['label'] ?></span>
                    <span class="text-sm text-slate-500"><?= $count ?> (<?= number_format($percentage, 1) ?>%)</span>
                </div>
                <div class="w-full bg-slate-200 rounded-full h-2">
                    <div class="<?= $priority['color'] ?> h-2 rounded-full transition-all" style="width: <?= $percentage ?>%"></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>