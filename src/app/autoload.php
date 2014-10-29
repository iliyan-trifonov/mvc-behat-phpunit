<?php

//autoloader PSR-0
/*spl_autoload_register(function ($className) {
    foreach (array("controllers", "models", "lib") as $type) {
        $file = BASE_PATH . "/" . $type . "/" . $className . ".php";
        if (file_exists($file)) {
            echo "autoloaded file = $file<br/>\n";
            include $file;
            break;
        }
    }
});*/

//autoloader PSR-4 Single Namespace
spl_autoload_register(function ($class) {
    $prefix = "Notepads\\";
    $base_dir = __DIR__ . "/";

    $len = strlen($prefix);

    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);

    $relative_class = str_replace(
        array("Lib\\", "Models\\", "Controllers\\"),
        array("lib\\", "models\\", "controllers\\"),
        $relative_class
    );

    $file = $base_dir . str_replace("\\", "/", $relative_class) . ".php";

    if (file_exists($file)) {
        require $file;
    }
});
