<?php
/**
 * Created by PhpStorm.
 * User: kgb
 * Date: 19.11.15
 * Time: 0:13
 */

namespace Waddle\Classes\Fields;

/**
 * Class TextAreaField
 * @package Waddle\Classes\Fields
 */
class TextAreaField extends AbstractField {

    public function getView() {
        $html = '<textarea ';
        foreach ($this->attributes as $name => $value) {
            $html .= $name . '="' . htmlspecialchars($value) . '" ';
        }
        $html .= '></textarea>';
        return $this->getLabelView($this->attributes['id']) . $html;
    }
}