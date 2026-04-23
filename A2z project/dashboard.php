<?php
include 'db_config.php';
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
$searchRoot = __DIR__ . '/search';
$query = trim($_GET['q'] ?? '');
$results = [];
$message = '';
if (!isset($_SESSION['history'])) {
    $_SESSION['history'] = [];
}
if ($query !== '') {
    $_SESSION['history'][] = [
        'action' => 'search',
        'query' => $query,
        'time' => date('Y-m-d H:i:s'),
    ];
    if (!is_dir($searchRoot)) {
        $message = 'Ù…Ø¬Ù„Ø¯ Ø§Ù„Ø¨Ø­Ø« ØºÙŠØ± Ù…ÙˆØ¬ÙˆØ¯. Ø£Ù†Ø´Ø¦ Ù…Ø¬Ù„Ø¯ search ÙˆØ£Ø¶Ù Ù…Ù„ÙØ§Øª.';
    } else {
        $directory = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($searchRoot, FilesystemIterator::SKIP_DOTS)
        );
        $allowedExt = ['txt', 'html', 'htm', 'md', 'json', 'php', 'js', 'css', 'xml', 'csv'];
        foreach ($directory as $file) {
            if (!$file->isFile()) {
                continue;
            }
            $ext = strtolower($file->getExtension());
            if (!in_array($ext, $allowedExt, true)) {
                continue;
            }
            $filename = $file->getFilename();
            $relativePath = str_replace('\\', '/', substr($file->getPathname(), strlen($searchRoot) + 1));
            $content = file_get_contents($file->getPathname());
            $matchName = mb_stripos($filename, $query) !== false;
            $matchContent = mb_stripos($content, $query) !== false;
            if ($matchName || $matchContent) {
                $snippet = '';
                if ($matchContent) {
                    $pos = mb_stripos($content, $query);
                    $start = max(0, $pos - 40);
                    $length = min(220, mb_strlen($content) - $start);
                    $snippet = mb_substr($content, $start, $length);
                    if ($start > 0) {
                        $snippet = '...' . $snippet;
                    }
                    if ($start + $length < mb_strlen($content)) {
                        $snippet .= '...';
                    }
                }
                $results[] = [
                    'title' => $filename,
                    'path' => $relativePath,
                    'snippet' => $snippet,
                ];
            }
        }
    }
}
function escapeText($text) {
    return htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function highlightQuery($text, $query) {
    $escaped = preg_quote($query, '/');
    return preg_replace_callback('/(' . $escaped . ')/iu', function ($match) {
        return '<mark>' . htmlspecialchars($match[1], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</mark>';
    }, escapeText($text));
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ø¯Ø§Ø´Ø¨ÙˆØ±Ø¯ A2Z</title>
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            background: linear-gradient(180deg, #0b8a53, #0f5d3a);
            color: #f8fafc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 32px;
            background: rgba(0, 0, 0, 0.25);
            backdrop-filter: blur(14px);
        }
        .brand {
            font-size: 1.2rem;
            font-weight: 800;
            letter-spacing: 0.12em;
        }
        .header-links {
            display: flex;
            gap: 14px;
        }
        .header-links a {
            padding: 12px 18px;
            border-radius: 999px;
            background: rgba(255,255,255,0.16);
            color: white;
            text-decoration: none;
            font-weight: 700;
        }
        .content {
            max-width: 1100px;
            margin: 36px auto 60px;
            padding: 0 24px;
        }
        .hero {
            display: grid;
            gap: 26px;
        }
        .hero-title {
            margin: 0;
            font-size: clamp(2.2rem, 4vw, 3.6rem);
            line-height: 1.05;
        }
        .hero-text {
            max-width: 760px;
            color: rgba(248,250,252,0.88);
            font-size: 1.05rem;
            line-height: 1.8;
        }
        .search-panel {
            margin-top: 26px;
            display: grid;
            gap: 14px;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.18);
            border-radius: 28px;
            padding: 28px 30px;
        }
        .search-panel input {
            width: 100%;
            padding: 16px 20px;
            border-radius: 16px;
            border: 1px solid rgba(255,255,255,0.25);
            background: rgba(255,255,255,0.12);
            color: white;
            font-size: 1rem;
            outline: none;
        }
        .search-panel button {
            width: fit-content;
            padding: 14px 26px;
            border: none;
            color: #0f172a;
            border-radius: 16px;
            background: #f8fafc;
            font-weight: 700;
            cursor: pointer;
        }
        .search-panel button:hover {
            background: #e2e8f0;
        }
        .results {
            display: grid;
            gap: 16px;
            margin-top: 22px;
        }
        .result-card {
            padding: 20px 22px;
            border-radius: 18px;
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.18);
        }
        .result-card a {
            color: #d9f99d;
            font-weight: 700;
            text-decoration: none;
            font-size: 1.08rem;
        }
        .result-meta {
            margin-top: 6px;
            color: rgba(248,250,252,0.72);
            font-size: 0.95rem;
        }
        .snippet {
            margin: 12px 0 0;
            color: rgba(248,250,252,0.9);
            line-height: 1.7;
        }
        .history {
            margin-top: 30px;
            display: grid;
            gap: 10px;
            color: rgba(248,250,252,0.9);
        }
        .history-item {
            padding: 16px 20px;
            border-radius: 16px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.12);
        }
        .empty-state {
            margin-top: 18px;
            color: rgba(248,250,252,0.86);
        }
        mark {
            background: #fef08a;
            color: #0f172a;
            border-radius: 6px;
            padding: 0 4px;
        }
    </style>
