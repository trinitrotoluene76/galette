<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Galette installation, database initialization
 *
 * PHP version 5
 *
 * Copyright © 2013 The Galette Team
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
 * @category  Core
 * @package   Galette
 *
 * @author    Johan Cwiklinski <johan@x-tnd.be>
 * @copyright 2013 The Galette Team
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GPL License 3.0 or (at your option) any later version
 * @version   SVN: $Id$
 * @link      http://galette.tuxfamily.org
 * @since     Available since 0.7.4dev - 2013-01-12
 */

use Galette\Core\Install as GaletteInstall;
use Galette\Core\Db as GaletteDb;


//before doing anything else, we'll have to convert data to UTF-8
//required since 0.7dev (if we're upgrading, 'f course)
if ( $install->isUpgrade() ) {
    //FIXME: maybe we can do that only on 0.7 upgrades,
    //to save time? (methods are safe if rerun)
    $zdb->convertToUTF($table_prefix);
}

//ok, let's run the scripts!
$db_installed = $install->executeScripts($zdb);
?>
                <h2><?php echo $install->getStepTitle(); ?></h2>
<?php
if ( $db_installed === false ) {
    echo '<p id="errorbox">' . _T("Database has not been installed!") . '</p>';
} else {
    echo '<p id="infobox">' . _T("Database has been installed :)")  . '</p>';
}
?>
                <ul class="leaders">
<?php
foreach ( $install->getDbInstallReport() as $r  ) {
    ?>
                    <li>
                        <span><?php echo $r['message']; ?></span>
                        <span><?php echo $install->getValidationImage($r['res']); ?></span>
                    </li>
    <?php
}
?>
                </ul>

                <form action="installer.php" method="POST">
                    <p id="btn_box">
<?php
if ( !$db_installed ) {
    ?>
                        <input type="submit" id="retry_btn" value="<?php echo _T("Retry"); ?>"/>
    <?php
}
?>

                        <input id="next_btn" type="submit" value="<?php echo _T("Next step"); ?>"<?php if ( !$db_installed ) { echo ' disabled="disabled"'; } ?>/>
<?php
if ( $db_installed ) {
    ?>
                        <input type="hidden" name="install_dbwrite_ok" value="1"/>
    <?php
}

if ( !$db_installed ) {
    //once DB is installed, that does not make sense to go back
    ?>
                        <input type="submit" id="btnback" name="stepback_btn" value="<?php echo _T("Back"); ?>"/>
    <?php
}
?>
                    </p>
                </form>