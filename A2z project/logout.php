<?php
session_start();
if (isset($_SESSION['user'])) {
    if (!isset($_SESSION['history'])) {
        $_SESSION['history'] = [];
    }
    $_SESSION['history'][] = [
        'action' => 'logout',
        'user' => $_SESSION['user'],
        'time' => date('Y-m-d H:i:s'),
    ];
}
session_unset();
session_destroy();
header('Location: index.html');
exit;