</head>
<body>
    <header>
        <div class="brand">Ù„ÙˆØ­Ø© ØªØ­ÙƒÙ… Ø§Ù„Ù…Ø³ØªØ®Ø¯Ù…</div>
        <div class="header-links">
            <a href="dashboard.php">Ø§Ù„Ø¯Ø§Ø´Ø¨ÙˆØ±Ø¯</a>
            <a href="logout.php">Logout</a>
            <a href="creatnewaccount.php">Create Account</a>
        </div>
    </header>
    <main class="content">
        <section class="hero">
            <h1 class="hero-title">Ø£Ù‡Ù„Ø§Ù‹ Ø¨ÙƒØŒ <?= htmlspecialchars($_SESSION['user'], ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="hero-text">Ù‡Ø°Ø§ Ø§Ù„Ø¯Ø§Ø´Ø¨ÙˆØ±Ø¯ Ù…Ø®ØµØµ Ù„ÙƒÙ„ Ù…Ø³ØªØ®Ø¯Ù… ÙÙŠ Ø§Ù„Ø¬Ù„Ø³Ø©. Ø§Ù„Ø¨Ø­Ø« Ù‡Ù†Ø§ ÙŠØ¹Ù…Ù„ Ø¹Ù„Ù‰ Ù…Ù„ÙØ§Øª Ø§Ù„Ù…Ø´Ø±ÙˆØ¹ØŒ ÙˆØ³Ø¬Ù„ Ø§Ù„Ø¹Ù…Ù„ÙŠØ§Øª ÙŠØ®Ø²Ù† ØªÙ„Ù‚Ø§Ø¦ÙŠØ§Ù‹ ÙÙŠ Ø§Ù„Ø³ÙŠØ´Ù† Ø¯ÙˆÙ† Ø§Ù„Ø­Ø§Ø¬Ø© Ø¥Ù„Ù‰ Ù‚Ø§Ø¹Ø¯Ø© Ø¨ÙŠØ§Ù†Ø§Øª.</p>
        </section>
        <section class="search-panel">
            <form method="get" action="dashboard.php">
                <input type="search" name="q" placeholder="Ø§ÙƒØªØ¨ ÙƒÙ„Ù…Ø© Ù„Ù„Ø¨Ø­Ø« Ø¯Ø§Ø®Ù„ Ø§Ù„Ù…Ù„ÙØ§Øª..." value="<?= htmlspecialchars($query, ENT_QUOTES, 'UTF-8') ?>" required>
                <button type="submit">Ø¨Ø­Ø«</button>
            </form>
            <?php if ($message): ?>
                <div class="empty-state"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
            <?php if ($query !== ''): ?>
                <?php if (empty($results) && !$message): ?>
                    <div class="empty-state">Ù„Ø§ ØªÙˆØ¬Ø¯ Ù†ØªØ§Ø¦Ø¬ Ù…Ø·Ø§Ø¨Ù‚Ø© Ù„Ù„Ø¨Ø­Ø«.</div>
                <?php endif; ?>
            <?php endif; ?>
            <?php if (!empty($results)): ?>
                <div class="results">
                    <?php foreach ($results as $item): ?>
                        <article class="result-card">
                            <a href="search/<?= escapeText($item['path']) ?>" target="_blank"><?= highlightQuery($item['title'], $query) ?></a>
                            <div class="result-meta"><?= escapeText($item['path']) ?></div>
                            <?php if ($item['snippet'] !== ''): ?>
                                <p class="snippet"><?= highlightQuery($item['snippet'], $query) ?></p>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
        <section class="history">
            <h2>Ø³Ø¬Ù„ Ø§Ù„Ø£Ù†Ø´Ø·Ø© ÙÙŠ Ø§Ù„Ø¬Ù„Ø³Ø©</h2>
            <?php if (!empty($_SESSION['history'])): ?>
                <?php foreach (array_reverse($_SESSION['history']) as $event): ?>
                    <div class="history-item">
                        <?= escapeText($event['time']) ?> - <?= escapeText($event['action']) ?>
                        <?php if (!empty($event['query'])): ?>
                            : <?= escapeText($event['query']) ?>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="history-item">Ù„Ø§ ØªÙˆØ¬Ø¯ Ø£Ù†Ø´Ø·Ø© Ø­ØªÙ‰ Ø§Ù„Ø¢Ù†.</div>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
