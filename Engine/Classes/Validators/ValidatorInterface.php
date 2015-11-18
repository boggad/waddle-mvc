<?php

namespace Engine\Classes\Validators;

/**
 * Interface ValidatorInterface
 * @author Timofey
 */
interface ValidatorInterface {
    /**
     * @return boolean
     */
    public function validate($data);
}
