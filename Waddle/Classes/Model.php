<?php

namespace Waddle\Classes;


abstract class Model {
    /**
     * @var boolean
     */
    protected $_exists = false;

    /**
     * @return boolean
     */
    public function getExists() {
        return $this->_exists;
    }

    /**
     * @param boolean $exists
     */
    private function setExists($exists) {
        $this->_exists = $exists;
    }
} 