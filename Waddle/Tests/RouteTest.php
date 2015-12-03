<?php
/**
 * Created by PhpStorm.
 * User: kgb
 * Date: 03.12.15
 * Time: 13:42
 */

namespace Waddle\Tests;


use Waddle\Classes\Route;

class RouteTest extends \PHPUnit_Framework_TestCase {

    /** @var \Waddle\Classes\Controller */
    private $mockController;

    public function setUp() {
        $this->mockController = $this->getMockBuilder('\Waddle\Classes\Controller')
                                    ->getMock();
        $this->mockController->method('testAction')->willReturn('test output');
    }

    private function argumentsListProvider() {
        return [
            ['/no/args/here', []],
            ['/{test}', ['test']],
            ['/test/{middle}/test', ['middle']],
            ['/{multiple}/{args}/', ['multiple', 'args']]
        ];
    }

    /**
     * @dataProvider argumentsListProvider
     */
    public function testArgumentsList($path, $expected) {
        /** @var $route Route */
        $route = new Route('test', $path, );
    }
}
