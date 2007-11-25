<?php
/**
 * Filename:  drawpiechart..php
 * Function:  Draws a pie chart at the divestats page.
 * @author  Rob Lensen <rob@bsdfreaks.nl>
 * @package phpdivinglog
 * @version 2.0
 * Last Modified: 2007-06-16
 */

require_once './config.inc.php';
require_once 'includes/jpgraph/src/jpgraph_pie.php';

global $_config, $_lang;
$request = new HandleRequest();
$request->set_request_uri($_SERVER['REQUEST_URI']);
$request->set_file_depth(0);
$request->handle_url();

$divestats = new Divestats();
$divestats->set_divestats_info($request);
$result = $divestats->get_divestats_info();
if($request->get_multiuser()){
    //get the prefix for a user_id
    $user = new User;
    $user->set_user_id($request->get_user_id());
    set_config_table_prefix($user->get_table_prefix());
} else {

}
reset_config_table_prefix();

//print_r($divestats);
$data =
array($divestats->depthrange1_per,$divestats->depthrange2_per,$divestats->depthrange3_per,$divestats->depthrange4_per,$divestats->depthrange5_per);

$graph = new PieGraph(650,200,"auto");
$graph->SetShadow();
;
//$graph->title->Set("A simple Pie plot");
$graph->SetFrame(false);
$p1 = new PiePlot($data);
//$legends = array('April (%d)','May (%d)','June (%d)');
$legends = array(   
$_config['length'] ? backhtmlentities($_lang['stats_depth1i']) : backhtmlentities($_lang['stats_depth1m']) . $divestats->depthrange[0].' (%d%%)',
$_config['length'] ? backhtmlentities($_lang['stats_depth2i']) : backhtmlentities($_lang['stats_depth2m']) . $divestats->depthrange[1].' (%d%%)' ,
$_config['length'] ? backhtmlentities($_lang['stats_depth3i']) : backhtmlentities($_lang['stats_depth3m']) . $divestats->depthrange[2].' (%d%%)',
$_config['length'] ? backhtmlentities($_lang['stats_depth4i']) : backhtmlentities($_lang['stats_depth4m']) . $divestats->depthrange[3].' (%d%%)',
$_config['length'] ? backhtmlentities($_lang['stats_depth5i']) : backhtmlentities($_lang['stats_depth5m']) . $divestats->depthrange[4].' (%d%%)'                        
                        );
$p1->SetLegends($legends);
//$p1->SetSliceColors(array('cadetblue1', 'lightskyblue3','skyblue3','dodgerblue','dodgerblue4'));
$p1->SetTheme('water');
$graph->legend->SetFont(FF_VERDANA, FS_NORMAL, 8);
$graph->legend->SetShadow(false);
$graph->Add($p1);
$graph->Stroke();
?>


