<?php

namespace Waddle\Classes\Validators;

/**
 * Class Required
 * @author Timofey
 */
class Required implements ValidatorInterface {

    private $message;
    /**
     * @var bool
     */
    private $canBeZero;

    /**
     * Regex constructor.
     * @param $message
     * @param bool $canBeZero
     */
    public function __construct($message, $canBeZero = true) {
        $this->message = $message;
        $this->canBeZero = $canBeZero;
    }

    /**
     * validate
     * @return boolean
     * @author Timofey
     **/
    public function validate($data) {
        if (is_string($data)) {
            return $data !== '';
        }
        if (!$this->canBeZero && is_numeric($data)) {
            return $data != 0;
        }
        return !is_null($data);
    }

    public function getError() {
        return $this->message;
    }
}
