<?php
/**
 * Created by PhpStorm.
 * User: kgb
 * Date: 19.11.15
 * Time: 0:16
 */

namespace Engine\Classes\Fields;


class SubmitField extends AbstractField {

    public function getView() {
        if (isset($this->attributes['value'])) {
            unset($this->attributes['value']);
        }
        $html = '<input type="submit" value="' . $this->label . '" ';
        foreach ($this->attributes as $name => $value) {
            $html .= $name . '="' . htmlspecialchars($value) . '" ';
        }
        $html .= '/>';
        return $this->getLabelView($this->attributes['id']) . $html;
    }
}