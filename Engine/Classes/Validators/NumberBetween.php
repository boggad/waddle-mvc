<?php

namespace Engine\Classes\Validators;

/**
 * Class NumberBetween
 * @author Timofey
 */
class NumberBetween implements ValidatorInterface {

    private $lowerBound;
    private $upperBound;
    private $message;

    /**
     * @param $message
     * @param int|float $lowerBound
     * @param int|float $upperBound
     */
    public function __construct(string $message, $lowerBound, $upperBound) {
        $this->lowerBound = $lowerBound;
        $this->upperBound = $upperBound;
        $this->message = $message;
    }

    /**
     * validate
     * @return boolean
     * @author Timofey
     **/
    public function validate($data) {
        if (!is_numeric($data)) {
            return false;
        }
        return ($data >= $this->lowerBound) && ($data <= $this->upperBound);
    }

    public function getError() {
        return $this->message;
    }
}
