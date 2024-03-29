<?php
/**
 * Filename: drawprofile.php
 * Function: This file uses JpGraph to draw the dive profile graph.
 * @author  Lloyd Borrett - www.borrett.id.au  ; Rob Lensen <rob@bsdfreaks.nl>
 * @package phpdivinglog
 * @version  $Rev$
 * Last Modified: $Date:   Fri Jul 29 22:43:17 2011 +0200 $
 * 
 * @copyright (C) 2006 Lloyd Borrett - http://www.borrett.id.au
 * 
 * Adapted from code by Olaf van Zandwijk - http://enschede.vanzandwijk.net
 * 
 * For use with Diving Log by Sven Knoch - http://www.divinglog.de
 * 
*/

/**
 * _cb_negate Callback to negate the argument
 * 
 * @param mixed $aVal 
 * @access protected
 * @return void
 */
function _cb_negate($aVal)
{
    return round(- $aVal, 2);
} 


/**
 * add_minute Callback to add minute symbol 
 * 
 * @param mixed $aVal 
 * @access public
 * @return void
 */
function add_minute($aVal)
{
    return $aVal . '&#8242;';
} 


/**
 * toFeet Callback to convert metres to feet
 * 
 * @param mixed $aVal 
 * @access public
 * @return void
 */
function toFeet($aVal)
{
    return round(- ($aVal * 3.2808399));
} 


/**
 * toMetres Callback to convert feet to metres
 * 
 * @param mixed $aVal 
 * @access public
 * @return void
 */
function toMetres($aVal)
{
    return round(- ($aVal * 0.3048));
} 

$config_file = "./config.inc.php";
require_once ($config_file);


global $_config;
$request = new HandleRequest();
$request->set_request_uri($_SERVER['REQUEST_URI']);
$request->set_file_depth(0);
$request->handle_url();

    $globals['divenr'] = $request->get_dive_nr();

$result = parse_mysql_query('onedive.sql');
reset_config_table_prefix();
$profile = $result['Profile'];
if (!$profile) {
/*{{{*/
    /**
     *  No profile data
     */
    $temp = 0;
    $ydata = array();
    $xdata = array();

    $items = $result['Divetime'] * 3;
    if ($_config['length']) {
        $depth = toFeet($result['Depth']);
    } else {
        $depth = - $result['Depth'];
    } 

    for ($i = 0; $i < $items; $i++) {
        $xdata[$i] = round($temp, 1);
        $temp += 1 / 3;

        $ydata[$i] = $depth;
        $decowarning[$i] = 0;
        $rbtwarning[$i] = 0;
        $ascwarning[$i] = 0;
        $decwarning[$i] = 0;
        $workwarning[$i] = 0;
    } 
    $ydata['0'] = 0;
    $ydata['1'] = $ydata['1'] / 2;
    $ydata[$items-2] = $ydata[$items-1] / 2;
    $ydata[$items-1] = 0;

    $average_depth = $depth;
/*}}}*/
} else {
/*{{{*/
    /**
     * Graph the profile data 
     */
    $length = (strlen($profile) / 12);
    $start = 0;
    $ydata = array();
    $ydata_asc = array();

    /**
     * Extract the data values from the profile data 
     */
    for ($i = 0; $i < $length; $i++) {
        $ydata[$i] = substr(substr($profile, $start, 12), 0, 5) / 100;
        $decowarning[$i] = substr(substr($profile, $start, 12), 5, 1);
        $rbtwarning[$i] = substr(substr($profile, $start, 12), 6, 1);
        $ascwarning[$i] = substr(substr($profile, $start, 12), 7, 1);
        $decwarning[$i] = substr(substr($profile, $start, 12), 8, 1);
        $workwarning[$i] = substr(substr($profile, $start, 12), 9, 1);
        $start += 12;
    } 

    /**
     * Use the profile interval time to assign time values to the data 
     */
    $profileint = ($result['ProfileInt'] / 60);
    $xdata = array();
    $temp = 0;
    for ($i = 0; $i < $length; $i++) {
        $xdata[$i] = round($temp, 1);
        $temp += $profileint;
    } 
	
    /**
     * Negate all profile data and convert units if required 
     */
    $n = count($ydata);
    for($i = 0; $i < $n; ++$i) {
        if ($_config['length']) {
            $ydata[$i] = toFeet($ydata[$i]);
        } else {
            $ydata[$i] = round(- $ydata[$i], 1);
        } 
    } 

    /**
     * Calculate the average depth 
     */
    $n = count($ydata);
    $total = array_sum($ydata);
    $average_depth = $total / $n; /*}}}*/
} 

/**
 *  Basic graph setup
 */
$graph =& new Graph(550, 400, "auto");

/**
 *  Set margins, colours, scale etc. for whole graph
 */
