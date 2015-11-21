<?php

namespace Waddle\Classes;

/**
 * Interface FormInterface
 * @author Timofey
 */
interface FormInterface
{
	public function __construct(Model $model);
	public function handleRequest($requestData);
	public function validate();
	public function getView($fieldName, array $attributes);
	public function get($fieldName);
	public function set($fieldName, $data);
}
