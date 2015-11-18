<?php
/**
 * Created by PhpStorm.
 * User: kgb
 * Date: 19.11.15
 * Time: 0:13
 */

namespace Engine\Classes\Fields;

/**
 * Class TextAreaField
 * @package Engine\Classes\Fields
 */
class TextAreaField extends AbstractField {

    public function getView(array $attributes) {
        $html = '<textarea ';
        foreach ($attributes as $name => $value) {
            $html .= $name . '="' . htmlspecialchars($value) . '" ';
        }
        $html .= '></textarea>';
        return $this->getLabelView($attributes['id']) . $html;
    }
}