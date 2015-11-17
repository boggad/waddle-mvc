<?php

namespace Engine\Classes\Fields;

/**
 * Class StringField
 * @author Timofey
 */
class StringField extends AbstractField {

	public function getView(array $attributes) {
		$html = '<input type="text" ';
		foreach ($attributes as $name => $value) {
			$html .= $name . '="' . htmlspecialchars($value) . '" ';
		}
		$html .= '/>';
		return $html;
	}
}

