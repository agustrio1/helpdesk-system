<?php
use App\Helpers\Security;

$title = 'Register - Helpdesk System';
$oldInput = $_SESSION['old_input'] ?? [];
unset($_SESSION['old_input']);
ob_start();
?>

<div>
    <h2 class="text-3xl font-bold text-slate-900 mb-2">Create account</h2>
    <p class="text-slate-600 mb-8">Get started with your helpdesk account</p>

    <form method="POST" action="/register" class="space-y-6">
        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

        <div>
            <label for="name" class="block text-sm font-medium text-slate-700 mb-2">
                Full name
            </label>
            <input 
                type="text" 
                name="name" 
                id="name" 
                required
                value="<?= Security::escape($oldInput['name'] ?? '') ?>"
                class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                placeholder="John Doe"
            >
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 mb-2">
                Email address
            </label>
            <input 
                type="email" 
                name="email" 
                id="email" 
                required
                value="<?= Security::escape($oldInput['email'] ?? '') ?>"
                class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                placeholder="you@example.com"
            >
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-slate-700 mb-2">
                Password
            </label>
            <input 
                type="password" 
                name="password" 
                id="password" 
                required
                class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                placeholder="Minimum 6 characters"
            >
            <p class="mt-1 text-xs text-slate-500">Must be at least 6 characters long</p>
        </div>

        <div>
            <label for="confirm_password" class="block text-sm font-medium text-slate-700 mb-2">
                Confirm password
            </label>
            <input 
                type="password" 
                name="confirm_password" 
                id="confirm_password" 
                required
                class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                placeholder="Re-enter your password"
            >
        </div>

        <button 
            type="submit"
            class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-medium py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center"
        >
            <span>Create account</span>
            <svg class="ml-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
            </svg>
        </button>
    </form>

    <div class="mt-6">
        <div class="relative">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-slate-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-2 bg-slate-50 text-slate-500">Already have an account?</span>
            </div>
        </div>

        <div class="mt-6">
            <a 
                href="/login"
                class="w-full inline-flex justify-center items-center px-4 py-3 border border-slate-300 rounded-lg text-slate-700 bg-white hover:bg-slate-50 font-medium transition duration-200"
            >
                Sign in instead
            </a>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/guest.php';
?>