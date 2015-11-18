<?php
/**
 * Created by PhpStorm.
 * User: kgb
 * Date: 19.11.15
 * Time: 0:27
 */

namespace Engine\Classes;

// TODO: BaseForm features: validating, rendering, populating with data.

/**
 * Class BaseForm
 * @package Engine\Classes
 */
class BaseForm implements FormInterface{
    protected $_model;
    protected $_fields;

    public function __construct(Model $model) {
        $this->_model = $model;
        $rfc = new \ReflectionClass(get_class($this));
        $fs = $rfc->getProperties();
        $this->_fields = [];
        /** @var \ReflectionProperty $prop */
        foreach ($fs as $prop) {
            if ($prop->getValue($this) instanceof \Engine\Classes\Fields\FieldInterface) {
                $this->_fields[] = $prop->getValue($this);
            }
        }
    }

    public function handleGetRequest() {
        // TODO: Implement handleGetRequest() method.
    }

    public function handlePostRequest() {
        // TODO: Implement handlePostRequest() method.
    }

    public function validate() {
        // TODO: Implement validate() method.
    }

    public function getView() {
        // TODO: Implement getView() method.
    }

    public function get($fieldName) {
        // TODO: Implement get() method.
    }

    public function set($fieldName) {
        // TODO: Implement set() method.
    }
}