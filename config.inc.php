<?php
/**
 * Filename:  includes/config.inc.php
 * Function:  Configuration file for phpDivingLog.
 * @author  Lloyd Borrett - www.borrett.id.au Rob Lensen <rob@bsdfreaks.nl>
 * @package phpdivinglog
 * @version $Rev$
 * Last Modified: $Date$
 */

/**
 *  database settings
 */
$_config['database_server'] = "localhost";
$_config['database_db'] = "phpdivinglog";
$_config['database_username'] = "dive";
$_config['database_password'] = "somepassword";

/**
 * Define the connection type use mysqli for php5 (check if mysqli is installed), otherwise enter mysql
 * The driver mysqli is not used everywhere (planned)
 */
$_config['database_type'] = "mysql"; 
/**
 * Enter prefix for single user mode (for multi user see below) 
 */
$_config['table_prefix'] = ""; 
/**
 *  language setting determines which language file is used
 */
$_config['language'] = "english";

/**
 * Some hosts don't support mod_rewrite, so we must have option to use old
 * query strings  like ?id=&user=
 * If query_string = false normal rewrite is used, if set to true query string is used
 */
$_config['query_string'] = false;

/**
 * change this to your website url
 */
$_config['web_root']        = 'http://www.mydivesite.com/divelog'; 
/**
 * Your path where divelog is located
 * EXAMPLE: http://www.foo.com/users/j/jo/john
 * abs_url_path = /users/j/jo/john
 * Nothing todo where phpdivinglog is installed on your harddrive!
 */
$_config['abs_url_path']    = '/divelog'; 

/**
 * See below for more file location settings 
 */

/**
 * Define if we need to embed phpDivinglog
 * @todo make embedding possible 
 */
$_config['embed_mode'] = false;

/**
 * when view_type = 1 grid with ajax is displayed view_type = 2 old table mode 
 */
$_config['view_type'] = 1;

/**
 * When multiuser is set to true, you need to define the table prefixes
 * Data for all the users should be in the same database
 */
$_config['multiuser'] = false;
/**
 * Define the table prefix for each user
 * $_config['user_prefix'][user_id] = 'table_prefix'
 * The user_id should start with 1 and can be defined. 
 */
$_config['user_prefix'][1] = 'rob';
$_config['user_prefix'][2] = 'sjaak';
$_config['user_prefix'][3] = 'sjaak2';

/**
 * number of items per page in the lists
 */
$_config['max_list'] = 15;

/**
 * Set to true if you want to display to profile in the dive detail 
 */
$_config['show_profile'] = true;

/**
 * comments in RTF format?
 */
$_config['dlog_comments_rtf'] = true;

/**
 * specify what to show in user information
 * @todo make these option work 
 */
/*
$_config['user_show'] = true;
$_config['user_show_general'] = true;
$_config['user_show_comments'] = true;
$_config['user_show_photo'] = true;
$_config['user_show_certs'] = true;
$_config['user_show_medical'] = true;
*/

/**
 * DIVE PROFILE SETTINGS
 * name of the graphic image to use as the dive profile background
 * image size is expected to be 550 x 400
 * set to "" for no background image
 * $_config['graph_background_image'] = "images/jellyfish-and-divers.jpg";
 */
$_config['graph_background_image'] = "";

/**
 * set to 'true' to show Y scales on two sides
 * set to 'false' to show Y scale only on left side
 */
$_config['graph_show_two_scales'] = true;

/**
 * set to 'true' to show both metric and imperial units on chart scales
 * set to 'false' to show only the length units specified by the unit conversion value
 */
$_config['graph_show_both_units'] = true;

/**
 * UNIT CONVERSION 
 * set values to true if you want to convert from metric units to imperial units,
 * or to false if values should be left as metric units. 
 */

/**
 * metres to feet
 */
$_config['length'] = false;

/**
 * bar to psi
 */
$_config['pressure'] = false;

/**
 * kilograms (kg) to pounds (lbs)
 */
$_config['weight'] = false;

/**
 * Celsius to Farenheight
 */
$_config['temp'] = false;

/**
 * litres to cubic feet
 */
$_config['volume'] = false;


/**
 * APPLICATION DETAILS
 * application name and revision number
 */
