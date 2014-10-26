<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Form renderer
 *
 * PHP version 5
 *
 * Copyright © 2014 The Galette Team
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

use Aura\Html\HelperLocatorFactory;
use Aura\Html\HelperLocator;
use Aura\Input\Fieldset;

/**
 * Form renderer
 *
 * @category  Forms
 * @name      Form
 * @package   Galette
 * @author    Johan Cwiklinski <johan@x-tnd.be>
 * @copyright 2014 The Galette Team
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GPL License 3.0 or (at your option) any later version
 * @link      http://galette.tuxfamily.org
 * @since     Available since 0.8.2dev - 2014-10-25
 */
class FormRenderer
{
    /**
     * Instanciate renderer
     *
     * @param Form $form to render
     */
    public function __construct(Form $form)
    {
        $this->_form = $form;
    }

    /**
     * Renders current form
     *
     * @return string HTML content
     */
    public function render()
    {
        $factory = new HelperLocatorFactory;
        $helper = $factory->newInstance();

        $html = '';
        $html = $helper->form(
            array(
                'id'        => 'self_adherent',
                'method'    => 'post',
                'action'    => '',
            )
        );

        foreach ( $this->_form->getInputNames() as $name ) {
            $input = $this->_form->get($name);

            if ( $input instanceof Fieldset ) {
                $html .= $helper->tag(
                    'fieldset', [
                        'class' => 'galette_form'
                    ]
                );

                $html .= $helper->tag('legend');
                $html .= $name;
                $html .= $helper->tag('/legend');

                $html .= $this->renderFieldset(
                    $input->get('galette_fieldset'),
                    $helper
                );

                $html .= $helper->tag('/fieldset');
            } else {
                //well, this is not possible.
                throw new \RuntimeException(
                    'Form can only contains fieldsets.'
                );
            }
        }

        $html .= $helper->tag('/form');

        return $html;
    }

    /**
     * Renders a field
     *
     * @param Fieldset      $fieldset The fieldset
     * @param HelperLocator $helper   Helper instance
     *
     * @return string
     */
    protected function renderFieldset(Fieldset $fieldset, HelperLocator $helper)
    {
        $html = $helper->tag('div');
        foreach ( $fieldset->getInputNames() as $name ) {
            $input = $fieldset->get($name);
            $label = $this->_form->getLabel($name);

            if ( $input['type'] !== 'hidden' ) {
                $html .= $helper->tag('p');

                if ( $label !== null ) {
                    if ( $input['type'] === 'radio' ) {
                        $html .= $helper->tag('span');
                        $html .= $label;
                        $html .= $helper->tag('/span');
                    } else {
                        $html .= $helper->label(
                            $label,
                            ['for' => $name]
                        );
                    }
                }
            }

            $html .= $helper->input(
                array(
                    'type'    => $input['type'],
                    'name'    => $input['name'],
                    'value'   => $input['value'],
                    'attribs' => $input['attribs'],
                    'options' => $input['options']
                )
            );

            if ( $input['type'] !== 'hidden' ) {
                $html .= $helper->tag('/p');
            }
        }
        $html .= $helper->tag('/div');
        return $html;
    }
}
