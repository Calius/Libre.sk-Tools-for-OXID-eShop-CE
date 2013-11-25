<?php
/**
* Changelog:
* 22.11.2013 - eComStyle.de | Josef Andreas Puckl - Small fixes & moved all files to modules-folder
* Module information
*/
$aModule = array(
    'id'           => 'libresksitemap',
    'title'        => 'Google Sitemap Generator',
    'description'  => 'Google Sitemap Generator',
    'version'      => '1.0',
    'thumbnail'    => '',
    'author'       => 'OXID Community',
    'email'        => '',
    'url'          => '',
	'extend'      => array(
	),
    'templates' => array(
        'libresk_sitemap.tpl'         => 'libresk-sitemap/out/admin/tpl/libresk_sitemap.tpl',       
    ),
    'files' => array(
        'libresk_sitemap'             => 'libresk-sitemap/admin/libresk_sitemap.php',
    ),
    'blocks' => array(
           
 ), 
     'settings' => array(

    ),
);