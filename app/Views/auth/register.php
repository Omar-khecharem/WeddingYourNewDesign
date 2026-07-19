<?php
/**
 * Register Page
 */
?>
<div class="min-h-[70vh] flex items-center justify-center py-12 px-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-lg border border-gray-200 p-8">
        <div class="text-center mb-8">
            <div class="w-16 h-16 rounded-full bg-premium-burgundy text-white text-2xl flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-user-plus"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">Create Account</h1>
            <p class="text-gray-500 text-sm mt-1">Join <?= e($appName ?? 'WeddingYour') ?> today</p>
        </div>

        <form method="POST" action="<?= url('register') ?>" class="space-y-4">
            <?= \App\Helpers\Security::csrfField() ?>
            
            <?= error() ?>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Full Name</label>
                <input type="text" name="name" value="<?= old('name') ?>" required class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-premium-burgundy transition-all">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email Address</label>
                <input type="email" name="email" value="<?= old('email') ?>" required class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-premium-burgundy transition-all">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Phone Number</label>
                <input type="tel" name="phone" value="<?= old('phone') ?>" class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-premium-burgundy transition-all">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Password</label>
                <input type="password" name="password" required minlength="8" class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-premium-burgundy transition-all">
                <p class="text-xs text-gray-400 mt-1">Minimum 8 characters</p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Confirm Password</label>
                <input type="password" name="password_confirmation" required class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-premium-burgundy transition-all">
            </div>

            <button type="submit" class="w-full bg-premium-burgundy text-white font-bold py-3.5 rounded-xl hover:opacity-90 transition-all shadow-lg shadow-premium-burgundy/20">
                Create Account <i class="fa-solid fa-user-plus ml-1.5"></i>
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-sm text-gray-500">Already have an account? <a href="<?= url('login') ?>" class="text-premium-burgundy font-semibold hover:underline">Sign in</a></p>
        </div>
    </div>
</div>
