<?php
/**
 * Created by PhpStorm.
 * User: kgb
 * Date: 19.11.15
 * Time: 0:27
 */

namespace Waddle\Classes;

// TODO: BaseForm features: validating, rendering, populating with data.

/**
 * Class BaseForm
 * @package Waddle\Classes
 */
class BaseForm implements FormInterface {
    protected $_model;
    /** @var Fields\FieldInterface[] */
    protected $_fields;

    /** @var boolean $handled */
    private $handled;

    /** @var array $_errors */
    protected $_errors;

    public function __construct(Model $model) {
        $this->_model = $model;
        $rfc = new \ReflectionClass(get_class($this));
        $fs = $rfc->getProperties();
        $this->_fields = [];
        /** @var \ReflectionProperty $prop */
        foreach ($fs as $prop) {
            if ($prop->getValue($this) instanceof \Waddle\Classes\Fields\FieldInterface) {
                $this->_fields[$prop->getName()] = $prop->getValue($this);
                $this->_fields[$prop->getName()]->setAttribute('name', $prop->getName());
            }
        }
        $this->handled = false;
        $this->_errors = [];
    }

    /**
     * @param $requestData {@code $_GET} or {@code $_POST}
     * @return bool
     */
    public function handleRequest($requestData) {
        // TODO: Implement handleGetRequest() method. Errors are saved as a field.
        // TODO: And then are being used in getView method
        foreach($requestData as $name => $value) {
            if (!isset($this->_fields[$name])) continue;
            $this->_fields[$name]->set($value);
        }

        return $this->validate();

    }

    /**
     * @return boolean
     */
    public function validate() {
        $this->_errors = [];
        foreach ($this->_fields as $name => $value) {
            $e = $value->validate();
            if ($e !== false) {
                $this->_errors[$name] = $e;
            }
        }
        return $this->_errors == [];
    }

    public function getView($fieldName, array $attributes) {
        $views = [];
        /**
         * @var string $fName
         * @var Fields\FieldInterface $f
         */
        foreach($this->_fields as $fName => $f) {
            $views[$fName] = $f->getView();
        }
        return new FormView($views, $this->_errors);
    }

    public function get($fieldName) {
        $this->_fields[$fieldName]->get();
    }

    public function set($fieldName, $data) {
        $this->_fields[$fieldName]->set($data);
    }
}
