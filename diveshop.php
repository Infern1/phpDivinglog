<?php 
/**
 * Filename: diveshop.php
 * Function: This file shows a list of dive shops, or the details for a dive shop,
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

$diveshop = new Diveshop();
$diveshop->set_diveshop_info($request);
$result = $diveshop->get_diveshop_info();
global $_config;
if ($diveshop->get_request_type() == 1) {
    $t->assign('diveshop_id', $diveshop->diveshop_nr);
    /**
     * Get the page header 
     */
    if ($result['ShopType'] != '') {
        $pagetitle = $result['ShopType'].' - '.$result['ShopName'];
    } else {
        $pagetitle = $_lang['dive_shop_pagetitle'].$result['ShopName'];
    }
    $t->assign('pagetitle',$pagetitle);
    $t->assign('colspanlinks','4');
    
    // First, Previous, Next, Last links and Dive #
    $links->get_std_links();
    $links->get_nav_links($request);
    // Show main dive shop details
    $diveshop->set_main_diveshop_details();
    $diveshop->set_diveshop_pictures();
    // Shop Dives
    $diveshop->set_dives_with_shop();
    // Shop Trips
    $diveshop->set_trips_with_shop();
    // Comments
    $diveshop->set_diveshop_comments();

} elseif ($diveshop->get_request_type() == 0) {
    $links->get_ovv_links();
    $diveshop->get_diveshop_overview();
    /**
     * Get the page header
     */
    $pagetitle = $_lang['dive_shops'];
    $t->assign('pagetitle',$pagetitle);

} elseif ($diveshop->get_request_type() == 3) {
    $diveshop->get_overview_divers();

} else {
    echo "strange...";
}

$t->assign('base_page','diveshop.php');
$t->assign('colspanlinks','4');
if ($_config['embed_mode'] == TRUE) {
    // Get the HTML output and send it to the requesting
    include('header.php');
    $t->display('diveshop.tpl');
    include('footer.php');
} else {
    $t->display('diveshop.tpl');
}

?>

