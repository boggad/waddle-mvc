<?php
/**
 * Created by PhpStorm.
 * User: kgb
 * Date: 19.11.15
 * Time: 0:06
 */

namespace Waddle\Classes\Fields;

/**
 * Class IntegerField
 * @package Waddle\Classes\Fields
 */
class IntegerField extends AbstractField {

    public function getView() {
        $html = '<input type="number" ';
        foreach ($this->attributes as $name => $value) {
            $html .= $name . '="' . htmlspecialchars($value) . '" ';
        }
        $html .= '/>';
        return $this->getLabelView($this->attributes['id']) . $html;
    }
}