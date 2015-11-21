<?php
/**
 * Created by PhpStorm.
 * User: kgb
 * Date: 18.11.15
 * Time: 22:47
 */

namespace Waddle\Classes\Validators;


class GreaterThan implements ValidatorInterface {

    private $lowerBound;
    private $message;

    /**
     * GreaterThan constructor.
     * @param $lowerBound
     */
    public function __construct($message, $lowerBound) {
        $this->lowerBound = $lowerBound;
        $this->message = $message;
    }


    /**
     * @return boolean
     */
    public function validate($data) {
        return is_numeric($data) && ($data >= $this->lowerBound);
    }

    public function getError() {
        return $this->message;
    }
}