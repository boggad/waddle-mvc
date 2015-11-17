<?php

namespace Engine\Classes;

/**
 * Interface FieldInterface
 * @author Timofey
 */
interface FieldInterface
{
	/**
     * @param mixed array $validators
     */
	public function __construct(array $validators);
	public function get();
	public function getHtmlEscaped();
	public function set(mixed $data);
	public function validate();
}
