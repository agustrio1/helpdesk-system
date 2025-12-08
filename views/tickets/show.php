<?php
use App\Helpers\Security;
use App\Models\ActivityLog;

$title = 'Ticket #' . $ticket['id'] . ' - Helpdesk System';
$pageTitle = 'Ticket Details';
$isAdmin = Security::isAdmin();
$isOwner = $ticket['user_id'] == $_SESSION['user_id'];
$isClosed = $ticket['status'] === 'closed';

// Define icon paths
$iconPaths = [
    'folder' => 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z',
    'tag' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z',
    'star' => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z',
    'heart' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
    'zap' => 'M13 10V3L4 14h7v7l9-11h-7z',
    'tool' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
    'users' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
    'phone' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z',
    'mail' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
    'shield' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
    'globe' => 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    'book' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
    'code' => 'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4',
    'database' => 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4',
    'chart' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'
];

ob_start();
?>

<div class="mb-6">
    <a href="/tickets" class="inline-flex items-center text-sm text-slate-600 hover:text-slate-900">
        <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Back to Tickets
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Ticket Header -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center flex-wrap gap-2 mb-2">
                            <span class="text-sm font-medium text-slate-500">Ticket #<?= $ticket['id'] ?></span>
                            <?php
                            $statusColors = [
                                'open' => 'bg-amber-100 text-amber-800',
                                'progress' => 'bg-sky-100 text-sky-800',
                                'closed' => 'bg-emerald-100 text-emerald-800'
                            ];
                            $priorityColors = [
                                'low' => 'bg-slate-100 text-slate-800',
                                'medium' => 'bg-amber-100 text-amber-800',
                                'high' => 'bg-orange-100 text-orange-800',
                                'urgent' => 'bg-red-100 text-red-800'
                            ];
                            ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $statusColors[$ticket['status']] ?>">
                                <?= ucfirst($ticket['status']) ?>
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $priorityColors[$ticket['priority']] ?>">
                                <?= ucfirst($ticket['priority']) ?> Priority
                            </span>
                            <?php if ($ticket['category_name']): ?>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium gap-1.5" 
                                  style="background-color: <?= $ticket['category_color'] ?>15; color: <?= $ticket['category_color'] ?>; border: 1px solid <?= $ticket['category_color'] ?>30;">
                                <span class="w-5 h-5 rounded flex items-center justify-center" style="background-color: <?= $ticket['category_color'] ?>">
                                    <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $iconPaths[$ticket['category_icon']] ?? $iconPaths['folder'] ?>" />
                                    </svg>
                                </span>
                                <?= Security::escape($ticket['category_name']) ?>
                            </span>
                            <?php endif; ?>
                        </div>
                        <h1 class="text-2xl font-bold text-slate-900"><?= Security::escape($ticket['title']) ?></h1>
                        <p class="mt-2 text-sm text-slate-600">
                            Created by <span class="font-medium"><?= Security::escape($ticket['user_name']) ?></span> 
                            on <?= date('M d, Y \a\t H:i', strtotime($ticket['created_at'])) ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="px-6 py-5">
                <h3 class="text-sm font-medium text-slate-700 mb-3">Description</h3>
                <div class="prose prose-sm max-w-none text-slate-600">
                    <?= $ticket['description'] ?>
                </div>
            </div>

            <!-- Attachments -->
            <?php if (!empty($attachments)): ?>
            <div class="px-6 py-5 border-t border-slate-200">
                <h3 class="text-sm font-medium text-slate-700 mb-3">Attachments (<?= count($attachments) ?>)</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <?php foreach ($attachments as $attachment): ?>
                    <a href="/<?= Security::escape($attachment['file_path']) ?>" 
                       target="_blank"
                       class="flex items-center p-3 border border-slate-200 rounded-lg hover:bg-slate-50 transition">
                        <svg class="h-8 w-8 text-slate-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        <div class="ml-3 flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-900 truncate">
                                <?= Security::escape($attachment['original_filename']) ?>
                            </p>
                            <p class="text-xs text-slate-500">
                                <?= number_format($attachment['file_size'] / 1024, 2) ?> KB
                            </p>
                        </div>
                        <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Activity Timeline -->
        <?php if (!empty($activities)): ?>
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200">
                <h3 class="text-lg font-medium text-slate-900">Activity Timeline</h3>
            </div>
            <div class="px-6 py-4">
                <div class="flow-root">
                    <ul class="-mb-8">
                        <?php foreach ($activities as $index => $activity): ?>
                        <li>
                            <div class="relative pb-8">
                                <?php if ($index < count($activities) - 1): ?>
                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-slate-200" aria-hidden="true"></span>
                                <?php endif; ?>
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="h-8 w-8 rounded-full bg-slate-100 flex items-center justify-center ring-8 ring-white">
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
                                            <?php else: ?>
                                            <svg class="h-5 w-5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div>
                                            <p class="text-sm text-slate-700">
                                                <?= ActivityLog::formatActivity($activity) ?>
                                            </p>
                                            <p class="mt-0.5 text-xs text-slate-500">
                                                <?= date('M d, Y \a\t H:i', strtotime($activity['created_at'])) ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Comments Section -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200">
                <h3 class="text-lg font-medium text-slate-900">Comments (<?= count($comments) ?>)</h3>
            </div>

            <div class="divide-y divide-slate-200 max-h-[500px] overflow-y-auto">
                <?php if (empty($comments)): ?>
                <div class="px-6 py-8 text-center">
                    <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <p class="mt-2 text-sm text-slate-500">No comments yet</p>
                </div>
                <?php else: ?>
                    <?php foreach ($comments as $comment): ?>
                    <div class="px-6 py-4" id="comment-<?= $comment['id'] ?>">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 rounded-full bg-slate-200 flex items-center justify-center">
                                    <span class="text-slate-600 font-medium">
                                        <?= strtoupper(substr($comment['user_name'], 0, 1)) ?>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center flex-wrap gap-2">
                                    <span class="text-sm font-medium text-slate-900">
                                        <?= Security::escape($comment['user_name']) ?>
                                    </span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium <?= $comment['user_role'] === 'admin' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-800' ?>">
                                        <?= ucfirst($comment['user_role']) ?>
                                    </span>
                                    <?php if ($comment['is_internal']): ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800">
                                        Internal
                                    </span>
                                    <?php endif; ?>
                                    <span class="text-xs text-slate-500">
                                        <?= date('M d, Y \a\t H:i', strtotime($comment['created_at'])) ?>
                                    </span>
                                </div>
                                <div class="mt-2 text-sm text-slate-600 prose prose-sm max-w-none">
                                    <?= $comment['comment'] ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Add Comment Form -->
            <?php if (!$isClosed): ?>
            <div class="px-6 py-5 bg-slate-50 border-t border-slate-200">
                <form method="POST" action="/comments" id="comment-form">
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                    <input type="hidden" name="ticket_id" value="<?= $ticket['id'] ?>">

                    <div class="mb-3">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Add Comment</label>
                        <div id="comment-editor" class="bg-white" style="min-height: 150px;"></div>
                        <input type="hidden" name="comment" id="comment-text" required>
                    </div>

                    <?php if ($isAdmin): ?>
                    <div class="mb-3">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_internal" value="1" class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-slate-300 rounded">
                            <span class="ml-2 text-sm text-slate-700">Internal comment (only visible to admins)</span>
                        </label>
                    </div>
                    <?php endif; ?>

                    <div class="flex justify-end">
                        <button 
                            type="submit"
                            class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition"
                        >
                            <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                            Post Comment
                        </button>
                    </div>
                </form>
            </div>
            <?php else: ?>
            <!-- Ticket Closed Notice -->
            <div class="px-6 py-5 bg-slate-100 border-t border-slate-200">
                <div class="flex items-start">
                    <svg class="h-5 w-5 text-slate-400 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-slate-700">Ticket Closed</p>
                        <p class="mt-1 text-sm text-slate-500">
                            This ticket has been closed and locked. No further modifications are allowed.
                        </p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="lg:col-span-1">
        <div class="bg-white shadow rounded-lg sticky top-20">
            <div class="px-6 py-4 border-b border-slate-200">
                <h3 class="text-lg font-medium text-slate-900">Ticket Information</h3>
            </div>

            <div class="px-6 py-4 space-y-4">
                <!-- Created By -->
                <div>
                    <label class="block text-xs font-medium text-slate-500 uppercase mb-1">Created By</label>
                    <p class="text-sm text-slate-900"><?= Security::escape($ticket['user_name']) ?></p>
                    <p class="text-xs text-slate-500"><?= Security::escape($ticket['user_email']) ?></p>
                </div>

                <!-- Category Info (Non-editable) -->
                <?php if ($ticket['category_name']): ?>
                <div class="pt-4 border-t border-slate-200">
                    <label class="block text-xs font-medium text-slate-500 uppercase mb-2">Category</label>
                    <div class="flex items-center p-3 rounded-lg border" style="background-color: <?= $ticket['category_color'] ?>10; border-color: <?= $ticket['category_color'] ?>30;">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center mr-3" style="background-color: <?= $ticket['category_color'] ?>">
                            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $iconPaths[$ticket['category_icon']] ?? $iconPaths['folder'] ?>" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium" style="color: <?= $ticket['category_color'] ?>">
                                <?= Security::escape($ticket['category_name']) ?>
                            </p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Customer Actions - Close Ticket -->
                <?php if (!$isAdmin && $isOwner && !$isClosed): ?>
                <div class="pt-4 border-t border-slate-200">
                    <label class="block text-xs font-medium text-slate-500 uppercase mb-2">Your Actions</label>
                    <form method="POST" action="/tickets/<?= $ticket['id'] ?>" onsubmit="return confirm('Are you sure you want to close this ticket? This action cannot be undone.')">
                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                        <input type="hidden" name="status" value="closed">
                        <button 
                            type="submit"
                            class="w-full inline-flex items-center justify-center px-4 py-2 bg-slate-600 hover:bg-slate-700 text-white text-sm font-medium rounded-lg transition"
                        >
                            <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Close This Ticket
                        </button>
                    </form>
                    <p class="mt-2 text-xs text-slate-500">Close this ticket if your issue has been resolved.</p>
                </div>
                <?php endif; ?>

                <!-- Admin Controls -->
                <?php if ($isAdmin): ?>
                    <?php if (!$isClosed): ?>
                    <div class="pt-4 border-t border-slate-200">
                        <label class="block text-xs font-medium text-slate-500 uppercase mb-2">Admin Controls</label>
                        
                        <!-- Assigned To -->
                        <div class="mb-4">
                            <label class="block text-xs font-medium text-slate-600 mb-1">Assigned To</label>
                            <form method="POST" action="/tickets/<?= $ticket['id'] ?>">
                                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                <select name="assigned_to" 
                                        onchange="this.form.submit()"
                                        class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                    <option value="0">Unassigned</option>
                                    <?php foreach ($admins as $admin): ?>
                                    <option value="<?= $admin['id'] ?>" <?= $ticket['assigned_to'] == $admin['id'] ? 'selected' : '' ?>>
                                        <?= Security::escape($admin['name']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </form>
                        </div>

                        <!-- Category with Search Select -->
                        <?php if (!empty($categories)): ?>
                        <div class="mb-4" x-data="categorySelector()">
                            <label class="block text-xs font-medium text-slate-600 mb-1">Category</label>
                            <div class="relative">
                                <button 
                                    type="button"
                                    @click="open = !open"
                                    @click.away="open = false"
                                    class="w-full px-3 py-2 text-left border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white flex items-center justify-between"
                                >
                                    <span class="flex items-center">
                                        <template x-if="selectedId">
                                            <div 
                                                class="w-6 h-6 rounded flex items-center justify-center mr-2"
                                                :style="`background-color: ${categories.find(c => c.id == selectedId)?.color || '#3B82F6'}`"
                                            >
                                                <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getIconPath(categories.find(c => c.id == selectedId)?.icon)" />
                                                </svg>
                                            </div>
                                        </template>
                                        <span class="text-sm" :class="{'text-slate-400': !selectedName}" x-text="selectedName || 'No Category'"></span>
                                    </span>
                                    <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                
                                <!-- Dropdown -->
                                <div 
                                    x-show="open"
                                    x-transition
                                    class="absolute z-50 w-full mt-1 bg-white border border-slate-200 rounded-lg shadow-lg overflow-hidden"
                                    style="display: none;"
                                >
                                    <!-- Search -->
                                    <div class="p-2 border-b border-slate-200">
                                        <input
                                            type="text"
                                            x-model="search"
                                            @click.stop
                                            placeholder="Search..."
                                            class="w-full px-2 py-1.5 text-xs border border-slate-300 rounded focus:outline-none focus:ring-1 focus:ring-emerald-500"
                                        >
                                    </div>
                                    
                                    <!-- Options -->
                                    <div class="max-h-48 overflow-y-auto">
                                        <!-- No Category -->
                                        <form method="POST" action="/tickets/<?= $ticket['id'] ?>">
                                            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                            <input type="hidden" name="category_id" value="">
                                            <button
                                                type="submit"
                                                class="w-full px-3 py-2 text-left text-xs hover:bg-slate-50 flex items-center border-b border-slate-100"
                                                :class="{'bg-emerald-50': !selectedId}"
                                            >
                                                <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                                <span class="text-slate-500">No Category</span>
                                            </button>
                                        </form>
                                        
                                        <!-- Category Options -->
                                        <template x-for="category in filteredCategories" :key="category.id">
                                            <form method="POST" action="/tickets/<?= $ticket['id'] ?>">
                                                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                                <input type="hidden" name="category_id" :value="category.id">
                                                <button
                                                    type="submit"
                                                    class="w-full px-3 py-2 text-left hover:bg-slate-50 flex items-center text-xs"
                                                    :class="{'bg-emerald-50': selectedId == category.id}"
                                                >
                                                    <div 
                                                        class="w-6 h-6 rounded flex items-center justify-center mr-2"
                                                        :style="`background-color: ${category.color}`"
                                                    >
                                                        <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getIconPath(category.icon)" />
                                                        </svg>
                                                    </div>
                                                    <span x-text="category.name"></span>
                                                </button>
                                            </form>
                                        </template>
                                        
                                        <!-- No Results -->
                                        <div 
                                            x-show="filteredCategories.length === 0" 
                                            class="px-3 py-6 text-center text-xs text-slate-500"
                                        >
                                            No categories found
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Status -->
                        <div class="mb-4">
                            <label class="block text-xs font-medium text-slate-600 mb-1">Status</label>
                            <form method="POST" action="/tickets/<?= $ticket['id'] ?>" onsubmit="return <?= $ticket['status'] !== 'closed' ? 'true' : 'confirm(\'Close this ticket? This will lock all modifications.\')' ?>">
                                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                <select name="status" 
                                        onchange="this.form.submit()"
                                        class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                    <option value="open" <?= $ticket['status'] === 'open' ? 'selected' : '' ?>>Open</option>
                                    <option value="progress" <?= $ticket['status'] === 'progress' ? 'selected' : '' ?>>In Progress</option>
                                    <option value="closed" <?= $ticket['status'] === 'closed' ? 'selected' : '' ?>>Closed</option>
                                </select>
                            </form>
                        </div>

                        <!-- Priority -->
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Priority</label>
                            <form method="POST" action="/tickets/<?= $ticket['id'] ?>">
                                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                <select name="priority" 
                                        onchange="this.form.submit()"
                                        class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                    <option value="low" <?= $ticket['priority'] === 'low' ? 'selected' : '' ?>>Low</option>
                                    <option value="medium" <?= $ticket['priority'] === 'medium' ? 'selected' : '' ?>>Medium</option>
                                    <option value="high" <?= $ticket['priority'] === 'high' ? 'selected' : '' ?>>High</option>
                                    <option value="urgent" <?= $ticket['priority'] === 'urgent' ? 'selected' : '' ?>>Urgent</option>
                                </select>
                            </form>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="pt-4 border-t border-slate-200">
                        <label class="block text-xs font-medium text-slate-500 uppercase mb-2">Ticket Information (Read Only)</label>
                        
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Assigned To</label>
                                <p class="text-sm text-slate-900 px-3 py-2 bg-slate-50 rounded-md border border-slate-200">
                                    <?= $ticket['assigned_name'] ? Security::escape($ticket['assigned_name']) : 'Unassigned' ?>
                                </p>
                            </div>
                            
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Status</label>
                                <p class="text-sm text-slate-900 px-3 py-2 bg-slate-50 rounded-md border border-slate-200">
                                    <?= ucfirst($ticket['status']) ?>
                                </p>
                            </div>
                            
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Priority</label>
                                <p class="text-sm text-slate-900 px-3 py-2 bg-slate-50 rounded-md border border-slate-200">
                                    <?= ucfirst($ticket['priority']) ?>
                                </p>
                            </div>
                        </div>

                        <div class="mt-4 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                            <div class="flex items-start">
                                <svg class="h-5 w-5 text-amber-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-amber-800">Ticket Locked</p>
                                    <p class="mt-1 text-xs text-amber-700">
                                        This ticket is closed and cannot be modified.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                <?php elseif ($ticket['assigned_name']): ?>
                <div class="pt-4 border-t border-slate-200">
                    <label class="block text-xs font-medium text-slate-500 uppercase mb-1">Assigned To</label>
                    <p class="text-sm text-slate-900"><?= Security::escape($ticket['assigned_name']) ?></p>
                </div>
                <?php endif; ?>

                <!-- Timestamps -->
                <div class="pt-4 border-t border-slate-200 space-y-2">
                    <div>
                        <label class="block text-xs font-medium text-slate-500 uppercase">Created</label>
                        <p class="text-sm text-slate-900"><?= date('M d, Y H:i', strtotime($ticket['created_at'])) ?></p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 uppercase">Last Updated</label>
                        <p class="text-sm text-slate-900"><?= date('M d, Y H:i', strtotime($ticket['updated_at'])) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function categorySelector() {
    return {
        open: false,
        search: '',
        selectedId: '<?= $ticket['category_id'] ?? '' ?>',
        selectedName: '<?= Security::escape($ticket['category_name'] ?? '') ?>',
        categories: <?= json_encode($categories ?? []) ?>,
        
        iconPaths: {
            'folder': 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z',
            'tag': 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z',
            'star': 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z',
            'heart': 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
            'zap': 'M13 10V3L4 14h7v7l9-11h-7z',
            'tool': 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
            'users': 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
            'phone': 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z',
            'mail': 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
            'shield': 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
            'globe': 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            'book': 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
            'code': 'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4',
            'database': 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4',
            'chart': 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'
        },
        
        get filteredCategories() {
            if (!this.search) return this.categories;
            const s = this.search.toLowerCase();
            return this.categories.filter(c => 
                c.name.toLowerCase().includes(s) ||
                (c.description && c.description.toLowerCase().includes(s))
            );
        },
        
        getIconPath(icon) {
            return this.iconPaths[icon] || this.iconPaths['folder'];
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    <?php if (!$isClosed): ?>
    // Initialize Comment Editor only if ticket is not closed
    const commentQuill = window.initQuillEditor('comment-editor', 'comment-text');
    <?php endif; ?>
});
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>