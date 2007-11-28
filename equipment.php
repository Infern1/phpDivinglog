<?php
/** 
 * Filename: equipment.php
 * Function: This file shows a list of dive equipment, or the details for a
 * piece of equipment, for phpDivingLog.
 * @author  Lloyd Borrett - www.borrett.id.au Rob Lensen <rob@bsdfreaks.nl>
 * @package phpdivinglog
 * @version $Rev$
 * Last Modified: $Date$
 * 
 * @copyright <p>(C) 2006 Lloyd Borrett - http://www.borrett.id.au
 * 
 * Adapted from code by Olaf van Zandwijk - http://enschede.vanzandwijk.net
 * 
 * For use with Diving Log by Sven Knoch - http://www.divinglog.de</p>
 * 
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
