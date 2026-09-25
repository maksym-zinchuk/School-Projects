<?php
declare(strict_types=1);
session_start();
require __DIR__ . '/config.php';
require_login();

$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();

    $project = $_FILES['project'] ?? null;
    $zip = $_FILES['zip'] ?? null;

    try {
        if (!$project || $project['error'] !== UPLOAD_ERR_OK) throw new RuntimeException('Wybierz ZIP projektu.');
        if (!$zip || $zip['error'] !== UPLOAD_ERR_OK) throw new RuntimeException('Wybierz ZIP z plikiem ZIP.');
        if (strtolower(pathinfo($project['name'], PATHINFO_EXTENSION)) !== 'zip') throw new RuntimeException('Projekt musi być archiwum ZIP.');
        if (strtolower(pathinfo($zip['name'], PATHINFO_EXTENSION)) !== 'zip') throw new RuntimeException('Plik ZIP musi być archiwum ZIP.');

        $pdo = db();
        $next = (int)$pdo->query('SELECT COALESCE(MAX(number),0)+1 FROM projects')->fetchColumn();

        $workDir = PROJECTS_DIR . '/' . $next;
        $extractDir = $workDir . '/files';
        $zipDir = ZIPS_DIR . '/' . $next;
        if (!mkdir($extractDir, 0755, true) || !mkdir($zipDir, 0755, true)) {
            throw new RuntimeException('Nie udało się utworzyć folderów do przesyłania.');
        }
        // Some hosts create these with an overly strict mode despite the
        // 0755 above (umask), which leads to 403 Forbidden when opening the
        // project later. Force it explicitly.
        @chmod($workDir, 0755);
        @chmod($extractDir, 0755);
        @chmod($zipDir, 0755);

        $projectTemp = $project['tmp_name'];
        unzip_safely($projectTemp, $extractDir);

        $startFile = trim((string)($_POST['start_file'] ?? ''));
        $index = find_index_file($extractDir, $startFile !== '' ? $startFile : null);
        if ($index === null) {
            recursive_remove($workDir);
            recursive_remove($zipDir);
            if ($startFile !== '') {
                throw new RuntimeException('Nie znaleziono pliku „' . $startFile . '” w przesłanym archiwum.');
            }
            throw new RuntimeException('W ZIP-ie projektu nie znaleziono pliku index.html, index.htm ani index.php. Podaj nazwę pliku startowego ręcznie poniżej.');
        }

        $parts = explode('/', str_replace('\\', '/', $index));
        $title = $parts[0] ?? '';
        if ($title === '' || in_array(strtolower($title), ['index.html','index.htm','index.php'], true)) {
            $title = pathinfo($project['name'], PATHINFO_FILENAME);
        }
        $title = str_replace(['_', '-'], ' ', $title);
        $title = trim($title) ?: 'Projekt ' . $next;

        $zipName = safe_filename($zip['name']);
        $zipPath = $zipDir . '/' . $zipName;
        if (!move_uploaded_file($zip['tmp_name'], $zipPath)) {
            recursive_remove($workDir);
            recursive_remove($zipDir);
            throw new RuntimeException('Nie udało się zapisać pliku ZIP.');
        }
        @chmod($zipPath, 0644);

        $relativeIndex = 'files/' . str_replace('\\', '/', $index);
        $stmt = $pdo->prepare('INSERT INTO projects (number,title,created_at,project_path,zip_path) VALUES (?,?,?,?,?)');
        $stmt->execute([$next, $title, date('Y-m-d H:i:s'), $relativeIndex, 'uploads/zips/' . $next . '/' . $zipName]);

        $success = 'Praca nr ' . $next . ' została pomyślnie dodana.';
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}
?>
<!doctype html>
<html lang="pl"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Dodaj pracę</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Caveat:wght@600;700&family=Public+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css"></head>
<body>
<header class="topbar"><div class="brand"><svg class="brand-mark" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 6h11a3 3 0 0 1 3 3v9H7a3 3 0 0 1-3-3V6Z"/><path d="M4 6a3 3 0 0 1 3-3h7"/><line x1="8" y1="10" x2="14" y2="10"/><line x1="8" y1="14" x2="14" y2="14"/></svg><span>Projekty szkolne</span></div><nav><a class="btn" href="index.php">← Tabela</a><a class="btn" href="logout.php">Wyloguj się</a></nav></header>
<main class="container narrow">
<section class="card form-card">
<h1>Dodaj pracę</h1>
<p class="muted">Prześlij dwa pliki ZIP. Numer, nazwę, datę i linki strona utworzy automatycznie.</p>
<?php if ($error): ?><div class="error"><?= h($error) ?></div><?php endif; ?>
<?php if ($success): ?><div class="success"><?= h($success) ?> <a href="index.php">Otwórz tabelę</a></div><?php endif; ?>
<form method="post" enctype="multipart/form-data">
<input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
<label>ZIP z projektem
  <input type="file" name="project" accept=".zip,application/zip" required>
  <small>W środku musi znajdować się index.html, index.htm lub index.php.</small>
</label>
<label>Plik startowy (opcjonalnie)
  <input type="text" name="start_file" value="<?= h($_POST['start_file'] ?? '') ?>" placeholder="np. restauracja.html">
  <small>Zostaw puste, jeśli masz index.html/index.htm/index.php. Jeśli strona startowa nazywa się inaczej, wpisz tu jej dokładną nazwę.</small>
</label>
<label>ZIP z plikiem ZIP
  <input type="file" name="zip" accept=".zip,application/zip" required>
</label>
<button class="btn primary wide">Prześlij pracę</button>
</form>
</section>
</main>
</body></html>
