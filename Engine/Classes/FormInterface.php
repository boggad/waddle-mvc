<?php

namespace Engine\Classes;

/**
 * Interface FormInterface
 * @author Timofey
 */
interface FormInterface
{
	public function __construct(Model $model);
	public function handleGetRequest();
	public function handlePostRequest();
	public function validate();
	public function getView();
	public function get($fieldName);
	public function set($fieldName);
}
