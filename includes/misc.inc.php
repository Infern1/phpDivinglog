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
{/*{{{*/
	$rs =  array();
	while(list($key,$val) = each($arr)) {
		if (is_array($val)) {
			$rs[$key] = htmlentities_array($val);
		} else {
            $rs[$key] = htmlentities($val, ENT_QUOTES, "UTF-8",0);
		}
	}
	return $rs;/*}}}*/
}

function action($value_of_clicked_field, $array_values) {
/*{{{*/

    global $_config;
    if($_config['multiuser'] && isset($_SESSION['user_id'])){
        $user_id = $_SESSION['user_id'];
        if($_config['query_string'])
        {
            $ext = "?user_id=$user_id&id=";
        } else {
            $ext = "/$user_id/";
        }
    } else {
        if($_config['query_string'])
        {
            $ext = "?id=";
        } else {
            $ext = "/";
        }
    }

    if(isset($_SESSION['request_type'])){        

        $request_type = $_SESSION['request_type'];
        if($request_type == 1){ 
            return "javascript:open_url(".$array_values["Number"].",'/index.php".$ext."' )";
        } elseif($request_type == 2){
            return "javascript:open_url(".$array_values["ID"].",'/divesite.php".$ext."' )";
        } elseif($request_type == 3){
            return "javascript:open_url(".$array_values["ID"].",'/equipment.php".$ext."' )";
        }
    }else {
        $request = new HandleRequest();
        $request->set_request_uri($_SERVER['REQUEST_URI']);
        $request->set_file_depth(0);
        $foo = $request->handle_url();
        $request_type = $_SESSION['request_type'];
        if($request_type == 1){ 
            return "javascript:open_url(".$array_values["Number"].",'/index.php".$ext."' )";
        } elseif($request_type == 2){
            return "javascript:open_url(".$array_values["ID"].",'/divesite.php".$ext."' )";
        } elseif($request_type == 3){
            return "javascript:open_url(".$array_values["ID"].",'/equipment.php".$ext."' )";
        }
    }
/*}}}*/
}

// Get the language values

// use english if it is not set in the configuration file
if(!isset($_config['language'])) {
	$_config['language'] = "english"; // if not set, get the english language file.
}
// first get the default english values
// so that we'll have values for anything not in the specified language file
if ($_config['language'] != "english") {
	if (!file_exists($_config['app_root'] .'includes/language/english.inc.php')) {
		print "<p>Language file includes/language/english.inc.php not found.</p>";
		exit;
	}
	include_once($_config['app_root'] . 'includes/language/english.inc.php');
}

// include the specified language file
$language_filename =  $_config['app_root'] . "includes/language/". $_config['language'] .".inc.php";
if (!file_exists($language_filename)) {
   print "<p>Language file '". $language_filename ."' not found.</p>";
   exit;
}
$_lang = array();
include_once ($language_filename);

// Convert applicable characters in the language file to entities
//$_lang = htmlentities_array($_lang);	


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
function sql_file($filename){
	global $_config; /*{{{*/
	$sqlpath = $_config['sqlpath'];
	global $globals;

    if($_config['multiuser'] && isset($_SESSION['user_id'])){
        $user_id = $_SESSION['user_id'];
        $_config['table_prefix'] = $_config['user_prefix'][$user_id];
    } else {
    }

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
	
		if ($_config['debug']) {
			echo "<hr>\n<b>".$location.":</b><br>\n<br>\n";
			echo nl2br(htmlentities($contents))."<br>\n<hr>\n";
		}
	
		return $contents;
	} else {
		return false;
	} /*}}}*/
}


/**
 * parse_mysql_query 
 * 
 * @param mixed $filename 
 * @access public
 * @return void
 */
