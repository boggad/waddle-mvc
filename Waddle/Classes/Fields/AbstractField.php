<?php

namespace Waddle\Classes\Fields;
use Classes\Exceptions\ValidationError;

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
            throw new ValidationError('Validation Failed in class "' .
                get_class($this) . '" on data: "' . $data . '"');
            // TODO: Add tests on throwing the exception
        }
    }

    public function validate() {
        $errors = [];
        /**
         * @var \Waddle\Classes\Validators\ValidatorInterface $val
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

    public function setAttribute($attrName, $attrValue) {
        $this->attributes[$attrName] = $attrValue;
    }
}
