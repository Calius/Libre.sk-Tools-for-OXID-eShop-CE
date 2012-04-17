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
 * @package   sitemap
 * @copyright (C) Dominik Smatana <ds@libre.sk> 2012
 */

/**
 * Generates/updates Google Sitemap files for all active frontend languages.
 * Admin Menu: Libre.sk Tools -> Google Sitemap
 * @package sitemap
 */
class libresk_sitemap extends oxAdminView
{

    public function render()
    {
        $myConfig  = $this->getConfig();

        parent::render();

        $oAuthUser = oxNew( 'oxuser' );
        $oAuthUser->loadAdminUser();
        $blisMallAdmin = $oAuthUser->oxuser__oxrights->value == "malladmin";

        if ( $blisMallAdmin ) {
			// user is shop admin

            $oSmarty = oxUtilsView::getInstance()->getSmarty();
			$oSmarty->assign( "oViewConf", $this->_aViewData["oViewConf"]);
			$oSmarty->assign( "shop", $this->_aViewData["shop"]);            

			echo $oSmarty->fetch("libresk_sitemap.tpl");


			// TODO lastmod
			$lastmod = date('Y-m-d');

			// Let's generate sitemap for all active frontend languages
			$aLanguages = oxLang::getInstance()->getLanguageArray();
			foreach ($aLanguages as $oLang) {
				if ($oLang->active == '1') {
					// this language is active
					$idLang = $oLang->id;

					$all = 0;

					$sitemap = 'feeds/sitemap-'.$oLang->abbr.'.xml';
					$file = $myConfig->getOutDir().'../'.$sitemap;
					echo('<p>Creating sitemap for '.$oLang->name.' language (ID = '.$idLang.')</p>');

					if (file_exists($file)) {
						unlink($file);
					}
					$output = fopen($file, 'w');

					fwrite($output, '<?xml version="1.0" encoding="UTF-8" ?>');
					fwrite($output, "\n");

					fwrite($output, '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">');
					fwrite($output, "\n");


					// MAIN PAGE
					$i = 0;
					$i++;
					$loc = $myConfig->getShopURL($idLang);
					$test = $this->writeToXML($output, $loc, $lastmod, '1.0');
					echo('<p>Main page: '.$i.'</p>');
					$all = $all + $i;

					// CMS PAGES
					$i = 0;
					echo('<p>CMS pages: ');
					$oPages = oxNew("oxContentList");
					$oPages->getList();
					foreach ($oPages as $oPage ) {
						$oPageL = oxNew('oxContent');
						$oPageL->loadInLang($idLang, $oPage->oxcontents__oxid->value);
						if ($oPageL->oxcontents__oxfolder->rawValue == 'CMSFOLDER_USERINFO') {
							if ($oPageL->oxcontents__oxloadid->rawValue == 'oxstartwelcome') {
								// no need to generate URL for start CMS page
							} else {
								$i++;
								$loc = $oPageL->getLink($idLang);
								$this->writeToXML($output, $loc, $lastmod, '0.5');
							}
						}
					}
					echo($i.'</p>');
					$all = $all + $i;

					// CATEGORIES
					$i = 0;
					echo('<p>Categories: ');
					$oCategories = oxNew("oxCategoryList");
					$oCategories->getList();
					foreach ($oCategories as $oCategory ) {
						$i++;
						$oCategoryL = oxNew('oxCategory');
						$oCategoryL->loadInLang($idLang, $oCategory->oxcategories__oxid->value); 
						$loc = $oCategoryL->getLink($idLang);
						$this->writeToXML($output, $loc, $lastmod, '0.7');
					}
					echo($i.'</p>');
					$all = $all + $i;


					// DISTRIBUTORS
					$i = 0;
					echo('<p>Distributors: ');
					$oVendors = oxNew("oxVendorList");
					$oVendors->getList();
					foreach ($oVendors as $oVendor) {
						$i++;
						$oVendorL = oxNew('oxVendor');
						$oVendorL->loadInLang($idLang, $oVendor->oxvendor__oxid->value); 
						$loc = $oVendorL->getLink($idLang);
						$this->writeToXML($output, $loc, $lastmod, '0.5');
					}
					echo($i.'</p>');
					$all = $all + $i;


					// TAGS
					$i = 0;
					echo('<p>Tags: ');
					$oTags = oxNew("oxTagCloud");
					$aTags = $oTags->getCloudArray();
					if (is_array($aTags)) {
						foreach ($aTags as $tag => $null ) {
							$i++;
							$loc = $oTags->getTagLink($tag);
							$this->writeToXML($output, $loc, $lastmod, '0.5');
						}
					}
					echo($i.'</p>');
					$all = $all + $i;


					// ARTICLES
					$i = 0;
					echo('<p>Articles: ');
					$oArticles = oxNew("oxArticleList");
					$oArticles->getList();
					foreach ($oArticles as $oArticle ) {
						$i++;
						$loc = $oArticle->getMainLink($idLang);
						$this->writeToXML($output, $loc, $lastmod, '0.5');
					}
					echo($i.'</p>');
					$all = $all + $i;

					fwrite($output, '</urlset>');
					fwrite($output, "\n");

					$test = fclose($output);

					$url = $myConfig->getShopURL($idLang).$sitemap;
					echo('<p>Generated '.$all.' URLs</p>');
					echo('<p><a href="'.$url.'">'.$url.'</a></p>');
					echo('<br>');
				}
			}
			echo('<p>DONE</p>');

			echo("<p>Don't forget to submit your new sitemap(s) to ".'<a href="https://www.google.com/webmasters/tools/">Google Webmaster Tools</a>.</p>');

            oxUtils::getInstance()->showMessageAndExit( "" );
        } else {
			// user is not shop admin
            return oxUtils::getInstance()->showMessageAndExit( "Access denied!" );
        }
    }


	function writeToXML($output, $loc, $lastmod, $priority) {

//echo($loc.'<br>');

		fwrite($output, "\t<url>");
		fwrite($output, "\n");

		fwrite($output, "\t\t<loc>".$loc.'</loc>');
		fwrite($output, "\n");

		fwrite($output, "\t\t<lastmod>".$lastmod.'</lastmod>');
		fwrite($output, "\n");

		//TODO changefreq
		fwrite($output, "\t\t<changefreq>weekly</changefreq>");
		fwrite($output, "\n");

		fwrite($output, "\t\t<priority>".$priority."</priority>");
		fwrite($output, "\n");

		fwrite($output, "\t</url>");
		fwrite($output, "\n");
	}

}

