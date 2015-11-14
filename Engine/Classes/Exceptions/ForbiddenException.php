<?php

namespace Engine\Classes\Exceptions;


class ForbiddenException extends HttpException {
    public function __construct($message = "Forbidden", $code = 403, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
} 