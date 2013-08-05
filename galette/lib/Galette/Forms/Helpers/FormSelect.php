<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Select element helper
 *
 * PHP version 5
 *
 * Copyright © 2012-2013 The Galette Team
 *
 * This file is part of Galette (http://galette.tuxfamily.org).
 *
 * Galette is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Galette is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Galette. If not, see <http://www.gnu.org/licenses/>.
 *
 * @category  Forms
 * @package   Galette
 *
 * @author    Johan Cwiklinski <johan@x-tnd.be>
 * @copyright 2012-2013 The Galette Team
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GPL License 3.0 or (at your option) any later version
 * @version   SVN: $Id$
 * @link      http://galette.tuxfamily.org
 * @since     Available since 0.71dev - 2012-02-11
 */

namespace Galette\Forms\Helpers;

/**
 * Radio element helper
 *
 * @category  Forms
 * @name      FormRadio
 * @package   Galette
 * @author    Johan Cwiklinski <johan@x-tnd.be>
 * @copyright 2012-2013 The Galette Team
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GPL License 3.0 or (at your option) any later version
 * @link      http://galette.tuxfamily.org
 * @since     Available since 0.71dev - 2012-02-11
 */
class FormSelect extends \Zend_View_Helper_FormSelect
{
    /**
     * Generates 'select' list of options.
     *
     * @param string|array $name    If a string, the element name.  If an
     * array, all other parameters are ignored, and the array elements
     * are extracted in place of added parameters.
     * @param mixed        $value   The option value to mark as 'selected'; if an
     * array, will mark all values in the array as 'selected' (used for
     * multiple-select elements).
     * @param array|string $attribs Attributes added to the 'select' tag.
     * the optional 'optionClasses' attribute is used to add a class to
     * the options within the select (associative array linking the option
     * value to the desired class)
     * @param array        $options An array of key-value pairs where the array
     * key is the radio value, and the array value is the radio text.
     * @param string       $listsep When disabled, use this list separator string
     * between list values.
     *
     * @return string The select tag and options XHTML.
     */
    public function gformSelect($name, $value = null, $attribs = null,
        $options = null, $listsep = "<br />\n"
    ) {
        $info = $this->_getInfo($name, $value, $attribs, $options, $listsep);
        extract($info); // name, id, value, attribs, options, listsep, disable

        // force $value to array so we can compare multiple values to multiple
        // options; also ensure it's a string for comparison purposes.
        $value = array_map('strval', (array) $value);

        // check if element may have multiple values
        $multiple = '';

        if (substr($name, -2) == '[]') {
            // multiple implied by the name
            $multiple = ' multiple="multiple"';
        }

        if (isset($attribs['multiple'])) {
            // Attribute set
            if ($attribs['multiple']) {
                // True attribute; set multiple attribute
                $multiple = ' multiple="multiple"';

                // Make sure name indicates multiple values are allowed
                if (!empty($multiple) && (substr($name, -2) != '[]')) {
                    $name .= '[]';
                }
            } else {
                // False attribute; ensure attribute not set
                $multiple = '';
            }
            unset($attribs['multiple']);
        }

        // handle the options classes
        $optionClasses = array();
        if (isset($attribs['optionClasses'])) {
            $optionClasses = $attribs['optionClasses'];
            unset($attribs['optionClasses']);
        }
        
        // now start building the XHTML.
        $disabled = '';
        if (true === $disable) {
            $disabled = ' disabled="disabled"';
        }

        // Build the surrounding select element first.
        $xhtml = '<select'
                . ' name="' . $this->view->escape($name) . '"'
                . ' id="' . $this->view->escape($id) . '"'
                . $multiple
                . $disabled
                . $this->_htmlAttribs($attribs)
                . ">\n    ";

        // build the list of options
        $list       = array();
        $translator = $this->getTranslator();
        foreach ((array) $options as $opt_value => $opt_label) {
            if (is_array($opt_label)) {
                $opt_disable = '';
                if (is_array($disable) && in_array($opt_value, $disable)) {
                    $opt_disable = ' disabled="disabled"';
                }
                if (null !== $translator) {
                    $opt_value = $translator->translate($opt_value);
                }
                $opt_id = ' id="' . $this->view->escape($id) . '-optgroup-'
                        . $this->view->escape($opt_value) . '"';
                $list[] = '<optgroup'
                        . $opt_disable
                        . $opt_id
                        . ' label="' . $this->view->escape($opt_value) .'">';
                foreach ($opt_label as $val => $lab) {
                    $list[] = $this->_build($val, $lab, $value, $disable, $optionClasses);
                }
                $list[] = '</optgroup>';
            } else {
                $list[] = $this->_build($opt_value, $opt_label, $value, $disable, $optionClasses);
            }
        }

        // add the options to the xhtml and close the select
        $xhtml .= implode("\n    ", $list) . "\n</select>";

        return $xhtml;
    }
}
