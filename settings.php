<?php
/** 
 * Filename: settings.php
 * Function: This file which contains settings for Phpdivinglog which don't need to be changed normally
 * @author  Lloyd Borrett - www.borrett.id.au ; Rob Lensen <rob@bsdfreaks.nl>
 * @license LGPL v3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * @package phpdivinglog
 * @version $Rev: 202 $
 * Last Modified: $Date$
 * 
*/

/**
 * APPLICATION DETAILS
 * application name and revision number
 */
$_config['app_name'] = "phpDivingLog";
$_config['app_version'] = "3.0-alpha";
$_config['app_url'] = "http://www.borrett.id.au/interests/phpdivinglog.htm";
$_config['dlog_url'] = "http://www.divinglog.de/";
$_config['dlog_version'] = "5.0";

/**
 * FILE LOCATIONS
 * set the file locations of the various graphic images you
 * want to be able to display
 */

/**
 * ABS path to your pear installation
 */
$_config['pear_path']       = $_config['app_root'] .  'includes/pear';  
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
ini_set('include_path', get_include_path() . PATH_SEPARATOR . 
                        $_config['pear_path'] . PATH_SEPARATOR . 
                        $_config['app_root']."includes/". PATH_SEPARATOR . 
                        $_config['app_root']."includes/jpgraph/"
                        );


/**
 * Fix $_SERVER['REQUEST_URI'] which is not set on Windows hosts 
 */
if(!isset($_SERVER['REQUEST_URI'])) {
    if(isset($_SERVER['SCRIPT_NAME']))
        $_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
    else
        $_SERVER['REQUEST_URI'] = $_SERVER['PHP_SELF'];
    if($_SERVER['QUERY_STRING']){
        $_SERVER['REQUEST_URI'] .= '?'.$_SERVER['QUERY_STRING'];
    }
}

require_once (ABSPATH_DIVELOG . 'includes/misc.inc.php');
require_once (ABSPATH_DIVELOG . 'includes/image-resize.php');
require_once (ABSPATH_DIVELOG . 'includes/class.datagrid.php');
// hack version example that works on both *nix and windows
// Smarty is assumend to be in 'includes/' dir under current script
define('SMARTY_DIR',str_replace("\\","/",getcwd()).'/includes/smarty/');
require_once(SMARTY_DIR . 'Smarty.class.php');

require_once 'classes.inc.php';
require_once 'PEAR.php';

require_once 'jpgraph.php';
require_once 'jpgraph_line.php';
require_once 'jpgraph_pie.php';

require_once 'Pager/Pager.php';
require_once 'Pager_Wrapper.php';
require_once 'MDB2.php';


/**
 * Smarty part shouldn't need a change 
 */
$t = new Smarty;
$t->setTemplateDir(TEMPLATE_DIR);

$t->setCompileDir($_config['app_root'] .DIRECTORY_SEPARATOR . 'compile');
/**
 * Before smarty we gonna check the rights in the compile dir 
 */
if(!is__writable($t->getCompileDir()."/")){
echo "Change the right on ".  $t->compile_dir ." so the webuser can write<br>
chmod -R 777 " .$t->compile_dir ;
}

$t->setCacheDir($_config['app_root'] . DIRECTORY_SEPARATOR . 'cache');
$t->setPluginsDir(array($_config['app_root'] . 'includes/smarty/plugins'));

/**
 *  Change comment on these when you're done developing to improve performance
 */
//$t->force_compile = false;
//$t->caching = false;

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
$t->assign('thumb_width',$_config['thumb-width']);
$t->assign('thumb_height',$_config['thumb-height']);

if(($_config['embed_mode'])){
    $t->assign('embed',true);
}

/**
 * Set to true for debug mode 
 */
$_config['debug'] = false;

## GLOBALS:  $db, $t
session_start();

?>
