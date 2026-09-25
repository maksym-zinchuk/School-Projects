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

$file = __DIR__ . '/' . $row['zip_path'];
$realBase = realpath(ZIPS_DIR);
$realFile = realpath($file);
if (!$realFile || !$realBase || !str_starts_with($realFile, $realBase . DIRECTORY_SEPARATOR) || !is_file($realFile)) {
    http_response_code(404); exit('Plik nie został znaleziony.');
}
header('Content-Type: application/zip');
header('Content-Length: ' . filesize($realFile));
header('Content-Disposition: attachment; filename="' . basename($realFile) . '"');
readfile($realFile);
exit;
