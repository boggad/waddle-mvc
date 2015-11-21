<?php
/**
 * Created by PhpStorm.
 * User: kgb
 * Date: 18.11.15
 * Time: 23:12
 */

namespace Waddle\Classes\Validators;


class Regex implements ValidatorInterface {

    private $regex;
    private $message;

    /**
     * Regex constructor.
     * @param $message
     * @param $regex
     */
    public function __construct($message, $regex) {
        $this->regex = $regex;
        $this->message = $message;
    }


    /**
     * @return boolean
     */
    public function validate($data) {
        if (!is_string($data)) {
            return false;
        }

        return preg_match($this->regex, $data) > 0;
    }

    public function getError() {
        return $this->message;
    }
}