<?php
use App\Helpers\Security;

$title = 'Create Ticket - Helpdesk System';
$pageTitle = 'Create New Ticket';
$oldInput = $_SESSION['old_input'] ?? [];
unset($_SESSION['old_input']);
ob_start();
?>

<div class="max-w-4xl">
    <div class="mb-6">
        <a href="/tickets" class="inline-flex items-center text-sm text-slate-600 hover:text-slate-900">
            <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Tickets
        </a>
    </div>

    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-5 border-b border-slate-200">
            <h3 class="text-lg font-medium text-slate-900">Submit a Support Ticket</h3>
            <p class="mt-1 text-sm text-slate-600">Please provide detailed information about your issue</p>
        </div>

        <form method="POST" action="/tickets" enctype="multipart/form-data" class="px-6 py-5 space-y-6" x-data="ticketForm()">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-slate-700 mb-2">
                    Ticket Title <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="title" 
                    id="title" 
                    required
                    value="<?= Security::escape($oldInput['title'] ?? '') ?>"
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                    placeholder="Brief summary of your issue"
                >
                <p class="mt-1 text-xs text-slate-500">Minimum 5 characters</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Priority -->
                <div>
                    <label for="priority" class="block text-sm font-medium text-slate-700 mb-2">
                        Priority <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <button 
                            type="button"
                            @click="priorityOpen = !priorityOpen"
                            @click.away="priorityOpen = false"
                            class="w-full px-4 py-2 text-left border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent bg-white flex items-center justify-between"
                        >
                            <span class="flex items-center">
                                <span 
                                    class="w-2 h-2 rounded-full mr-2"
                                    :class="{
                                        'bg-slate-400': selectedPriority === 'low',
                                        'bg-blue-500': selectedPriority === 'medium',
                                        'bg-orange-500': selectedPriority === 'high',
                                        'bg-red-600': selectedPriority === 'urgent'
                                    }"
                                ></span>
                                <span x-text="priorityLabels[selectedPriority]"></span>
                            </span>
                            <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        
                        <input type="hidden" name="priority" x-model="selectedPriority">
                        
                        <div 
                            x-show="priorityOpen"
                            x-transition
                            class="absolute z-10 w-full mt-1 bg-white border border-slate-200 rounded-lg shadow-lg"
                            style="display: none;"
                        >
                            <template x-for="(label, value) in priorityLabels" :key="value">
                                <button
                                    type="button"
                                    @click="selectedPriority = value; priorityOpen = false"
                                    class="w-full px-4 py-2 text-left hover:bg-slate-50 flex items-center first:rounded-t-lg last:rounded-b-lg"
                                    :class="{'bg-emerald-50': selectedPriority === value}"
                                >
                                    <span 
                                        class="w-2 h-2 rounded-full mr-3"
                                        :class="{
                                            'bg-slate-400': value === 'low',
                                            'bg-blue-500': value === 'medium',
                                            'bg-orange-500': value === 'high',
                                            'bg-red-600': value === 'urgent'
                                        }"
                                    ></span>
                                    <span x-text="label"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Category with Search -->
                <?php if (!empty($categories)): ?>
                <div>
                    <label for="category_id" class="block text-sm font-medium text-slate-700 mb-2">
                        Category (Optional)
                    </label>
                    <div class="relative">
                        <button 
                            type="button"
                            @click="categoryOpen = !categoryOpen"
                            @click.away="categoryOpen = false"
                            class="w-full px-4 py-2 text-left border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent bg-white flex items-center justify-between"
                        >
                            <span class="flex items-center">
                                <template x-if="selectedCategory">
                                    <div 
                                        class="w-6 h-6 rounded-md flex items-center justify-center mr-2"
                                        :style="`background-color: ${categories.find(c => c.id == selectedCategory)?.color || '#3B82F6'}`"
                                    >
                                        <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getIconPath(categories.find(c => c.id == selectedCategory)?.icon)" />
                                        </svg>
                                    </div>
                                </template>
                                <template x-if="!selectedCategory">
                                    <svg class="w-4 h-4 text-slate-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                </template>
                                <span x-text="selectedCategoryName || 'Select a category'" :class="{'text-slate-400': !selectedCategoryName}"></span>
                            </span>
                            <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        
                        <input type="hidden" name="category_id" x-model="selectedCategory">
                        
                        <div 
                            x-show="categoryOpen"
                            x-transition
                            class="absolute z-10 w-full mt-1 bg-white border border-slate-200 rounded-lg shadow-lg overflow-hidden"
                            style="display: none;"
                        >
                            <!-- Search Input -->
                            <div class="p-2 border-b border-slate-200">
                                <div class="relative">
                                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    <input
                                        type="text"
                                        x-model="categorySearch"
                                        @click.stop
                                        placeholder="Search categories..."
                                        class="w-full pl-9 pr-3 py-2 text-sm border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                                    >
                                </div>
                            </div>
                            
                            <!-- Categories List -->
                            <div class="max-h-60 overflow-y-auto">
                                <!-- Clear Selection -->
                                <button
                                    type="button"
                                    @click="selectedCategory = ''; selectedCategoryName = ''; categoryOpen = false"
                                    class="w-full px-4 py-2 text-left text-sm text-slate-500 hover:bg-slate-50 flex items-center border-b border-slate-100"
                                    :class="{'bg-emerald-50': !selectedCategory}"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    No Category
                                </button>
                                
                                <!-- Category Options -->
                                <template x-for="category in filteredCategories" :key="category.id">
                                    <button
                                        type="button"
                                        @click="selectCategory(category)"
                                        class="w-full px-4 py-2.5 text-left hover:bg-slate-50 flex items-center group"
                                        :class="{'bg-emerald-50': selectedCategory == category.id}"
                                    >
                                        <div class="flex items-center flex-1">
                                            <div 
                                                class="w-8 h-8 rounded-lg flex items-center justify-center mr-3"
                                                :style="`background-color: ${category.color || '#3B82F6'}`"
                                            >
                                                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getIconPath(category.icon)" />
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-slate-900" x-text="category.name"></div>
                                                <div class="text-xs text-slate-500" x-text="category.description" x-show="category.description"></div>
                                            </div>
                                        </div>
                                        <svg 
                                            x-show="selectedCategory == category.id"
                                            class="w-5 h-5 text-emerald-600" 
                                            fill="none" 
                                            viewBox="0 0 24 24" 
                                            stroke="currentColor"
                                        >
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </button>
                                </template>
                                
                                <!-- No Results -->
                                <div 
                                    x-show="filteredCategories.length === 0" 
                                    class="px-4 py-8 text-center text-sm text-slate-500"
                                >
                                    <svg class="w-12 h-12 mx-auto text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    No categories found
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Description with Rich Text Editor -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Description <span class="text-red-500">*</span>
                </label>
                <div id="quill-editor" class="bg-white" style="min-height: 200px;"></div>
                <input type="hidden" name="description" id="description" required>
                <p class="mt-2 text-xs text-slate-500">Minimum 10 characters. Please provide as much detail as possible.</p>
            </div>

            <!-- File Attachments -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Attachments (Optional)
                </label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 border-dashed rounded-lg hover:border-emerald-400 transition">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-slate-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-slate-600">
                            <label for="attachments" class="relative cursor-pointer rounded-md font-medium text-emerald-600 hover:text-emerald-500">
                                <span>Upload files</span>
                                <input 
                                    id="attachments" 
                                    name="attachments[]" 
                                    type="file" 
                                    multiple 
                                    class="sr-only"
                                    accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx"
                                    @change="handleFiles($event)"
                                >
                            </label>
                            <p class="pl-1">or drag and drop</p>
                        </div>
                        <p class="text-xs text-slate-500">PNG, JPG, GIF, PDF, DOC up to 5MB each</p>
                    </div>
                </div>

                <!-- File Preview -->
                <div x-show="files.length > 0" class="mt-4 space-y-2" style="display: none;">
                    <p class="text-sm font-medium text-slate-700">Selected files:</p>
                    <template x-for="(file, index) in files" :key="index">
                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg border border-slate-200">
                            <div class="flex items-center space-x-3">
                                <svg class="h-8 w-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-slate-900" x-text="file.name"></p>
                                    <p class="text-xs text-slate-500" x-text="file.size"></p>
                                </div>
                            </div>
                            <button 
                                type="button"
                                @click="removeFile(index)"
                                class="text-red-500 hover:text-red-700"
                            >
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end space-x-4 pt-4 border-t border-slate-200">
                <a href="/tickets" class="px-4 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                    Cancel
                </a>
                <button 
                    type="submit"
                    class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium transition flex items-center"
                >
                    <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Create Ticket
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function ticketForm() {
    return {
        // Priority
        priorityOpen: false,
        selectedPriority: '<?= $oldInput['priority'] ?? 'medium' ?>',
        priorityLabels: {
            'low': 'Low - General inquiry',
            'medium': 'Medium - Normal issue',
            'high': 'High - Important issue',
            'urgent': 'Urgent - Critical issue'
        },
        
        // Category
        categoryOpen: false,
        selectedCategory: '<?= $oldInput['categoryId'] ?? '' ?>',
        selectedCategoryName: '',
        categorySearch: '',
        categories: <?= json_encode($categories ?? []) ?>,
        
        // Files
        files: [],
        
        // Icon paths mapping
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
            if (!this.categorySearch) {
                return this.categories;
            }
            
            const search = this.categorySearch.toLowerCase();
            return this.categories.filter(cat => 
                cat.name.toLowerCase().includes(search) ||
                (cat.description && cat.description.toLowerCase().includes(search))
            );
        },
        
        getIconPath(iconName) {
            return this.iconPaths[iconName] || this.iconPaths['folder'];
        },
        
        selectCategory(category) {
            this.selectedCategory = category.id;
            this.selectedCategoryName = category.name;
            this.categoryOpen = false;
            this.categorySearch = '';
        },
        
        handleFiles(event) {
            const fileList = Array.from(event.target.files);
            this.files = fileList.map(file => ({
                name: file.name,
                size: this.formatFileSize(file.size)
            }));
        },
        
        removeFile(index) {
            this.files.splice(index, 1);
            const input = document.getElementById('attachments');
            input.value = '';
        },
        
        formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        },
        
        init() {
            // Set initial category name if selected
            if (this.selectedCategory) {
                const category = this.categories.find(c => c.id == this.selectedCategory);
                if (category) {
                    this.selectedCategoryName = category.name;
                }
            }
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Quill Editor
    const quill = window.initQuillEditor('quill-editor', 'description');
    
    // Set old input if available
    const oldDescription = <?= json_encode($oldInput['description'] ?? '') ?>;
    if (oldDescription) {
        quill.root.innerHTML = oldDescription;
    }
});
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>