function parse_mysql_query($filename, $sql_query = 0, $debug = false){
    global $_config; /*{{{*/
    $username = $_config['database_username'];
	$password = $_config['database_password'];
	$server = $_config['database_server'];
	$db = $_config['database_db'];

    $result = array();
    if(($sql_query)){
        $query = $sql_query;
    } else {
        $query = sql_file($filename);
    }
    if ($query) {
        $connection = mysql_connect($server, $username, $password);
		mysql_select_db($db, $connection);
        mysql_query("SET CHARACTER SET 'utf8'", $connection);
		$server_query = mysql_query($query, $connection);
        if (mysql_errno()  ) {
			echo "<hr>\n<b>MySQL error " . mysql_errno(). ": " . mysql_error() . "\n:</b><br>\n";
		    echo "Query: $query <br><hr>";
            exit;
        }
		if(mysql_num_rows($server_query) == 1){
            $result = mysql_fetch_assoc($server_query);
        } else {
            for($i=0; $query_output = mysql_fetch_assoc($server_query); $i++) {
                while(list($key, $val) = each($query_output)) {
                    if(is_string($val)) {
                        //$val = utf8_encode($val);
                        $query_output[$key] = $val;
                    }
                }
                $result[$i] = $query_output;
            }
        }
    }
	return $result; 
    /*}}}*/
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
	if (!$number) { /*{{{*/
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
	return $get; /*}}}*/
}

/**
 * GetRequestVar get the info from the url request and split it into chunks
 *
 * @param mixed $url
 * @param mixes $request_file_depth
 * @access public
 * return void
 */
function GetRequestVar($url, $request_file_depth=0){ 
/*{{{*/
    global $_config, $t,$_POST;
    $paginas = NULL;
    if($_config['query_string']){
        $url_array = parse_url($url);
        if(isset($url_array['query'])){
            parse_str($url_array['query'],$output);
            $paginas = array();
            $file = basename($url_array['path']);
            $paginas[0] = $file;
            if(!isset($output['DG_ajaxid']) && !isset($output['pageID']) ){
                foreach($output as $el){
                    $paginas[] .= $el;
                }
            } elseif(isset($output['pageID'])){
                if($_config['multiuser']){
                    $paginas[] .= $output['user_id']; 
                }
                $paginas[] .= "list";
            } else {
                $paginas[] .= $output['user_id'];
                $paginas[] .= $output['id'];
            }
        }
    } else {
        $number_folders =  $request_file_depth ; //number of folders from the root of the script
        $adres = $url;
        $possessid = strpos($adres,"?PHPSESSID");
        if ($possessid !== false) {
            $adres = substr($adres,0,$possessid);
        }
        //$adres = $_SERVER['REQUEST_URI'];
        $adres = substr($adres,1);
        $adres = $adres."/";
        $array = explode("/",$adres);
        $paginas = array();
        for($i = $number_folders; $i< count($array) ; $i++)
        {
            if(!empty($array[$i])){ 
                $paginas[] = $array[$i]; 
            }  
        }

    }
    //print_r($paginas);
    return $paginas; /*}}}*/
}

/**
 * is__writable It can accept files or folders, but folders should end with a trailing slash! The function attempts to actually
 * write a file, so it will correctly return true when a file/folder can be written to when the user has ACL write access to it.
 * 
 * @param mixed $path 
 * @access public
 * @return void
 */
function is__writable($path) {
//will work in despite of Windows ACLs bug /*{{{*/
//NOTE: use a trailing slash for folders!!!
//see http://bugs.php.net/bug.php?id=27609
//see http://bugs.php.net/bug.php?id=30931

    if ($path{strlen($path)-1}=='/') // recursively return a temporary file path
        return is__writable($path.uniqid(mt_rand()).'.tmp');
    else if (is_dir($path))
        return is__writable($path.'/'.uniqid(mt_rand()).'.tmp');
    // check tmp file for read/write capabilities
    $rm = file_exists($path);
    $f = @fopen($path, 'a');
    if ($f===false)
        return false;
    fclose($f);
    if (!$rm)
        unlink($path);
    return true; /*}}}*/
}

/**
 * GetProfileData Extract averagedepth and sac  info from the profile data
 * 
 * @param mixed $result 
 * @access public
 * @return void
 */
function GetProfileData($result){
    	global $_config; /*{{{*/
        global $_lang;
        $profile = $result['Profile'];
        $length = ( strlen($profile) / 12 );
		$profileint = ($result['ProfileInt'] / 60);
        /**
         * Divetime calculation changed to Divetime from Divelog table see:
         * http://www.divinglog.de/phpbb/viewtopic.php?p=3094#3094
         */
        $divetime = $profileint * $length;
        if(isset($result['Divetime'])){
            $divetime = $result['Divetime'];
        } else {
            $divetime = $profileint * $length;
        }
		$start = 0;
		$ydata = 0;
		for ($i=0; $i < $length; $i++) {
        		$ydata = $ydata + (substr(substr($profile,$start,12),0,5) / 100);
		        $start += 12;
		}
		$averagedepth = $ydata / $length;
		$sac = (($result['PresS'] - $result['PresE']) * $result['Tanksize']) / ($divetime * ($averagedepth / 10 + 1));

		if ($_config['length']) {
			$averagedepth = MetreToFeet($averagedepth / 3.2808399, 2) ."&nbsp;";
		} else {
			$averagedepth = number_format($averagedepth, 2) ."&nbsp;";
		}
		if ($_config['volume']) {
			$sac = LitreToCuft($sac, 2) ."&nbsp;". $_lang['unit_rate_imp'];
		} else {
			$sac = number_format($sac, 2) ."&nbsp;". $_lang['unit_rate'];
		}
        return array('averagedepth' => $averagedepth , 'sac' => $sac); /*}}}*/
}


define('MetreToFeet', "calc:(Depth*3.2808399)");
function MetreToFeet($value, $precision = 2) 
{
    return round(($value * sqrt( 3.2808399 ) ), $precision);
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
 * backhtmlentities resolve problem for displaying wrong characters
 * 
 * @param mixed $str_h 
 * @access public
 * @return void
 */
function backhtmlentities($str_h){
   $trans = get_html_translation_table(HTML_ENTITIES); /*{{{*/
   $trans = array_flip($trans);
   $str_h = strtr($str_h, $trans);
   return $str_h; /*}}}*/
}

/**
 * DECtoDMS  Converts decimal longitude / latitude to d, dm or dms
 * ( degrees / minutes / seconds ) 
 * This is the piece of code which may appear to 
 * be inefficient, but to avoid issues with floating
 * point math we extract the integer part and the float
 * part by using a string function.
 * @param mixed $dec 
 * @access public
 * @return void
 */
function DECtoDMS($dec, $fmt) {
	if ($dec == "") { /*{{{*/
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
	return $dms; /*}}}*/
} 


/**
 * convert_date converts the date to format the user whishes
 * 
 * @param mixed $date 
 * @access public
 * @return void
 */
function convert_date($value){
    global $_config; /*{{{*/
    $mask = $_config['date_format'];
    if($value != "") {
        $format='';
        $separator='';
        if (strpos($mask,':')>0){
            $arrMask=explode(':',$mask);
            $theType=$arrMask[0]; 
            $format=(empty($arrMask[1])) ? $format : $arrMask[1];
            $separator=(empty($arrMask[2])) ? $separator: $arrMask[2];
        }
        $arrDdate = datecheck($value,'ymd','-', $format, $separator);
        if ($arrDdate != false)    $value =$arrDdate['todate'] ;
    } 
    return  $value;
/*}}}*/
}

/**
 * datecheck 
 * 
 * @param mixed $date 
 * @param string $format 
 * @param string $separator 
 * @param string $toformat 
 * @param string $toseparator 
 * @access public
 * @return void
 */
function datecheck($date,$format='ymd',$separator='-',$toformat='mdy',$toseparator='-') {
        $format = ($format=='')?'ymd':strtolower($format); /*{{{*/
        if (count($datebits=explode($separator,$date))!=3) return false;
        $year = intval($datebits[strpos($format, 'y')]);
        $month = intval($datebits[strpos($format, 'm')]);
        $day = intval($datebits[strpos($format, 'd')]);
        $year=($year <10 )? '200'.$year:$year;
        $year=($year <50 )? '20' .$year:$year;
        $year=($year <100)? '19' .$year:$year;
        $month=($month <10)? '0' .$month:$month;
        $day=($day <10)? '0' .$day:$day;
        if (($month<1) || ($month>12) || ($day<1) || (($month==2) && ($day>28+(!($year%4))-(!($year%100))+(!($year%400)))) || ($day>30+(($month>7)^($month&1)))) return false; // date out of range 
        $arrDate= array('y' => $year, 'm' => $month, 'd' => $day, 'iso' => $year.'-'.$month.'-'.$day, 'fromdate'=> $date, 'todate' => '' );
        $arrDate['todate'] = $arrDate[$toformat[0]].$toseparator.$arrDate[$toformat[1]].$toseparator.$arrDate[$toformat[2]];
        return $arrDate; /*}}}*/
}

/**
 * add_unit_depth 
 * 
 * @param mixed $value 
 * @access public
 * @return void
 */
function add_unit_depth($value){
    global $_config, $_lang; /*{{{*/
    if(!empty($value)){
        if($_config['length']){
            $value .=  " ".$_lang['unit_length_short_imp']  ;
        } else {
            $value .= " ".$_lang['unit_length_short']  ;
        }
    }
    return $value; /*}}}*/
}

/**
 * add_unit_time 
 * 
 * @param mixed $value 
 * @access public
 * @return void
 */
function add_unit_time($value){
    global $_config, $_lang; /*{{{*/
    $value .=  " ".$_lang['unit_time_short'];
    return $value; /*}}}*/
}

/**
 * latitude_format 
 * 
 * @param mixed $coord 
 * @access public
 * @return void
 */
function latitude_format($coord){
//	Change coordinates into a displayable format
	global $_config; /*{{{*/

	if ($coord == "") {
		$dms = "";
	} else {
//		Converts decimal latitude to degrees / minutes / seconds
		$dms = DECtoDMS(abs($coord),$_config['coord_format']);

//		Add North or South
		if ($coord < 0) {
			$dms .= " S";
		} elseif ($coord > 0) {
			$dms .= " N";
		}
	}
	return $dms; /*}}}*/
}

/**
 * longitude_format 
 * 
 * @param mixed $coord 
 * @access public
 * @return void
 */
function longitude_format($coord){
//	Change coordinates into a displayable format
	global $_config; /*{{{*/

	if ($coord == "") {
		$dms = "";
	} else {
//		Converts decimal longitude to degrees / minutes / seconds
		$dms = DECtoDMS(abs($coord),$_config['coord_format']);

//		Add East or West
		if ($coord < 0) {
			$dms .= " W";
		} elseif ($coord > 0) {
			$dms .= " E";
		}
	}
	return $dms; /*}}}*/
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
//    $_config['table_prefix'] = $prefix;
}

/**
 * reset_config_table_prefix 
 * 
 * @access public
 * @return void
 */
function reset_config_table_prefix(){
    global $_config;
//    unset($_config['table_prefix']);
}

/**
 * resize_image 
 * 
 * @param mixed $img 
 * @access public
 * @return void
 */
function resize_image($img){
    global $_config; /*{{{*/
    $obj = new Thumbnail($img); 
    $obj->size_width($_config['pic-width']);
    $obj->process();
    $obj->save($img); /*}}}*/
}

/**
 * make_thumb 
 * 
 * @param mixed $img 
 * @param mixed $thumb 
 * @access public
 * @return void
 */
function make_thumb($img,$thumb, $i = 0 ){
    global $_config, $t; /*{{{*/
    $obj = new Thumbnail($img);
    $obj->size_auto($_config['thumb-width']); 
    $obj->process();
    $t->assign('resize',1);
    $t->assign('img',$img);
    set_time_limit(30);
    $obj->save($thumb);
    flush();
    //    echo "Error". $obj->error_msg; /*}}}*/
}

/**
 * count_all count non-empty elements in an array of any dimension
 * 
 * @param mixed $arg 
 * @access public
 * @return void
 */
function count_all($arg){
    // skip if argument is empty /*{{{*/
    if ($arg){ 
        // not an array, return 1 (base case) 
        if(!is_array($arg)) 
            return 1; 
    // else call recursively for all elements $arg 
    $count =0;
    foreach($arg as $key => $val) 
        $count += count_all($val); 
        return $count;       
    } /*}}}*/
} 
?>
