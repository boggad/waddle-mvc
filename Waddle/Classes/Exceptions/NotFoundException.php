<?php

namespace Waddle\Classes\Exceptions;


class NotFoundException extends HttpException {
    public function __construct($message = "404 Page Not Found!", $code = 404, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
        header("HTTP/1.1 404 Not Found");
    }
} 