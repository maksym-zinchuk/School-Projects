<?php
declare(strict_types=1);
session_start();
require __DIR__ . '/config.php';
$rows = db()->query('SELECT * FROM projects ORDER BY number DESC')->fetchAll(PDO::FETCH_ASSOC);
$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);
$loggedIn = is_logged_in();
$colCount = $loggedIn ? 7 : 6;
?>
<!doctype html>
<html lang="pl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Projekty szkolne</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Caveat:wght@600;700&family=Public+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="topbar">
  <div class="brand">
    <svg class="brand-mark" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
      <path d="M4 6h11a3 3 0 0 1 3 3v9H7a3 3 0 0 1-3-3V6Z"/>
      <path d="M4 6a3 3 0 0 1 3-3h7"/>
      <line x1="8" y1="10" x2="14" y2="10"/>
      <line x1="8" y1="14" x2="14" y2="14"/>
    </svg>
    <span>Projekty szkolne</span>
  </div>
  <nav>
    <?php if ($loggedIn): ?>
      <a class="btn primary" href="upload.php">+ Dodaj pracę</a>
      <a class="btn" href="logout.php">Wyloguj się</a>
    <?php else: ?>
      <a class="btn primary" href="login.php">Zaloguj się</a>
    <?php endif; ?>
  </nav>
</header>
<main class="container">
  <section class="hero">
    <div>
      <h1>Moje prace</h1>
      <p>Tutaj przechowywane są projekty szkolne oraz archiwa ZIP z kodem.</p>
    </div>
    <div class="count"><span class="count-num"><?= count($rows) ?></span><span class="count-label">prac</span></div>
  </section>

  <?php if ($flash): ?><div class="success"><?= h($flash) ?></div><?php endif; ?>

  <section class="card">
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>№</th><th>Nazwa</th><th>Data</th><th>Projekt</th><th>ZIP</th><th>Rozmiar</th>
            <?php if ($loggedIn): ?><th>Akcje</th><?php endif; ?>
          </tr>
        </thead>
        <tbody>
        <?php if (!$rows): ?>
          <tr><td colspan="<?= $colCount ?>" class="empty">Nie ma jeszcze żadnych przesłanych prac.</td></tr>
        <?php else: foreach ($rows as $row): ?>
          <?php
            $zipFull = __DIR__ . '/' . $row['zip_path'];
            $zipSize = is_file($zipFull) ? filesize($zipFull) : 0;
          ?>
          <tr>
            <td class="number"><?= (int)$row['number'] ?></td>
            <td><strong><?= h($row['title']) ?></strong></td>
            <td><?= h(date('d.m.Y H:i', strtotime($row['created_at']))) ?></td>
            <td><a class="link" href="project.php?id=<?= (int)$row['id'] ?>" target="_blank">Otwórz projekt ↗</a></td>
            <td><a class="link" href="download.php?id=<?= (int)$row['id'] ?>">Pobierz ZIP ↓</a></td>
            <td class="size"><?= h(format_file_size((int)$zipSize)) ?></td>
            <?php if ($loggedIn): ?>
            <td>
              <div class="actions-cell">
                <a class="btn" href="edit.php?id=<?= (int)$row['id'] ?>">Edytuj</a>
                <form method="post" action="delete.php" class="delete-form" onsubmit="return confirm('Na pewno usunąć tę pracę? Tej operacji nie da się cofnąć.');">
                  <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
                  <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
                  <button type="submit" class="btn danger">Usuń</button>
                </form>
              </div>
            </td>
            <?php endif; ?>
          </tr>
        <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </section>
</main>
</body>
</html>
