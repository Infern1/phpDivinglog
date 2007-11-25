<?php
/**
 * Filename: divesite.php
 * Function: This file shows a list of dive sites, or the details for a dive site,
 * for phpDivingLog.
 * @author  Lloyd Borrett - www.borrett.id.au Rob Lensen <rob@bsdfreaks.nl>
 * @package phpdivinglog
 * Last Modified: 2006-08-21
 * /***************************************************************************
 * 
 * phpDivingLog
 * Copyright (C) 2006 Lloyd Borrett - http://www.borrett.id.au
 * 
 * Adapted from code by Olaf van Zandwijk - http://enschede.vanzandwijk.net
 * 
 * For use with Diving Log by Sven Knoch - http://www.divinglog.de
 * 
 * This file and all dependant and otherwise related files are part of phpDivingLog.
 * 
 * phpDivingLog is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * phpDivingLog is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * /**************************************************************************
 */

// include_once misc file
$config_file = "./config.inc.php";
require_once ($config_file);

$request = new HandleRequest();
$request->set_request_uri($_SERVER['REQUEST_URI']);
$request->set_file_depth(0);
$request->handle_url();

$links = new TopLevelMenu($request);

$divesite = new Divesite();
$divesite->set_divesite_info($request);
$result = $divesite->get_divesite_info();
global $_config;

if($divesite->get_request_type() == 1){

    $t->assign('divesite_id',$divesite->divesite_nr);
    //	Get the page header
    $pagetitle = $_lang['dive_site_pagetitle'].$result[0]['Place'];
    $t->assign('pagetitle',$pagetitle);
    $t->assign('colspanlinks','4');

    $links->get_std_links();
    $links->get_nav_links($request);
    //print_r($divesite);

    $divesite->set_main_divesite_details();
    $divesite->set_dives_at_location();
    $divesite->set_divesite_comments();

} elseif($divesite->get_request_type() == 0) {
    $links->get_ovv_links();
    $divesite->get_divesite_overview();
    //	Get the page header
    $pagetitle = $_lang['dive_sites'];
    $t->assign('pagetitle',$pagetitle);
    /*
       if ($_config['length']) {
       $MaxDepth = MetreToFeet($locationlist[$i]['MaxDepth'], 0) ."&nbsp;". $_lang['unit_length_short_imp'];
       } else {
       $MaxDepth = $locationlist[$i]['MaxDepth'] ."&nbsp;". $_lang['unit_length_short'] ;
       }
     */
} elseif($divesite->get_request_type() == 3){
    $divesite->get_overview_divers();
} else {
    echo "strange...";
}

$t->assign('base_page','divesite.php');
$t->assign('colspanlinks','4');
$t->display('divesite.tpl');
?>
