<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>We'll be back soon &middot; WeddingYour</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= asset('css/app.css') ?>">
<style>
body{margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center;background:#F9F8F4;font-family:'Montserrat',sans-serif;color:#1F2937;padding:20px}
.m-card{text-align:center;max-width:520px;padding:48px 40px}
.m-icon{width:96px;height:96px;margin:0 auto 32px;background:#F0EAE3;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:40px;color:#B8845A}
h1{font-family:'Playfair Display',serif;font-size:36px;font-weight:700;margin:0 0 12px;color:#1F2937}
p{font-size:15px;line-height:1.7;color:#6B7280;margin:0 0 32px}
.pulse{animation:pulse 2s ease-in-out infinite}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.5}}
</style>
</head>
<body>
<div class="m-card">
<div class="m-icon pulse"><i class="fa-solid fa-screwdriver-wrench"></i></div>
<h1>We'll Be Back Soon</h1>
<p>Our website is currently undergoing scheduled maintenance. We apologize for the inconvenience and will be back online shortly.</p>
<div class="w-16 h-0.5 bg-premium-champagne mx-auto mb-6"></div>
<p class="text-xs text-gray-400">&mdash; WeddingYour Team</p>
</div>
</body>
</html>
