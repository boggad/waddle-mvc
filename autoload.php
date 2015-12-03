<?php

spl_autoload_register(function ($class) {

    $prefix = 'Waddle';
    $base_dir = __DIR__ . DIRECTORY_SEPARATOR . $prefix;

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);

    $file = $base_dir . str_replace('\\', DIRECTORY_SEPARATOR, $relative_class) . '.php';
    if (file_exists($file)) {
        /** @noinspection PhpIncludeInspection */
        require_once $file;
    }
});

spl_autoload_register(function($class) {
    $path = __DIR__.DIRECTORY_SEPARATOR.$class.'.php';
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $path);
    if (file_exists($path))
        require_once $path;
});

require_once 'vendor/autoload.php';
