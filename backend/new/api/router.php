<?php
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requested_file = __DIR__ . $uri;

if (file_exists($requested_file) && is_file($requested_file)) {
    return false;
} else {
    include_once __DIR__ . '/index.php';
}