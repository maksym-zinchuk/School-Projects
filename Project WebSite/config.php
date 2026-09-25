<?php
declare(strict_types=1);

const DATA_DIR = __DIR__ . '/data';
const PROJECTS_DIR = __DIR__ . '/uploads/projects';
const ZIPS_DIR = __DIR__ . '/uploads/zips';
const DB_FILE = DATA_DIR . '/database.sqlite';

const ADMIN_USER = 'admin';
// Change this password before putting the site online.
const ADMIN_PASSWORD = '16.07.08.Maksym';

date_default_timezone_set('Europe/Warsaw');

// Some hosts (e.g. InfinityFree) apply a restrictive umask to PHP, so files
// and folders we create can end up unreadable by the webserver (403
// Forbidden). Forcing the umask here makes mkdir()/fopen() honor the modes
// we actually pass in.
umask(0022);

function ensure_dirs(): void {
    foreach ([DATA_DIR, PROJECTS_DIR, ZIPS_DIR] as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        @chmod($dir, 0755);
    }
}

// Recursively force safe, webserver-readable permissions on an extracted
// project (directories 0755, files 0644), regardless of what umask/ZIP
// entry permissions produced.
function recursive_chmod(string $path, int $dirMode = 0755, int $fileMode = 0644): void {
    if (is_dir($path)) {
        @chmod($path, $dirMode);
        $items = scandir($path);
        foreach ($items ?: [] as $item) {
            if ($item === '.' || $item === '..') continue;
            recursive_chmod($path . '/' . $item, $dirMode, $fileMode);
        }
    } elseif (is_file($path)) {
        @chmod($path, $fileMode);
    }
}

function db(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;
    ensure_dirs();
    $pdo = new PDO('sqlite:' . DB_FILE);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec('PRAGMA foreign_keys = ON');
    $pdo->exec('CREATE TABLE IF NOT EXISTS projects (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        number INTEGER NOT NULL UNIQUE,
        title TEXT NOT NULL,
        created_at TEXT NOT NULL,
        project_path TEXT NOT NULL,
        zip_path TEXT NOT NULL
    )');
    return $pdo;
}

function is_logged_in(): bool {
    return !empty($_SESSION['logged_in']);
}

function require_login(): void {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function h(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function format_file_size(int $bytes): string {
    if ($bytes <= 0) return '—';
    $units = ['B', 'KB', 'MB', 'GB'];
    $i = 0;
    $size = (float)$bytes;
    while ($size >= 1024 && $i < count($units) - 1) {
        $size /= 1024;
        $i++;
    }
    $decimals = ($i === 0) ? 0 : 1;
    return str_replace('.', ',', number_format($size, $decimals)) . ' ' . $units[$i];
}

function csrf_token(): string {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function check_csrf(): void {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        http_response_code(400);
        exit('Nieprawidłowy token CSRF.');
    }
}

function safe_filename(string $name): string {
    $name = preg_replace('/[^\p{L}\p{N}\._-]+/u', '_', $name) ?? 'file';
    return trim($name, '._') ?: 'file';
}

function unique_dir_name(string $name): string {
    $name = safe_filename($name);
    return $name !== '' ? $name : 'project';
}

function find_index_file(string $baseDir, ?string $preferredName = null): ?string {
    $preferred = ['index.html', 'index.htm', 'index.php'];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($baseDir, FilesystemIterator::SKIP_DOTS)
    );

    $found = [];
    foreach ($iterator as $file) {
        if (!$file->isFile()) continue;
        $relative = str_replace('\\', '/', substr($file->getPathname(), strlen($baseDir) + 1));
        $found[strtolower(basename($relative))][] = $relative;
    }

    if ($preferredName !== null && $preferredName !== '') {
        $key = strtolower(basename($preferredName));
        if (!empty($found[$key])) {
            usort($found[$key], fn($a, $b) => substr_count($a, '/') <=> substr_count($b, '/'));
            return $found[$key][0];
        }
        return null;
    }

    foreach ($preferred as $name) {
        if (!empty($found[$name])) {
            usort($found[$name], fn($a, $b) => substr_count($a, '/') <=> substr_count($b, '/'));
            return $found[$name][0];
        }
    }
    return null;
}

// Lists every html/htm/php file inside an already-extracted project, for
// picking a start page after the fact (edit.php).
function list_html_like_files(string $baseDir): array {
    if (!is_dir($baseDir)) return [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($baseDir, FilesystemIterator::SKIP_DOTS)
    );
    $out = [];
    foreach ($iterator as $file) {
        if (!$file->isFile()) continue;
        if (!preg_match('/\.(html?|php)$/i', $file->getFilename())) continue;
        $out[] = str_replace('\\', '/', substr($file->getPathname(), strlen($baseDir) + 1));
    }
    sort($out);
    return $out;
}

function unzip_safely(string $zipFile, string $destination): void {
    $zip = new ZipArchive();
    if ($zip->open($zipFile) !== true) {
        throw new RuntimeException('Nie udało się otworzyć archiwum ZIP.');
    }

    $destinationReal = realpath($destination);
    if ($destinationReal === false) {
        throw new RuntimeException('Folder docelowy jest niedostępny.');
    }

    for ($i = 0; $i < $zip->numFiles; $i++) {
        $name = $zip->getNameIndex($i);
        if ($name === false) continue;
        $name = str_replace('\\', '/', $name);

        if ($name === '' || str_starts_with($name, '/') || preg_match('~(^|/)\.\.(/|$)~', $name)) {
            throw new RuntimeException('ZIP zawiera niebezpieczną ścieżkę.');
        }

        $target = $destination . '/' . $name;
        $parent = dirname($target);
        if (!is_dir($parent)) mkdir($parent, 0755, true);

        if (str_ends_with($name, '/')) {
            if (!is_dir($target)) mkdir($target, 0755, true);
            continue;
        }

        $stream = $zip->getStream($name);
        if (!$stream) throw new RuntimeException('Nie udało się odczytać pliku z ZIP.');

        $out = fopen($target, 'wb');
        if (!$out) throw new RuntimeException('Nie udało się zapisać pliku projektu.');
        stream_copy_to_stream($stream, $out);
        fclose($out);
        fclose($stream);
        @chmod($target, 0644);
    }
    $zip->close();

    // Belt-and-braces: make sure every folder/file we just extracted is
    // actually readable by the webserver, no matter what the host's umask
    // or the ZIP's stored permissions were.
    recursive_chmod($destination);
}

function recursive_remove(string $path): void {
    if (!file_exists($path)) return;
    if (is_file($path) || is_link($path)) { unlink($path); return; }
    $items = scandir($path);
    foreach ($items ?: [] as $item) {
        if ($item === '.' || $item === '..') continue;
        recursive_remove($path . '/' . $item);
    }
    rmdir($path);
}
