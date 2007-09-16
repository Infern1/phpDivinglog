<?php
/**
 * Filename:  includes/misc.inc.php
 * Function:  Miscellaneous functions file for phpDivingLog.
 * Last Modified: 2006-08-21
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

require_once('../config.inc.php');

/**
 * htmlentities_array HTML on screen function
 * 
 * @param array $arr 
 * @access public
 * @return void
 */
function htmlentities_array($arr = array()) 
{
	$rs =  array();
	while(list($key,$val) = each($arr)) {
		if (is_array($val)) {
			$rs[$key] = htmlentities_array($val);
		} else {
			$rs[$key] = htmlentities($val, ENT_QUOTES);
		}
	}
	return $rs;
}

// Get the language values

// use english if it is not set in the configuration file
if(!isset($_config['language'])) {
	$_config['language'] = "english"; // if not set, get the english language file.
}

// first get the default english values
// so that we'll have values for anything not in the specified language file
if ($_config['language'] != "english") {
	if (!file_exists('includes/language/english.inc.php')) {
		print "<p>Language file includes/language/english.inc.php not found.</p>";
		exit;
	}
	include ('includes/language/english.inc.php');
}

// include the specified language file
$language_filename = "includes/language/". $_config['language'] .".inc.php";
if (!file_exists($language_filename)) {
   print "<p>Language file '". $language_filename ."' not found.</p>";
   exit;
}
$_lang = array();
include ($language_filename);

// Convert applicable characters in the language file to entities
$_lang = htmlentities_array($_lang);	


/**
 * sql_file Returns the contents of an SQL statement file as a string. $filename is  
 * the name of the SQL-file to be parsed. Variables defined under 'global'  
 * may be used in the sql-scripts and will be replaced by their values!     
 * The globals term should include all the variables witch are used in the  
 * sql-script, so the variables are parsed correctly			  
 *
 * @param mixed $filename 
 * @access public
 * @return void
 */
function sql_file($filename) 
{
	global $_config;
	$sqlpath = $_config['sqlpath'];
	global $globals;

	$location = $sqlpath.$filename;
	if (!file_exists($location)) {
		die ("SQL file '". $location ."' not found !");
	}

	if ($location) {
		$contents = implode('', file($location));
		eval ("\$contents = \"$contents\";");
		//Support the "'" character from a HTTP GET variabele
		$contents = str_replace ("\'", "'", $contents);	
	     
		/* Handles debug-information: if debugging is enabled by setting	*/
		/* $debug =1 in the script, thiswrites debug output.			*/
	
		if ($debug) {
			echo "<hr>\n<b>".$location.":</b><br>\n<br>\n";
			echo nl2br(htmlentities($contents))."<br>\n<hr>\n";
		}
	
		return $contents;
	} else {
		return false;
	}
}


/**
 * parse_mysql_query 
 * 
 * @param mixed $filename 
 * @access public
 * @return void
 */
function parse_mysql_query($filename) 
{
    global $_config;
    $username = $_config['database_username'];
	$password = $_config['database_password'];
	$server = $_config['database_server'];
	$db = $_config['database_db'];
    $result = array();
	$query = sql_file($filename);
    if ($query) {
		$connection = mysql_connect($server, $username, $password);
		mysql_select_db($db, $connection);
        mysql_query("SET CHARACTER SET 'utf8'", $connection);
		$server_query = mysql_query($query, $connection);
		if (mysql_errno() ) {
			echo "<hr>\n<b>MySQL error " . mysql_errno(). ": " . mysql_error() . "\n:</b><br>\n";
		    echo "Query: $query <br><hr>";
            exit;
        }
		for($i=0; $query_output = mysql_fetch_assoc($server_query); $i++) {
			while(list($key, $val) = each($query_output)) {
				if(is_string($val)) {
					$val = utf8_encode($val);
					$query_output[$key] = $val;
				}
			}
			$result[$i] = $query_output;
		}
	}
	return $result;
}

/**
 * check_number Only allow the characters 0 to 9.
 * 
 * @param mixed $number 
 * @access public
 * @return void  Return nothing otherwise.
 */
function check_number($number) 
{
	if (!$number) {
		$get = "";
	} else {
		if (ereg('[^0-9]', $number)) {
			$get = "";
		} else {
			if ($number == 0) {
				$get = "";
			} else {
				$get = $number;
			}
		}
	}
	return $get;
}


function GetRequestVar($url, $request_file_depth=0){ 

    $number_folders = $request_file_depth ; //number of folders from the root of the script
    $adres = $url;
    //$adres = $_SERVER['REQUEST_URI'];
    $adres = substr($adres,1);
    $adres = $adres."/";
    $array = explode("/",$adres);
    $paginas = array();
    for($i = $number_folders; $i< count($array) ; $i++)
    {
        $array_key = $array[$i]; //neem arraykey, bijvoorbeeld id
        if(isset($array[$i + 1])){ 
            $array_value = $array[$i]; //neem arrayvalue, bij id hoort 33, bij id hoort 33 in dit voorbeeld 
            $paginas[$array_value] = $array[$i + 1]; 
        }  
    }
    return $paginas;
}

