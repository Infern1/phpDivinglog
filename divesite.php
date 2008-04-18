<?php
/**
 * Filename: divesite.php
 * Function: This file shows a list of dive sites, or the details for a dive site,
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

$divesite = new Divesite();
$divesite->set_divesite_info($request);
$result = $divesite->get_divesite_info();
global $_config;

if($divesite->get_request_type() == 1){

    $t->assign('divesite_id',$divesite->divesite_nr);
    /**
     * Get the page header 
     */
    $pagetitle = $_lang['dive_site_pagetitle'].$result[0]['Place'];
    $t->assign('pagetitle',$pagetitle);
    $t->assign('colspanlinks','4');

    $links->get_std_links();
    $links->get_nav_links($request);
    //print_r($divesite);

    $divesite->set_main_divesite_details();
    $divesite->set_dives_at_location();
    $divesite->set_divesite_comments();

} elseif($divesite->get_request_type() == 0) {
    $links->get_ovv_links();
    $divesite->get_divesite_overview();
    /**
     * Get the page header
     */
    $pagetitle = $_lang['dive_sites'];
    $t->assign('pagetitle',$pagetitle);
    /*
       if ($_config['length']) {
       $MaxDepth = MetreToFeet($locationlist[$i]['MaxDepth'], 0) ."&nbsp;". $_lang['unit_length_short_imp'];
       } else {
       $MaxDepth = $locationlist[$i]['MaxDepth'] ."&nbsp;". $_lang['unit_length_short'] ;
       }
     */
} elseif($divesite->get_request_type() == 3){
    $divesite->get_overview_divers();
} else {
    echo "strange...";
}

$t->assign('base_page','divesite.php');
$t->assign('colspanlinks','4');
if($_config['embed_mode'] == TRUE){
    // Get the HTML output and send it to the requesting
    include('header.php');
    $t->display('divesite.tpl');
    include('footer.php');
} else {
    $t->display('divesite.tpl');
}

?>
