<?php
/** 
 * Filename: divegallery.php
 * Function: This file displays all images available to phpDivingLog.
 * @author  Rob Lensen <rob@bsdfreaks.nl>
 * @package phpdivinglog
 * @version  $Rev: 172 $
 * Last Modified: $Date: 2007-11-30 15:17:52 +0100 (Fri, 30 Nov 2007) $
 *
 * For use with Diving Log by Sven Knoch - http://www.divinglog.de
 *
*/

$config_file = "./config.inc.php";
require_once ($config_file);

$request = new HandleRequest();
$request->set_request_uri($_SERVER['REQUEST_URI']);
$request->set_file_depth(0);
$request->handle_url();
$links = new TopLevelMenu($request);

/**
 * Create a new class Divestats with info from the HandleRequest class 
 */
$divegallery = new DivePictures();
$divegallery->set_divegallery_info($request);
$result = $divegallery->get_divegallery_info();
global $_config;

if($request->get_multiuser()){
    $user_id = $request->get_user_id();
    if(!empty($user_id)){
        // Display the Dive List
        $links->get_ovv_links();
        // Get the page header
        $pagetitle = $_lang['dive_gallery'];
        $t->assign('pagetitle',$pagetitle);
        $t->assign('colspanlinks','4');
        // Dive Statistics
        $divegallery->set_all_dive_pictures();
        $user = new User();
        $user->set_user_id($request->get_user_id());
        set_config_table_prefix($user->get_table_prefix());
        $dbinfo = parse_mysql_query('dbinfo.sql');
        reset_config_table_prefix();
    } else {
        $divestats->get_overview_divers();
    }
} else {
    // Display the Dive List
    $links->get_ovv_links();
    // Get the page header
    $pagetitle = $_lang['dive_gallery'];
    $t->assign('pagetitle',$pagetitle);
    $t->assign('colspanlinks','4');
    // Dive Gallery
    $divegallery->set_all_dive_pictures();
    
    //$dbinfo = parse_mysql_query('dbinfo.sql');
}

$t->display('divegallery.tpl');

?>
