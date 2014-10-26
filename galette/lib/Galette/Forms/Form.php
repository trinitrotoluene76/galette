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
 * @copyright 2014 The Galette Team
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GPL License 3.0 or (at your option) any later version
 * @version   SVN: $Id$
 * @link      http://galette.tuxfamily.org
 * @since     Available since 0.8.2dev - 2014-10-25
 */

namespace Galette\Forms;

use Galette\Entity\Adherent;
use Galette\Entity\FieldsConfig;
use Galette\Entity\Status;
use Galette\Repository\Titles;
use Galette\Forms\Helpers\FormRadio;
use Galette\Forms\Helpers\FormSelect;

use Zend\Form\Form as ZForm;
use Zend\Form\Fieldset;

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
class Form extends ZForm
{
    private $_table;
    private $_zdb;
    private $_i18n;

    /**
     * Constructor
     *
     * Registers form view helper as decorator
     *
     * @param Db     $zdb     Database instance
     * @param I18n   $i18n    I18n instance
     * @param string $table   Table name
     * @param mixed  $options Options
     *
     * @return void
     */
    public function __construct($zdb, $i18n, $table, $options = null)
    {
        $this->_zdb = $zdb;
        $this->_i18n = $i18n;
        $this->_table = $table;

        parent::__construct('');

        $this->setAttribute('method', 'post');
        $this->setAttribute('id', $this->_table . '_form');
        $this->setAttribute('action', '');

        /*parent::__construct($options);
        $this->setAttrib('id', $this->_table . '_form');
        $view = new \Zend_View();
        $this->setView($view);
        $helper = new FormRadio();
        $view->registerHelper($helper, 'gformRadio');
        $helper = new FormSelect();
        $view->registerHelper($helper, 'gformSelect');*/
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
            $fieldset = new Fieldset(
                $elt->label,
                array(
                    'label' => $elt->label
                )
            );
            $fieldset->setattribute('class', 'galette_form');

            foreach ( $elt->elements as $field ) {
                switch ( $field->type ) {
                case FieldsConfig::TYPE_HIDDEN:
                    $class = '\Zend\Form\Element\Hidden';
                    break;
                case FieldsConfig::TYPE_BOOL:
                    $class = '\Zend\Form\Element\Checkbox';
                    break;
                case FieldsConfig::TYPE_DATE:
                    $class = '\Zend\Form\Element\Date';
                    break;
                case FieldsConfig::TYPE_RADIO:
                    $class = '\Zend\Form\Element\Radio';
                    break;
                case FieldsConfig::TYPE_SELECT:
                    $class = '\Zend\Form\Element\Select';
                    break;
                default:
                case FieldsConfig::TYPE_STR:
                    $class = '\Zend\Form\Element\Text';
                }
                $element =  new $class(
                    $field->field_id,
                    array(
                        'label' => $field->label,
                        'label_options' => array(
                            'always_wrap' => false
                        )
                    )
                );
                $element->setAttribute('id', $field->field_id);

                if ( $field->field_id == 'titre_adh' ) {
                    $element->setValueOptions(Titles::getArrayList($this->_zdb));
                }

                if ( $field->field_id == 'sexe_adh' ) {
                    $element->setValueOptions(
                        array(
                            Adherent::NC    => _T("Unspecified"),
                            Adherent::MAN   => _T("Man"),
                            Adherent::WOMAN => _T("Woman")
                        )
                    );
                }

                if ( $field->field_id == 'pref_lang' ) {
                    $element->setValueOptions(
                        $this->_i18n->getArrayList()
                    );
                }

                if ( $field->field_id == 'id_statut' ) {
                    $status = new Status();
                    $element->setValueOptions(
                        $status->getList()
                    );
                }

                /*if ( $field->required == 1 ) {
                    $element->setRequired(true);
                }
                $element->setLabel($field->label);
                $this->_validators($element, $field);*/
                $fieldset->add($element);
            }
            /*$zf->addElements($elements);

            $zf->getDecorator('HtmlTag')->setOption('tag', 'div');
            $zf->getDecorator('Fieldset')->setOption('class', 'galette_form');
            $zf->removeDecorator('DtDdWrapper');

            $this->addSubForm($zf, 'subform_' . rand(0, 50));*/
            $this->add($fieldset);
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
    /*private function _validators($element, $field)
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
    }*/

    /**
     * Loads default decorators. Change display according to
     * Galette's theming conventions.
     *
     * @return void
     */
    /*public function loadDefaultDecorators()
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
    }*/

    /**
     * Assures form rendering
     *
     * @return string
     */
    public function render()
    {
        $fhelper = new \Zend\Form\View\Helper\Form();

        $html = '';

        $this->prepare();

        $zview = new \Zend\View\Renderer\PhpRenderer();
        $plugins = $zview->getHelperPluginManager();
        $config  = new \Zend\Form\View\HelperConfig;
        $config->configureServiceManager($plugins);
        $formLabel = $zview->plugin('formLabel');
        $formRow = $zview->plugin('formRow');
        $formCollection = $zview->plugin('formCollection');

        $html = $fhelper->openTag($this);

        $fieldsets = $this->getFieldsets();
        foreach ( $fieldsets as $fieldset ) {
            $html .= $formCollection->render($fieldset);
        }

        $html .= $fhelper->closeTag();
        return $html;
    }
}
