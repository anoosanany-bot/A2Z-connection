<?php
header('Content-Type: text/html; charset=UTF-8');
mb_internal_encoding('UTF-8');
$query = trim($_GET['q'] ?? '');
if ($query === '') {
    echo '<p class="empty">اكتب كلمة أو اسم للبحث.</p>';
    exit;
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
$mysqli = require __DIR__ . '/db_config.php';
$searchTerm = '%' . $query . '%';
$stmt = $mysqli->prepare(
    'SELECT id, title, content, url FROM search_items WHERE title LIKE ? OR content LIKE ? ORDER BY title ASC LIMIT 50'
);
if (!$stmt) {
    echo '<p class="empty">فشل إعداد الاستعلام: ' . escapeText($mysqli->error) . '</p>';
    exit;
}
$stmt->bind_param('ss', $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();
if (!$result || $result->num_rows === 0) {
    echo '<p class="empty">لا توجد نتائج مطابقة.</p>';
    exit;
}
while ($row = $result->fetch_assoc()) {
    $url = $row['url'] ?: '#';
    $snippet = trim(mb_substr($row['content'], 0, 200));
    echo '<article class="result-item">';
    echo '<a href="' . escapeText($url) . '" target="_blank">' . highlightQuery($row['title'], $query) . '</a>';
    echo '<div class="result-meta">' . escapeText($url) . '</div>';
    if ($snippet !== '') {
        echo '<p class="snippet">' . highlightQuery($snippet, $query) . '</p>';
    }
    echo '</article>';
}
$stmt->close();
$mysqli->close();
