<div class="min-h-[70vh] flex items-center justify-center py-12 px-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-lg border border-gray-200 p-8">
        <div class="text-center mb-8">
            <div class="w-16 h-16 rounded-full bg-premium-burgundy text-white text-2xl flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-lock"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">Forgot Password</h1>
            <p class="text-gray-500 text-sm mt-1">Enter your email and we'll notify the admin</p>
        </div>

        <form method="POST" action="<?= url('forgot-password') ?>" class="space-y-4">
            <?= \App\Helpers\Security::csrfField() ?>
            
            <?= error() ?>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email Address</label>
                <input type="email" name="email" value="<?= old('email') ?>" required class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-premium-burgundy focus:ring-1 focus:ring-premium-burgundy transition-all" placeholder="your@email.com">
            </div>

            <button type="submit" class="w-full bg-premium-burgundy text-white font-bold py-3.5 rounded-xl hover:opacity-90 transition-all shadow-lg shadow-premium-burgundy/20">
                Send Request <i class="fa-solid fa-paper-plane ml-1.5"></i>
            </button>
        </form>

        <div class="mt-6 text-center space-y-2">
            <p class="text-sm text-gray-500">Remember your password? <a href="<?= url('login') ?>" class="text-premium-burgundy font-semibold hover:underline">Sign in</a></p>
        </div>
    </div>
</div>
