<?php

namespace Deploy\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Form\Element;

class BtsFormRow extends AbstractHelper
{
    public function __invoke(Element $element, $extras = null)
    {
        $view = $this->getView();

        $hasError = is_array($element->getMessages()) && count($element->getMessages()) > 0;

        $existingAttributes = $element->getLabelAttributes();

        $labelClass = 'control-label';
        if (isset($existingAttributes['class'])) {
            $existingAttributes['class'] .= ' ' . $labelClass;
        } else {
            $existingAttributes['class'] = $labelClass;
        }
        $element->setLabelAttributes($existingAttributes);

        $o = "<div class=\"form-group " . ($hasError ? 'has-error' : '') . "\">\n";
        $o .= $view->formLabel($element) . "\n";
        $o .= $view->formElement($element) . "\n";

        if (!empty($extras) || $hasError) {
            $o .= "<p class=\"help-block\">" . $extras . "</p>\n";
        }

        $o .= "</div>";

        return $o;
    }
}
