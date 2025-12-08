<?php
use App\Helpers\Security;

$title = 'Create Category - Helpdesk System';
$pageTitle = 'Create New Category';
ob_start();

// Define icons with their SVG paths
$iconList = [
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

$selectedIcon = $_SESSION['old_input']['icon'] ?? 'folder';
?>

<div class="max-w-3xl mx-auto">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="/categories" 
           class="inline-flex items-center text-sm text-slate-600 hover:text-slate-900">
            <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Categories
        </a>
    </div>

    <!-- Page Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-900">Create New Category</h2>
        <p class="mt-1 text-sm text-slate-600">Add a new ticket category to organize your support tickets</p>
    </div>

    <!-- Create Form -->
    <div class="bg-white shadow rounded-lg">
        <form method="POST" action="/categories/store" class="p-6 space-y-6">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

            <!-- Category Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 mb-2">
                    Category Name <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       required
                       value="<?= Security::escape($_SESSION['old_input']['name'] ?? '') ?>"
                       class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                       placeholder="e.g., Technical Support, Billing, General Inquiry">
                <p class="mt-1 text-xs text-slate-500">Minimum 2 characters</p>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-slate-700 mb-2">
                    Description
                </label>
                <textarea id="description" 
                          name="description" 
                          rows="4"
                          class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                          placeholder="Brief description of this category..."><?= Security::escape($_SESSION['old_input']['description'] ?? '') ?></textarea>
            </div>

            <!-- Color Picker -->
            <div>
                <label for="color" class="block text-sm font-medium text-slate-700 mb-2">
                    Category Color
                </label>
                <div class="flex items-center space-x-4">
                    <input type="color" 
                           id="color" 
                           name="color" 
                           value="<?= Security::escape($_SESSION['old_input']['color'] ?? '#3B82F6') ?>"
                           class="h-12 w-20 border border-slate-300 rounded cursor-pointer">
                    <div class="flex-1">
                        <p class="text-sm text-slate-600">Choose a color to represent this category</p>
                        <p class="text-xs text-slate-500 mt-1">Current: <span id="color-preview" class="font-mono"><?= strtoupper($_SESSION['old_input']['color'] ?? '#3B82F6') ?></span></p>
                    </div>
                </div>
            </div>

            <!-- Icon Selection -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Category Icon
                </label>
                <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 gap-3">
                    <?php foreach ($iconList as $iconName => $iconPath): ?>
                    <label class="relative cursor-pointer group">
                        <input type="radio" 
                               name="icon" 
                               value="<?= $iconName ?>" 
                               class="peer sr-only" 
                               <?= $iconName === $selectedIcon ? 'checked' : '' ?>>
                        <div class="p-3 border-2 border-slate-200 rounded-lg hover:border-emerald-400 hover:bg-emerald-50 peer-checked:border-emerald-600 peer-checked:bg-emerald-50 transition-all duration-200">
                            <svg class="h-6 w-6 text-slate-600 group-hover:text-emerald-600 peer-checked:text-emerald-600 transition-colors" 
                                 fill="none" 
                                 viewBox="0 0 24 24" 
                                 stroke="currentColor">
                                <path stroke-linecap="round" 
                                      stroke-linejoin="round" 
                                      stroke-width="2" 
                                      d="<?= $iconPath ?>" />
                            </svg>
                        </div>
                        <!-- Icon name tooltip -->
                        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-slate-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
                            <?= ucfirst($iconName) ?>
                        </div>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Status Toggle -->
            <div>
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" 
                           name="is_active" 
                           value="1"
                           checked
                           class="h-4 w-4 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500">
                    <span class="ml-2 text-sm font-medium text-slate-700">Active</span>
                </label>
                <p class="mt-1 text-xs text-slate-500">Inactive categories won't be available for new tickets</p>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-200">
                <a href="/categories" 
                   class="px-4 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 transition">
                    Create Category
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Update color preview
document.getElementById('color').addEventListener('input', function(e) {
    document.getElementById('color-preview').textContent = e.target.value.toUpperCase();
});
</script>

<?php
// Clear old input after displaying
unset($_SESSION['old_input']);

$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>