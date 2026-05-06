<?php
session_start();

require 'config.php';
require 'models.php';
require 'controllers.php';

$page = $_GET['page'] ?? 'login';

if ($page === 'logout') {
    $_SESSION = [];
    session_destroy();
    setcookie('remember_user', '', time() - 3600, '/');
    header('Location: index.php?page=login');
    exit;
}

if ($page === 'ajax') {
    header('Content-Type: application/json');
    if (!isset($_SESSION['user'])) {
        http_response_code(403);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }
    $type = $_GET['type'] ?? '';
    $q    = trim($_GET['q'] ?? '');

    if ($type === 'game' && $_SESSION['user']['role'] === 'admin') {
        echo json_encode($q === '' ? getGames($conn) : searchGames($conn, $q));
    } elseif ($type === 'level' && $_SESSION['user']['role'] === 'admin') {
        echo json_encode($q === '' ? getLevels($conn) : searchLevels($conn, $q));
    } elseif ($type === 'score' && $_SESSION['user']['role'] === 'player') {
        echo json_encode($q === '' ? getPlayerScores($conn, $_SESSION['user']['id'])
                                   : searchPlayerScores($conn, $_SESSION['user']['id'], $q));
    } else {
        http_response_code(403);
        echo json_encode(['error' => 'Forbidden']);
    }
    exit;
}

$publicPages = ['login', 'register'];

if (in_array($page, $publicPages) && isset($_SESSION['user'])) {
    header('Location: index.php?page=' . $_SESSION['user']['role']);
    exit;
}

if (!in_array($page, $publicPages) && !isset($_SESSION['user'])) {
    header('Location: index.php?page=login');
    exit;
}


if ($page === 'admin'  && $_SESSION['user']['role'] !== 'admin')   { header('Location: index.php?page=login'); exit; }
if ($page === 'player' && $_SESSION['user']['role'] !== 'player')  { header('Location: index.php?page=login'); exit; }

switch ($page) {
    case 'login':    loginCtrl($conn);    break;
    case 'register': registerCtrl($conn); break;
    case 'admin':    adminCtrl($conn);    break;
    case 'player':   playerCtrl($conn);   break;
    default:
        header('Location: index.php?page=login');
        exit;
}

mysqli_close($conn);
?>
