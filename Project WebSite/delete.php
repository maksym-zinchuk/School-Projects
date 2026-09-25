<?php
declare(strict_types=1);
session_start();
require __DIR__ . '/config.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}
check_csrf();

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if ($id) {
    $pdo = db();
    $stmt = $pdo->prepare('SELECT * FROM projects WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $number = (int)$row['number'];
        recursive_remove(PROJECTS_DIR . '/' . $number);
        recursive_remove(ZIPS_DIR . '/' . $number);
        $del = $pdo->prepare('DELETE FROM projects WHERE id = ?');
        $del->execute([$id]);
        $_SESSION['flash'] = 'Praca nr ' . $number . ' została usunięta.';
    }
}

header('Location: index.php');
exit;
