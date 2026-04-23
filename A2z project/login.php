<?php
session_start();
if (isset($_SESSION['user'])) {
    header('Location: dashboard.php');
    exit;
}
$loginError = '';
$accounts = $_SESSION['accounts'] ?? ['user' => '1234'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['username'] ?? '');
    $pass = trim($_POST['password'] ?? '');
    if ($user !== '' && isset($accounts[$user]) && $accounts[$user] === $pass) {
        $_SESSION['user'] = $user;
        $_SESSION['history'][] = [
            'action' => 'login',
            'user' => $user,
            'time' => date('Y-m-d H:i:s'),
        ];
        header('Location: dashboard.php');
        exit;
    }
    $loginError = 'اسم المستخدم أو كلمة المرور غير صحيحة.';
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - A2Z</title>
    <style>
        :root {
            color-scheme: light;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #1f2937;
            background: #eef2ff;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: radial-gradient(circle at top left, rgba(99,102,241,0.24), transparent 35%),
                        linear-gradient(180deg, #eef2ff 0%, #f8fafc 100%);
        }
        .browser-shell {
            width: min(100%, 580px);
            border-radius: 30px;
            background: rgba(255,255,255,0.88);
            box-shadow: 0 28px 60px rgba(15,23,42,0.14);
            overflow: hidden;
            border: 1px solid rgba(148,163,184,0.16);
        }
        .browser-bar {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 20px;
            background: rgba(248,250,252,0.95);
            border-bottom: 1px solid rgba(148,163,184,0.16);
        }
        .browser-dots {
            display: flex;
            gap: 8px;
        }
        .browser-dots span {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }
        .dot-red { background: #f87171; }
        .dot-yellow { background: #fbbf24; }
        .dot-green { background: #34d399; }
        .address {
            flex: 1;
            color: #475569;
            font-size: 0.95rem;
            opacity: 0.9;
            background: rgba(241,245,249,0.9);
            border-radius: 999px;
            padding: 9px 16px;
        }
        .auth-card {
            padding: 32px 30px 40px;
        }
        .auth-card h1 {
            margin: 0 0 10px;
            font-size: clamp(2rem, 2.5vw, 2.3rem);
            color: #0f172a;
        }
        .auth-card p {
            margin: 0 0 24px;
            line-height: 1.8;
            color: #475569;
        }
        .field {
            display: grid;
            gap: 10px;
            margin-bottom: 20px;
        }
        label {
            display: block;
            font-size: 0.95rem;
            color: #334155;
        }
        input {
            width: 100%;
            padding: 16px 14px;
            border-radius: 16px;
            border: 1px solid rgba(148,163,184,0.3);
            background: #f8fafc;
            font-size: 1rem;
            color: #0f172a;
        }
        input:focus {
            outline: none;
            border-color: #818cf8;
            box-shadow: 0 0 0 4px rgba(99,102,241,0.12);
        }
        button {
            width: 100%;
            margin-top: 4px;
            padding: 16px 20px;
            border: none;
            border-radius: 16px;
            background: linear-gradient(135deg, #4f46e5, #6366f1);
            color: white;
            font-size: 1rem;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        button:hover {
            transform: translateY(-1px);
            box-shadow: 0 18px 30px rgba(99,102,241,0.18);
        }
        .alert {
            padding: 16px;
            border-radius: 18px;
            margin-bottom: 18px;
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        .alert.success {
            background: #dcfce7;
            color: #166534;
            border-color: #bbf7d0;
        }
        .hint {
            margin-top: 18px;
            font-size: 0.95rem;
            color: #94a3b8;
        }
        .footer-link {
            display: block;
            margin-top: 18px;
            text-align: center;
            color: #4f46e5;
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="browser-shell">
        <div class="browser-bar">
            <div class="browser-dots">
                <span class="dot-red"></span>
                <span class="dot-yellow"></span>
                <span class="dot-green"></span>
            </div>
            <div class="address">a2z.local/login</div>
        </div>
        <div class="auth-card">
            <h1>ØªØ³Ø¬ÙŠÙ„ Ø§Ù„Ø¯Ø®ÙˆÙ„</h1>
            <p>ÙˆØ§Ø¬Ù‡Ø© Ù†Ø¸ÙŠÙØ© ÙˆÙ…Ø±ÙŠØ­Ø© ØªØ´Ø¨Ù‡ Ù†Ø§ÙØ°Ø© Ù…ØªØµÙØ­ ÙØ¹Ù„ÙŠØ©ØŒ Ù…Ø¹ Ø¯Ø¹Ù… ÙƒØ§Ù…Ù„ Ù„Ù„ØºØ© Ø§Ù„Ø¹Ø±Ø¨ÙŠØ© ÙˆØ§Ù„Ù†Øµ Ø§Ù„Ù…ØªØ¹Ø¯Ø¯ Ø§Ù„Ù„ØºØ§Øª.</p>
            <?php if ($logged): ?>
                <div class="alert success">ØªÙ… ØªØ³Ø¬ÙŠÙ„ Ø§Ù„Ø¯Ø®ÙˆÙ„ Ø¨Ù†Ø¬Ø§Ø­ Ø¨Ø§Ø³Ù… <?= htmlspecialchars($_SESSION['user'], ENT_QUOTES, 'UTF-8') ?></div>
                <a class="footer-link" href="index.html">Ø§Ù„Ø¹ÙˆØ¯Ø© Ø¥Ù„Ù‰ Ø§Ù„ØµÙØ­Ø© Ø§Ù„Ø±Ø¦ÙŠØ³ÙŠØ©</a>
            <?php else: ?>
                <?php if ($loginError): ?>
                    <div class="alert"><?= htmlspecialchars($loginError, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
                <form method="post" autocomplete="off">
                    <div class="field">
                        <label for="username">Ø§Ø³Ù… Ø§Ù„Ù…Ø³ØªØ®Ø¯Ù…</label>
                        <input id="username" name="username" type="text" autocomplete="username" required>
                    </div>
                    <div class="field">
                        <label for="password">ÙƒÙ„Ù…Ø© Ø§Ù„Ù…Ø±ÙˆØ±</label>
                        <input id="password" name="password" type="password" autocomplete="current-password" required>
                    </div>
                    <button type="submit">Ø¯Ø®ÙˆÙ„</button>
                </form>
                <p class="hint">ØªØ¬Ø±Ø¨Ø© Ø³Ø±ÙŠØ¹Ø©: user / 1234</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
