<?php

namespace Engine\Classes\Fields;

/**
 * Class DecimalField
 * @author Timofey
 */
class DecimalField extends AbstractField {

    public function getView(array $attributes) {
        $html = '<input type="text" pattern="([0-9]+|[0-9]+[\.][0-9]+)" ';
        foreach ($attributes as $name => $value) {
            $html .= $name . '="' . htmlspecialchars($value) . '" ';
        }
        $html .= '/>';
        return $this->getLabelView($attributes['id']) . $html;
    }
}

