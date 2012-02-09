<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Form element
 *
 * PHP version 5
 *
 * Copyright © 2012 The Galette Team
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
 * @copyright 2012 The Galette Team
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GPL License 3.0 or (at your option) any later version
 * @version   SVN: $Id$
 * @link      http://galette.tuxfamily.org
 * @since     Available since 0.71dev - 2012-02-09
 */

/** @ignore */

/**
 * Form element
 *
 * @category  Forms
 * @name      GaletteForm
 * @package   Galette
 * @author    Johan Cwiklinski <johan@x-tnd.be>
 * @copyright 2012 The Galette Team
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GPL License 3.0 or (at your option) any later version
 * @link      http://galette.tuxfamily.org
 * @since     Available since 0.71dev - 2012-02-09
 */
class GaletteForm extends Zend_Form
{
    private $_table;

    /**
     * Constructor
     *
     * Registers form view helper as decorator
     *
     * @param string $table   Table name
     * @param mixed  $options Options
     *
     * @return void
     */
    public function __construct($table, $options = null)
    {
        $this->_table = $table;
        parent::__construct($options);
        $this->setAttrib('id', $this->_table . '_form');
        $this->setView(new Zend_View());
        $this->setMethod('post');
        $this->_loadElements();
    }

    /**
     * Load Form elements
     *
     * @return void
     */
    private function _loadElements()
    {
        $a = new Adherent();
        $fc = new FieldsConfig(Adherent::TABLE, $a->fields);
        $categories = $fc->getCategorizedFields();

        foreach ( $categories as $c ) {
            $zf = new Zend_Form_SubForm();

            $zf->setLegend('Subform' . round(0, 50));
            $elements = array();
            foreach ( $c as $field ) {
                $elt =  new GaletteTextElement($field['field_id']);
                $elt->setLabel($fc->getLabel($field['field_id']));
                $elements[] = $elt;
            }
            $zf->addElements($elements);

            $zf->getDecorator('HtmlTag')->setOption('tag', 'div');
            $zf->getDecorator('Fieldset')->setOption('class', 'galette_form');
            $zf->removeDecorator('DtDdWrapper');

            $this->addSubForm($zf, 'subform_' . rand(0, 50));
        }
    }

    /**
     * Loads default decorators. Change display according to
     * Galette's theming conventions.
     *
     * @return void
     */
    public function loadDefaultDecorators()
    {
        $this->setDecorators(
            array(
                'FormElements',
                array(
                    'HtmlTag',
                    array(
                        'tag' => 'div'
                    )
                ),
                'Form',
            )
        );
    }
}
