<?php

namespace Engine\Classes\Validators;

/**
 * Class NumberBetween
 * @author Timofey
 */
class NumberBetween {

	private $lowerBound;
	private $upperBound;

	/**
	 * @param numeric $lowerBound
     * @param numeric $upperBound
	 */
	public function __construct($lowerBound, $upperBound)
	{
        $this->lowerBound = $lowerBound;
        $this->upperBound = $upperBound;
	}

	/**
	 * validate
	 * @return boolean
	 * @author Timofey
	 **/
	public function validate($data) {
		if (!is_numeric($data)) return false;
		return ($data >= $this->lowerBound) && ($data <= $this->upperBound);
	}
}
