<?php

namespace Waddle\Classes\Exceptions;


use Waddle\Classes\App;

class HttpException extends \Exception {
    public function __construct($message = "", $code = 0, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
        if ($code >= 200) {
            header('HTTP/1.1 ' . $code . ' ' , $message);
        }
    }


    public function render($layout) {
        include_once $layout;
    }

    public function __toString() {
        return '<h1>Error Code: '.$this->code.'</h1><h3>'.$this->message.'</h3>';
    }
} 