<?php

namespace Waddle\Classes\Validators;

/**
 * Class Email
 * @author Timofey
 */
class Email implements ValidatorInterface {

    private $message;

    /**
     * Email constructor.
     * @param $message
     */
    public function __construct($message) {
        $this->message = $message;
    }

    /**
     * validate
     * @return boolean
     * @author Timofey
     **/
    public function validate($data) {
        if (!is_string($data)) {
            return false;
        }

        return preg_match('/[\w]+[\+]?[\w\.-]*@[\w\.\-]+\.[\w]{2,}/i', $data) > 0;
    }

    /**
     * @return string
     */
    public function getError() {
        return $this->message;
    }
}
