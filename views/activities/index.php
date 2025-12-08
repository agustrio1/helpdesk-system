<?php
use App\Helpers\Security;
use App\Models\ActivityLog;

$title = 'Activity Log - Helpdesk System';
$pageTitle = 'Recent Activities';
$isAdmin = Security::isAdmin();
ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
    <div>
        <h2 class="text-2xl font-bold text-slate-900">Activity Log</h2>
        <p class="mt-1 text-sm text-slate-600">Track all system activities and changes</p>
    </div>
    <?php if ($isAdmin): ?>
    <a href="/activities/export" 
       class="inline-flex items-center justify-center px-4 py-2 border border-slate-300 rounded-md shadow-sm text-sm font-medium text-slate-700 bg-white hover:bg-slate-50 transition">
        <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
        </svg>
        Export Log
    </a>
    <?php endif; ?>
</div>

<!-- Activity Timeline -->
<div class="bg-white shadow rounded-lg">
    <div class="px-6 py-4 border-b border-slate-200">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-slate-900">Recent Activities</h3>
            <span class="text-sm text-slate-500"><?= count($activities) ?> activities</span>
        </div>
    </div>

    <div class="px-6 py-4">
        <?php if (empty($activities)): ?>
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="mt-2 text-sm text-slate-500">No activities found</p>
        </div>
        <?php else: ?>
        <div class="flow-root">
            <ul class="-mb-8">
                <?php 
                $groupedActivities = [];
                foreach ($activities as $activity) {
                    $date = date('Y-m-d', strtotime($activity['created_at']));
                    $groupedActivities[$date][] = $activity;
                }
                
                foreach ($groupedActivities as $date => $dateActivities): 
                ?>
                <!-- Date Header -->
                <li class="mb-4">
                    <div class="relative pb-2">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="h-8 w-8 rounded-full bg-slate-100 flex items-center justify-center">
                                    <svg class="h-5 w-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-slate-900">
                                    <?php 
                                    $dateObj = new DateTime($date);
                                    $today = new DateTime();
                                    $yesterday = new DateTime('yesterday');
                                    
                                    if ($dateObj->format('Y-m-d') === $today->format('Y-m-d')) {
                                        echo 'Today';
                                    } elseif ($dateObj->format('Y-m-d') === $yesterday->format('Y-m-d')) {
                                        echo 'Yesterday';
                                    } else {
                                        echo $dateObj->format('F j, Y');
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Activities for this date -->
                    <ul class="ml-12 space-y-4">
                        <?php foreach ($dateActivities as $index => $activity): ?>
                        <li>
                            <div class="relative pb-4">
                                <?php if ($index < count($dateActivities) - 1): ?>
                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-slate-200" aria-hidden="true"></span>
                                <?php endif; ?>
                                
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white
                                            <?php
                                            switch($activity['action']) {
                                                case 'created':
                                                    echo 'bg-emerald-100';
                                                    break;
                                                case 'updated':
                                                    echo 'bg-blue-100';
                                                    break;
                                                case 'commented':
                                                    echo 'bg-purple-100';
                                                    break;
                                                case 'closed':
                                                    echo 'bg-slate-100';
                                                    break;
                                                default:
                                                    echo 'bg-slate-100';
                                            }
                                            ?>">
                                            <?php if ($activity['action'] === 'created'): ?>
                                            <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                            </svg>
                                            <?php elseif ($activity['action'] === 'updated'): ?>
                                            <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            <?php elseif ($activity['action'] === 'commented'): ?>
                                            <svg class="h-5 w-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                            </svg>
                                            <?php elseif ($activity['action'] === 'closed'): ?>
                                            <svg class="h-5 w-5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <?php else: ?>
                                            <svg class="h-5 w-5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                    
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <p class="text-sm text-slate-700">
                                                    <?= ActivityLog::formatActivity($activity) ?>
                                                </p>
                                                <div class="mt-1 flex items-center text-xs text-slate-500 space-x-2">
                                                    <span><?= date('g:i A', strtotime($activity['created_at'])) ?></span>
                                                    <span>•</span>
                                                    <a href="/tickets/<?= $activity['ticket_id'] ?>" 
                                                       class="hover:text-emerald-600">
                                                        Ticket #<?= $activity['ticket_id'] ?>: <?= Security::escape($activity['ticket_title']) ?>
                                                    </a>
                                                    <?php if ($activity['ticket_status']): ?>
                                                    <span>•</span>
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                        <?php
                                                        switch($activity['ticket_status']) {
                                                            case 'open':
                                                                echo 'bg-amber-100 text-amber-800';
                                                                break;
                                                            case 'progress':
                                                                echo 'bg-sky-100 text-sky-800';
                                                                break;
                                                            case 'closed':
                                                                echo 'bg-emerald-100 text-emerald-800';
                                                                break;
                                                            default:
                                                                echo 'bg-slate-100 text-slate-800';
                                                        }
                                                        ?>">
                                                        <?= ucfirst($activity['ticket_status']) ?>
                                                    </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>