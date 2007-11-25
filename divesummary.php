<?php
/**
 * Filename: divesummary.php
 * Function: This file displays some short details from phpDivingLog.
 * Version:  phpDivingLog v1.9 2007-03-07
 * @author  Lloyd Borrett - www.borrett.id.au Rob Lensen <rob@bsdfreaks.nl>
 * @package phpdivinglog
 * @version 2.0
 * Last Modified: 2007-03-07
 * /***************************************************************************
 * 
 * phpDivingLog
 * Copyright (C) 2006 Lloyd Borrett - http://www.borrett.id.au
 * 
 * Adapted from code by Olaf van Zandwijk - http://enschede.vanzandwijk.net
 * 
 * For use with Diving Log by Sven Knoch - http://www.divinglog.de
 * 
 * This file and all dependant and otherwise related files are part of phpDivingLog.
 * 
 * phpDivingLog is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * phpDivingLog is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * /**************************************************************************
 */

// include_once misc file
$misc_filename = "includes/misc.inc.php";
if (!file_exists($misc_filename)) {
    print "<p>Misc file '" . $misc_filename . "' not found.</p>";
    exit;
} 
include_once ($misc_filename);

// Dive Statistics

// Get the last dive
$lastdive = parse_mysql_query('lastdive.sql');
$divenumber = $lastdive[0]['Number'];
$divedate = $lastdive[0]['Divedate'];
$entrytime = $lastdive[0]['Entrytime'];
$place = $lastdive[0]['Place'];
$placeid = $lastdive[0]['PlaceID'];
$city = $lastdive[0]['City'];
$country = $lastdive[0]['Country'];

// Get the Min and Max values
$divestats = parse_mysql_query('divestats.sql');

// Get the program details
$dbinfo = parse_mysql_query('dbinfo.sql');

// Get the certification details
$divecert = parse_mysql_query('brevetlist.sql');
$certs = count($divecert);

// Links to Dive Log, Dive Sites
$links .= "   <div class=\"crumbs\" style=\"text-align:center;\">\n";
$links .= "    <a href=\"../divelog/index.php\" class=\"crumbs\" title=\"";
$links .= $_lang['dive_log_linktitle'] . "\">" . $_lang['dive_log'] . "</a><br>\n";
$links .= "    <a href=\"../divelog/divesite.php\" class=\"crumbs\" title=\"";
$links .= $_lang['dive_sites_linktitle'] . "\">" . $_lang['dive_sites'] . "</a><br>\n";
$links .= "    <a href=\"../divelog/equipment.php\" class=\"crumbs\" title=\"";
$links .= $_lang['dive_equip_linktitle'] . "\">" . $_lang['dive_equip'] . "</a><br>\n";
$links .= "    <a href=\"../divelog/divestats.php\" class=\"crumbs\" title=\"";
$links .= $_lang['dive_stats_linktitle'] . "\">" . $_lang['dive_stats'] . "</a>\n";
$links .= "   </div>\n";

// Start the output
echo "    &nbsp;<br>\n";
echo "    <div class=\"rightHDR\">\n";
echo "      LLOYD'S DIVING LOG<br>\n";
echo "      SUMMARY\n";
echo "    </div>\n";

// Total dives
echo "    <p class=\"rightLIST\">\n";
echo "      <span class=\"small\">" . $_lang['stats_totaldives'] . "</span><br>\n";
echo "      <b>" . $divenumber . "</b></p>\n";

// Total bottom time
echo "    <p class=\"rightLIST\">\n";
echo "      <span class=\"small\">" . $_lang['stats_totaltime'] . "</span><br>\n";
echo "      <b>" . floor($divestats[0]['BottomTime'] / 60) . ":" . sprintf("%02d",($divestats[0]['BottomTime'] % 60)) . " " . $_lang['stats_totaltime_units'] . "</b></p>\n";

// Last dive
echo "    <p class=\"rightLIST\">\n";
echo "      <span class=\"small\">" . $_lang['stats_divedatemax'] . "</span><br>\n";
echo "      <b>" . $entrytime . "</b><br>\n";
echo "      <b>" . date($_lang['logbook_divedate_format'], strtotime($divedate)) . "</b><br>\n";
if ($place != "") {
    echo "      <b><a href=\"../divelog/divesite.php?loc=" . $placeid;
    echo "\" title=\"" . $place . " " . $_lang['logbook_place_linktitle'];
    echo "\">" . $place . "</a></b><br>\n";
} 
if ($city != "") {
    echo "      <b>" . $city . "</b><br>\n";
} 
if ($country != "") {
    echo "      <b>" . $country . "</b><br>\n";
} 
echo "       [<b><a href=\"../divelog/index.php?nr=" . $divenumber;
echo "\" title=\"" . $_lang['dlog_number_title'] . $divenumber;
echo "\">" . $divenumber . "</a></b>]</p>\n";

// Dive certifications
if ($certs != 0) {
    echo "    <p class=\"rightLIST\"><span class=\"small\">" . $_lang['cert_brevet'] . "</span><b><br>\n";

    for ($i = 0; $i < count($divecert); $i++) {
        echo "      " . $divecert[$i]['Org'] . " " . $divecert[$i]['Brevet'] . "<br>\n";
    } 
    echo "    </b></p>\n";
} 

echo $links;

// echo "    <hr align=\"center\" width=\"75%\">\n";

// Powered by
echo "    <p class=\"rightLIST\"><span class=\"small\">" . $_lang['poweredby'] . "\n";
echo "      <a href=\"http://www.divinglog.de/\" target=\"_blank\"\n";
echo "      title=\"Diving Log web site\">" . $dbinfo[0]['PrgName'] . "</a>\n";
echo "      " . $dbinfo[0]['DBVersion'] . "<br>\n";
echo "      " . $_lang['and'] . "\n";
echo "      <a href=\"../interests/phpdivinglog.htm\"\n";
echo "      target=\"_blank\"";
echo "      title=\"phpDivingLog web site\">" . $_config['app_name'] . "</a> ";
echo $_config['app_version'] . "</span></p>\n";

echo "  &nbsp;<br>\n";

?>
