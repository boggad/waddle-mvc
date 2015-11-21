<?php

namespace Waddle\Classes\Fields;

/**
 * Class DecimalField
 * @author Timofey
 */
class DecimalField extends AbstractField {

    public function getView() {
        $html = '<input type="text" pattern="([0-9]+|[0-9]+[\.][0-9]+)" ';
        foreach ($this->attributes as $name => $value) {
            $html .= $name . '="' . htmlspecialchars($value) . '" ';
        }
        $html .= '/>';
        return $this->getLabelView($this->attributes['id']) . $html;
    }
}

