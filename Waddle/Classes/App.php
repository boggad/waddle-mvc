<?php


namespace Waddle\Classes;


use Waddle\Classes\Exceptions\HttpException;
use Waddle\Classes\Exceptions\NotFoundException;

class App {
    use AnnotationManager;


    private $_config;
    private $_entitymanagers;
    private $routingMap;

    /**
     * @var Route
     */
    private $curRoute;

    /**
     * @param string $name
     * @param array $args
     * @return string
     * @throws HttpException
     */
    public function path($name, $args = []) {
        if (!isset($this->routingMap[$name])) {
            throw new HttpException('Cannot find path named "' . $name . '"', 400);
        }
        /**
         * @var $route Route
         */
        $route = $this->routingMap[$name];
        $res = $route->getPathPrototype();
        foreach ($args as $key => $value) {
            $res = preg_replace('/{' . $key . '}/', $value, $res);
        }
        return $res;
    }

    /**
     * @return bool
     */
    public function isAuthenticated() {
        if (!isset($_SESSION['hash'])) return false;
        $_login = $this->getAdminLogin();
        $_pass = $this->getAdminPassHash();
        return $_SESSION['hash'] == md5($_login . $_pass . 'sol');
    }

    public function run() {
        //var_dump($this->routingMap);
        $request = isset($_GET['req']) ? $_GET['req'] : '';
        $request = '/' . $request;
        try {
            /**
             * @var $route Route
             */
            foreach($this->routingMap as $route) {
                if ($route->matchPath($request)) {
                    $this->curRoute = $route;
                    $route->invoke($this);
                    return;
                }
            }
            throw new NotFoundException('Cannot find route for requested URI "' . $request . '".');
        } catch (HttpException $e) {
            $layout = __DIR__ . \sl('/../../Src/Layouts/errors/') . $e->getCode() . '.php';
            if (file_exists($layout)) {
                $e->render($this, $layout);
            } else {
                echo $e;
            }
        } catch (\Exception $e) {
            echo '<h1>PHP Error: ' . $e->getMessage() . '</h1><p>' . $e->getTraceAsString() . '</p>';
        }
    }

    public function getCurController() {
        return $this->curRoute->getController();
    }

    public function getCurAction() {
        return $this->curRoute->getMethod();
    }

    /**
     * @return string
     */
    public function getCurPathName() {
        return $this->curRoute->getName();
    }

    public function evalController($model, $args) {
        $a = explode('::', $model);
        $class = 'Src\Controllers\\' . $a[0] . 'Controller';
        $method = $a[1];
        /**
         * @var $route Route
         */
        foreach($this->routingMap as $route) {
            if ($route->getController() == $class && $route->getMethod() == $method) {
                $route->setArguments($args);
                $route->invoke($this);
                return;
            }
        }
        throw new NotFoundException('Cannot find method "' . $method . '" in class "' . $class . '".');
    }

    public function asset($name, $v = false, $rewrite = true) {
        $v = $v ? '?v=' . $v : '';
        if ((pathinfo($name)['extension'] == 'css') && ($rewrite === true))
            return $this->css_rewrite($name);
        return '/assets/' . $name . $v;
    }

    private function css_rewrite($css_file) {
        $file = __DIR__ . '/../../Web/assets/' . $css_file;
        $file_basename = pathinfo($css_file)['basename'];
        $css = file_get_contents($file);
        $regexp = '/asset\(([a-zA-Z0-9\?\=\&\#\/\._ -]+)?,\s?([0-9]+)\)/';
        $css = preg_replace_callback($regexp,
            function ($matches) {
                return '\'' . $this->asset($matches[1], $matches[2]) . '\'';
            }, $css);
        $regexp = '/asset\(([a-zA-Z0-9\?\=\&\#\/\._ -]+)\)/';
        $css = preg_replace_callback($regexp,
            function ($matches) {
                return '\'' . $this->asset($matches[1]) . '\'';
            }, $css);
        $hash = hash('crc32',$css);
        if (file_exists(__DIR__ . '/../../Web/assets/css_rewrite/' . $hash . '_' . $file_basename)) {
            return '/assets/css_rewrite/' . $hash . '_' . $file_basename;
        }
        $newPath = '/assets/css_rewrite/' . $hash . '_' . $file_basename;
        $newFile = __DIR__ . '/../../Web/assets/css_rewrite/' . $hash . '_' . $file_basename;
        $f = fopen($newFile, 'w');
        fwrite($f, $css);
        fclose($f);
        return $newPath;
    }

    public function assetView($name) {
        return __DIR__ . '/../../Src/Views/' . $name . '.php';
    }

    public function getConfig() {
        return $this->_config;
    }

    public function getSiteTitle() {
        return $this->_config['site_title'];
    }

    /**
     * @param $name
     * @return EntityManager
     */
    public function getEntityManager($name) {
        return $this->_entitymanagers[$name];
    }

    function __construct($config) {
        $this->_config = $config;
        $this->_entitymanagers = array();
        $files = scandir(__DIR__ . '/../../Src/Models');
        foreach ($files as $file) {
            $pi = pathinfo($file);
            if ($pi['extension'] != 'php') continue;
            if (isset(static::getClassAnnotation('Src\Models\\' . $pi['filename'])['ORM_Entity'])) {
                $this->_entitymanagers[$pi['filename']] = new EntityManager($this, 'Src\Models\\'.$pi['filename']);
            }
        }

        $this->routingMap = array();

        $files = scandir(__DIR__ . '/../../Src/Controllers');
        foreach ($files as $file) {
            $pi = pathinfo($file);
            if ($pi['extension'] != 'php') continue;
            $class = 'Src\Controllers\\' . $pi['filename'];
            $rc = new \ReflectionClass($class);
            $methods = $rc->getMethods();
            foreach($methods as $m) {
                $meta = static::getMethodAnnotation($m);
                if (isset($meta['RoutePath'])) {
                    if (isset($meta['RouteName'])) {
                        $this->routingMap[$meta['RouteName']] =
                            new Route($meta['RouteName'], $meta['RoutePath'], $class, $m->getName());
                    } else {
                        $name = hash('crc32', $meta['RoutePath']);
                        $this->routingMap[$name] =
                            new Route($name, $meta['RoutePath'], $class, $m->getName());
                    }
                }
            }
        }

    }

    public function getAdminLogin() {
        return $this->getConfig()['admin_login'];
    }

    public function getAdminPassHash() {
        return $this->getConfig()['admin_hash'];
    }

}


?>