<?php

namespace Waddle\Classes\Fields;

/**
 * Interface FieldInterface
 * @author Timofey
 */
interface FieldInterface {
    /**
     * @param mixed array $validators
     * @param array $attributes
     * @param bool $label
     */
    public function __construct(array $validators, array $attributes, $label = false);

    public function setAttribute($attrName, $attrValue);

    public function get();

    public function getHtmlEscaped();

    public function set($data);

    public function validate();

    public function getView();
}
