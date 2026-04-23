<?php
session_start();
$accounts = $_SESSION['accounts'] ?? ['user' => '1234'];
$feedback = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['username'] ?? '');
    $pass = trim($_POST['password'] ?? '');
    $confirm = trim($_POST['confirm_password'] ?? '');
    if ($name === '' || $pass === '' || $confirm === '') {
        $error = 'Ø±Ø¬Ø§Ø¡Ù‹ Ø§Ù…Ù„Ø£ Ø¬Ù…ÙŠØ¹ Ø§Ù„Ø­Ù‚ÙˆÙ„.';
    } elseif ($pass !== $confirm) {
        $error = 'ÙƒÙ„Ù…Ø© Ø§Ù„Ù…Ø±ÙˆØ± ÙˆØ§Ù„ØªØ£ÙƒÙŠØ¯ ØºÙŠØ± Ù…ØªØ·Ø§Ø¨Ù‚ÙŠÙ†.';
    } elseif (isset($accounts[$name])) {
        $error = 'Ø§Ø³Ù… Ø§Ù„Ù…Ø³ØªØ®Ø¯Ù… Ù…ÙˆØ¬ÙˆØ¯ Ø¨Ø§Ù„ÙØ¹Ù„.';
    } else {
        $accounts[$name] = $pass;
        $_SESSION['accounts'] = $accounts;
        $_SESSION['history'][] = [
            'action' => 'create_account',
            'user' => $name,
            'time' => date('Y-m-d H:i:s'),
        ];
        $feedback = 'ØªÙ… Ø¥Ù†Ø´Ø§Ø¡ Ø§Ù„Ø­Ø³Ø§Ø¨ Ù…Ø¤Ù‚ØªØ§Ù‹ ÙÙŠ Ø§Ù„Ø³ÙŠØ´Ù†. Ø§Ù„Ø¢Ù† ÙŠÙ…ÙƒÙ†Ùƒ ØªØ³Ø¬ÙŠÙ„ Ø§Ù„Ø¯Ø®ÙˆÙ„.';
    }
}
$_SESSION['accounts'] = $accounts;
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ø¥Ù†Ø´Ø§Ø¡ Ø­Ø³Ø§Ø¨ - A2Z</title>
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            background: radial-gradient(circle at top right, rgba(0,255,106,0.24), transparent 30%), linear-gradient(180deg, #0f4f2e 0%, #02280f 100%);
            color: #eef2ff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 22px;
        }
        .card {
            width: min(540px, 100%);
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.14);
            border-radius: 28px;
            padding: 32px;
            backdrop-filter: blur(15px);
        }
        h1 {
            margin: 0 0 18px;
            font-size: 2.4rem;
        }
        p {
            margin: 0 0 24px;
            color: rgba(238,242,255,0.8);
            line-height: 1.7;
        }
        .field {
            margin-bottom: 18px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 700;
        }
        input {
            width: 100%;
            padding: 14px 16px;
            border-radius: 14px;
            border: 1px solid rgba(255,255,255,0.16);
            background: rgba(255,255,255,0.08);
            color: white;
            font-size: 1rem;
        }
        button {
            width: 100%;
            padding: 16px 18px;
            border: none;
            border-radius: 16px;
            color: #0f172a;
            font-weight: 700;
            background: #bef264;
            cursor: pointer;
        }
        button:hover {
            opacity: 0.95;
        }
        .alert {
            margin-bottom: 18px;
            padding: 16px;
            border-radius: 16px;
        }
        .error {
            background: #fca5a5;
            color: #7f1d1d;
        }
        .success {
            background: #dcfce7;
            color: #166534;
        }
        .link-row {
            margin-top: 18px;
            display: flex;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }
        .link-row a {
            color: #bef264;
            text-decoration: none;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>Ø¥Ù†Ø´Ø§Ø¡ Ø­Ø³Ø§Ø¨ Ø¬Ø¯ÙŠØ¯</h1>
        <p>Ø³ÙŠØªÙ… Ø­ÙØ¸ Ø§Ù„Ø­Ø³Ø§Ø¨ Ø§Ù„Ø¬Ø¯ÙŠØ¯ ÙÙŠ Ø§Ù„Ø³ÙŠØ´Ù† ÙÙ‚Ø· Ø­ØªÙ‰ Ø§Ù„Ø¢Ù†. Ø§Ù„ØªØ³Ø¬ÙŠÙ„ Ø¯Ø§Ø®Ù„ Ù‚Ø§Ø¹Ø¯Ø© Ø§Ù„Ø¨ÙŠØ§Ù†Ø§Øª Ø³ÙŠØªÙ… Ù„Ø§Ø­Ù‚Ø§Ù‹.</p>
        <?php if ($error): ?>
            <div class="alert error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <?php if ($feedback): ?>
            <div class="alert success"><?= htmlspecialchars($feedback, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <form method="post" autocomplete="off">
            <div class="field">
                <label for="username">Ø§Ø³Ù… Ø§Ù„Ù…Ø³ØªØ®Ø¯Ù…</label>
                <input id="username" name="username" type="text" required>
            </div>
            <div class="field">
                <label for="password">ÙƒÙ„Ù…Ø© Ø§Ù„Ù…Ø±ÙˆØ±</label>
                <input id="password" name="password" type="password" required>
            </div>
            <div class="field">
                <label for="confirm_password">ØªØ£ÙƒÙŠØ¯ ÙƒÙ„Ù…Ø© Ø§Ù„Ù…Ø±ÙˆØ±</label>
                <input id="confirm_password" name="confirm_password" type="password" required>
            </div>
            <button type="submit">Ø¥Ù†Ø´Ø§Ø¡ Ø§Ù„Ø­Ø³Ø§Ø¨</button>
        </form>
        <div class="link-row">
            <a href="login.php">Ø§Ù„Ø¹ÙˆØ¯Ø© Ù„ØªØ³Ø¬ÙŠÙ„ Ø§Ù„Ø¯Ø®ÙˆÙ„</a>
            <a href="dashboard.php">Ø§Ù„Ø¹ÙˆØ¯Ø© Ù„Ù„Ø¯Ø§Ø´Ø¨ÙˆØ±Ø¯</a>
        </div>
    </div>
</body>
</html>
