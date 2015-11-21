<?php

namespace Waddle\Classes;

abstract class Controller {
    protected $app;
    protected $layout;

    function __construct(App $app, $eval = false) {
        $this->app = $app;
        $this->layout = $eval?'':'default_layout';
    }

    function render($view, $args) {
		$baseName = __DIR__.\sl('/../../Src/Views/').$view;
		if (file_exists($baseName . '.php')) {
			$content = $baseName . '.php';
		} elseif (file_exists($baseName . '.html')) {
			$content = $baseName . '.html';
		} elseif (file_exists($baseName . '.htm')) {
			$content = $baseName . '.htm';
		} else {
			throw new \Exception('View "' . $view . '" was not found!');
		}

        /** @noinspection PhpUnusedLocalVariableInspection */
        $app = $this->app;
        foreach($args as $varName => $varValue) {
            $$varName = $varValue;
        }
        if ($this->layout != '')
            require_once __DIR__.\sl('/../../Src/Layouts/').$this->layout.'.php';
        else
            require_once $content;
    }

    function redirectToRoute($routeName, $args = []) {
        $path = $this->app->path($routeName, $args);
        header('Location: ' . $path);
        die();
    }
} 
