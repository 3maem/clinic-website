<?php
// Router for PHP built-in server: emulates Apache mod_rewrite for WordPress pretty permalinks.
$root = __DIR__ . '/site';
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = $root . $path;
if ($path !== '/' && file_exists($file) && !is_dir($file)) {
    return false; // serve static files / direct PHP files as-is
}
if (is_dir($file) && file_exists(rtrim($file, '/') . '/index.php')) {
    $_SERVER['SCRIPT_NAME'] = rtrim($path, '/') . '/index.php';
    require rtrim($file, '/') . '/index.php';
    return true;
}
$_SERVER['SCRIPT_NAME'] = '/index.php';
require $root . '/index.php';
