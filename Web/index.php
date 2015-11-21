<?php
session_set_cookie_params(7200);
session_start();

define('DS', DIRECTORY_SEPARATOR);

require_once '../autoload.php';



require __DIR__ . '/../Waddle/Classes/App.php';
require __DIR__ . '/../Waddle/config.php';

use Waddle\Classes\App;

try {
    $app = new App($config);
    $app->run();
} catch (\Exception $e) {
    echo '<h1>PHP Error: '.$e->getMessage().'</h1><p>'.$e->getTraceAsString().'</p>';
}

//session_destroy();
?>