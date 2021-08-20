<?php
/**
 * Filename: divecity.php
 * Function: This file shows a list of dive city/island, or the details for a dive city/island,
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

$divecity = new Divecity();
$divecity->set_divecity_info($request);
$result = $divecity->get_divecity_info();
global $_config;
if ($divecity->get_request_type() == 1) {
    $t->assign('divecity_id', $divecity->divecity_nr);
    /**
     * Get the page header 
     */
    $pagetitle = $_lang['city_details_pagetitle'].$result['City'];
    $t->assign('pagetitle',$pagetitle);
    $t->assign('colspanlinks','4');
    
    // First, Previous, Next, Last links and City #
    $links->get_std_links();
    $links->get_nav_links($request);
    // Show main dive city details
    $divecity->set_main_divecity_details();
    // City Sites
    $divecity->set_sites_in_city();
    // City Dives
    $divecity->set_dives_in_city();
    // Comments
    $divecity->set_divecity_comments();

} elseif ($divecity->get_request_type() == 0) {
    $links->get_ovv_links();
    $divecity->get_divecity_overview();
    /**
     * Get the page header
     */
    $pagetitle = $_lang['dive_cities'];
    $t->assign('pagetitle',$pagetitle);
} else {
    echo "strange...";
}

$t->assign('base_page','divecity.php');
$t->assign('colspanlinks','4');
if ($_config['embed_mode'] == TRUE) {
    // Get the HTML output and send it to the requesting
    include('header.php');
    $t->display('divecity.tpl');
    include('footer.php');
} else {
    $t->display('divecity.tpl');
}
