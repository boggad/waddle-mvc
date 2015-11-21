<?php

namespace Waddle\Classes\Fields;

/**
 * Class StringField
 * @author Timofey
 */
class StringField extends AbstractField {

    public function getView() {
        $html = '<input type="text" ';
        foreach ($this->attributes as $name => $value) {
            $html .= $name . '="' . htmlspecialchars($value) . '" ';
        }
        $html .= '/>';
        return $this->getLabelView($this->attributes['id']) . $html;
    }
}

