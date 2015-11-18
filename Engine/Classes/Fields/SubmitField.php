<?php
/**
 * Created by PhpStorm.
 * User: kgb
 * Date: 19.11.15
 * Time: 0:16
 */

namespace Engine\Classes\Fields;


class SubmitField extends AbstractField {

    public function getView(array $attributes) {
        if (isset($attributes['value'])) {
            unset($attributes['value']);
        }
        $html = '<input type="submit" value="' . $this->label . '" ';
        foreach ($attributes as $name => $value) {
            $html .= $name . '="' . htmlspecialchars($value) . '" ';
        }
        $html .= '/>';
        return $this->getLabelView($attributes['id']) . $html;
    }
}