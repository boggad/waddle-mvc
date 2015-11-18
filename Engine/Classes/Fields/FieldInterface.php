<?php

namespace Engine\Classes\Fields;

/**
 * Interface FieldInterface
 * @author Timofey
 */
interface FieldInterface {
    /**
     * @param mixed array $validators
     */
    public function __construct(array $validators);

    public function get();

    public function getHtmlEscaped();

    public function set($data);

    public function validate();

    public function getView(array $attributes);
}