$graph->SetScale("linlin", 0, 0, $xdata[0], $xdata[(count($xdata)-1)]);
if ($_config['graph_show_two_scales'] || $_config['graph_show_both_units']) {
    $graph->SetY2Scale("lin"); // Y2 axis
} else {
    $graph->SetBox(true, '#000000', 1);
} 
$graph->img->SetMargin(50, 50, 30, 70);
$graph->SetFrame(true,$_config['background_color'],1);
// $graph->SetAxisStyle(AXSTYLE_BOXOUT);
if ($_config['graph_background_image'] != "") {
    $graph->SetMarginColor('#ffffff');
    $graph->SetColor('#f3f3f3');
    $graph->SetBackgroundImage($_config['graph_background_image'], BGIMG_FILLFRAME);
} else {
    $graph->SetMarginColor($_config['background_color']);
    $graph->SetColor('#f3f3f3');
} 

/**
 * Set the graph title and font 
 */
$graph->title->Set($_lang['dive_profile_title'] . $result['Number']);
$graph->title->SetFont(FF_VERDANA, FS_BOLD, 12);
if ($_config['graph_background_image'] != "") {
    $graph->title->SetColor("#ffffff");
} else {
    $graph->title->SetColor("#000000");
} 

/**
 *  Set the x-axis and title it
 */
$graph->xaxis->SetPos('min');

$graph->xaxis->title->Set($_lang['dive_profile_xaxis_title']);
$graph->xaxis->title->SetFont(FF_VERDANA, FS_BOLD, 9);
if ($_config['graph_background_image'] != "") {
    $graph->xaxis->title->SetColor("#ffffff");
} else {
    $graph->xaxis->title->SetColor("#000000");
} 

$graph->xaxis->SetTitleMargin(15);
$graph->xaxis->SetLabelFormatCallback("add_minute");
$graph->xaxis->SetFont(FF_VERDANA, FS_NORMAL, 8);
$graph->xaxis->scale->ticks->SetSide(SIDE_DOWN);
if ($_config['graph_background_image'] != "") {
    $graph->xaxis->SetColor("lightblue", "#ffffff");
} else {
    $graph->xaxis->SetColor("#000000", "#000000");
} 

// Set the y-axis and title it
$graph->yscale->SetGrace(0, 0);
if ($_config['length']) {
    $graph->yaxis->title->Set($_lang['dive_profile_yimperial_title']);
} else {
    $graph->yaxis->title->Set($_lang['dive_profile_ymetric_title']);
} 
$graph->yaxis->title->SetFont(FF_VERDANA, FS_BOLD, 9);
if ($_config['graph_background_image'] != "") {
    $graph->yaxis->title->SetColor("#ffffff");
} else {
    $graph->yaxis->title->SetColor("#000000");
} 

$graph->yaxis->SetTitleMargin(35);
$graph->yaxis->SetLabelFormatCallback("_cb_negate");
$graph->yaxis->SetFont(FF_VERDANA, FS_NORMAL, 8);
$graph->yaxis->scale->ticks->SetSide(SIDE_LEFT);
if ($_config['graph_background_image'] != "") {
    $graph->yaxis->SetColor("lightblue", "#ffffff");
    $graph->SetGridDepth(DEPTH_FRONT);
    $graph->ygrid->SetColor("blue");
} else {
    $graph->yaxis->SetColor("#000000", "#000000");
//    $graph->SetGridDepth(DEPTH_FRONT);
    $graph->ygrid->SetColor("blue");
} 

/**
 * Set legend details 
 */
$graph->legend->Pos(0.5, 0.99, 'center', 'bottom');
$graph->legend->SetLayout(LEGEND_HOR);
$graph->legend->SetLineWeight(2);
$graph->legend->SetFont(FF_VERDANA, FS_NORMAL, 8);
$graph->legend->SetShadow(false);

/**
 * lp_depth is the depth data line 
 */
$lp_depth = new LinePlot($ydata, $xdata);
$lp_depth->SetLegend($_lang['dive_profile_depth_legend']);
if ($_config['graph_background_image'] != "") {
    $lp_depth->SetWeight(2);
    $lp_depth->SetColor("yellow");
} else {
    $lp_depth->SetWeight(1);
    $lp_depth->SetFillColor("#99d0e9");
    $lp_depth->SetColor("darkblue");
} 

for ($a = 0; $a < count($ydata); $a++) {
    $ydata_asc[$a] = $ydata[$a] * $ascwarning[$a]; /*{{{*/
    $ydata_desc[$a] = $ydata[$a] * $decwarning[$a];
    $ydata_deco[$a] = $ydata[$a] * $decowarning[$a];
    $ydata_rbt[$a] = $ydata[$a] * $rbtwarning[$a];
    $ydata_work[$a] = $ydata[$a] * $workwarning[$a];

    $ydata_avg[$a] = $average_depth;

    if (intval($ydata_asc[$a]) === 0) {
        $ydata_asc[$a] = "";
    } else {
        $ydata_asc[$a-1] = $ydata[$a-1];
    } 

    if (intval($ydata_desc[$a]) === 0) {
        $ydata_desc[$a] = "";
    } else {
        $ydata_desc[$a-1] = $ydata[$a-1];
    } 

    if (intval($ydata_deco[$a]) === 0) {
        $ydata_deco[$a] = "";
    } else {
        $ydata_deco[$a-1] = $ydata[$a-1];
    } 

    if (intval($ydata_rbt[$a]) === 0) {
        $ydata_rbt[$a] = "";
    } else {
        $ydata_rbt[$a-1] = $ydata[$a-1];
    } 

    if (intval($ydata_work[$a]) === 0) {
        $ydata_work[$a] = "";
    } else {
        $ydata_work[$a-1] = $ydata[$a-1];
    } /*}}}*/
} 

