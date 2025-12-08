<?php
use App\Helpers\Security;

$title = 'Login - Helpdesk System';
ob_start();
?>

<div>
    <h2 class="text-3xl font-bold text-slate-900 mb-2">Welcome back</h2>
    <p class="text-slate-600 mb-8">Sign in to your account to continue</p>

    <form method="POST" action="/login" class="space-y-6">
        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 mb-2">
                Email address
            </label>
            <input 
                type="email" 
                name="email" 
                id="email" 
                required
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
                placeholder="Enter your password"
            >
        </div>

        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <input 
                    type="checkbox" 
                    id="remember" 
                    name="remember"
                    class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-slate-300 rounded"
                >
                <label for="remember" class="ml-2 block text-sm text-slate-700">
                    Remember me
                </label>
            </div>
        </div>

        <button 
            type="submit"
            class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-medium py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center"
        >
            <span>Sign in</span>
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
                <span class="px-2 bg-slate-50 text-slate-500">Don't have an account?</span>
            </div>
        </div>

        <div class="mt-6">
            <a 
                href="/register"
                class="w-full inline-flex justify-center items-center px-4 py-3 border border-slate-300 rounded-lg text-slate-700 bg-white hover:bg-slate-50 font-medium transition duration-200"
            >
                Create new account
            </a>
        </div>
    </div>

    <div class="mt-8 p-4 bg-slate-100 rounded-lg border border-slate-200">
        <p class="text-xs text-slate-600 font-medium mb-2">Demo Credentials:</p>
        <div class="space-y-1 text-xs text-slate-500">
            <p><span class="font-medium">Admin:</span> admin@helpdesk.com / admin123</p>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/guest.php';
?>