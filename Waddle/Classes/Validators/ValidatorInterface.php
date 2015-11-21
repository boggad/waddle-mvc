<?php

namespace Waddle\Classes\Validators;

/**
 * Interface ValidatorInterface
 * @author Timofey
 */
interface ValidatorInterface {

    /**
     * @return string
     */
    public function getError();

    /**
     * @return boolean
     */
    public function validate($data);
}
