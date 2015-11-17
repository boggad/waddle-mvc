<?php

namespace Engine\Classes\Validators;

/**
 * Class Required
 * @author Timofey
 */
class Required implements ValidatorInterface {
	/**
	 * validate
	 * @return boolean
	 * @author Timofey
	 **/
	public function validate($data) {
		if (is_string($data)) {
			return $data !== '';
		}
		if (is_numeric($data)) {
			return $data != 0;
		}
		return !is_null($data);
	}
}
