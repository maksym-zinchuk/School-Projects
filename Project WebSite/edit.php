<?php
declare(strict_types=1);
session_start();
require __DIR__ . '/config.php';
require_login();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) { http_response_code(404); exit('Nie znaleziono.'); }

$pdo = db();
$stmt = $pdo->prepare('SELECT * FROM projects WHERE id = ?');
$stmt->execute([$id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$row) { http_response_code(404); exit('Nie znaleziono.'); }

$filesDir = PROJECTS_DIR . '/' . (int)$row['number'] . '/files';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    try {
        $title = trim((string)($_POST['title'] ?? ''));
        $startFile = trim((string)($_POST['start_file'] ?? ''));
        if ($title === '') throw new RuntimeException('Nazwa nie może być pusta.');
        if ($startFile === '') throw new RuntimeException('Podaj plik startowy.');

        $filesDirReal = realpath($filesDir);
        $candidate = realpath($filesDir . '/' . $startFile);
        if ($filesDirReal === false || $candidate === false || !str_starts_with($candidate, $filesDirReal . DIRECTORY_SEPARATOR) || !is_file($candidate)) {
            throw new RuntimeException('Nie znaleziono pliku „' . $startFile . '” w tym projekcie.');
        }

        $relative = 'files/' . str_replace('\\', '/', substr($candidate, strlen($filesDirReal) + 1));
        $upd = $pdo->prepare('UPDATE projects SET title = ?, project_path = ? WHERE id = ?');
        $upd->execute([$title, $relative, $id]);

        $row['title'] = $title;
        $row['project_path'] = $relative;
        $success = 'Zapisano zmiany.';
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

$currentStart = str_starts_with($row['project_path'], 'files/') ? substr($row['project_path'], 6) : $row['project_path'];
$options = list_html_like_files($filesDir);
?>
<!doctype html>
<html lang="pl"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Edytuj pracę</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Caveat:wght@600;700&family=Public+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css"></head>
<body>
<header class="topbar"><div class="brand"><svg class="brand-mark" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 6h11a3 3 0 0 1 3 3v9H7a3 3 0 0 1-3-3V6Z"/><path d="M4 6a3 3 0 0 1 3-3h7"/><line x1="8" y1="10" x2="14" y2="10"/><line x1="8" y1="14" x2="14" y2="14"/></svg><span>Projekty szkolne</span></div><nav><a class="btn" href="index.php">← Tabela</a><a class="btn" href="logout.php">Wyloguj się</a></nav></header>
<main class="container narrow">
<section class="card form-card">
<h1>Edytuj pracę nr <?= (int)$row['number'] ?></h1>
<p class="muted">Zmień nazwę lub wskaż, który plik ma się otwierać pod „Otwórz projekt”.</p>
<?php if ($error): ?><div class="error"><?= h($error) ?></div><?php endif; ?>
<?php if ($success): ?><div class="success"><?= h($success) ?> <a href="index.php">Wróć do tabeli</a></div><?php endif; ?>
<form method="post">
<input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
<input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
<label>Nazwa
  <input type="text" name="title" value="<?= h($row['title']) ?>" required>
</label>
<label>Plik startowy
  <input type="text" name="start_file" list="start-files" value="<?= h($currentStart) ?>" required>
  <datalist id="start-files">
    <?php foreach ($options as $opt): ?><option value="<?= h($opt) ?>"><?php endforeach; ?>
  </datalist>
  <small>Zacznij pisać, żeby zobaczyć podpowiedzi z plików wewnątrz tego projektu (np. restauracja.html).</small>
</label>
<button class="btn primary wide">Zapisz zmiany</button>
</form>
</section>
</main>
</body></html>
