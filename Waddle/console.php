<?php

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

function c($msq, $code) {
    return chr(27) . "[" . $code . "m" . $msq . chr(27) . "[0m";
}

use Waddle\Classes\DbConnection;

$args = getopt('cuh', ['create-database', 'update-schema']);

if (isset($args['h']) || (count($args) == 0)) {
    echo "\t\t" . c("***Engine Console Manager***", 42) . PHP_EOL;
    echo "Available commands:\n";
    echo "\t-c, --create-database\t Creates database according to the configuration.\n";
    echo "\t-u, --update-schema\t Updates current schema according to the ORM mappings in Models directory.\n";
    echo "\t-h \t\t\t Shows this help message.\n";

    exit(0);
}

include_once 'config.php';
/** @var array $config */

if (isset($args['c']) || isset($args['create-database'])) {
    $providerClass = $config['db']['provider'];
    $providerClass[0] = strtoupper($providerClass[0]);
    $providerClass = 'Waddle\Classes\\' . $providerClass . 'Connection';
    /** @var DbConnection $provider */
    $provider = new $providerClass(
        $config['db']['host'],
        $config['db']['user'],
        $config['db']['password']
    );
    $r = @$provider->createDatabase($config['db']['name']);
    if ($r === true) {
        echo c("Database '" . $config['db']['name'] . "' has been created!", 32) . PHP_EOL;
    } else {
        echo c($r, 31) . PHP_EOL;
    }
    exit(0);
}

if (isset($args['u']) || isset($args['update-schema'])) {
    $providerClass = $config['db']['provider'];
    $providerClass[0] = strtoupper($providerClass[0]);
    $providerClass = 'Waddle\Classes\\' . $providerClass . 'Connection';
    /** @var DbConnection $provider */
    $provider = new $providerClass(
        $config['db']['host'],
        $config['db']['user'],
        $config['db']['password']
    );


}
