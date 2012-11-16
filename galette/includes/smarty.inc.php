<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Smarty main initialisation
 *
 * PHP version 5
 *
 * Copyright © 2006-2013 The Galette Team
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
 * @category  Main
 * @package   Galette
 *
 * @author    Frédéric Jaqcuot <unknown@unknow.com>
 * @author    Georges Khaznadar (i18n using gettext) <unknown@unknow.com>
 * @author    Johan Cwiklinski <johan@x-tnd.be>
 * @copyright 2006-2013 The Galette Team
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GPL License 3.0 or (at your option) any later version
 * @version   SVN: $Id$
 * @link      http://galette.tuxfamily.org
 * @since     Available since 0.63
 */

use \Slim\Extras\Views\Smarty as SmartyView;

if (!defined('GALETTE_ROOT')) {
       die("Sorry. You can't access directly to this file");
}

if ( !defined('GALETTE_TPL_SUBDIR') ) {
    define('GALETTE_TPL_SUBDIR', 'templates/' . $preferences->pref_theme . '/');
}

if ( !defined('GALETTE_THEME') ) {
    define('GALETTE_THEME', 'themes/' . $preferences->pref_theme . '/');
}


SmartyView::$smartyDirectory = GALETTE_SMARTY_PATH;
SmartyView::$smartyTemplatesDirectory = GALETTE_ROOT . GALETTE_TPL_SUBDIR;
SmartyView::$smartyCompileDirectory = GALETTE_COMPILE_DIR;
SmartyView::$smartyCacheDirectory = GALETTE_CACHE_DIR;

SmartyView::$smartyExtensions = array(
    GALETTE_SLIM_EXTRAS_PATH . 'Views/Extension/Smarty',
    GALETTE_ROOT . 'includes/smarty_plugins'
);

$smarty = SmartyView::getInstance();
$smarty->assign('login', $login);
$smarty->assign('logo', $logo);
$smarty->assign('template_subdir', GALETTE_THEME);
foreach ( $plugins->getTplAssignments() as $k=>$v ) {
    $smarty->assign($k, $v);
}
$smarty->assign('headers', $plugins->getTplHeaders());
$smarty->assign('plugin_actions', $plugins->getTplAdhActions());
$smarty->assign('plugin_detailled_actions', $plugins->getTplAdhDetailledActions());
$smarty->assign('jquery_dir', 'js/jquery/');
$smarty->assign('jquery_version', JQUERY_VERSION);
$smarty->assign('jquery_ui_version', JQUERY_UI_VERSION);
$smarty->assign('jquery_markitup_version', JQUERY_MARKITUP_VERSION);
$smarty->assign('scripts_dir', 'js/');
$smarty->assign('PAGENAME', basename($_SERVER['SCRIPT_NAME']));
$smarty->assign('galette_base_path', './');
/** FIXME: on certains pages PHP notice that GALETTE_VERSION does not exists
although it appears correctly*/
$smarty->assign('GALETTE_VERSION', GALETTE_VERSION);
$smarty->assign('GALETTE_MODE', GALETTE_MODE);
/** galette_lang should be removed and languages used instead */
$smarty->assign('galette_lang', $i18n->getAbbrev());
$smarty->assign('languages', $i18n->getList());
$smarty->assign('plugins', $plugins);
$smarty->assign('preferences', $preferences);
$smarty->assign('pref_slogan', $preferences->pref_slogan);
$smarty->assign('pref_theme', $preferences->pref_theme);
$smarty->assign('pref_editor_enabled', $preferences->pref_editor_enabled);
$smarty->assign('pref_mail_method', $preferences->pref_mail_method);
if ( isset($session['mailing']) ) {
    $smarty->assign('existing_mailing', true);
}

$smarty->muteExpectedErrors();

$smarty->registerClass('GaletteMail', '\Galette\Core\GaletteMail');

/**
* Return member name. Smarty cannot directly use static functions
*
* @param array $params Parameters
*
* @return Adherent::getSName
* @see Adherent::getSName
*/
function getMemberName($params)
{
    extract($params);
    return Galette\Entity\Adherent::getSName($id);
}
$smarty->registerPlugin(
    'function',
    'memberName',
    'getMemberName'
);

$s = new Galette\Entity\Status();
$statuses_list = $s->getList();

/**
 * Return status label
 *
 * @param array $params Parameters
 *
 * @return string
 */
function getStatusLabel($params)
{
    extract($params);
    global $statuses_list;
    return $statuses_list[$id];
}
$tpl->registerPlugin(
    'function',
    'statusLabel',
    'getStatusLabel'
);

