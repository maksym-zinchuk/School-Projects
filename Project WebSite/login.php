<?php
declare(strict_types=1);
session_start();
require __DIR__ . '/config.php';

if (is_logged_in()) { header('Location: upload.php'); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';
    if (hash_equals(ADMIN_USER, $user) && hash_equals(ADMIN_PASSWORD, $pass)) {
        session_regenerate_id(true);
        $_SESSION['logged_in'] = true;
        header('Location: upload.php');
        exit;
    }
    $error = 'Nieprawidłowy login lub hasło.';
}
?>
<!doctype html>
<html lang="pl"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Logowanie</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Caveat:wght@600;700&family=Public+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css"></head>
<body>
<main class="auth">
  <form class="card auth-card" method="post">
    <a class="back" href="index.php">← Strona główna</a>
    <h1>Logowanie</h1>
    <p class="muted">Zaloguj się, aby dodawać prace.</p>
    <?php if ($error): ?><div class="error"><?= h($error) ?></div><?php endif; ?>
    <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
    <label>Login<input name="username" autocomplete="username" required></label>
    <label>Hasło<input type="password" name="password" autocomplete="current-password" required></label>
    <button class="btn primary wide">Zaloguj się</button>
  </form>
</main>
</body></html>