if ($_config['graph_show_two_scales'] || $_config['graph_show_both_units']) {
    /*{{{*/
    /*
     * Create secondary Y2 scale 
     */
    $l2plot = new LinePlot($ydata, $xdata);
    $l2plot->SetWeight(0); // Optimize
    if ($_config['graph_show_both_units']) {
        if ($_config['length']) {
            $graph->y2axis->title->Set($_lang['dive_profile_ymetric_title']);
            $graph->y2axis->SetLabelFormatCallback('toMetres');
        } else {
            $graph->y2axis->title->Set($_lang['dive_profile_yimperial_title']);
            $graph->y2axis->SetLabelFormatCallback('toFeet');
        } 
    } else {
        if ($_config['length']) {
            $graph->y2axis->title->Set($_lang['dive_profile_yimperial_title']);
            $graph->y2axis->SetLabelFormatCallback('_cb_negate');
        } else {
            $graph->y2axis->title->Set($_lang['dive_profile_ymetric_title']);
            $graph->y2axis->SetLabelFormatCallback('_cb_negate');
        } 
    } 
    $graph->y2axis->title->SetMargin(5); // Some extra margin to clear labels
    $graph->y2axis->title->SetFont(FF_VERDANA, FS_BOLD, 9);
    $graph->y2axis->scale->ticks->SetSide(SIDE_RIGHT);
    if ($_config['graph_background_image'] != "") {
        $graph->y2axis->title->SetColor("#ffffff");
        $graph->y2axis->SetColor("lightblue", "#ffffff");
    } else {
        $graph->y2axis->title->SetColor("#000000");
        $graph->y2axis->SetColor("#000000", "#000000");
    } 
/*}}}*/
} 

/**
 * lp_avg is the Average depth 
 */
$lp_avg = new LinePlot($ydata_avg, $xdata);
$lp_avg->SetWeight(1);
$lp_avg->SetLegend($_lang['dive_profile_avgdepth_title']);
if ($_config['graph_background_image'] != "") {
    $lp_avg->SetColor("black");
} else {
    $lp_avg->SetColor("black");
} 

/**
 * lp_asc is the ascent warning 
 */
$lp_asc = new LinePlot($ydata_asc, $xdata);
$lp_asc->SetWeight(2);
$lp_asc->SetLegend($_lang['dive_profile_ascent_legend']);
if ($_config['graph_background_image'] != "") {
    $lp_asc->SetColor("red");
} else {
    $lp_asc->SetColor("red");
} 

/**
 * lp_desc is the descent warning 
 */
$lp_desc = new LinePlot($ydata_desc, $xdata);
$lp_desc->SetWeight(2);
if ($_config['graph_background_image'] != "") {
    $lp_desc->SetColor("red");
} else {
    $lp_desc->SetColor("red");
} 

/**
 * lp_deco is the deco warning 
 */
$lp_deco = new LinePlot($ydata_deco, $xdata);
$lp_deco->SetWeight(2);
$lp_deco->SetLegend($_lang['dive_profile_deco_legend']);
if ($_config['graph_background_image'] != "") {
    $lp_deco->SetColor("green");
} else {
    $lp_deco->SetColor("green");
} 

/**
 * lp_rbt is the RBT warning 
 */
$lp_rbt = new LinePlot($ydata_rbt, $xdata);
$lp_rbt->SetWeight(2);
$lp_rbt->SetLegend($_lang['dive_profile_rbt_legend']);
if ($_config['graph_background_image'] != "") {
    $lp_rbt->SetColor("purple");
} else {
    $lp_rbt->SetColor("purple");
} 

/**
 * lp_work is the work warning 
 */
$lp_work = new LinePlot($ydata_work, $xdata);
$lp_work->SetWeight(2);
$lp_work->SetLegend($_lang['dive_profile_work_legend']);
if ($_config['graph_background_image'] != "") {
    $lp_work->SetColor("orange");
} else {
    $lp_work->SetColor("orange");
} 

/**
 * Add the graphs 
 */
$graph->Add($lp_depth);
if ($_config['graph_show_two_scales'] || $_config['graph_show_both_units']) {
    $graph->AddY2($l2plot);
} 
$graph->Add($lp_avg);
$graph->Add($lp_asc);
$graph->Add($lp_desc);
$graph->Add($lp_deco);
$graph->Add($lp_rbt);
$graph->Add($lp_work);

/**
 * Output the graph to cache 
 */
$graph->Stroke();

?>
