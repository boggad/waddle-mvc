<?php
/**
 * Created by PhpStorm.
 * User: Тимофей
 * Date: 02.09.2015
 * Time: 00:10
 */

namespace Engine\Classes;


class Route {

    private $name;
    private $path;
    private $pathPrototype;
    private $controller;
    private $method;
    private $arguments;

    public function __construct($name, $path, $controller, $method) {
        $this->name = $name;
        $this->path = $path;
        $this->pathPrototype = $path;
        $this->path = preg_replace('/({[\w]+})/', '([\\w-]+)', $this->path);
        $this->path = preg_replace('/\//', '\\/', $this->path);
        $this->path = '/^' . $this->path . '$/';
        $this->controller = $controller;
        $this->method = $method;
        $this->arguments = [];
        if (preg_match_all('/{([\w]+)}/', $path, $matches) > 0) {
            foreach ($matches[1] as $m) {
                $this->arguments[$m] = false;
            }
        }
    }

    /**
     * @return string
     */
    public function getPathPrototype() {
        return $this->pathPrototype;
    }


    /**
     * @param $path
     * @return boolean
     */
    public function matchPath($path) {
        //var_dump($this->path);

        if (preg_match_all($this->path, $path, $matches) > 0) {
            $matches = array_slice($matches, 1, count($matches));
            for ($i = 0; $i < count($matches); $i++) {
                $this->arguments[key($this->arguments)] = current($matches)[0];
                next($this->arguments);
                next($matches);
            }
            return true;
        } else {
            return false;
        }
    }

    public function invoke(App $app) {
        $ctrl = new $this->controller($app);
        $rm = new \ReflectionMethod($this->controller, $this->method);
        $argsToPass = [];
        $params = $rm->getParameters();
        foreach ($params as $param) {
            $argsToPass[$param->getPosition()] = $this->arguments[$param->getName()];
        }
        ksort($argsToPass);
        $rm->invokeArgs($ctrl, $argsToPass);
    }

    /**
     * @return string
     */
    public function getController() {
        return $this->controller;
    }

    /**
     * @return string
     */
    public function getMethod() {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * @return array
     */
    public function getArguments() {
        return $this->arguments;
    }

    /**
     * @param $name
     * @param $value
     */
    public function setArgument($name, $value) {
        $this->arguments[$name] = $value;
    }

    /**
     * @param $args
     */
    public function setArguments($args) {
        $this->arguments = $args;
    }
} 