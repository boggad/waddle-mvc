<?php

namespace Engine\Classes\Validators;

/**
 * Interface ValidatorInterface
 * @author Timofey
 */
interface ValidatorInterface {

    public function getError();

    /**
     * @return boolean
     */
    public function validate($data);
}
