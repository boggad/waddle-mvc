<?php

namespace Engine\Classes\Fields;

/**
 * Class DecimalField
 * @author Timofey
 */
abstract class AbstractField implements FieldInterface {

    protected $validators;
    protected $data;
    protected $label;

    /**
     * @param mixed array $validators
     * @param bool $label
     */
    public function __construct(array $validators, $label = false) {
        $this->validators = is_null($validators) ? array() : $validators;
        $this->label = $label;
    }

    public function get() {
        return $this->data;
    }

    public function getHtmlEscaped() {
        return htmlspecialchars($this->data);
    }

    public function set($data) {
        if ($this->validate()) {
            $this->data = $data;
        } else {
            // TODO throw new ValidationError()
        }
    }

    public function validate() {
        /**
         * @var \Engine\Classes\Validators\ValidatorInterface $val
         */
        foreach ($this->validators as $val) {
            if (!$val->validate($this->data)) {
                return false;
            }
        }
        return true;
    }

    protected function getLabelView($idAttr) {
        return '<label for="' . $idAttr . '">' . $this->label . '</label>';
    }

    public abstract function getView(array $attributes);
}
