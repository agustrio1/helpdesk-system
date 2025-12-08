<?php
use App\Helpers\Security;
$userName = Security::escape($_SESSION['user_name'] ?? 'User');
$userRole = $_SESSION['user_role'] ?? 'customer';
$userAvatar = $_SESSION['user_avatar'] ?? null;
$isAdmin = Security::isAdmin();
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= Security::generateCSRFToken() ?>">
    <title><?= $title ?? 'Helpdesk System' ?></title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body class="h-full">
    
    <div class="min-h-full">
        <!-- Mobile sidebar backdrop -->
        <div id="sidebar-backdrop" 
             class="fixed inset-0 z-40 bg-slate-900 bg-opacity-50 lg:hidden hidden backdrop-blur-sm transition-opacity"
             onclick="toggleSidebar()"></div>

        <!-- Mobile sidebar -->
        <div id="mobile-sidebar"
             class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-2xl lg:hidden transform -translate-x-full transition-transform duration-300 ease-in-out">
            <?php include __DIR__ . '/../components/sidebar.php'; ?>
        </div>

        <!-- Static sidebar for desktop -->
        <div class="hidden lg:fixed lg:inset-y-0 lg:flex lg:w-64 lg:flex-col">
            <div class="flex flex-col flex-grow bg-white border-r border-slate-200 overflow-y-auto">
                <?php include __DIR__ . '/../components/sidebar.php'; ?>
            </div>
        </div>

        <!-- Main content -->
        <div class="lg:pl-64 flex flex-col flex-1">
            <!-- Top navbar -->
            <div class="sticky top-0 z-10 flex-shrink-0 bg-white border-b border-slate-200 backdrop-blur-lg bg-white/95">
                <div class="flex h-16 items-center px-4 sm:px-6 lg:px-8">
                    <!-- Mobile menu button -->
                    <button onclick="toggleSidebar()" 
                            type="button"
                            class="lg:hidden -ml-2 mr-3 inline-flex items-center justify-center rounded-lg p-2 text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors">
                        <span class="sr-only">Open sidebar</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    
                    <!-- Page Title -->
                    <div class="flex-1 min-w-0">
                        <h1 class="text-xl font-semibold text-slate-900 truncate">
                            <?= $pageTitle ?? 'Dashboard' ?>
                        </h1>
                    </div>
                    
                    <!-- Right section -->
                    <div class="flex items-center gap-3 sm:gap-4">
                        <!-- Divider -->
                        <div class="hidden sm:block h-6 w-px bg-slate-200"></div>

                        <!-- User profile button -->
                        <a href="/profile" class="flex items-center gap-3 rounded-lg px-2 py-1.5 hover:bg-slate-100 transition-colors group">
                            <!-- Avatar -->
                            <div class="flex-shrink-0">
                                <?php if (!empty($userAvatar) && file_exists(__DIR__ . '/../../public/' . $userAvatar)): ?>
                                    <img src="/<?= Security::escape($userAvatar) ?>" 
                                         alt="<?= $userName ?>"
                                         class="h-8 w-8 rounded-full object-cover ring-2 ring-slate-100 group-hover:ring-slate-200 transition-all">
                                <?php else: ?>
                                    <div class="h-8 w-8 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center ring-2 ring-slate-100 group-hover:ring-slate-200 transition-all">
                                        <span class="text-white font-semibold text-sm">
                                            <?= strtoupper(substr($userName, 0, 1)) ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- User info (hidden on mobile) -->
                            <div class="hidden md:block text-left">
                                <p class="text-sm font-medium text-slate-900 leading-tight">
                                    <?= $userName ?>
                                </p>
                                <p class="text-xs text-slate-500 leading-tight">
                                    <?= ucfirst($userRole) ?>
                                </p>
                            </div>

                            <!-- Chevron icon -->
                            <svg class="hidden md:block h-4 w-4 text-slate-400 group-hover:text-slate-600 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Alerts -->
            <?php include __DIR__ . '/../components/alerts.php'; ?>

            <!-- Page Content -->
            <main class="flex-1">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
                    <?= $content ?? '' ?>
                </div>
            </main>

            <!-- Footer (optional) -->
            <footer class="bg-white border-t border-slate-200 mt-auto">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                    <p class="text-center text-sm text-slate-500">
                        © <?= date('Y') ?> Helpdesk System. All rights reserved.
                    </p>
                </div>
            </footer>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('mobile-sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
                // Prevent body scroll when sidebar is open
                document.body.style.overflow = 'hidden';
            } else {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }

        // Auto-dismiss alerts
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('[data-alert]');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-1rem)';
                    alert.style.transition = 'all 0.3s ease-in-out';
                    setTimeout(() => alert.remove(), 300);
                }, 5000);
            });
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('mobile-sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            
            if (!sidebar.classList.contains('-translate-x-full') && 
                !sidebar.contains(event.target) && 
                !event.target.closest('[onclick="toggleSidebar()"]')) {
                toggleSidebar();
            }
        });
    </script>
    
    <script type="module" src="/assets/js/app.js"></script>
</body>
</html>