<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | <?= APP_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .login-bg { background: linear-gradient(135deg, #1e1b2e 0%, #2d1f3d 50%, #1a1a2e 100%); }
        .glow { box-shadow: 0 0 40px rgba(139, 92, 246, 0.15), 0 0 80px rgba(139, 92, 246, 0.05); }
        .input-glow:focus { box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.2); }
    </style>
</head>
<body class="login-bg min-h-screen flex items-center justify-center p-4">
    <?= flashMessage() ?>
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-extrabold text-white tracking-tight">Admin</h1>
            <p class="text-purple-300/60 text-sm mt-2">Restricted access — administrators only</p>
        </div>
        <div class="bg-white/5 backdrop-blur-xl rounded-2xl p-8 glow border border-white/10">
            <?php $old = \App\Helpers\Session::get('old', []); ?>
            <form method="POST" action="<?= url('13091998') ?>" class="space-y-5">
                <?= \App\Helpers\Security::csrfField() ?>
                <div>
                    <label class="block text-sm font-medium text-purple-200 mb-1.5">Email</label>
                    <input type="email" name="email" value="<?= e($old['email'] ?? '') ?>" required autocomplete="off" class="input-glow w-full bg-white/10 border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-purple-300/30 focus:outline-none focus:border-purple-500/50 transition-all" placeholder="admin@weddingyour.com">
                </div>
                <div>
                    <label class="block text-sm font-medium text-purple-200 mb-1.5">Password</label>
                    <input type="password" name="password" required class="input-glow w-full bg-white/10 border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-purple-300/30 focus:outline-none focus:border-purple-500/50 transition-all" placeholder="••••••••">
                </div>
                <button type="submit" class="w-full bg-purple-600 hover:bg-purple-500 text-white font-bold py-3 px-4 rounded-xl text-sm transition-all duration-200 hover:shadow-lg hover:shadow-purple-500/25">
                    Access Admin Panel
                </button>
            </form>
        </div>
        <div class="text-center mt-6">
            <a href="<?= url('') ?>" class="text-purple-400/50 hover:text-purple-300 text-xs transition-colors">&larr; Back to site</a>
        </div>
    </div>
</body>
</html>
