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
    private $message;

    /**
     * LowerThan constructor.
     * @param $message
     * @param $upperBound
     */
    public function __construct(string $message, $upperBound) {
        $this->upperBound = $upperBound;
        $this->message = $message;
    }


    /**
     * @return boolean
     */
    public function validate($data) {
        return is_numeric($data) && ($data <= $this->upperBound);
    }

    public function getError() {
        return $this->message;
    }
}