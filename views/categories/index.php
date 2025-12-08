<?php
use App\Helpers\Security;

$title = 'Manage Categories - Helpdesk System';
$pageTitle = 'Ticket Categories';

// Define icon paths (same as create form)
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

// Helper function to get icon path
function getIconPath($iconName, $iconPaths) {
    return $iconPaths[$iconName] ?? $iconPaths['folder'];
}

ob_start();
?>

<div class="mb-6 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
    <div>
        <h2 class="text-2xl font-bold text-slate-900">Ticket Categories</h2>
        <p class="mt-1 text-sm text-slate-600">Organize tickets by category</p>
    </div>
    <a href="/categories/create" 
       class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 transition">
        <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        New Category
    </a>
</div>

<!-- Statistics Cards -->
<?php if (!empty($stats)): ?>
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-slate-600">Total Categories</p>
                <p class="text-2xl font-bold text-slate-900 mt-1"><?= $stats['total'] ?? 0 ?></p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-slate-600">Active Categories</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1"><?= $stats['active'] ?? 0 ?></p>
            </div>
            <div class="bg-emerald-100 rounded-full p-3">
                <svg class="h-6 w-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-slate-600">Total Tickets</p>
                <p class="text-2xl font-bold text-sky-600 mt-1"><?= $stats['total_tickets'] ?? 0 ?></p>
            </div>
            <div class="bg-sky-100 rounded-full p-3">
                <svg class="h-6 w-6 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Categories List -->
<?php if (empty($categories)): ?>
<div class="bg-white shadow rounded-lg p-12 text-center">
    <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
    </svg>
    <p class="mt-2 text-sm text-slate-500">No categories found</p>
</div>
<?php else: ?>

<!-- Desktop Table View (Hidden on Mobile) -->
<div class="hidden md:block bg-white shadow rounded-lg overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200">
        <thead class="bg-slate-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Category</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Description</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Tickets</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-slate-700 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-slate-200">
            <?php foreach ($categories as $category): 
                $iconPath = getIconPath($category['icon'] ?? 'folder', $iconPaths);
            ?>
            <tr class="hover:bg-slate-50">
                <td class="px-6 py-4">
                    <div class="flex items-center">
                        <div class="h-10 w-10 rounded-lg flex items-center justify-center" 
                             style="background-color: <?= $category['color'] ?>20;">
                            <svg class="h-6 w-6" style="color: <?= $category['color'] ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $iconPath ?>" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-slate-900"><?= Security::escape($category['name']) ?></p>
                            <p class="text-xs text-slate-500">ID: <?= $category['id'] ?></p>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <p class="text-sm text-slate-600 max-w-xs truncate">
                        <?= Security::escape($category['description'] ?? '-') ?>
                    </p>
                </td>
                <td class="px-6 py-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">
                        <?= isset($category['ticket_count']) ? $category['ticket_count'] : 0 ?> tickets
                    </span>
                </td>
                <td class="px-6 py-4">
                    <form method="POST" action="/categories/<?= $category['id'] ?>/toggle" class="inline">
                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                        <button type="submit" 
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $category['is_active'] ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-800' ?>">
                            <?= $category['is_active'] ? 'Active' : 'Inactive' ?>
                        </button>
                    </form>
                </td>
                <td class="px-6 py-4 text-right text-sm font-medium">
                    <a href="/categories/<?= $category['id'] ?>/edit" 
                       class="text-emerald-600 hover:text-emerald-900 mr-3">
                        Edit
                    </a>
                    <?php if ($category['ticket_count'] == 0): ?>
                    <button onclick="deleteCategory(<?= $category['id'] ?>)"
                            class="text-red-600 hover:text-red-900">
                        Delete
                    </button>
                    <?php else: ?>
                    <span class="text-slate-400 cursor-not-allowed" title="Cannot delete category with tickets">
                        Delete
                    </span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Mobile Card View (Hidden on Desktop) -->
<div class="md:hidden space-y-4">
    <?php foreach ($categories as $category): 
        $iconPath = getIconPath($category['icon'] ?? 'folder', $iconPaths);
    ?>
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <!-- Card Header -->
        <div class="p-4 border-b border-slate-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="h-10 w-10 rounded-lg flex items-center justify-center flex-shrink-0" 
                         style="background-color: <?= $category['color'] ?>20;">
                        <svg class="h-6 w-6" style="color: <?= $category['color'] ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $iconPath ?>" />
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-slate-900 truncate"><?= Security::escape($category['name']) ?></p>
                        <p class="text-xs text-slate-500">ID: <?= $category['id'] ?></p>
                    </div>
                </div>
                <form method="POST" action="/categories/<?= $category['id'] ?>/toggle" class="flex-shrink-0">
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                    <button type="submit" 
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $category['is_active'] ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-800' ?>">
                        <?= $category['is_active'] ? 'Active' : 'Inactive' ?>
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Card Body -->
        <div class="p-4 space-y-3">
            <!-- Description -->
            <div>
                <p class="text-xs font-medium text-slate-500 mb-1">Description</p>
                <p class="text-sm text-slate-700">
                    <?= Security::escape($category['description'] ?? 'No description') ?>
                </p>
            </div>
            
            <!-- Ticket Count -->
            <div>
                <p class="text-xs font-medium text-slate-500 mb-1">Tickets</p>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">
                    <?= isset($category['ticket_count']) ? $category['ticket_count'] : 0 ?> tickets
                </span>
            </div>
        </div>
        
        <!-- Card Footer (Actions) -->
        <div class="px-4 py-3 bg-slate-50 border-t border-slate-200 flex items-center justify-end space-x-3">
            <a href="/categories/<?= $category['id'] ?>/edit" 
               class="inline-flex items-center px-3 py-1.5 border border-slate-300 rounded-md text-xs font-medium text-slate-700 bg-white hover:bg-slate-50 transition">
                <svg class="mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit
            </a>
            <?php if ($category['ticket_count'] == 0): ?>
            <button onclick="deleteCategory(<?= $category['id'] ?>)"
                    class="inline-flex items-center px-3 py-1.5 border border-red-300 rounded-md text-xs font-medium text-red-700 bg-white hover:bg-red-50 transition">
                <svg class="mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Delete
            </button>
            <?php else: ?>
            <button disabled
                    class="inline-flex items-center px-3 py-1.5 border border-slate-200 rounded-md text-xs font-medium text-slate-400 bg-slate-50 cursor-not-allowed"
                    title="Cannot delete category with tickets">
                <svg class="mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Delete
            </button>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php endif; ?>

<script>
function deleteCategory(id) {
    if (!confirm('Are you sure you want to delete this category? This action cannot be undone.')) {
        return;
    }
    
    fetch('/categories/' + id, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Failed to delete category');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting the category');
    });
}
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>