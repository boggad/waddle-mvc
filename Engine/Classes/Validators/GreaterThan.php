<?php
/**
 * Created by PhpStorm.
 * User: kgb
 * Date: 18.11.15
 * Time: 22:47
 */

namespace Engine\Classes\Validators;


class GreaterThan implements ValidatorInterface {

    private $lowerBound;

    /**
     * GreaterThan constructor.
     * @param $lowerBound
     */
    public function __construct($lowerBound) {
        $this->lowerBound = $lowerBound;
    }


    /**
     * @return boolean
     */
    public function validate($data) {
        return is_numeric($data) && ($data >= $this->lowerBound);
    }
}