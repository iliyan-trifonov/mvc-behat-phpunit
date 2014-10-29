<?php

$dir = __DIR__ . '/src/public/';
$file = $dir . $_SERVER['REQUEST_URI'];
//TODO: add allowed file ext
if (is_dir($file) || !file_exists($file)) {
    include $dir . '/index.php';
} else {
    return false;
}
