<?php
/**
 * Filename: divesummary.php
 * Function: This file displays some short details from phpDivingLog.
 * Version:  phpDivingLog v1.9 2007-03-07
 * @author  Lloyd Borrett - www.borrett.id.au Rob Lensen <rob@bsdfreaks.nl>
 * @package phpdivinglog
 * @version $Rev$
 * Last Modified: $date$
 * 
 * @copyright (C) 2006 Lloyd Borrett - http://www.borrett.id.au
 * 
 * Adapted from code by Olaf van Zandwijk - http://enschede.vanzandwijk.net
 * 
 * For use with Diving Log by Sven Knoch - http://www.divinglog.de
* 
*/

$config_file = "./config.inc.php";
require_once ($config_file);

/**
 * Dive Statistics
 */
$request = new HandleRequest();
$request->set_request_uri($_SERVER['REQUEST_URI']);
$request->set_file_depth(0);
$request->handle_url();
$links = new TopLevelMenu($request);

/**
 * Create a new class Divestats with info from the HandleRequest class 
 */
$divestats = new Divestats();
$divestats->set_divestats_info($request);
$result = $divestats->get_divestats_info();
$divestats->get_lastdive_info();

global $_config;

if($request->get_multiuser()){
    $user_id = $request->get_user_id();
    if(!empty($user_id)){
        $links->get_ovv_links();
        // Dive Statistics
        $divestats->set_all_statistics();
        $divestats->set_lastdive_info();
        // User Information
        $user = new User();
        $user->set_user_id($request->get_user_id());
        // Application Information
        $ver = new AppInfo($request);
        $ver->SetAppInfo();
   } else {
        $t->assign('no_id',1);
    }
} else {
    // Display the Dive List
    $links->get_ovv_links();
    // Dive Statistics
    $divestats->set_all_statistics();
    $divestats->set_lastdive_info();
    // Equipment Service Information
    $service = new Equipment($request);
    $service->set_equipment_service_info();
    // Application Information
    $ver = new AppInfo($request);
    $ver->SetAppInfo();
}

$t->display('divesummary.tpl');


?>
