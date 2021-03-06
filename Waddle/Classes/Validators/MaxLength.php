<?php
/**
 * Created by PhpStorm.
 * User: kgb
 * Date: 18.11.15
 * Time: 23:45
 */

namespace Waddle\Classes\Validators;


class MaxLength implements ValidatorInterface {

    private $length;
    private $message;

    /**
     * MaxLength constructor.
     * @param $message
     * @param $length
     */
    public function __construct($message, $length) {
        $this->length = $length;
        $this->message = $message;
    }

    /**
     * @return boolean
     */
    public function validate($data) {
        return mb_strlen($data, 'utf-8') <= $this->length;
    }

    public function getError() {
        return $this->message;
    }
}