/**
 * GetProfileData Extract averagedepth and sac  info from the profile data
 * 
 * @param mixed $result 
 * @access public
 * @return void
 */
function GetProfileData($result){
    	global $_config;
        global $_lang;
        $profile = $result[0]['Profile'];
        $length = ( strlen($profile) / 12 );
		$profileint = ($result[0]['ProfileInt'] / 60);
		$divetime = $profileint * $length;
		$start = 0;
		$ydata = 0;
		for ($i=0; $i < $length; $i++) {
        		$ydata = $ydata + (substr(substr($profile,$start,12),0,5) / 100);
		        $start += 12;
		}
		$averagedepth = $ydata / $length;
		$sac = (($result[0]['PresS'] - $result[0]['PresE']) * $result[0]['Tanksize']) / ($divetime * ($averagedepth / 10 + 1));

		if ($_config['length']) {
			$averagedepth = MetreToFeet($averagedepth, 2) ."&nbsp;". $_lang['unit_length_imp'];
		} else {
			$averagedepth = number_format($averagedepth, 2) ."&nbsp;". $_lang['unit_length'];
		}
		if ($_config['volume']) {
			$sac = LitreToCuft($sac, 1) ."&nbsp;". $_lang['unit_rate_imp'];
		} else {
			$sac = number_format($sac, 2) ."&nbsp;". $_lang['unit_rate'];
		}
        return array('averagedepth' => $averagedepth , 'sac' => $sac);
}

define("MetreToFeet", "calc:(Depth*3.2808399)");
function MetreToFeet($value, $precision) 
{
	return round(($value * 3.2808399), $precision);
}
 
function BarToPsi($value, $precision) 
{
	return round(($value * 14.503774), $precision);
}

function KgToLbs($value, $precision) 
{
	return round(($value * 2.2046226), $precision);
}

function CelsiusToFahrenh($value, $precision) 
{
	return round((($value * (9 / 5)) + 32), $precision);
}
 
function LitreToCuft($value, $precision) 
{
	return round(($value * 7), $precision);
}

/**
 * DECtoDMS  Converts decimal longitude / latitude to DMS
 * ( Degrees / minutes / seconds ) 
 * This is the piece of code which may appear to 
 * be inefficient, but to avoid issues with floating
 * point math we extract the integer part and the float
 * part by using a string function.
 * @param mixed $dec 
 * @access public
 * @return void
 */
function DECtoDMS($dec)
{
	if ($dec == "") {
		$dms = "";
	} else {
		$vars = explode(".",$dec);
		$deg = $vars[0];
		$tempma = "0.".$vars[1];

		$tempma = $tempma * 3600;
		$min = floor($tempma / 60);
		$sec = $tempma - ($min*60);

		$dms = $deg . '&#176;';
		if (($min != 0) || ($sec != 0)) {
			$dms .= ' ' . $min . '&#8242;';
			if ($sec != 0) {
//				format to xx.xx
				$s = number_format($sec,2);
//				remove trailing zeroes
				$s = rtrim($s,"0");
//				remove a trailing decimal point
				$s = rtrim($s,".");
				$dms .= ' ' . $s . '&#8243;';
			}
		}
	}
	return $dms;
}    


/**
 * latitude_format 
 * 
 * @param mixed $coord 
 * @access public
 * @return void
 */
function latitude_format($coord) 
{
//	Change coordinates into a displayable format

	if ($coord == "") {
		$dms = "";
	} else {
//		Converts decimal latitude to degrees / minutes / seconds
		$dms = DECtoDMS(abs($coord));

//		Add North or South
		if ($coord < 0) {
			$dms .= " S";
		} elseif ($coord > 0) {
			$dms .= " N";
		}
	}
	return $dms;
}


/**
 * longitude_format 
 * 
 * @param mixed $coord 
 * @access public
 * @return void
 */
function longitude_format($coord) 
{
//	Change coordinates into a displayable format

	if ($coord == "") {
		$dms = "";
	} else {
//		Converts decimal longitude to degrees / minutes / seconds
		$dms = DECtoDMS(abs($coord));

//		Add East or West
		if ($coord < 0) {
			$dms .= " W";
		} elseif ($coord > 0) {
			$dms .= " E";
		}
	}
	return $dms;
}

/**
 * set_config_table_prefix 
 * 
 * @param mixed $prefix 
 * @access public
 * @return void
 */
function set_config_table_prefix($prefix){
    global $_config;
    $_config['table_prefix'] = $prefix;
}

/**
 * reset_config_table_prefix 
 * 
 * @access public
 * @return void
 */
function reset_config_table_prefix(){
    global $_config;
    unset($_config['table_prefix']);
}

?>
