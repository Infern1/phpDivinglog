<?php
/** 
 * Filename: divestats.php
 * Function: This file displays the dive statistics for phpDivingLog.
 * @author  Lloyd Borrett - www.borrett.id.au Rob Lensen <rob@bsdfreaks.nl>
 * @package phpdivinglog
 * @version  $Rev$
 * Last Modified: $Date$
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
global $_config;

if ($request->get_multiuser()) {
    $user_id = $request->get_user_id();
    if (!empty($user_id)) {
        // Display the Dive List
        $links->get_ovv_links();
        // Get the page header
        $pagetitle = $_lang['dive_stats'];
        $t->assign('pagetitle',$pagetitle);
        $t->assign('colspanlinks','4');
        // Dive Statistics
        $divestats->set_all_statistics();
        $app_info = new AppInfoi($request);
        $app_info->SetAppInfo(); 
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
    $pagetitle = $_lang['dive_stats'];
    $t->assign('pagetitle',$pagetitle);
    $t->assign('colspanlinks','4');
    // Dive Statistics
    $divestats->set_all_statistics();
    $app_info = new AppInfo($request);
    $app_info->SetAppInfo(); 
}


if ($_config['embed_mode'] == TRUE) {
    // Get the HTML output and send it to the requesting
   	if(isset($_GET['header']) && htmlspecialchars($_GET['header']) == "TRUE"){
		//$packet_id = wddx_packet_start("HEADER");
		$t->assign('piechart_display' , 1);

        $output = $t->fetch('header_meta.tpl');
		$output .= $t->fetch('header_css.tpl');
		$output .= $t->fetch('header_script.tpl');
		$packet = wddx_serialize_value($output);
		echo $packet;
	} elseif( isset($_GET['title']) && htmlspecialchars($_GET['title']) == "TRUE"){
			$output = $t->fetch('header_title.tpl');
			$packet = wddx_serialize_value($output);
			echo $packet;
	} elseif( isset($_GET['footer']) && htmlspecialchars($_GET['footer']) == "TRUE"){
		//$output = $t->fetch('footer.tpl');
		//$packet = wddx_serialize_value($output);
		//echo $packet;
	}	else {
		$output =  $t->fetch('divestats.tpl');
		echo $output;
    }


} else {
    $t->display('divestats.tpl');
}
?>
