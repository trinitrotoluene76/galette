<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Form element
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
 * @since     Available since 0.71dev - 2012-02-09
 */

namespace Galette\Forms;

/** @ignore */
require_once 'Zend/View.php';
require_once 'Zend/Form.php';
require_once 'Zend/Form/SubForm.php';
require_once 'Zend/Form/Decorator/HtmlTag.php';
require_once 'Zend/Form/Decorator/Label.php';
require_once 'Zend/Form/Decorator/FormElements.php';
require_once 'Zend/Validate/EmailAddress.php';

use Galette\Entity\Adherent;
use Galette\Entity\FieldsConfig;
use Galette\Repository\Titles;
use Galette\Forms\Helpers\FormRadio;

/**
 * Form element
 *
 * @category  Forms
 * @name      Form
 * @package   Galette
 * @author    Johan Cwiklinski <johan@x-tnd.be>
 * @copyright 2012 The Galette Team
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GPL License 3.0 or (at your option) any later version
 * @link      http://galette.tuxfamily.org
 * @since     Available since 0.71dev - 2012-02-09
 */
class Form extends \Zend_Form
{
    private $_table;
    private $_zdb;

    /**
     * Constructor
     *
     * Registers form view helper as decorator
     *
     * @param Db     $zdb     Database instance
     * @param string $table   Table name
     * @param mixed  $options Options
     *
     * @return void
     */
    public function __construct($zdb, $table, $options = null)
    {
        $this->_zdb = $zdb;
        $this->_table = $table;
        parent::__construct($options);
        $this->setAttrib('id', $this->_table . '_form');
        $view = new \Zend_View();
        $this->setView($view);
        $this->setMethod('post');
        $helper = new FormRadio();
        $view->registerHelper($helper, 'gformRadio');
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
        $elements = $fc->getFormElements();
        $categories = $fc->getCategorizedFields();

        foreach ( $elements as $elt ) {
            $zf = new \Zend_Form_SubForm();

            $zf->setLegend($elt->label);
            $elements = array();
            foreach ( $elt->elements as $field ) {
                switch ( $field->type ) {
                case FieldsConfig::TYPE_HIDDEN:
                    $class = 'Galette\Forms\Elements\Hidden';
                    break;
                case FieldsConfig::TYPE_BOOL:
                    $class = 'Galette\Forms\Elements\Checkbox';
                    break;
                case FieldsConfig::TYPE_DATE:
                    $class = 'Galette\Forms\Elements\Date';
                    break;
                case FieldsConfig::TYPE_RADIO:
                    $class = 'Galette\Forms\Elements\Radio';
                    break;
                default:
                case FieldsConfig::TYPE_STR:
                    $class = 'Galette\Forms\Elements\Text';
                }
                $element =  new $class($field->field_id);

                if ( $field->field_id == 'titre_adh' ) {
                    $element->setMultiOptions(Titles::getList($this->_zdb));
                }

                if ( $field->required == 1 ) {
                    $element->setRequired(true);
                }
                $element->setLabel($field->label);
                $this->_validators($element, $field);
                $elements[] = $element;
            }
            $zf->addElements($elements);

            $zf->getDecorator('HtmlTag')->setOption('tag', 'div');
            $zf->getDecorator('Fieldset')->setOption('class', 'galette_form');
            $zf->removeDecorator('DtDdWrapper');

            $this->addSubForm($zf, 'subform_' . rand(0, 50));
        }
    }

    /**
     * Append validators
     *
     * @param mixed  $element The form element we want
     * @param object $field   Field configuration
     *
     * @return void
     */
    private function _validators($element, $field)
    {
        if ( $field->max_length != ''
            && ($field->type == FieldsConfig::TYPE_STR
            || $field->type == FieldsConfig::TYPE_PASS)
        ) {
            $element->addValidator(
                'StringLength',
                false,
                array(0, $field->max_length)
            );
        }

        if ( $field->type == FieldsConfig::TYPE_PASS ) {
            
        }

        if ( $field->type == FieldsConfig::TYPE_EMAIL ) {
            
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