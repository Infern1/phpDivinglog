<?php

/**
 * Filename:  includes/misc.inc.php
 * Function:  Miscellaneous functions file for phpDivingLog.
 * @author  Lloyd Borrett - www.borrett.id.au ; Rob Lensen <rob@bsdfreaks.nl>
 * @package phpdivinglog
 * @version $Rev$
 * Last Modified: $Date:   Fri Jul 29 22:43:17 2011 +0200 $
 * @copyright (C) 2006 Lloyd Borrett - http://www.borrett.id.au
 * 
 * Adapted from code by Olaf van Zandwijk - http://enschede.vanzandwijk.net
 * 
 * For use with Diving Log by Sven Knoch - http://www.divinglog.de
 *
 */

global $_config;

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
	foreach ($arr as $key => $val) {
		if (is_array($val)) {
			$rs[$key] = htmlentities_array($val);
		} else {
			$rs[$key] = htmlentities($val, ENT_QUOTES, "UTF-8", 0);
		}
	}
	return $rs;
}

function action($value_of_clicked_field, $array_values)
{
	global $_config;

	if ($_config['query_string']) {
		$ext = "?id=";
	} else {
		$ext = "/";
	}


	if (isset($_SESSION['request_type'])) {
		$request_type = $_SESSION['request_type'];
		if ($request_type == 1) {
			return "javascript:open_url(" . $array_values["Number"] . ",'/index.php" . $ext . "' )";
		} elseif ($request_type == 2) {
			return "javascript:open_url(" . $array_values["ID"] . ",'/divesite.php" . $ext . "' )";
		} elseif ($request_type == 3) {
			return "javascript:open_url(" . $array_values["ID"] . ",'/equipment.php" . $ext . "' )";
		} elseif ($request_type == 8) {
			return "javascript:open_url(" . $array_values["ID"] . ",'/divetrip.php" . $ext . "' )";
		} elseif ($request_type == 9) {
			return "javascript:open_url(" . $array_values["ID"] . ",'/diveshop.php" . $ext . "' )";
		} elseif ($request_type == 10) {
			return "javascript:open_url(" . $array_values["ID"] . ",'/divecountry.php" . $ext . "' )";
		} elseif ($request_type == 11) {
			return "javascript:open_url(" . $array_values["ID"] . ",'/divecity.php" . $ext . "' )";
		}
	} else {
		$request = new HandleRequest();
		$request->set_request_uri($_SERVER['REQUEST_URI']);
		$request->set_file_depth(0);
		$foo = $request->handle_url();
		$request_type = $_SESSION['request_type'];
		if ($request_type == 1) {
			return "javascript:open_url(" . $array_values["Number"] . ",'/index.php" . $ext . "' )";
		} elseif ($request_type == 2) {
			return "javascript:open_url(" . $array_values["ID"] . ",'/divesite.php" . $ext . "' )";
		} elseif ($request_type == 3) {
			return "javascript:open_url(" . $array_values["ID"] . ",'/equipment.php" . $ext . "' )";
		} elseif ($request_type == 8) {
			return "javascript:open_url(" . $array_values["ID"] . ",'/divetrip.php" . $ext . "' )";
		} elseif ($request_type == 9) {
			return "javascript:open_url(" . $array_values["ID"] . ",'/diveshop.php" . $ext . "' )";
		} elseif ($request_type == 10) {
			return "javascript:open_url(" . $array_values["ID"] . ",'/divecountry.php" . $ext . "' )";
		} elseif ($request_type == 11) {
			return "javascript:open_url(" . $array_values["ID"] . ",'/divecity.php" . $ext . "' )";
		}
	}
}

// Get the language values

// use english if it is not set in the configuration file
if (!isset($_config['language'])) {
	$_config['language'] = "english"; // if not set, get the english language file.
}
// first get the default english values
// so that we'll have values for anything not in the specified language file
if ($_config['language'] != "english") {
	if (!file_exists($_config['app_root'] . 'includes/language/english.inc.php')) {
		print "<p>Language file includes/language/english.inc.php not found.</p>";
		exit;
	}
	include_once($_config['app_root'] . 'includes/language/english.inc.php');
}

