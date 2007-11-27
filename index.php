<?php
/**
 * Filename: index.php
 * Function: This file is the main root file for phpDivingLog.
 * @author  Lloyd Borrett - www.borrett.id.au Rob Lensen <rob@bsdfreaks.nl>
 * @package phpdivinglog
 * @version $Rev$
 * Last Modified: $Date$
*/

/*
phpDivingLog
Copyright (C) 2006 Lloyd Borrett - http://www.borrett.id.au

Adapted from code by Olaf van Zandwijk - http://enschede.vanzandwijk.net

For use with Diving Log by Sven Knoch - http://www.divinglog.de

This file and all dependant and otherwise related files are part of phpDivingLog.

phpDivingLog is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

phpDivingLog is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

*/


// include_once misc file
$config_file = "./config.inc.php";
require_once ($config_file);

$request = new HandleRequest();
$request->set_request_uri($_SERVER['REQUEST_URI']);
$request->set_file_depth(0);
$request->handle_url();
//print_r($request);

$links = new TopLevelMenu($request);

/**
 * Create a new class Divelog with info from the HandleRequest class 
 */
$divelog = new Divelog();
$divelog->set_divelog_info($request);
$result = $divelog->get_divelog_info();
global $_config;
//print_r($divelog);
//print_r($request);
if ($divelog->get_request_type() == 1) {
    //	We have a dive number, so display the dive details
    $links->get_std_links();/*{{{*/
    $links->get_nav_links($request );

    //	Get the page header
    $pagetitle = $_lang['dive_details_pagetitle'].$result[0]['Number'];
    $t->assign('colspanlinks','5');	
    //include($_config['header']);
    // Show Dive Information
    //	Show main dive details
    $divelog->set_main_dive_details();
    //	Show buddy details
    $divelog->set_buddy_details();
    // Dive Pictures
    $divelog->set_dive_pictures();
    // Dive Profile
    $divelog->set_dive_profile();
    // Conditions
    $divelog->set_dive_conditions();
    // Breath details
    $divelog->set_breathing_details();
    //Dive details
    $divelog->set_dive_details();
    // Equipment
    $divelog->set_equipment();
    // Comments
    $divelog->set_comments();
    $t->assign('links',$links);/*}}}*/
}elseif($divelog->get_request_type() == 0){
    $links->get_ovv_links();
    $divelog->get_dive_overview();
} elseif($divelog->get_request_type() == 3){
    $divelog->get_overview_divers();
} else{
    echo "shouldn't come here!";
    exit;
}
// Get the page footer
//include ($_config['footer_index']);
$t->assign('colspanlinks','5');
if($_config['embed_mode'] == TRUE){
    // Get the HTML output and send it to the requesting
    $output =  $t->fetch('index.tpl');
    echo $output;
} else {
    $t->display('index.tpl');
}

?>
