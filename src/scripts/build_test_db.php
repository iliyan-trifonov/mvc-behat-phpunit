<?php

$path = dirname(__DIR__);

$config = (object) parse_ini_file($path . '/app/config/config.ini');
//TODO: config class to populate default values
if (!isset($config->dbname) || $config->dbname == '') {
    $config->dbname = 'test';
}

//$connection = new SQLite3($path . '/data/test.db');
$connection = new PDO("sqlite:" . $path . "/data/".$config->dbname.".sq3");
$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$connection->exec(
    " DROP TABLE IF EXISTS `users`;
      DROP TABLE IF EXISTS `notepads`;
      CREATE TABLE `users` (
        `id` INTEGER PRIMARY KEY AUTOINCREMENT,
        `username` TEXT NOT NULL,
        `password` TEXT NOT NULL);
      INSERT INTO `users` (`username`,`password`) VALUES ('testu1','testp1');
      INSERT INTO `users` (`username`,`password`) VALUES ('testu2','testp2');
      CREATE TABLE `notepads` (
        `id` INTEGER PRIMARY KEY AUTOINCREMENT,
        `userid` INTEGER NOT NULL,
        `name` TEXT NULL,
        `text` TEXT NULL);
      INSERT INTO `notepads` (`userid`,`name`,`text`)
        VALUES ('1','test notepad1', 'test text1');
      INSERT INTO `notepads` (`userid`,`name`,`text`)
        VALUES ('1','test notepad2', 'test text2');
    "
);
