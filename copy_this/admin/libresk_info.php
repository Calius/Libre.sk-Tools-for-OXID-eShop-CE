<?php
/**
 *    This file is part of Libre.sk Tools for OXID eShop Community Edition.
 *
 *    Libre.sk Tools is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    Libre.sk Tools is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with Libre.sk Tools.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      https://projects.oxidforge.org/projects/libresk-sitemap/
 * @package   commons
 * @copyright (C) Dominik Smatana <ds@libre.sk> 2012
 */

/**
 * Info page for Libre.sk Tools
 * Admin Menu: Libre.sk Tools -> About
 * @package commons
 */
class libresk_info extends oxAdminView
{

    public function render()
    {
        $myConfig  = $this->getConfig();

        parent::render();

        $oSmarty = oxUtilsView::getInstance()->getSmarty();
		$oSmarty->assign( "oViewConf", $this->_aViewData["oViewConf"]);
		$oSmarty->assign( "shop", $this->_aViewData["shop"]);            

		echo $oSmarty->fetch("libresk_info.tpl");

		echo('<p><a href="https://projects.oxidforge.org/projects/libresk-commons/">https://projects.oxidforge.org/projects/libresk-commons/</a></p>');
		echo('<p><a href="https://projects.oxidforge.org/projects/libresk-sitemap/">https://projects.oxidforge.org/projects/libresk-sitemap/</a></p>');
		
		oxUtils::getInstance()->showMessageAndExit( "" );
    }

}

