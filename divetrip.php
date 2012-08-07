<?php
/**
 * Filename: divetrip.php
 * Function: This file shows a list of dive trips, or the details for a dive trip,
 * for phpDivingLog.
 * @author  Lloyd Borrett - www.borrett.id.au Rob Lensen <rob@bsdfreaks.nl>
 * @package phpdivinglog
 * @version $Rev$
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

$divetrip = new Divetrip();
$divetrip->set_divetrip_info($request);
$result = $divetrip->get_divetrip_info();
global $_config;
if ($divetrip->get_request_type() == 1) {
    $t->assign('divetrip_id', $divetrip->divetrip_nr);
    /**
     * Get the page header 
     */
    $pagetitle = $_lang['dive_trip_pagetitle'].$result['TripName'];
    $t->assign('pagetitle',$pagetitle);
    $t->assign('colspanlinks','4');
    
    // First, Previous, Next, Last links and Trip #
    $links->get_std_links();
    $links->get_nav_links($request);
    // Show main dive trip details
    $divetrip->set_main_divetrip_details();
    // Show buddy details
    $divetrip->set_buddy_details();
    // Trip Pictures
    $divetrip->set_divetrip_pictures();
    // Trip Dives
    $divetrip->set_dives_on_trip();
    // Comments
    $divetrip->set_divetrip_comments();

} elseif ($divetrip->get_request_type() == 0) {
    $links->get_ovv_links();
    $divetrip->get_divetrip_overview();
    /**
     * Get the page header
     */
    $pagetitle = $_lang['dive_trips'];
    $t->assign('pagetitle',$pagetitle);
} elseif ($divetrip->get_request_type() == 3) {
    $divetrip->get_overview_divers();
} else {
    echo "strange...";
}

$t->assign('base_page','divetrip.php');
$t->assign('colspanlinks','4');
if ($_config['embed_mode'] == TRUE) {
    // Get the HTML output and send it to the requesting
   	if(isset($_GET['header']) && htmlspecialchars($_GET['header']) == "TRUE"){
		//$packet_id = wddx_packet_start("HEADER");
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
		$output =  $t->fetch('divetrip.tpl');
		echo $output;
    }

} else {
    $t->display('divetrip.tpl');
}

?>

