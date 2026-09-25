# Projekty szkolne — PHP

## Strona projektu
- URL: https://zinchukmaksym.infinityfreeapp.com

## Wymagania
- PHP 8.1+
- rozszerzenie PDO_SQLite
- rozszerzenie ZIP
- serwer WWW Apache/Nginx
- uprawnienia do zapisu dla `data/` i `uploads/`

## Instalacja
1. Wgraj wszystkie pliki na hosting.
2. Upewnij się, że PHP może zapisywać do `data/` i `uploads/`.
3. Otwórz stronę — baza SQLite utworzy się automatycznie.
4. W `config.php` zmień `ADMIN_PASSWORD`.
5. Domyślny login: `admin`.

## Jak dodać pracę
W panelu administracyjnym przesyłane są dwa pliki ZIP:
- ZIP projektu — w środku musi znajdować się `index.html`, `index.htm` lub `index.php`;
- ZIP z plikiem ZIP.

Strona automatycznie:
- nadaje kolejny numer;
- ustala nazwę na podstawie pierwszego folderu w ZIP (jeśli istnieje);
- ustawia aktualną datę i godzinę;
- znajduje plik indeksowy;
- zapisuje ZIP i tworzy przycisk pobierania.

## Ograniczenia przesyłania
Na hostingu mogą obowiązywać ustawienia `upload_max_filesize` i `post_max_size` w PHP. Jeśli plik ZIP jest duży, zwiększ te wartości w `php.ini`/ustawieniach hostingu.

## Bezpieczeństwo
Przed publikacją koniecznie zmień hasło administratora w `config.php`.
