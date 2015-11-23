<?php
/**
 * Created by PhpStorm.
 * User: kgb
 * Date: 22.11.15
 * Time: 15:42
 */

namespace Src\Controllers;


class DefaultControllerTest extends \PHPUnit_Framework_TestCase  {

    public function testHelloWorld() {
        /**
         * @var \Waddle\Classes\App $app
         */
        $app = $this->getMockBuilder('Waddle\Classes\App')
                    ->disableOriginalConstructor()
                    ->getMock();
        $controller = new DefaultController($app);
        $this->expectOutputRegex('/<h1>Hello, World!<\/h1>/');
        $controller->indexAction('World!');
    }
}