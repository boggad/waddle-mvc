<?php
/**
 * Created by PhpStorm.
 * User: kgb
 * Date: 18.11.15
 * Time: 23:12
 */

namespace Engine\Classes\Validators;


class Regex implements ValidatorInterface {

    private $regex;

    /**
     * Regex constructor.
     * @param $regex
     */
    public function __construct($regex) {
        $this->regex = $regex;
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
}