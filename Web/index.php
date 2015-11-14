<?php
session_set_cookie_params(7200);
session_start();

if (strtolower(substr(PHP_OS, 0, 3)) == 'win') {
    define('DS', '\\');
} else {
    define('DS', '/');
}


function sl($path) {
    $p = str_replace('/', DS, $path);
    $p = str_replace('\\', DS, $p);
    return $p;
}

spl_autoload_register(function($class) {
    $path = __DIR__.'/../'.$class.'.php';
    $path = \sl($path);
    if (file_exists($path))
        require_once $path;
});

require __DIR__ . '/../Engine/Classes/App.php';
require __DIR__ . '/../Engine/config.php';

use Engine\Classes\App;

try {
    $app = new App($config);
    $app->run();
} catch (\Exception $e) {
    echo '<h1>PHP Error: '.$e->getMessage().'</h1><p>'.$e->getTraceAsString().'</p>';
}

//session_destroy();
?>