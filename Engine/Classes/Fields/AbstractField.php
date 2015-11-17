<?php

namespace Engine\Classes\Fields;

/**
 * Class StringField
 * @author Timofey
 */
abstract class AbstractField implements FieldInterface {

	protected $validators;
	protected $data;

	/**
     * @param mixed array $validators
     */
	public function __construct(array $validators) {
		$this->validators = is_null($validators)?array():$validators;
	}

	public function get() {
		return $this->data;
	}

	public function getHtmlEscaped() {
		return htmlspecialchars($this->data);
	}

	public function set(mixed $data) {
		if ($this->validate()) {
			$this->data = $data;
		} else {
			// TODO throw new ValidationError()
		}
	}

	public function validate() {
		/**
		 * @var /Engine/Classes/Validators/ValidatorInterface $val
		 */
		foreach ($this->validators as $val) {
			if (!$val->validate($this->data)) return false;
		}
		return true;
	}

	public abstract function getView(array $attributes);
}
