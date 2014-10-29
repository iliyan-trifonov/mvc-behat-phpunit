<?php

//ob_start();//session testing

$current = dirname(__DIR__);

//packagist libs
require_once $current . '/vendor/autoload.php';

//app loaders
if (!defined("BASE_PATH")) {
    define("BASE_PATH", $current . "/src/app/");
}
require_once BASE_PATH . '/autoload.php';
require_once __DIR__ . '/BaseTestClass.php';
