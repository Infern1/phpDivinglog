<?php
/**
 * Filename: divecountry.php
 * Function: This file shows a list of dive countries, or the details for a dive country,
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

$divecountry = new Divecountry();
$divecountry->set_divecountry_info($request);
$result = $divecountry->get_divecountry_info();
global $_config;
if ($divecountry->get_request_type() == 1) {
    $t->assign('divecountry_id', $divecountry->divecountry_nr);
    /**
     * Get the page header 
     */
    $pagetitle = $_lang['country_details_pagetitle'].$result['Country'];
    $t->assign('pagetitle',$pagetitle);
    $t->assign('colspanlinks','4');
    
    // First, Previous, Next, Last links and Country #
    $links->get_std_links();
    $links->get_nav_links($request);
    // Show main dive country details
    $divecountry->set_main_divecountry_details();
    // Country Trips
    $divecountry->set_trips_in_country();
    // Country Sites
    $divecountry->set_sites_in_country();
    // Country Dives
    $divecountry->set_dives_in_country();
    // Comments
    $divecountry->set_divecountry_comments();

} elseif ($divecountry->get_request_type() == 0) {
    $links->get_ovv_links();
    $divecountry->get_divecountry_overview();
    /**
     * Get the page header
     */
    $pagetitle = $_lang['dive_countries'];
    $t->assign('pagetitle',$pagetitle);
} elseif ($divecountry->get_request_type() == 3) {
    $divecountry->get_overview_divers();
} else {
    echo "strange...";
}

$t->assign('base_page','divecountry.php');
$t->assign('colspanlinks','4');
if ($_config['embed_mode'] == TRUE) {
    // Get the HTML output and send it to the requesting
    include('header.php');
    $t->display('divecountry.tpl');
    include('footer.php');
} else {
    $t->display('divecountry.tpl');
}

?>

