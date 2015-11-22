<?php
/**
 * Created by PhpStorm.
 * User: kgb
 * Date: 22.11.15
 * Time: 15:42
 */

namespace Src\Controllers;


use Waddle\Classes\App;

class DefaultControllerTest extends \PHPUnit_Framework_TestCase  {

    public function testHelloWorld() {
        $config = array(
            'default_controller' => 'default',
            'site_title' => '',
            'admin_login' => '',
            'admin_hash' => '',
            /*'db' => array(
                'provider' => 'mysql',
                'host' => 'localhost',
                'user' => 'root',
                'password' => '',
                'name' => 'database'
            )*/ // TODO: Needs to add lazy init for DdProvider!
        );
        $app = new App($config);
        $controller = new DefaultController($app);
        $this->expectOutputRegex('/<h1>Hello, World!<\/h1>/');
        $controller->indexAction('World!');
    }
}