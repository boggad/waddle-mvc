<?php
/**
 * Created by PhpStorm.
 * User: kgb
 * Date: 19.11.15
 * Time: 13:00
 */

namespace Engine\Classes;


class FormView {

    /** @var array $_fieldsViews */
    protected $_fieldsViews;

    /** @var array $_errors */
    protected $_errors;

    /**
     * FormView constructor.
     * @param array $_fieldsViews
     * @param array $_errors
     */
    public function __construct(array $_fieldsViews, array $_errors) {
        $this->_fieldsViews = $_fieldsViews;
        $this->_errors = $_errors;
    }


    public function getFieldView($fieldName) {
        return isset($this->_fieldsViews[$fieldName]) ?
            $this->_fieldsViews[$fieldName] :
            null;
    }

    /**
     * @param $fieldName
     * @return array
     */
    public function getErrors($fieldName) {
        return $this->_errors[$fieldName];
    }
}