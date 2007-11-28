<?php
/** 
 * Filename: divestats.php
 * Function: This file displays the dive statistics for phpDivingLog.
 * @author  Lloyd Borrett - www.borrett.id.au Rob Lensen <rob@bsdfreaks.nl>
 * @package phpdivinglog
 * @version  $Rev$
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

/**
 * Create a new class Divestats with info from the HandleRequest class 
 */
$divestats = new Divestats();
$divestats->set_divestats_info($request);
$result = $divestats->get_divestats_info();
global $_config;

if($request->get_multiuser()){
    $user_id = $request->get_user_id();
    if(!empty($user_id)){
        // Display the Dive List
        $links->get_ovv_links();
        // Get the page header
        $pagetitle = $_lang['dive_stats'];
        $t->assign('pagetitle',$pagetitle);
        $t->assign('colspanlinks','4');
        // Dive Statistics
        $divestats->set_all_statistics();
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
    
    //$dbinfo = parse_mysql_query('dbinfo.sql');
}
/*
echo "<table class=\"divetable\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\">\n";

echo " <tr><td class=\"spacing\">&nbsp;</td></tr>\n";

echo " <tr class=\"divetitle\">\n";
echo "  <td>". $_lang['poweredby'] ."\n";
echo "   <a href=\"http://www.divinglog.de/\" target=\"_blank\"\n";
echo "   title=\"Diving Log web site\">". $dbinfo[0]['PrgName'] ."</a>\n";
echo "   ". $dbinfo[0]['DBVersion'] .$_lang['and'] ."\n";
echo "   <a href=\"http://www.borrett.id.au/interests/phpdivinglog.htm\"\n";
echo "   target=\"_blank\"\n";
echo "   title=\"phpDivingLog web site\">". $_config['app_name'] ."</a> ";
echo $_config['app_version'] ."</td>\n";
echo " </tr>\n";

echo "</table>\n";*/

$t->display('divestats.tpl');

?>
