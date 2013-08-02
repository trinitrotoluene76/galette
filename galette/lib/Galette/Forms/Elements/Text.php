<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Text form element
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

namespace Galette\Forms\Elements;

/** @ignore */
require_once 'Zend/Form/Element/Text.php';

/**
 * Text form element
 *
 * @category  Forms
 * @name      Text
 * @package   Galette
 * @author    Johan Cwiklinski <johan@x-tnd.be>
 * @copyright 2012-2013 The Galette Team
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GPL License 3.0 or (at your option) any later version
 * @link      http://galette.tuxfamily.org
 * @since     Available since 0.71dev - 2012-02-09
 */
class Text extends \Zend_Form_Element_Text
{

    /**
     * Set eleemnt as required. That version adds HTML5 required attribute
     *
     * @param boolean $flag flag
     *
     * @return see Zend_Form_Element_Text::setRequired
     */
    public function setRequired($flag = true)
    {
        /** set HTML5 required attribute */
        if ( $flag === true ) {
            $this->setAttrib('required', 'required');
        }
        return parent::setRequired($flag);
    }

    /**
     * Loads default decorators. Change display according to
     * Galette's theming conventions.
     *
     * @return void
     */
    public function loadDefaultDecorators()
    {
        if ( $this->loadDefaultDecoratorsIsDisabled() ) {
            return;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->addDecorator('ViewHelper')
                ->addDecorator(
                    new \Zend_Form_Decorator_Label(
                        array(
                            'escape' => false,
                            'class' => 'galette_form_label'
                        )
                    )
                )->addDecorator(
                    new \Zend_Form_Decorator_HtmlTag(
                        array(
                            'tag' => 'p',
                            'class' => 'galette_form_line'
                        )
                    )
                );
        }
    }
}
