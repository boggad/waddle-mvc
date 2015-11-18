<?php
/**
 * Created by PhpStorm.
 * User: kgb
 * Date: 18.11.15
 * Time: 22:47
 */

namespace Engine\Classes\Validators;


class LowerThan implements ValidatorInterface {

    private $upperBound;

    /**
     * LowerThan constructor.
     * @param $upperBound
     */
    public function __construct($upperBound) {
        $this->upperBound = $upperBound;
    }


    /**
     * @return boolean
     */
    public function validate($data) {
        return is_numeric($data) && ($data <= $this->upperBound);
    }
}