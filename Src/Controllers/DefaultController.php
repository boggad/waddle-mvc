<?php

namespace Src\Controllers;

use Engine\Classes\Controller;

class DefaultController extends Controller {

    /**
     * @RouteName(main_page)
     * @RoutePath(/{name})
     */
    function indexAction($name) {

        $this->render('main_page', [
            'name' => $name
        ]);
    }

} 
