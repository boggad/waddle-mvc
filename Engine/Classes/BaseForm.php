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
    /** @var Fields\FieldInterface[] */
    protected $_fields;

    /** @var boolean $handled */
    private $handled;

    public function __construct(Model $model) {
        $this->_model = $model;
        $rfc = new \ReflectionClass(get_class($this));
        $fs = $rfc->getProperties();
        $this->_fields = [];
        /** @var \ReflectionProperty $prop */
        foreach ($fs as $prop) {
            if ($prop->getValue($this) instanceof \Engine\Classes\Fields\FieldInterface) {
                $this->_fields[$prop->getName()] = $prop->getValue($this);
            }
        }
        $this->handled = false;
    }

    /**
     * @param $requestData {@code $_GET} or {@code $_POST}
     */
    public function handleRequest($requestData) {
        // TODO: Implement handleGetRequest() method. Errors are saved as a field.
        // TODO: And then are being used in getView method

    }

    public function validate() {
        // TODO: Implement validate() method.
    }

    public function getView($fieldName, array $attributes) {
        $views = [];
        $errors = [];
        /**
         * @var string $fName
         * @var Fields\FieldInterface $f
         */
        foreach($this->_fields as $fName => $f) {
            $views[$fName] = $f->getView();
            if ($this->handled) {
                $errors[$fName] = $f->validate();
            }
        }
        return new FormView($views, $errors);
    }

    public function get($fieldName) {
        $this->_fields[$fieldName]->get();
    }

    public function set($fieldName, $data) {
        $this->_fields[$fieldName]->set($data);
    }
}