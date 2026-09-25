<?php
declare(strict_types=1);
session_start();
require __DIR__ . '/config.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) { http_response_code(404); exit('Nie znaleziono.'); }
$stmt = db()->prepare('SELECT * FROM projects WHERE id = ?');
$stmt->execute([$id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$row) { http_response_code(404); exit('Nie znaleziono.'); }

$target = __DIR__ . '/uploads/projects/' . (int)$row['number'] . '/' . $row['project_path'];
$realTarget = realpath($target);
$base = realpath(PROJECTS_DIR . '/' . (int)$row['number']);
if (!$realTarget || !$base || !str_starts_with($realTarget, $base . DIRECTORY_SEPARATOR) || !is_file($realTarget)) {
    http_response_code(404); exit('Nie znaleziono pliku indeksowego projektu.');
}

$ext = strtolower(pathinfo($realTarget, PATHINFO_EXTENSION));
if ($ext === 'php') {
    // Internal redirect so PHP projects execute normally.
    $relative = str_replace('\\', '/', substr($realTarget, strlen(__DIR__) + 1));
    header('Location: ' . $relative);
    exit;
}
header('Location: ' . str_replace('\\', '/', substr($realTarget, strlen(__DIR__) + 1)));
exit;
