<?php

namespace Engine\Classes\Validators;

/**
 * Class Email
 * @author Timofey
 */
class Email implements ValidatorInterface {
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
}
