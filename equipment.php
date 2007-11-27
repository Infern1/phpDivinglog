<?php
/** 
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
*/

/**
 * Filename: equipment.php
 * Function: This file shows a list of dive equipment, or the details for a
 * piece of equipment, for phpDivingLog.
 * @author  Lloyd Borrett - www.borrett.id.au Rob Lensen <rob@bsdfreaks.nl>
 * @package phpdivinglog
 * @version $Rev$
 * Last Modified: $Date$
*/

$config_file = "./config.inc.php";
require_once ($config_file);

$request = new HandleRequest();
$request->set_request_uri($_SERVER['REQUEST_URI']);
$request->set_file_depth(0);
$request->handle_url();

$links = new TopLevelMenu($request);

$equipment = new Equipment();
$equipment->set_equipment_info($request);
$result = $equipment->get_equipment_info();
global $_config;
if($equipment->get_request_type() == 1){
    $t->assign('equipment_id', $equipment->get_equipment_nr());
    $links->get_std_links();
    $links->get_nav_links($request );

	$pagetitle = $_lang['equip_details_pagetitle'].$result[0]['Object'];
    $t->assign('pagetitle',$pagetitle);
//	First, Previous, Next, Last links and Dive #
	$t->assign('colspanlinks','4');
    //	Show main dive details
    $equipment->set_main_equipment_details();
    // Comments
    $equipment->set_comments();

} elseif( $equipment->get_request_type() == 0){
    $links->get_ovv_links();
    $equipment->get_equipment_overview();

} elseif($equipment->get_request_type() == 3){
    $equipment->get_overview_divers();
}
else {
echo "strange...";

}




$t->assign('base_page','equipment.php');
$t->display('equipment.tpl');
?>
