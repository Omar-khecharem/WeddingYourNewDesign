<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — <?= APP_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; overflow: hidden; }
        .bg-grid { background-image: radial-gradient(rgba(255,255,255,0.04) 1px, transparent 1px); background-size: 32px 32px; }
        @keyframes float { 0%,100%{transform:translateY(0) scale(1)} 50%{transform:translateY(-20px) scale(1.02)} }
        @keyframes float2 { 0%,100%{transform:translateY(0) scale(1)} 50%{transform:translateY(-12px) scale(1.01)} }
        @keyframes pulse-glow { 0%,100%{opacity:0.4} 50%{opacity:0.8} }
        .orb-1 { animation: float 8s ease-in-out infinite; }
        .orb-2 { animation: float2 6s ease-in-out infinite 1s; }
        .orb-3 { animation: pulse-glow 4s ease-in-out infinite; }
        @keyframes slideUp { 0%{opacity:0;transform:translateY(24px) scale(0.97)} 100%{opacity:1;transform:translateY(0) scale(1)} }
        .card-enter { animation: slideUp 0.7s cubic-bezier(0.16,1,0.3,1) both; }
        .card-enter:nth-child(1) { animation-delay: 0.1s; }
        .card-enter:nth-child(2) { animation-delay: 0.2s; }
        .card-enter:nth-child(3) { animation-delay: 0.3s; }
        @keyframes shimmer { 0%{background-position:-200% 0} 100%{background-position:200% 0} }
        .shimmer-text { background: linear-gradient(90deg,#c084fc,#a78bfa,#c084fc); background-size: 200% 100%; -webkit-background-clip: text; -webkit-text-fill-color: transparent; animation: shimmer 4s ease-in-out infinite; }
        @keyframes border-glow { 0%,100%{border-color:rgba(139,92,246,0.15)} 50%{border-color:rgba(139,92,246,0.35)} }
        .glow-border { animation: border-glow 3s ease-in-out infinite; }
        @media (prefers-reduced-motion: reduce) { * { animation-duration: 0.01ms !important; animation-iteration-count: 1 !important; transition-duration: 0.01ms !important; } }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 relative overflow-hidden" style="background:#0f0d1a">

    <!-- Animated Background -->
    <div class="fixed inset-0 bg-grid opacity-60"></div>
    <div class="fixed inset-0" style="background:radial-gradient(ellipse 80% 60% at 50% -10%,rgba(139,92,246,0.12),transparent);"></div>
    <div class="fixed inset-0" style="background:radial-gradient(ellipse 60% 50% at 80% 100%,rgba(139,92,246,0.06),transparent);"></div>

    <!-- Floating Orbs -->
    <div class="orb-1 fixed w-[500px] h-[500px] rounded-full pointer-events-none opacity-20" style="top:-15%;left:-10%;background:radial-gradient(circle,rgba(139,92,246,0.3),transparent 70%)"></div>
    <div class="orb-2 fixed w-[400px] h-[400px] rounded-full pointer-events-none opacity-15" style="bottom:-10%;right:-5%;background:radial-gradient(circle,rgba(192,132,252,0.25),transparent 70%)"></div>
    <div class="orb-3 fixed w-[600px] h-[600px] rounded-full pointer-events-none" style="top:50%;left:50%;transform:translate(-50%,-50%);background:radial-gradient(circle,rgba(139,92,246,0.06),transparent 60%)"></div>

    <?= flashMessage() ?>

    <div class="w-full max-w-[420px] relative z-10 card-enter">

        <!-- Brand -->
        <div class="text-center mb-8 card-enter">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-purple-500 to-purple-700 shadow-lg shadow-purple-500/25 mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
            </div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Admin Panel</h1>
            <p class="text-purple-300/40 text-sm mt-1.5 font-medium">Restricted access — administrators only</p>
        </div>

        <!-- Card -->
        <div class="card-enter" style="background:rgba(255,255,255,0.03);backdrop-filter:blur(24px);-webkit-backdrop-filter:blur(24px);border:1px solid rgba(255,255,255,0.06);border-radius:20px;box-shadow:0 0 40px rgba(139,92,246,0.06),0 8px 32px rgba(0,0,0,0.3);">
            <div class="p-7 sm:p-8">
                <?php $old = \App\Helpers\Session::get('old', []); ?>
                <form method="POST" action="<?= url('13091998') ?>" class="space-y-5">
                    <?= \App\Helpers\Security::csrfField() ?>

                    <!-- Email -->
                    <div>
                        <label class="block text-xs font-semibold tracking-wide text-purple-200/70 mb-2 uppercase">Email</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-purple-300/30 group-focus-within:text-purple-400/60 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                            </div>
                            <input type="email" name="email" value="<?= e($old['email'] ?? '') ?>" required autocomplete="off" placeholder="admin@weddingyour.com" class="w-full bg-white/5 border border-white/10 rounded-xl pl-10 pr-4 py-3 text-sm text-white placeholder-purple-300/20 focus:outline-none focus:border-purple-500/40 focus:bg-white/[0.07] transition-all duration-200">
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-xs font-semibold tracking-wide text-purple-200/70 mb-2 uppercase">Password</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-purple-300/30 group-focus-within:text-purple-400/60 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                            </div>
                            <input type="password" name="password" required placeholder="••••••••" class="w-full bg-white/5 border border-white/10 rounded-xl pl-10 pr-4 py-3 text-sm text-white placeholder-purple-300/20 focus:outline-none focus:border-purple-500/40 focus:bg-white/[0.07] transition-all duration-200">
                        </div>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-purple-500 hover:from-purple-500 hover:to-purple-400 text-white font-bold py-3 rounded-xl text-sm transition-all duration-200 shadow-lg shadow-purple-500/20 hover:shadow-xl hover:shadow-purple-500/30 hover:-translate-y-0.5 active:translate-y-0">
                        <span class="flex items-center justify-center gap-2">
                            Access Panel
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                        </span>
                    </button>
                </form>
            </div>

            <!-- Footer -->
            <div class="px-7 sm:px-8 pb-6 pt-0">
                <div class="border-t border-white/[0.04] pt-4 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-emerald-400/60 orb-3"></div>
                        <span class="text-[11px] text-purple-300/30 font-medium">Secured connection</span>
                    </div>
                    <a href="<?= url('') ?>" class="text-[11px] text-purple-400/30 hover:text-purple-300/60 transition-colors font-medium">&larr; Back to site</a>
                </div>
            </div>
        </div>

        <!-- Version -->
        <p class="text-center mt-6 text-[10px] text-purple-300/15 tracking-wider uppercase font-medium"><?= APP_NAME ?> v2.0 &middot; Administrative Interface</p>
    </div>

    <script>
    (function(){
        var inputs = document.querySelectorAll('input');
        inputs.forEach(function(input) {
            input.addEventListener('focus', function() {
                this.closest('.relative').querySelector('svg').classList.add('text-purple-400/60');
            });
            input.addEventListener('blur', function() {
                this.closest('.relative').querySelector('svg').classList.remove('text-purple-400/60');
            });
        });
        var card = document.querySelector('[style*="backdrop-filter"]');
        if (card) {
            document.addEventListener('mousemove', function(e) {
                var rect = card.getBoundingClientRect();
                var x = (e.clientX - rect.left) / rect.width - 0.5;
                var y = (e.clientY - rect.top) / rect.height - 0.5;
                card.style.transform = 'perspective(800px) rotateY(' + (x * 2) + 'deg) rotateX(' + (-y * 2) + 'deg)';
            });
            card.addEventListener('mouseleave', function() {
                card.style.transform = 'perspective(800px) rotateY(0deg) rotateX(0deg)';
                card.style.transition = 'transform 0.5s ease';
                setTimeout(function() { card.style.transition = ''; }, 500);
            });
        }
    })();
    </script>
</body>
</html>