// include the specified language file
$language_filename =  $_config['app_root'] . "includes/language/" . $_config['language'] . ".inc.php";
if (!file_exists($language_filename)) {
	print "<p>Language file '" . $language_filename . "' not found.</p>";
	exit;
}
$_lang = array();
include_once($language_filename);


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



	$location = $sqlpath . $filename;
	if (!file_exists($location)) {
		die("SQL file '" . $location . "' not found !");
	}

	if ($location) {
		$contents = implode('', file($location));
		eval("\$contents = \"$contents\";");
		//Support the "'" character from a HTTP GET variabele
		$contents = str_replace("\'", "'", $contents);

		/* Handles debug-information: if debugging is enabled by setting	*/
		/* $debug =1 in the script, thiswrites debug output.			*/

		if ($_config['debug']) {
			echo "<hr>\n<b>" . $location . ":</b><br>\n<br>\n";
			echo nl2br(htmlentities($contents)) . "<br>\n<hr>\n";
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
 * @return array
 */
function parse_mysql_query($filename, $sql_query = 0, $debug = false)
{
	global $_config, $globals, $wpdb;
	$globals['sql_num_rows'] = 0;
	$result = array();
	if (($sql_query)) {
		$query = $sql_query;
	} else {
		$query = sql_file($filename);
	}
	if ($query) {
		if ($debug == true) {
			//    echo "query is: $query <br>";
		}
		/* change character set to utf8 */
    $wpdb->set_charset($wpdb->dbh, "utf8");
    $res = $wpdb->get_results($query,ARRAY_A );
    $globals['sql_num_rows'] = $wpdb->num_rows; //mysqli_affected_rows($connection);
		if ($globals['sql_num_rows'] == 1) {
      $result = $res[0];
		} else {
      $result = $res;
		}
	}
	return $result;
}


/**
 * rows_mysql_query 
 * 
 * @param mixed $filename 
 * @access public
 * @return int
 */
function rows_mysql_query()
{
	global $globals;
	return $globals['sql_num_rows'];
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
		if (preg_match('[^0-9]', $number)) {
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

/**
 * GetRequestVar get the info from the url request and split it into chunks
 *
 * @param mixed $url
 * @param mixes $request_file_depth
 * @access public
 * @return array
 */
function GetRequestVar($url, $request_file_depth = 0)
{
	global $_config, $t, $_POST;
	$paginas = NULL;
	if ($_config['query_string']) {
		$url_array = parse_url($url);
		if (isset($url_array['query'])) {
			parse_str($url_array['query'], $output);
			$paginas = array();
			$file = basename($url_array['path']);
			$paginas[0] = $file;
			if (!isset($output['DG_ajaxid']) && !isset($output['pageID'])) {
				foreach ($output as $el) {
					$paginas[] .= $el;
				}
			} elseif (isset($output['pageID'])) {

				$paginas[] .= "list";
			} else {
				$paginas[] .= $output['user_id'];
				$paginas[] .= $output['id'];
			}
		}
	} else {
		$number_folders = $request_file_depth; //number of folders from the root of the script
		$adres = $url;
		$possessid = strpos($adres, "?PHPSESSID");
		if ($possessid !== false) {
			$adres = substr($adres, 0, $possessid);
		}
		//$adres = $_SERVER['REQUEST_URI'];
		$adres = substr($adres, 1);
		$adres = $adres . "/";
		$array = explode("/", $adres);
		$paginas = array();
		for ($i = $number_folders; $i < count($array); $i++) {
			if (!empty($array[$i])) {
				$paginas[] = $array[$i];
			}
		}
	}
	//print_r($paginas);
	return $paginas;
}


/**
 * function to override PHP's is_writable() which can occasionally be unreliable due to O/S and F/S differences
 * attempts to open the specified file for writing. Returns true if successful, false if not.
 * if a directory is specified, uses PHP's is_writable() anyway
 *
 * @var string
 * @return boolean
 */
function is__writeable($filepath, $make_unwritable = true)
{
	if (is_dir($filepath)) return is_writable($filepath);
	$fp = @fopen($filepath, 'a');
	if ($fp) {
		@fclose($fp);
		if ($make_unwritable) set_unwritable($filepath);
		$fp = @fopen($filepath, 'a');
		if ($fp) {
			@fclose($fp);
			return true;
		}
	}
	return false;
}

/**
 * attempts to make the specified file read-only
 *
 * @var string
 * @return boolean
 */
function set_unwritable($filepath)
{
	return @chmod($filepath, 0444);
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

/**
 * GetProfileData Extract averagedepth and sac info from the profile data
 * 
 * @param mixed $result 
 * @access public
 * @return void
 */
function GetProfileData($result)
{
	global $_config;
	global $_lang;
	$profile = $result['Profile'];
	$length = (strlen($profile) / 12);
	$profileint = ($result['ProfileInt'] / 60);

	/**
	 * Divetime calculation changed to Divetime from Divelog table see:
	 * http://www.divinglog.de/phpbb/viewtopic.php?p=3094#3094
	 */
	$divetime = $profileint * $length;
	if (isset($result['Divetime'])) {
		$divetime = $result['Divetime'];
	} else {
		$divetime = $profileint * $length;
	}
	$start = 0;
	$ydata = 0;
	$ydata_ar = array();
	$ydata_asc_ar = array();
	$merged = array();
	$merged_asc = array();
	$merged_avg = array();
	$merged_deco = array();
	$merged_rbt  = array();
	$merged_desc = array();
	$merged_work = array();

	for ($i = 0; $i < $length; $i++) {
		$ydata = $ydata + (substr(substr($profile, $start, 12), 0, 5) / 100);
		$ydata_ar[$i] = substr(substr($profile, $start, 12), 0, 5) / 100;
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
	$n = count($ydata_ar);
	for ($i = 0; $i < $n; ++$i) {
		if ($_config['length']) {
			$ydata_ar[$i] = toFeet($ydata_ar[$i]);
		} else {
			$ydata_ar[$i] = round(-$ydata_ar[$i], 1);
		}
	}
	$averagedepth = $ydata / $length;
	for ($a = 0; $a < count($ydata_ar); $a++) {
		$ydata_asc[$a] = $ydata_ar[$a] * $ascwarning[$a];
		$ydata_desc[$a] = $ydata_ar[$a] * $decwarning[$a];
		$ydata_deco[$a] = $ydata_ar[$a] * $decowarning[$a];
		$ydata_rbt[$a] = $ydata_ar[$a] * $rbtwarning[$a];
		$ydata_work[$a] = $ydata_ar[$a] * $workwarning[$a];
		$ydata_avg[$a] = round($averagedepth, 2) * -1.0;

		if (intval($ydata_asc[$a]) === 0) {
			//$ydata_asc[$a] = "";
		} else {
			$ydata_asc[$a - 1] = $ydata_ar[$a - 1];
		}
		if (intval($ydata_desc[$a]) === 0) {
			//$ydata_desc[$a] = "";
		} else {
			$ydata_desc[$a - 1] = $ydata_ar[$a - 1];
		}
		if (intval($ydata_deco[$a]) === 0) {
			//$ydata_deco[$a] = "";
		} else {
			$ydata_deco[$a - 1] = $ydata_ar[$a - 1];
		}
		if (intval($ydata_rbt[$a]) === 0) {
			//$ydata_rbt[$a] = "";
		} else {
			$ydata_rbt[$a - 1] = $ydata_ar[$a - 1];
		}
		if (intval($ydata_work[$a]) === 0) {
			//$ydata_work[$a] = "";
		} else {
			$ydata_work[$a - 1] = $ydata_ar[$a - 1];
		}
	}
	for ($i = 0; $i < $n; ++$i) {
		$merged[$i] = array($xdata[$i], $ydata_ar[$i]);
		$merged_asc[$i] = array($xdata[$i], $ydata_asc[$i]);
		$merged_avg[$i] = array($xdata[$i], $ydata_avg[$i]);
		$merged_deco[$i] = array($xdata[$i], $ydata_deco[$i]);
		$merged_rbt[$i] = array($xdata[$i], $ydata_rbt[$i]);
		$merged_desc[$i] = array($xdata[$i], $ydata_desc[$i]);
		$merged_work[$i] = array($xdata[$i], $ydata_work[$i]);
	}

	$sac = (($result['PresS'] - $result['PresE']) * $result['Tanksize']) / ($divetime * ($averagedepth / 10 + 1));

	if ($_config['length']) {
		$averagedepth = MetreToFeet($averagedepth / 3.2808399, 2) . "&nbsp;";
	} else {
		$averagedepth = number_format($averagedepth, 2) . "&nbsp;";
	}
	if ($_config['volume']) {
		$sac = LitreToCuft($sac, 2) . "&nbsp;" . $_lang['unit_rate_imp'];
	} else {
		$sac = number_format($sac, 2) . "&nbsp;" . $_lang['unit_rate'];
	}
	return array(
		'averagedepth'  => $averagedepth,
		'sac'           => $sac,
		'data'          => $merged,
		'ascdata'       => $merged_asc,
		'avgdata'       => $merged_avg,
		'decodata'      => $merged_deco,
		'rbtdata'       => $merged_rbt,
		'descdata'      => $merged_desc,
		'workdata'      => $merged_work
	);
}


/**
 * formatBytes returns a readble file size
 * 
 * @param mixed $size 
 * @param int $precision 
 * @access public
 * @return void
 */
function formatBytes($size, $precision = 2)
{
	$base = log($size) / log(1024);
	$suffixes = array('', 'k', 'M', 'G', 'T');

	return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
}

define('MetreToFeet', "calc:(Depth*3.2808399)");
function MetreToFeet($value, $precision = 2)
{
	return round(($value * (3.2808399)), $precision);
}

function BarToPsi($value, $precision = 2)
{
	return round(($value * 14.503774), $precision);
}

function KgToLbs($value, $precision = 2)
{
	return round(($value * 2.2046226), $precision);
}

function CelsiusToFahrenh($value, $precision = 2)
{
	return round((($value * (9 / 5)) + 32), $precision);
}

function LitreToCuft($value, $precision = 2)
{
	return round(($value * 0.035335689046), $precision);
}

/**
 *  DECtoDMS  Converts decimal longitude / latitude to d, dm or dms
 * ( degrees / minutes / seconds ) 
 * This is the piece of code which may appear to 
 * be inefficient, but to avoid issues with floating
 * point math we extract the integer part and the float
 * part by using a string function.
 * @param mixed $dec 
 * @access public
 * @return void
 */
function DECtoDMS($dec, $fmt)
{
	if ($dec == "") {
		$dms = "";
	} else {
		switch ($fmt) {
			case "dm":
				// degrees and minutes
				$vars = explode(".", $dec);
				$deg = $vars[0];
				$tempma = "0." . $vars[1];

				$min = $tempma * 60;
				$dms = $deg . '&#176;';
				if ($min != 0) {
					// format to xx.xxxx
					$m = number_format($min, 4);
					// remove trailing zeroes
					$m = rtrim($m, "0");
					// remove a trailing decimal point
					$m = rtrim($m, ".");
					$dms .= '&nbsp;' . $m . '&#8242;';
				}
				break;

			case "dms":
				// degrees, minutes and seconds
				$vars = explode(".", $dec);
				$deg = $vars[0];
				$tempma = "0." . $vars[1];

				$tempmb = $tempma * 3600;
				$min = floor($tempmb / 60);
				$sec = $tempmb - ($min * 60);
				$dms = $deg . '&#176;';
				if (($min != 0) || ($sec != 0)) {
					$dms .= '&nbsp;' . $min . '&#8242;';
					if ($sec != 0) {
						// format to xx.xx
						$s = number_format($sec, 2);
						// remove trailing zeroes
						$s = rtrim($s, "0");
						// remove a trailing decimal point
						$s = rtrim($s, ".");
						$dms .= '&nbsp;' . $s . '&#8243;';
					}
				}
				break;

			default:
				// degress only

				// format to xx.xxxxxx
				$d = number_format($dec, 6);
				// remove trailing zeroes
				$d = rtrim($d, "0");
				// remove a trailing decimal point
				$d = rtrim($d, ".");
				$dms = $d . '&#176;';
		}
	}
	return $dms;
}

/**
 * add_unit_depth 
 * 
 * @param mixed $value 
 * @access public
 * @return void
 */
function add_unit_depth($value)
{
	global $_config, $_lang;
	if (!empty($value)) {
		$value = number_format($value, 1, $_config['decsep'], '');
		if ($_config['length']) {
			$value .=  " " . $_lang['unit_length_short_imp'];
		} else {
			$value .= " " . $_lang['unit_length_short'];
		}
	}
	return $value;
}

/**
 * add_unit_time 
 * 
 * @param mixed $value 
 * @access public
 * @return void
 */
function add_unit_time($value)
{
	global $_config, $_lang;
	$value .=  " " . $_lang['unit_time_short'];
	return $value;
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
	// Change coordinates into a displayable format
	global $_config;

	if ($coord == "") {
		$dms = "";
	} else {
		//		Converts decimal latitude to degrees / minutes / seconds
		$dms = DECtoDMS(abs($coord), $_config['coord_format']);

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
	// Change coordinates into a displayable format
	global $_config;

	if ($coord == "") {
		$dms = "";
	} else {
		//		Converts decimal longitude to degrees / minutes / seconds
		$dms = DECtoDMS(abs($coord), $_config['coord_format']);

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
function set_config_table_prefix($prefix)
{
	global $_config;
	//    $_config['table_prefix'] = $prefix;
}

/**
 * reset_config_table_prefix 
 * 
 * @access public
 * @return void
 */
function reset_config_table_prefix()
{
	global $_config;
	//    unset($_config['table_prefix']);
}


/**
 * count_all count non-empty elements in an array of any dimension
 * 
 * @param mixed $arg 
 * @access public
 * @return int
 */
function count_all($arg)
{
	// skip if argument is empty 
	if ($arg) {
		// not an array, return 1 (base case) 
		if (!is_array($arg))
			return 1;
		// else call recursively for all elements $arg 
		$count = 0;
		foreach ($arg as $key => $val)
			$count += count_all($val);
		return $count;
	}
}

function _make_url_clickable_cb($matches)
{
	$ret = '';
	$url = $matches[2];

	if (empty($url))
		return $matches[0];
	// removed trailing [.,;:] from URL
	if (in_array(substr($url, -1), array('.', ',', ';', ':')) === true) {
		$ret = substr($url, -1);
		$url = substr($url, 0, strlen($url) - 1);
	}
	return $matches[1] . "<a href=\"$url\" rel=\"nofollow\">$url</a>" . $ret;
}

function _make_web_ftp_clickable_cb($matches)
{
	$ret = '';
	$dest = $matches[2];
	$dest = 'http://' . $dest;

	if (empty($dest))
		return $matches[0];
	// removed trailing [,;:] from URL
	if (in_array(substr($dest, -1), array('.', ',', ';', ':')) === true) {
		$ret = substr($dest, -1);
		$dest = substr($dest, 0, strlen($dest) - 1);
	}
	return $matches[1] . "<a href=\"$dest\" rel=\"nofollow\">$dest</a>" . $ret;
}

function _make_email_clickable_cb($matches)
{
	$email = $matches[2] . '@' . $matches[3];
	return $matches[1] . "<a href=\"mailto:$email\">$email</a>";
}

function make_clickable($ret)
{
	$ret = ' ' . $ret;
	// in testing, using arrays here was found to be faster
	$ret = preg_replace_callback('#([\s>])([\w]+?://[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]*)#is', '_make_url_clickable_cb', $ret);
	$ret = preg_replace_callback('#([\s>])((www|ftp)\.[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]*)#is', '_make_web_ftp_clickable_cb', $ret);
	$ret = preg_replace_callback('#([\s>])([.0-9a-z_+-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,})#i', '_make_email_clickable_cb', $ret);

	// this one is not in an array because we need it to run last, for cleanup of accidental links within links
	$ret = preg_replace("#(<a( [^>]+?>|>))<a [^>]+?>([^>]+?)</a></a>#i", "$1$3</a>", $ret);
	$ret = trim($ret);
	return $ret;
}

/**
 * WGS84 Distance Calculation
 * Here is a function in PHP that returns the distance between two points on earth 
 * based on the WGS84 datum. This is nothing new. It is called the Vincenty's formula 
 * and the theory can be found at http://www.ngs.noaa.gov/PUBS_LIB/inverse.pdf
 *
 * You just need to send the latitude and longitude in decimal degrees.
 *
 * Sourced from http://forums.mysql.com/read.php?23,85608,170800#msg-170800
 */
function distInvVincenty($lat1, $lon1, $lat2, $lon2)
{
	/* WGS84 stuff */
	$a = 6378137;
	$b = 6356752.3142;
	$f = 1 / 298.257223563;
	/* end of WGS84 stuff */

	$L = deg2rad($lon2 - $lon1);
	$U1 = atan((1 - $f) * tan(deg2rad($lat1)));
	$U2 = atan((1 - $f) * tan(deg2rad($lat2)));
	$sinU1 = sin($U1);
	$cosU1 = cos($U1);
	$sinU2 = sin($U2);
	$cosU2 = cos($U2);

	$lambda = $L;
	$lambdaP = 2 * pi();
	$iterLimit = 20;
	while ((abs($lambda - $lambdaP) > pow(10, -12)) && ($iterLimit-- > 0)) {
		$sinLambda = sin($lambda);
		$cosLambda = cos($lambda);
		$multiplier = ($cosU1 * $sinU2 - $sinU1 * $cosU2 * $cosLambda);
		$sinSigma = sqrt(($cosU2 * $sinLambda) * ($cosU2 * $sinLambda) + ($cosU1 * $sinU2 - $sinU1 * $cosU2 * $cosLambda) * $multiplier);
		if ($sinSigma == 0) {
			return 0;
		}
		$cosSigma = $sinU1 * $sinU2 + $cosU1 * $cosU2 * $cosLambda;
		$sigma = atan2($sinSigma, $cosSigma);
		$sinAlpha = $cosU1 * $cosU2 * $sinLambda / $sinSigma;
		$cosSqAlpha = 1 - $sinAlpha * $sinAlpha;
		$cos2SigmaM = $cosSigma - 2 * $sinU1 * $sinU2 / $cosSqAlpha;
		if (is_nan($cos2SigmaM)) {
			$cos2SigmaM = 0;
		}
		$C = $f / 16 * $cosSqAlpha * (4 + $f * (4 - 3 * $cosSqAlpha));
		$lambdaP = $lambda;
		$cos_sqrt = (-1 + 2 * $cos2SigmaM * $cos2SigmaM);
		$lambda = $L + (1 - $C) * $f * $sinAlpha * ($sigma + $C * $sinSigma * ($cos2SigmaM + $C * $cosSigma * $cos_sqrt));
	}
	if ($iterLimit == 0) {
		return "NaN";
	}
	// formula failed to converge 

	$uSq = $cosSqAlpha * ($a * $a - $b * $b) / ($b * $b);
	$A = 1 + $uSq / 16384 * (4096 + $uSq * (-768 + $uSq * (320 - 175 * $uSq)));
	$B = $uSq / 1024 * (256 + $uSq * (-128 + $uSq * (74 - 47 * $uSq)));
	$sin_sigma_sqrt = (-3 + 4 * $sinSigma * $sinSigma);
	$cos_sigma_sqrt = (-3 + 4 * $cos2SigmaM * $cos2SigmaM);
	$cos_2_sigma = (-1 + 2 * $cos2SigmaM * $cos2SigmaM);
	$part2 = ($cosSigma * $cos_2_sigma - $B / 6 * $cos2SigmaM * $sin_sigma_sqrt * $cos_sigma_sqrt);
	$deltaSigma = $B * $sinSigma * ($cos2SigmaM + $B / 4 * $part2);

	$s = $b * $A * ($sigma - $deltaSigma);
	return "Distance: " . $s;
}




function base_url($atRoot = FALSE, $atCore = FALSE, $parse = FALSE)
{
	if (isset($_SERVER['HTTP_HOST'])) {
		$http = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http';
		$hostname = $_SERVER['HTTP_HOST'];
		$dir =  str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);

		$core = preg_split('@/@', str_replace($_SERVER['DOCUMENT_ROOT'], '', realpath(dirname(__FILE__))), NULL, PREG_SPLIT_NO_EMPTY);
		$core = $core[0];

		$tmplt = $atRoot ? ($atCore ? "%s://%s/%s/" : "%s://%s/") : ($atCore ? "%s://%s/%s/" : "%s://%s%s");
		$end = $atRoot ? ($atCore ? $core : $hostname) : ($atCore ? $core : $dir);
		$base_url = sprintf($tmplt, $http, $hostname, $end);
	} else $base_url = 'http://localhost/';

	if ($parse) {
		$base_url = parse_url($base_url);
		if (isset($base_url['path'])) if ($base_url['path'] == '/') $base_url['path'] = '';
	}

	return $base_url;
}

/**
 * Validates a file name and path against an allowed set of rules.
 *
 * A return value of `1` means the file path contains directory traversal.
 *
 * A return value of `2` means the file path contains a Windows drive path.
 *
 * A return value of `3` means the file is not in the allowed files list.
 *
 * @since 1.2.0
 *
 * @param string   $file          File path.
 * @param string[] $allowed_files Optional. Array of allowed files.
 * @return int 0 means nothing is wrong, greater than 0 means something was wrong.
 */
function validate_file($file, $allowed_files = array())
{
	if (!is_scalar($file) || '' === $file) {
		return 0;
	}

	// `../` on its own is not allowed:
	if ('../' === $file) {
		return 1;
	}

	// More than one occurence of `../` is not allowed:
	if (preg_match_all('#\.\./#', $file, $matches, PREG_SET_ORDER) && (count($matches) > 1)) {
		return 1;
	}

	// `../` which does not occur at the end of the path is not allowed:
	if (false !== strpos($file, '../') && '../' !== mb_substr($file, -3, 3)) {
		return 1;
	}

	// Files not in the allowed file list are not allowed:
	if (!empty($allowed_files) && !in_array($file, $allowed_files, true)) {
		return 3;
	}

	// Absolute Windows drive paths are not allowed:
	if (':' === substr($file, 1, 1)) {
		return 2;
	}

	return 0;
}
