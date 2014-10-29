<?php
/**
 * @codingStandardsIgnoreFile
 */

//index.php is the bootstrap file

//app's root
define("BASE_PATH", dirname(__DIR__) . '/app/');

//config
$config = (object) parse_ini_file(BASE_PATH . '/config/config.ini');
//TODO: config class to populate default values
if (!isset($config->dbname) || $config->dbname == '') {
    $config->dbname = 'test';
}

//autoloader
require BASE_PATH . '/autoload.php';

//database
$connection = new PDO("sqlite:" . BASE_PATH . "/../data/".$config->dbname.".sq3");
$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//router
$router = new Notepads\Lib\Router($_SERVER["REQUEST_URI"]);
$router->parseUrl();

//request
$request = new Notepads\Lib\Request($_GET, $_POST, $_SERVER);

//services factory
$serviceFactory = new Notepads\Models\ServiceFactory($connection);

//view
$view = new Notepads\Lib\Template(BASE_PATH . '/views/', $router->controller);

//execute
$class = "Notepads\\Controllers\\" . $router->controller;
$controller = new $class($request, $serviceFactory, $view, $router);//DI
call_user_func_array(array($controller, $router->action), $router->params);
