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
    protected $attributes;

    /**
     * @param mixed array $validators
     * @param array $attributes
     * @param bool $label
     */
    public function __construct(array $validators, array $attributes,  $label = false) {
        $this->validators = is_null($validators) ? array() : $validators;
        $this->label = $label;
        $this->attributes = $attributes;
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
        $errors = [];
        /**
         * @var \Engine\Classes\Validators\ValidatorInterface $val
         */
        foreach ($this->validators as $val) {
            if (!$val->validate($this->data)) {
                $errors[] = $val->getError();
            }
        }
        return (count($errors) > 0) ? $errors : false;
    }

    protected function getLabelView($idAttr) {
        return '<label for="' . $idAttr . '">' . $this->label . '</label>';
    }

    public abstract function getView();
}