$_config['app_name'] = "phpDivingLog";
$_config['app_version'] = "2.0";


/**
 * FILE LOCATIONS
 * set the file locations of the various graphic images you
 * want to be able to display
 */
/**
 * ABS path to your website (no need to change normaly)
 */
define('ABSPATH_DIVELOG', dirname(__FILE__).'/');
$_config['app_root']        = ABSPATH_DIVELOG;  
/**
 * ABS path to your pear installation
 */
$_config['pear_path']       = $_config['app_root'] . DIRECTORY_SEPARATOR . 'pear'. DIRECTORY_SEPARATOR ;  
/**
 * logbook pics
 */
$_config['picpath_web']     = "images/pictures/"; 
/**
 * place maps
 */
$_config['mappath_web'] = "images/maps/";
/**
 * equipment pics
 */
$_config['equippath_web'] = "images/equipment/";
/**
 *  country flags
 */
$_config['flagpath_web'] = "images/flags/"; 
/**
 *  user photo and certificate scans
 */
$_config['userpath_web'] = "images/userinfo/"; 
/**
 *  buddy photos
 */
$_config['buddypath_web'] = "images/buddies/"; 
/**
 * path to sql select statements 
 */
$_config['sqlpath'] = $_config['app_root']."/sql/";


/**
 *
 * Normally no need to change below here!!!
 * If that's the case maybe support a BUG
 *
 */


/**
 * Path to .ihtml (template) files
 */
define('TEMPLATE_DIR', $_config['app_root']. '/tpl/');
if ( ! defined( "PATH_SEPARATOR" ) ) {
  if ( strpos( $_ENV[ "OS" ], "Win" ) !== false )
      define( "PATH_SEPARATOR", ";" );
  else define( "PATH_SEPARATOR", ":" );
}
ini_set('include_path', get_include_path() . PATH_SEPARATOR . $_config['pear_path'] . PATH_SEPARATOR . $_config['app_root'].DIRECTORY_SEPARATOR."include". PATH_SEPARATOR . $_config['app_root']);


require_once (ABSPATH_DIVELOG . '/includes/misc.inc.php');
require_once 'phpmydatagrid.class.php';
require_once 'smarty/Smarty.class.php';
require_once 'classes.inc.php';
require_once 'PEAR.php';
require_once 'Pager_Wrapper.php';
require_once 'MDB2.php';
require_once 'includes/jpgraph/src/jpgraph.php';
require_once 'includes/jpgraph/src/jpgraph_line.php';




/**
 * Smarty part shouldn't need a change 
 */
$t = new smarty;
$t->template_dir = TEMPLATE_DIR;

$t->compile_dir = $_config['app_root'] .DIRECTORY_SEPARATOR . 'compile';
/**
 * Before smarty we gonna check the rights in the compile dir 
 */
if(!is__writable($t->compile_dir."/")){
echo "Change the right on ".  $t->compile_dir ." so the webuser can write<br>
chmod -R 777 " .$t->compile_dir ;
}

$t->cache_dir = $_config['app_root'] . DIRECTORY_SEPARATOR . 'cache';
$t->plugins_dir = array($_config['app_root'] . '/include', $_config['app_root'] . '/smarty/plugins');

/**
 *  Change comment on these when you're done developing to improve performance
 */
$t->force_compile = false;
$t->caching = false;

/**
 * Define settings for MDB2 connection
 */
$dsn = array(
        'phptype'  => $_config['database_type'],
        'username' => $_config['database_username'],
        'password' => $_config['database_password'],
        'hostspec' => $_config['database_server'],
        'database' => $_config['database_db'],
        );

$options = array(
        'debug'       => 2,
        'portability' => MDB2_PORTABILITY_ALL,
        );

$db =& MDB2::connect($dsn, $options);
if (PEAR::isError($db)) {
  die($db->getMessage());
}


/**
 * Assign any global smarty values here.
 */
$t->assign('title', 'My divelog online');
$t->assign('web_root', $_config['web_root']);
$t->assign('app_path',$_config['abs_url_path']);

/**
 * Set to true for debug mode 
 */
$_config['debug'] = false;

## GLOBALS:  $db, $t
session_start();

?>
