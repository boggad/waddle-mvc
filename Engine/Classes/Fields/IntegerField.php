<?php
/**
 * Created by PhpStorm.
 * User: kgb
 * Date: 19.11.15
 * Time: 0:06
 */

namespace Engine\Classes\Fields;

/**
 * Class IntegerField
 * @package Engine\Classes\Fields
 */
class IntegerField extends AbstractField {

    public function getView(array $attributes) {
        $html = '<input type="number" ';
        foreach ($attributes as $name => $value) {
            $html .= $name . '="' . htmlspecialchars($value) . '" ';
        }
        $html .= '/>';
        return $this->getLabelView($attributes['id']) . $html;
    }
}