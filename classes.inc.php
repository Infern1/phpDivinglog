<?php
/**
 * Classes file contains all classes needed for phpdivinglog
 * 
 * @package phpdivinglog
 * @version $Rev$
 * @copyright Copyright (C) 2007 Rob Lensen. All rights reserved.
 * @author Rob Lensen <rob@bsdfreaks.nl> 
 * @license LGPL v3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * $Date$
 */


/**
 * HandleRequest handles the url and set the settings accordingly
 * 
 * @package phpdivinglog
 * @copyright Copyright (C) 2007 Rob Lensen. All rights reserved.
 * @author Rob Lensen <rob@bsdfreaks.nl> 
 * @license LGPL v3 http://www.gnu.org/licenses/lgpl-3.0.txt
 */
class HandleRequest {
    /*{{{*/
    var $request_uri;     
    var $request_file_depth;
    var $multiuser;
    var $user_id;
    var $equipment_nr;
    var $dive_nr;
    var $site_nr;
    var $diver_choice;
    var $divetrip_nr;
    var $diveshop_nr;
    var $divecountry_nr;
    var $divecity_nr;
    var $special_req;

    /**
     * requested_page contains the requested page of the paginate
     * 
     * @var mixed
     * @access public
     */
    var $requested_page;
    /**
     * view_request
     * 0 = overview
     * 1 = details
     * 
     * @var mixed
     * @access public
     */
    var $view_request;
    /**
     * request_type 
     * 1 = dives ; 
     * 2 = divesite 
     * 3 = dive equipment
     * 4 = dive statistics
     * 5 = dive profile or dive piechart
     * 6 = dive gallery
     * 7 = resizer
     * 8 = dive trip
     * 9 = dive shop
     * 10 = country
     * 11 = city
     *
     * @var mixed
     * @access public
     */
    var $request_type;     

    /**
     * HandleRequest constructor for the class, sets the defaults from the config file
     * 
     * @access public
     * @return void
     */
    function HandleRequest() {
        global $_config, $t; /*{{{*/
        $this->multiuser = $_config['multiuser'];
        $_SESSION['request_type'] = 1;
        $_SESSION['user_id'] = NULL;

        if ($_config['query_string']) {
            if ($this->multiuser) {
                $t->assign('sep1','?user_id=');
                $t->assign('sep2','&id=');
                $t->assign('list','&view=list');
                $t->assign('service','&view=service');
                $t->assign('kml','&view=kml');
                $t->assign('gpx','&view=gpx');
            } else {
                $t->assign('sep2','?id=');
                $t->assign('list','?view=list');
                $t->assign('service','?view=service');
                $t->assign('kml','?view=kml');
                $t->assign('gpx','?view=gpx');
            }
        } else {
            $t->assign('sep1',"/");
            $t->assign('sep2',"/");
            $t->assign('list','/list');
            $t->assign('service','/service');
            $t->assign('kml','/kml');
            $t->assign('gpx','/gpx');
        }
    /*}}}*/
    }

    /**
     * set_request_uri 
     * 
     * @param mixed $uri 
     * @access public
     * @return void
     */
    function set_request_uri($uri) {
        $this->request_uri = $uri;
    }

    /**
     * set_file_depth according the location of the file
     *
     * if the file is in the root of the app, the depth is 0
     * this function will find the absolute depth of the application
     * say the app url is foo.com/bar/foo/user/divelog we have to add 3 to the depth
     * (bar + foo + user) = 3
     * 
     * @param mixed $depth 
     * @access public
     * @return void
     */
    function set_file_depth($depth = 0) {
        global $_config;
        $app_depth = count_all((preg_split("#/#", $_config['abs_url_path'])));
        $this->request_file_depth = $depth + $app_depth;
    }

    function get_user_id() {
        return $this->user_id;
    }

    function get_dive_nr() {
        return $this->dive_nr;
    }

    function get_site_nr() {
        return $this->site_nr;
    }

    function get_equipment_nr() {
        return $this->equipment_nr;
    }

    function get_request_type() {
        return $this->get_request_type;
    }

    function get_view_request() {
        return $this->view_request;
    }

    function get_requested_page() {
        return $this->requested_page;
    }

    function get_multiuser() {
        return $this->multiuser;
    }

    function get_divetrip_nr() {
        return $this->divetrip_nr;
    }

    function get_diveshop_nr() {
        return $this->diveshop_nr;
    }

    function get_divecountry_nr() {
        return $this->divecountry_nr;
    }

    function get_divecity_nr() {
        return $this->divecity_nr;
    }
    

    /**
     * handle_url will handle the url according the type of setup. 
     * The url handling differs between single and multiuser setup 
     * 
     * @access public
     * @return void
     */
    function handle_url() {
    /*{{{*/
        global $_config, $t;
        if ($this->multiuser) {
            //  The url should contain a least one select person, otherwise return to the person chooser
            // Get the last two parts of the array
            $split_request = GetRequestVar($this->request_uri, $this->request_file_depth);
            // check if the user is set, otherwise show person chooser
            if (isset($split_request[1]) && Users::is_valid_user($split_request[1])) {
                $file_req = $split_request[0];
                $this->user_id = check_number($split_request[1]);
                $_SESSION['user_id'] = $this->user_id;
                if (isset($split_request[2]) && ($split_request[2] == 'list')) {
                    $this->view_request = 0;
                    if (isset($split_request[3])) {
                        $this->requested_page = $split_request[3];
                    }
                } elseif (isset($split_request[2])) {
                    $t->assign('dive_detail' ,1);
                    $this->view_request = 1;    
                } else {
                    $this->diver_choice = true;
                }
                switch ($file_req) {
                    case "index.php":
                        if ($this->view_request == 1)
                            $this->dive_nr = check_number($split_request[2]);
                        $this->request_type = 1;
                        break;
                    case 'divesite.php':
                        if ($this->view_request == 1)
                            $this->site_nr = check_number($split_request[2]);
                        $this->request_type = 2;
                        break;
                    case 'equipment.php':
                        if ($this->view_request == 1)
                            $this->equipment_nr = check_number($split_request[2]);
                        $this->request_type = 3;
                        break;
                    case 'divestats.php':
                        $this->request_type = 4;
                        break;
                     case 'drawprofile.php':
                        $this->request_type = 5;
                        if ($this->view_request == 1)
                            $this->dive_nr = check_number($split_request[2]);
                        break;
                    case 'drawpiechart.php':
                        $this->diver_choice = false;
                        $this->request_type = 5;
                        break;                   
                    case 'divesummary.php':
                        $this->diver_choice = false;
                        $this->request_type = 5;
                        break;
                    case 'divegallery.php':
                        $this->request_type = 6;
                        break;
                     case 'resize.php':
                        $this->request_type = 7;
                        break;
                    case 'divetrip.php':
                        if ($this->view_request == 1)
                            $this->divetrip_nr = check_number($split_request[2]);
                        $this->diver_choice = true;
                        $this->request_type = 8;
                        break;
                    case 'diveshop.php':
                        if ($this->view_request == 1)
                            $this->diveshop_nr = check_number($split_request[2]);
                        $this->request_type = 9;
                        break;
                    case 'divecountry.php':
                        if ($this->view_request == 1)
                            $this->divecountry_nr = check_number($split_request[2]);
                        $this->request_type = 10;
                        break;
                    case 'divecity.php':
                        if ($this->view_request == 1)
                            $this->divecity_nr = check_number($split_request[2]);
                        $this->request_type = 11;
                        break;
                    default:
                        $this->diver_choice = true;
                        $this->request_type = 1;
                    break;

                }
                $_SESSION['request_type'] = $this->request_type;

            } else {
                $this->diver_choice = true;
            }
        } else {
            // Find what the client wants to see and if a record is requested set it
            $split_request = GetRequestVar($this->request_uri, $this->request_file_depth);
            if (!empty($split_request[0])) {
                if (isset($split_request[1]) && ($split_request[1] == 'list')) {
                    //var_dump($split_request);
                    $this->view_request = 0;
                    if (($_config['view_type'] == 2) && isset($split_request[2]) && !isset($split_request[3]) ) {
                        //Get the paginate page only if view is type 2
                        $this->requested_page = $split_request[2];
                    } elseif ($_config['view_type'] == 1 && isset($split_request[2])) {
                        //There some special request so get this info
                        //Set the special request so the class will be able to check for it
                        $this->special_req = $split_request[2];
                    } elseif (($_config['view_type'] == 2) && isset($split_request[2]) && isset($split_request[3])) {
                        $this->requested_page = $split_request[3];
                        //There some special request so get this info
                        //Set the special request so the class will be able to check for it
                        $this->special_req = $split_request[2];
                    }

                } elseif (isset($split_request[1])) {
                    $t->assign('dive_detail' ,1);
                    $this->view_request = 1;    
                }

                $file_req = $split_request[0];
                $id = 0;
                if (!empty($split_request[1])) {
                    $id = check_number($split_request[1]);
                }

                switch ($file_req) {
                    case 'index.php':
                        $this->dive_nr = $id;
                        $this->request_type = 1;
                        break;
                    case 'divesite.php':
                        $this->site_nr = $id;
                        $this->request_type = 2;
                        break;
                    case 'equipment.php':
                        $this->equipment_nr = $id;
                        $this->request_type = 3;
                        break;
                    case 'divestats.php':
                        $this->user_id = $id;
                        $this->request_type = 4;
                        break;
                    case 'divesummary.php':
                        $this->user_id = $id;
                        $this->request_type = 4;
                        break;
                    case 'drawprofile.php':
                        $this->dive_nr = $id;
                        $this->request_type = 5;
                        break;
                    case 'divegallery.php':
                        $this->user_id = $id;
                        $this->request_type = 6;
                        break;
                    case 'resize.php':
                        $this->user_id = $id;
                        $this->request_type = 7;
                        break;
                    case 'divetrip.php':
                        $this->divetrip_nr = $id;
                        $this->request_type = 8;
                        break;
                    case 'diveshop.php':
                        $this->diveshop_nr = $id;
                        $this->request_type = 9;
                        break;
                    case 'divecountry.php':
                        $this->divecountry_nr = $id;
                        $this->request_type = 10;
                        break;
                    case 'divecity.php':
                        $this->divecity_nr = $id;
                        $this->request_type = 11;
                        break;
                    default:
                        // defaults to main page
                        break;
                }
                $_SESSION['request_type'] = $this->request_type;
            }
        }
    /*}}}*/
    }

/*}}}*/
}

/**
 * User contains functions for setting and getting the user info
 * 
 * @package phpdivinglog
 * @copyright Copyright (C) 2007 Rob Lensen. All rights reserved.
 * @author Rob Lensen <rob@bsdfreaks.nl> 
 * @license LGPL v3 http://www.gnu.org/licenses/lgpl-3.0.txt
 */
class User {
    var $user_id; /*{{{*/
    var $table_prefix;
    var $username;
    var $multiuser;
    
    /**
     * User 
     * 
     * @access public
     * @return void
     */
    function User() {
        global $_config;
        $this->multiuser = $_config['multiuser'];
        if (!$this->multiuser) {
            if (isset($_config['table_prefix'])) {
                $this->table_prefix = $_config['table_prefix'];
            }
        }
    }

    /**
     * set_user_id 
     * 
     * @param mixed $id 
     * @access public
     * @return void
     */
    function set_user_id($id) {
        $this->user_id = $id;
        /**
         *  Set table prefix according config file
         *  @todo maybe get the table prefix from some table
         */
         $this->set_table_prefix($id);
    }
    
    /**
     * get_username 
     * 
     * @access public
     * @return void
     */
    function get_username() {
        global $_config, $globals;
        $user_data = parse_mysql_query('personal.sql',0,TRUE);
        $this->username = '';
        if (isset($user_data['FirstName']) && ($user_data['FirstName'] != "")) {
            $this->username = $user_data['FirstName'];
        }
        if (isset($user_data['LastName']) && ($user_data['LastName'] != "")) {
            if ($this->username == '') {
                $this->username = $user_data['LastName'];
            } else {
                $this->username .= ' '.$user_data['LastName'];
            }
        }
        return $this->username;
    }

    /**
     * set_table_prefix 
     * 
     * @param mixed $id 
     * @access public
     * @return void
     */
    function set_table_prefix($id) {
        global $_config;
        // Get the prefix from the config file
        if (isset($_config['user_prefix'][$id])) {
            $this->table_prefix = $_config['user_prefix'][$id];
        }
    }

    function get_table_prefix() {
        return $this->table_prefix;
    }
/*}}}*/
}

/**
 * Grid Functions for the phpMyDatagrid grid which are globally used
 * 
 * @package phpdivinglog
 * @copyright Copyright (C) 2007 Rob Lensen. All rights reserved.
 * @author Rob Lensen <rob@bsdfreaks.nl> 
 * @license LGPL v3 http://www.gnu.org/licenses/lgpl-3.0.txt
 */

class TableGrid{
    var $language; /*{{{*/
    var $gridtable;

    /**
     * Grid 
     * 
     * @access public
     * @return void
     */
    function TableGrid($user_id = 0,$data) {
        global $_config;
        $this->language = $_config['language'];
        $objGrid = new dataGrid($data,$void);
        if ($_config['query_string']) {
            // $objGrid->methodForm('GET');
            // $objGrid->linkparam("user_id=".$user_id."&id=list");
            $objGrid->URLa = "user_id=".$user_id."&id=list";
        } else {
            // $objGrid->useSEO = true;
            // $objGrid->URLa = "index.php";
        }
        $objGrid->setRowActionFunction("action");
        $objGrid->rowsOnPage = $_config['max_list'];
        $this->gridtable =& $objGrid;
        $this->SetGridLanguage();
    }
    
    /**
     * get_grid caputure the output of the grid and return the html to the class
     * 
     * @param mixed $objGrid 
     * @access public
     * @return void
     */
    function get_grid($objGrid) {
        ob_start();
        $objGrid->grid();
        $objGrid -> desconectar(); 
        $grid = ob_get_clean();
        return $grid;
    }

    function get_grid_class() {
        return $this->gridtable;
    }

    /**
     * SetGridLanguage 
     * 
     * @param  void
     * @access public
     * @return void
     */
    function SetGridLanguage() {
        global $_lang, $_config; /*{{{*/
        switch ($this->language) {
            case 'english' :
                // Do nothing since default is english for phpmydatgrid
                break;
          /*  case 'nederlands': case 'dutch' :
                $this->gridtable->setLanguage("ne");
                break;*/
            case 'deutch': case 'german' :
                $this->gridtable->setLanguage("de");
                break;
            case 'espa.ol': case 'es' :
                $this->gridtable->Language("es");
                break;
            case 'francais': case 'fr' :
                $this->gridtable->setLanguage("fr");
                break;
            case 'italian' : case 'it' :
                $this->gridtable->setLanguage("it");
                break;
            case '.e.tina': case 'cs' :
                $this->gridtable->setLanguage("cs");
                break;
            case 'portuguese' : case 'portugese' :
                $this->gridtable->setLanguage("pt");
                break;
            default:
                if (!isset($_lang['grid_cancel'])) {
                    echo "<center><strong>ERROR: No language found for the grid (".$_config['language']. "). Please fix your language file.</strong></center><br>";
                } else {
                    $this->gridtable->message['cancel'] = $_lang['grid_cancel'];
                    $this->gridtable->message['close'] = $_lang['grid_close'];
                    $this->gridtable->message['save'] = $_lang['grid_save'];
                    $this->gridtable->message['saving'] = $_lang['grid_saving'];
                    $this->gridtable->message['loading'] = $_lang['grid_loading'];
                    $this->gridtable->message['edit'] = $_lang['grid_edit'];
                    $this->gridtable->message['delete'] = $_lang['grid_delete'];
                    $this->gridtable->message['add'] = $_lang['grid_add'];
                    $this->gridtable->message['view'] = $_lang['grid_view'];
                    $this->gridtable->message['addRecord'] = $_lang['grid_addRecord'];
                    $this->gridtable->message['edtRecord'] = $_lang['grid_edtRecord'];
                    $this->gridtable->message['chkRecord'] = $_lang['grid_chkRecord'];
                    $this->gridtable->message['false'] = $_lang['grid_false'];
                    $this->gridtable->message['true'] = $_lang['grid_true'];
                    $this->gridtable->message['prev'] = $_lang['grid_prev'];
                    $this->gridtable->message['next'] = $_lang['grid_next'];
                    $this->gridtable->message['confirm'] = $_lang['grid_confirm'];
                    $this->gridtable->message['search'] = $_lang['grid_search'];
                    $this->gridtable->message['resetSearch'] = $_lang['grid_resetSearch'];
                    $this->gridtable->message['doublefield'] = $_lang['grid_doublefield'];
                    $this->gridtable->message['norecords'] = $_lang['grid_norecords'];
                    $this->gridtable->message['errcode'] = $_lang['grid_errcode'];
                    $this->gridtable->message['noinsearch'] = $_lang['grid_noinsearch'];
                    $this->gridtable->message['noformdef'] = $_lang['grid_noformdef'];
                    $this->gridtable->message['cannotadd'] = $_lang['grid_cannotadd'];
                    $this->gridtable->message['cannotedit'] = $_lang['grid_cannotedit'];
                    $this->gridtable->message['cannotsearch'] = $_lang['grid_cannotsearch'];
                    $this->gridtable->message['cannotdel'] = $_lang['grid_cannotdel'];
                    $this->gridtable->message['sqlerror'] = $_lang['grid_sqlerror'];
                    $this->gridtable->message['errormsg'] = $_lang['grid_errormsg'];
                    $this->gridtable->message['errorscript'] = $_lang['grid_errorscript'];
                    $this->gridtable->message['display'] = $_lang['grid_display'];
                    $this->gridtable->message['to'] = $_lang['grid_to'];
                    $this->gridtable->message['of'] = $_lang['grid_of'];
                }
        }
    /*}}}*/
    }
/*}}}*/
}

/**
 * TablePager class that sets the application wide defaults for the PEAR Pager module 
 * 
 * @package phpdivinglog
 * @version $Rev$
 * @copyright Copyright (C) 2007 Rob Lensen. All rights reserved.
 * @author Rob Lensen <rob@bsdfreaks.nl> 
 * @license LGPL v3 http://www.gnu.org/licenses/lgpl-3.0.txt
 */
class TablePager{
/*{{{*/
    var $options;
    var $pager;
    /**
     * TablePager default constructor which creates the defaults 
     * 
     * @access public
     * @return void
     */
    function TablePager($cpage, $path) {
        global $_config;
        if ($_config['query_string']) {
            $pager_options = array( 
                    'mode' => 'Sliding', 
                    'perPage' => 16, 
                    'append' => true,
                    'currentPage' => $cpage,
                    'path' => '' ,
                    'fileName' => '%d',
                    'delta' => 2, ); 
        } else {
            $pager_options = array( 
                    'mode' => 'Sliding', 
                    'perPage' => 16, 
                    'append' => false,
                    'currentPage' => $cpage,
                    'path' => $path ,
                    'fileName' => '%d',
                    'delta' => 2, ); 
        }
        $this->options = $pager_options;
    }

    /**
     * return_tablepager_options 
     * 
     * @access public
     * @return void
     */
    function return_tablepager_options() {
        return $this->options;
    }

    /**
     * set_tablepager_itemdata 
     * 
     * @param mixed $itemdata 
     * @access public
     * @return void
     */
    function set_tablepager_itemdata($itemdata) {
        $this->options['itemData'] = $itemdata;
    }

    /**
     * create_pager 
     * 
     * @access public
     * @return void
     */
    function create_pager() {
        $this->pager = & Pager::factory($this->options);
    }

    /**
     * return_pager_links 
     * 
     * @access public
     * @return void
     */
    function return_pager_links() {
        $links = $this->pager->links;
        return $links;
    }

    function return_getCurrentPageID() {
        return $this->pager->getCurrentPageID();
    }

    function return_numPages() {
        return $this->pager->numPages();
    }

    /**
     * return_pager_data 
     * 
     * @access public
     * @return void
     */
    function return_pager_data() {
        $data = $this->pager->getPageData();
        if (!is_array($data)) {
            $data = array();
        }
        return $data;
    }
/*}}}*/
}

/**
 * Users Class needed for multiple user phpDivinglog, gets the info from the config file 
 * 
 * @package phpdivinglog
 * @copyright Copyright (C) 2007 Rob Lensen. All rights reserved.
 * @author Rob Lensen <rob@bsdfreaks.nl> 
 * @license LGPL v3 http://www.gnu.org/licenses/lgpl-3.0.txt
 */
class Users{
/*{{{*/
    var $user_ids;
    var $usernames;
    var $user_array;
    var $table_prefixes;
    
    /**
     * Users default constructor for the class, which sets just the ids 
     * 
     * @access public
     * @return void
     */
    function Users() {
        global $_config, $t;
        // Get the user_ids and put them in a array
        if ($_config['multiuser']) {
            $this->user_ids = array_keys($_config['user_prefix']);
        }
    }
    /**
     * get_divers sets the divers which are defined in the config file
     * @todo maybe users should be getted from the database
     * @access public
     * @return void
     */
    function get_divers() {
        global $_config, $t;
        // Get the user_ids and put them in a array
        if ($_config['multiuser']) {
            $this->user_ids = array_keys($_config['user_prefix']);
        }
    }

    /**
     * return_divers returns an array of the divers, more easy. 
     * 
     * @access public
     * @return void
     */
    function return_divers() {
        get_divers();
        return $this->user_ids;
    }

    /**
     * is_valid_user checks if a given id is a valid user
     * should be called with the Scope Resolution Operator
     * @param mixed $id 
     * @access public
     * @return void
     */
    function is_valid_user($id) {
        global $_config, $t;
        $user_ids =  array_keys($_config['user_prefix']);
        if (in_array($id, $user_ids)) {
            // user is in, return true
            return true;
        } else {
            return false;
        }
    }

    function get_table_prefix() {
        global $_config;
        // We have the ids so just get the prefix
        foreach ($this->user_ids as $id) {
            $this->table_prefixes[] = $_config['user_prefix'][$id];
        }
    }

    function set_user_data() {
        global $_config;
        // Get the personal info of each user from the DB
        foreach ($this->table_prefixes as $prefix) {
            // First set the prefix
            $_config['table_prefix'] = $prefix;
            // Get the data
            $user_data[] = array_shift(parse_mysql_query('personal.sql'));
        }
        $i=0;
        foreach ($this->user_ids as $id) {
            $this->user_array[] = array_merge($user_data[$i], array( "ID" => $id));
            $i++;
        }
        // unset($_config['table_prefix']);

    }
    function get_user_data() {
        $this->get_divers();
        $this->get_table_prefix();
        $this->set_user_data();
        return $this->user_array;
    }
/*}}}*/
}

/**
 * TopLevelMenu class to generate the standard top level menu
 * 
 * @package phpdivinglog
 * @copyright Copyright (C) 2007 Rob Lensen. All rights reserved.
 * @author Rob Lensen <rob@bsdfreaks.nl> 
 * @license LGPL v3 http://www.gnu.org/licenses/lgpl-3.0.txt
 */
class TopLevelMenu {
    /*{{{*/
    var $table_prefix;
    var $user_id;
    var $multiuser;

    /**
     * TopLevelMenu default constructor for the class TopLevelMenu 
     * 
     * @access public
     * @return void
     */
    function TopLevelMenu($request = 0) {
        global $_config, $t;
        // Get the user_ids and put them in a array
        if ($_config['multiuser']) {
            $this->multiuser = true;
        }
        if ($this->multiuser) {
            $this->user_id = $request->get_user_id();
            $user = new User();
            $user->set_user_id($this->user_id);
            $this->table_prefix = $user->get_table_prefix();
        } else {
            // if prefix is set get it.
            if (isset($_config['table_prefix'])) {
                $this->table_prefix = $_config['table_prefix'];
            }
        }
    }

    /**
     * get_std_links gets the links for the details screen 
     * 
     * @access public
     * @return void
     */
    function get_std_links() {
        global $t, $_lang; /*{{{*/
        // Dive Log, Dive Sites, Dive Shops, Dive Trips, Dive Statistics
        $t->assign('diver_choice_linktitle', $_lang['diver_choice_linktitle']);
        $t->assign('diver_choice', $_lang['diver_choice']);
        $t->assign('dive_log_linktitle', $_lang['dive_log_linktitle']);
        $t->assign('dive_log', $_lang['dive_log']);
        $t->assign('dive_sites_linktitle', $_lang['dive_sites_linktitle']);
        $t->assign('dive_sites', $_lang['dive_sites']);
        $t->assign('dive_equip_linktitle', $_lang['dive_equip_linktitle']);
        $t->assign('dive_equip', $_lang['dive_equip']);
        $t->assign('dive_shops_linktitle', $_lang['dive_shops_linktitle']);
        $t->assign('dive_shops', $_lang['dive_shops']);
        $t->assign('dive_trips_linktitle', $_lang['dive_trips_linktitle']);
        $t->assign('dive_trips', $_lang['dive_trips']);
        $t->assign('dive_country_linktitle', $_lang['dive_country_linktitle']);
        $t->assign('dive_countries', $_lang['dive_countries']);
        $t->assign('dive_city_linktitle', $_lang['dive_city_linktitle']);
        $t->assign('dive_cities', $_lang['dive_cities']);
        $t->assign('dive_stats_linktitle', $_lang['dive_stats_linktitle']);
        $t->assign('dive_stats', $_lang['dive_stats']);
        $t->assign('dive_gallery_linktitle', $_lang['dive_gallery_linktitle']);
        $t->assign('dive_gallery', $_lang['dive_gallery']);

        if ($this->multiuser) {
            $t->assign('multiuser_id', $this->user_id);
        }
    /*}}}*/
    }

    /**
     * get_ovv_links sets the links in the overview screen
     * 
     * @access public
     * @return void
     */
    function get_ovv_links() {
        global $t, $_lang; /*{{{*/
        // Start filling the data in the links_overview.tpl file
        $t->assign('base_page','index.php');
        // Dive Sites, Dive Statistics
        $t->assign('diver_choice_linktitle', $_lang['diver_choice_linktitle']);
        $t->assign('diver_choice', $_lang['diver_choice']);
        $t->assign('dive_log', $_lang['dive_log']);
        $t->assign('dive_log_linktitle', $_lang['dive_log_linktitle']);
        $t->assign('dive_sites_linktitle', $_lang['dive_sites_linktitle']);
        $t->assign('dive_sites', $_lang['dive_sites'] );
        $t->assign('dive_equip_linktitle', $_lang['dive_equip_linktitle']);
        $t->assign('dive_equip', $_lang['dive_equip']);
        $t->assign('dive_shops_linktitle', $_lang['dive_shops_linktitle']);
        $t->assign('dive_shops', $_lang['dive_shops']);
        $t->assign('dive_trips_linktitle', $_lang['dive_trips_linktitle']);
        $t->assign('dive_trips', $_lang['dive_trips']);
        $t->assign('dive_country_linktitle', $_lang['dive_country_linktitle']);
        $t->assign('dive_countries', $_lang['dive_countries']);
        $t->assign('dive_city_linktitle', $_lang['dive_city_linktitle']);
        $t->assign('dive_cities', $_lang['dive_cities']);
        $t->assign('dive_stats_linktitle', $_lang['dive_stats_linktitle']);
        $t->assign('dive_stats', $_lang['dive_stats']);
        $t->assign('dive_gallery_linktitle', $_lang['dive_gallery_linktitle']);
        $t->assign('dive_gallery', $_lang['dive_gallery']);

        if ($this->multiuser) {
            $t->assign('multiuser_id', $this->user_id);
        }
        // Get the page header
        $pagetitle = $_lang['dive_log'];
        $t->assign('pagetitle',$pagetitle);
    /*}}}*/
    }

    /**
     * get_nav_links according the requested page
     * 
     * @param mixed $request
     * @access public
     * @return void
     */
    function get_nav_links($request) {
        /*{{{*/
        global $t, $globals, $_lang, $_config;

        if ($request->request_type == 1) {
            $divelist = parse_mysql_query('divelist.sql');
            $divecount = $globals['sql_num_rows'];
            for ($i=0; $i<$divecount; $i++) {
                $divelist[$i] = $divelist[$i]['Number'];
            }
            $get_nr = $request->get_dive_nr();
            $thisdive = array_search($get_nr, $divelist);
            // First, Previous
            if (array_search($get_nr, $divelist) != ($divecount - 1)) {
                $t->assign('divenr','1');
                $t->assign('first_dive',$divelist[$divecount - 1]);
                $t->assign('first_dive_linktitle', $_lang['first_dive_linktitle']);
                $t->assign('first',$_lang['first']);
                $t->assign('next_dive',$divelist[$thisdive + 1]);
                $t->assign('previous_dive_linktitle', $_lang['previous_dive_linktitle']);
                $t->assign('previous', $_lang['previous']);
            } 
            // Next, Last
            if (array_search($get_nr, $divelist) != 0) {
                $t->assign('divenr_not_null','1');
                $t->assign('next_dive_nr',$divelist[$thisdive - 1]);
                $t->assign('next_dive_linktitle', $_lang['next_dive_linktitle']);
                $t->assign('next', $_lang['next']);
                $t->assign('last_dive_nr', $divelist['0']);
                $t->assign('last_dive_linktitle', $_lang['last_dive_linktitle']);
                $t->assign('last', $_lang['last'] );
            }

        } elseif ($request->request_type == 2) {
            // First, Previous, Next, Last links and Dive #
            $sitelist = parse_mysql_query('sitelist.sql');
            $last = $globals['sql_num_rows'] - 1;
            $position = -1;
            $sitecount = $globals['sql_num_rows'];
            for ($i=0; $i<$sitecount; $i++) {
                if ($sitelist[$i]['ID'] == $globals['placeid']) {
                    $position = $i;
                }
            }

            // First, Previous
            if ($position != 0 ) {
                $t->assign('position',$position);
                $t->assign('first_site_id', $sitelist[0]['ID']);
                $t->assign('first_site_linktitle', $_lang['first_site_linktitle']);
                $t->assign('first', $_lang['first']);
                $t->assign('previous_site_id', $sitelist[$position - 1]['ID']);
                $t->assign('previous_site_linktitle', $_lang['previous_site_linktitle']);
                $t->assign('previous', $_lang['previous']);
            }

            // Next, Last
            if ($position != $last) {
                $t->assign('divesite_not_null','1');
                $t->assign('next_divesite_nr', $sitelist[$position + 1]['ID']);
                $t->assign('next_site_linktitle', $_lang['next_site_linktitle']);
                $t->assign('next', $_lang['next'] );
                $t->assign('last_divesite_nr', $sitelist[$last]['ID']);
                $t->assign('last_site_linktitle', $_lang['last_site_linktitle'] );
                $t->assign('last', $_lang['last'] );
            } 

        } elseif ($request->request_type == 3) {
            $gearlist = parse_mysql_query('gearlist.sql');
            $last = $globals['sql_num_rows'] - 1;
            $position = -1;
            $gearcount = $globals['sql_num_rows'];
            for ($i=0; $i<$gearcount; $i++) {
                if ($gearlist[$i]['ID'] == $globals['gear']) {
                    $position = $i;
                }
            }    
            // First, Previous
            if ($position != 0 ) {
                $t->assign('equipment_first','1');
                $t->assign('first_eq_id', $gearlist[0]['ID']);
                $t->assign('first_equip_linktitle', $_lang['first_equip_linktitle']);
                $t->assign('first', $_lang['first']);
                $t->assign('previous_eq_id', $gearlist[$position - 1]['ID']);
                $t->assign('previous_equip_linktitle', $_lang['previous_equip_linktitle']);
                $t->assign('previous', $_lang['previous']);
            }

            // Dive Log, Dive Sites, Dive Statistics
            $t->assign('diver_choice_linktitle', $_lang['diver_choice_linktitle']);
            $t->assign('diver_choice', $_lang['diver_choice']);
            $t->assign('dive_log_linktitle', $_lang['dive_log_linktitle']);
            $t->assign('dive_log', $_lang['dive_log']);
            $t->assign('dive_sites_linktitle', $_lang['dive_sites_linktitle']);
            $t->assign('dive_sites',$_lang['dive_sites']);
            $t->assign('dive_equip_linktitle', $_lang['dive_equip_linktitle']);
            $t->assign('dive_equip',$_lang['dive_equip']);
            $t->assign('dive_stats_linktitle', $_lang['dive_stats_linktitle']);
            $t->assign('dive_stats', $_lang['dive_stats']);
            $t->assign('dive_gallery_linktitle', $_lang['dive_gallery_linktitle']);
            $t->assign('dive_gallery', $_lang['dive_gallery']);

            // Next, Last
            if ($position != $last) {
                $t->assign('equipment_not_null','1');
                $t->assign('next_eq_id', $gearlist[$position + 1]['ID']);
                $t->assign('next_equip_linktitle', $_lang['next_equip_linktitle']);
                $t->assign('next', $_lang['next'] );
                $t->assign('last_eq_id', $gearlist[$last]['ID']);
                $t->assign('last_equip_linktitle', $_lang['last_equip_linktitle'] );
                $t->assign('last', $_lang['last'] );
            }

        } elseif ($request->request_type == 8) {
            // First, Previous, Next, Last links and Trip #
            $triplist = parse_mysql_query('triplist.sql');
            $last = $globals['sql_num_rows'] - 1;
            $position = -1;
            $tripcount = $globals['sql_num_rows'];
            for ($i=0; $i<$tripcount; $i++) {
                if ($triplist[$i]['ID'] == $globals['tripid']) {
                    $position = $i;
                }
            }

            // First, Previous
            if ($position != 0 ) {
                $t->assign('trip_first','1');
                $t->assign('first_trip_id', $triplist[0]['ID']);
                $t->assign('first_trip_linktitle', $_lang['first_trip_linktitle']);
                $t->assign('first', $_lang['first']);
                $t->assign('previous_trip_id', $triplist[$position - 1]['ID']);
                $t->assign('previous_trip_linktitle', $_lang['previous_trip_linktitle']);
                $t->assign('previous', $_lang['previous']);
            }

            // Next, Last
            if ($position != $last) {
                $t->assign('divetrip_not_null','1');
                $t->assign('next_divetrip_nr', $triplist[$position + 1]['ID']);
                $t->assign('next_trip_linktitle', $_lang['next_trip_linktitle']);
                $t->assign('next', $_lang['next'] );
                $t->assign('last_divetrip_nr', $triplist[$last]['ID']);
                $t->assign('last_trip_linktitle', $_lang['last_trip_linktitle'] );
                $t->assign('last', $_lang['last'] );
            } 

        } elseif ($request->request_type == 9) {
            // First, Previous, Next, Last links and Dive #
            $shoplist = parse_mysql_query('shoplist.sql');
            $last = $globals['sql_num_rows'] - 1;
            $position = -1;
            $shopcount = $globals['sql_num_rows'];
            for ($i=0; $i<$shopcount; $i++) {
                if ($shoplist[$i]['ID'] == $globals['shopid']) {
                    $position = $i;
                }
            }

            // First, Previous
            if ($position != 0 ) {
                $t->assign('shop_first','1');
                $t->assign('first_shop_id', $shoplist[0]['ID']);
                $t->assign('first_shop_linktitle', $_lang['first_shop_linktitle']);
                $t->assign('first', $_lang['first']);
                $t->assign('previous_shop_id', $shoplist[$position - 1]['ID']);
                $t->assign('previous_shop_linktitle', $_lang['previous_shop_linktitle']);
                $t->assign('previous', $_lang['previous']);
            }

            // Next, Last
            if ($position != $last) {
                $t->assign('diveshop_not_null','1');
                $t->assign('next_diveshop_nr', $shoplist[$position + 1]['ID']);
                $t->assign('next_shop_linktitle', $_lang['next_shop_linktitle']);
                $t->assign('next', $_lang['next'] );
                $t->assign('last_diveshop_nr', $shoplist[$last]['ID']);
                $t->assign('last_shop_linktitle', $_lang['last_shop_linktitle'] );
                $t->assign('last', $_lang['last'] );
            } 

        } elseif ($request->request_type == 10) {
            // First, Previous, Next, Last links and Country #
            $countrylist = parse_mysql_query('countrylist.sql');
            $last = $globals['sql_num_rows'] - 1;
            $position = -1;
            $countrycount = $globals['sql_num_rows'];
            for ($i=0; $i<$countrycount; $i++) {
                if ($countrylist[$i]['ID'] == $globals['countryid']) {
                    $position = $i;
                }
            }

            // First, Previous
            if ($position != 0 ) {
                $t->assign('country_first','1');
                $t->assign('first_country_id', $countrylist[0]['ID']);
                $t->assign('first_country_linktitle', $_lang['first_country_linktitle']);
                $t->assign('first', $_lang['first']);
                $t->assign('previous_country_id', $countrylist[$position - 1]['ID']);
                $t->assign('previous_country_linktitle', $_lang['previous_country_linktitle']);
                $t->assign('previous', $_lang['previous']);
            }

            // Next, Last
            if ($position != $last) {
                $t->assign('divecountry_not_null','1');
                $t->assign('next_country_nr', $countrylist[$position + 1]['ID']);
                $t->assign('next_country_linktitle', $_lang['next_country_linktitle']);
                $t->assign('next', $_lang['next'] );
                $t->assign('last_country_nr', $countrylist[$last]['ID']);
                $t->assign('last_country_linktitle', $_lang['last_country_linktitle'] );
                $t->assign('last', $_lang['last'] );
            } 

        } elseif ($request->request_type == 11) {
            // First, Previous, Next, Last links and City #
            $citylist = parse_mysql_query('citylist.sql');
            $last = $globals['sql_num_rows'] - 1;
            $position = -1;
            $citycount = $globals['sql_num_rows'];
            for ($i=0; $i<$citycount; $i++) {
                if ($citylist[$i]['ID'] == $globals['cityid']) {
                    $position = $i;
                }
            }

            // First, Previous
            if ($position != 0 ) {
                $t->assign('city_first','1');
                $t->assign('first_city_id', $citylist[0]['ID']);
                $t->assign('first_city_linktitle', $_lang['first_city_linktitle']);
                $t->assign('first', $_lang['first']);
                $t->assign('previous_city_id', $citylist[$position - 1]['ID']);
                $t->assign('previous_city_linktitle', $_lang['previous_city_linktitle']);
                $t->assign('previous', $_lang['previous']);
            }

            // Next, Last
            if ($position != $last) {
                $t->assign('divecity_not_null','1');
                $t->assign('next_city_nr', $citylist[$position + 1]['ID']);
                $t->assign('next_city_linktitle', $_lang['next_city_linktitle']);
                $t->assign('next', $_lang['next'] );
                $t->assign('last_city_nr', $citylist[$last]['ID']);
                $t->assign('last_city_linktitle', $_lang['last_city_linktitle'] );
                $t->assign('last', $_lang['last'] );
            } 
            // End filling the links section
        }
    /*}}}*/
    }

/*}}}*/
}

/**
 * Divelog contains all functions for displaying the dive information
 * 
 * @package phpdivinglog
 * @copyright Copyright (C) 2007 Rob Lensen. All rights reserved.
 * @author Rob Lensen <rob@bsdfreaks.nl> 
 * @license LGPL v3 http://www.gnu.org/licenses/lgpl-3.0.txt
 */
class Divelog {
    var $multiuser; /*{{{*/
    var $user_id;
    var $dive_nr;
    var $result;
    var $table_prefix;
    var $profile_info;
    var $averagedepth;
    var $requested_page;
    var $sac;
    var $userdefined;
    var $userdefined_count;
    var $request_type; // request_type = 0 overview, request_type = 1 details
   
    /**
     * Divelog default constructor sets some defaults for this class
     * 
     * @access public
     * @return void
     */
    function Divelog() {
        global $_config;
        $this->multiuser = $_config['multiuser'];
    }
    
    function get_request_type() {
        return $this->request_type;
    }

    /**
     * set_divelog_info sets the basics for this class from the url requested 
     * 
     * @param mixed $request 
     * @access public
     * @return void
     */
    function set_divelog_info($request) {
        // We need to extract the info from the request
        /*{{{*/
        if (!$request->diver_choice) {
            // Find request type
            if ($request->get_view_request() == 1) {
                $this->request_type = 1;
                $this->dive_nr = $request->get_dive_nr();
            } else {
                $this->request_type = 0;
                $this->requested_page = $request->get_requested_page();
            }
            if ($this->multiuser) {
                $this->user_id = $request->get_user_id();
                $user = new User();
                $user->set_user_id($this->user_id);
                $this->table_prefix = $user->get_table_prefix();
            } else {
                $user = new User();
                $this->table_prefix = $user->get_table_prefix();
            }
        } else {
            $this->request_type = 3;
        }
    /*}}}*/
    }

    /**
     * get_divelog_info check if dive_nr isset, if true get the dive info
     *  otherwise get data for the overview
     * 
     * @access public
     * @return void
     */
    function get_divelog_info() {
        global $globals, $_config; /*{{{*/
        if (!empty($this->dive_nr)) {
            $this->request_type = 1;
            // Get the dive details from database
            $globals['divenr'] = $this->dive_nr;
            $this->result = parse_mysql_query('onedive.sql');

            // Set profile data
            // Calculate average depth and SAC for this dive
            $result = $this->result;
            $profile = $result['Profile'];
            if (!$profile) {
                $this->averagedepth = "-";
                $this->sac = "-";
            } else {
                $profile_info = GetProfileData($result);
                $this->averagedepth = $profile_info['averagedepth'];
                $this->sac = $profile_info['sac'];
            }   
        } else {
            /**
             * If the request type is not already set (by divers choice), set it to overview  
             */
            if ($this->request_type != 3) {
                $this->request_type = 0;
            }
        }
        return $this->result; 
    /*}}}*/
    }

    /**
     * get_userdefined 
     * 
     * @access public
     * @return void
     */
    function get_userdefined() {
        global $globals, $_config; /*{{{*/
        // Get the userdefined values for this dive, if any
        $globals['logid'] = $this->result['ID'];
        $this->userdefined = parse_mysql_query('userdefined.sql');
        $count = $globals['sql_num_rows'];
        if ($count == '0') {
            $this->userdefined_count = 0;
        } else {
            $this->userdefined_count = 1;
        }
    /*}}}*/
    }

    /**
     * get_main_dive_details 
     * 
     * @access public
     * @return void
     */
    function set_main_dive_details() {
        global $t, $_config, $_lang, $globals; /*{{{*/
        $result = $this->result; 
 
        $t->assign('dive_tab_logbook', $_lang['dive_tab_logbook']);
        $t->assign('dive_tab_breathing', $_lang['dive_tab_breathing']);
        $t->assign('dive_tab_comments', $_lang['dive_tab_comments']);
        $t->assign('dive_tab_photos', $_lang['dive_tab_photos']);
        $t->assign('dive_tab_profile', $_lang['dive_tab_profile']);
        $t->assign('dive_tab_userdefined', $_lang['dive_tab_userdefined']);
       
        $t->assign('pagetitle', $_lang['dive_details_pagetitle'].$result['Number']);
        $t->assign('logbook_divedate', $_lang['logbook_divedate']);
        $t->assign('logbook_entrytime', $_lang['logbook_entrytime']);
        $t->assign('logbook_divetime', $_lang['logbook_divetime']);
        $t->assign('logbook_depth', $_lang['logbook_depth']);

        $t->assign('comma_separated', $_config['comma_separated']);
        $t->assign('comma_separator', $_config['comma_separator']);

        if (isset($result['Divedate']) && ($result['Divedate'] != "")) {
            $t->assign('dive_date', date($_lang['logbook_divedate_format'], strtotime($result['Divedate'])));
        } else {
            $t->assign('dive_date','-');
        }

        if (isset($result['Entrytime']) && ($result['Entrytime'] != "")) {
            $t->assign('entry_time',$result['Entrytime']);
        } else {
            $t->assign('entry_time','-');
        }

        if (isset($result['Divetime']) && ($result['Divetime'] != "")) {
            $t->assign('dive_time', $result['Divetime'] ."&nbsp;". $_lang['unit_time']);
        } else {
            $t->assign('dive_time','-');
        }

        if (isset($result['Depth']) && ($result['Depth'] != "")) {
            if ($_config['length']) {
                $t->assign('dive_depth', MetreToFeet($result['Depth'], 0) ."&nbsp;". $_lang['unit_length_imp']);
            } else {
                $t->assign('dive_depth', $result['Depth'] ."&nbsp;". $_lang['unit_length']);
            }
        } else {
            $t->assign('dive_depth','-');
        }

        // Show dive location details
        $t->assign('logbook_place', $_lang['logbook_place']);
        $t->assign('logbook_city', $_lang['logbook_city']);
        $t->assign('logbook_country', $_lang['logbook_country']);
        $t->assign('dive_shop_head', $_lang['logbook_dive_shop']);
        $t->assign('dive_trip_head', $_lang['logbook_dive_trip']);

        if (!empty($result['PlaceID'])) {
            $t->assign('dive_site_nr', $result['PlaceID']);
            $t->assign('dive_place', $result['Place']);
            $t->assign('logbook_place_linktitle', $_lang['logbook_place_linktitle']);
        }

        if (!empty($result['City'])) {
            $t->assign('dive_city_nr', $result['CityID']);
            $t->assign('dive_city', $result['City']);
            $t->assign('logbook_city_linktitle', $_lang['logbook_city_linktitle']);
        } else {
            $t->assign('dive_city','-');
        }

        if (!empty($result['Country'])) {
            $t->assign('dive_country_nr', $result['CountryID']);
            $t->assign('dive_country',$result['Country']);
            $t->assign('logbook_country_linktitle', $_lang['logbook_country_linktitle']);
        } else {
            $t->assign('dive_country','-');
        }

        if (!empty($result['ShopID'])) {
            $globals['shopid'] = $result['ShopID'];
            $diveshop = parse_mysql_query('oneshop.sql');
            $t->assign('dive_shop_nr', $result['ShopID']);
            $t->assign('dive_shop_name', $diveshop['ShopName']);
            $t->assign('logbook_shop_linktitle', $_lang['logbook_shop_linktitle']);
            if ($diveshop['ShopType'] != '') {
                $t->assign('dive_shop_head', $diveshop['ShopType'].':');
            }
        } else {
            $t->assign('dive_shop_nr', '');
            $t->assign('dive_shop_name','-');
        }

        if (!empty($result['TripID'])) {
            $globals['tripid'] = $result['TripID'];
            $divetrip = parse_mysql_query('onetrip.sql');
            $t->assign('dive_trip_nr', $result['TripID']);
            $t->assign('dive_trip_name', $divetrip['TripName']);
            $t->assign('logbook_trip_linktitle', $_lang['logbook_trip_linktitle']);
        } else {
            $t->assign('dive_trip_nr', '');
            $t->assign('dive_trip_name','-');
        }

    /*}}}*/
    }

    /**
     * set_buddy_details sets the buddy info
     * 
     * @access public
     * @return void
     */
    function set_buddy_details() {
        global $t, $_lang; /*{{{*/
        $result = $this->result; 
        $t->assign('logbook_buddy', $_lang['logbook_buddy']);
        $t->assign('logbook_divemaster', $_lang['logbook_divemaster']);

        if (isset($result['Buddy']) && $result['Buddy'] != "") {
            $t->assign('buddy', $result['Buddy']);
        } else {
            $t->assign('buddy','-');
        }

        if (isset($result['Divemaster']) && $result['Divemaster'] != "") {
            $t->assign('divemaster', $result['Divemaster']);
        } else {
            $t->assign('divemaster','-');
        }
    /*}}}*/
    }

    function get_log_id_for_dive_nr($dive_nr) {
        global $_config,$t, $_lang, $globals;
    }

    /**
     * dive_has_pictures checks if there are any pictures for a specific dive 
     * 
     * This function should be called stand alone!
     * @param mixed $dive_nr 
     * @access public
     * @return void
     */
    function dive_has_pictures($dive_nr) {
        global $_config, $t, $_lang, $globals; /*{{{*/
        $pic_class = new DivePictures;
        /**
         * We need to get the LogID for this dive 
         */
        $request = new HandleRequest();
        $request->set_request_uri($_SERVER['REQUEST_URI']);
        $request->set_file_depth(0);
        $request->handle_url();
        $multiuser = 0;
        $table_prefix = NULL;
        if ($_config['multiuser']) {
            $multiuser = true;
        }
        if ($multiuser) {
            $user_id = $request->get_user_id();
            $user = new User();
            $user->set_user_id($user_id);
            // $table_prefix = $user->get_table_prefix();
        } else {
            // if prefix is set get it.
            if (!empty($_config['table_prefix'])) {
              // $table_prefix = $_config['table_prefix'];
            }
        }
        $globals['divenr'] = $dive_nr; 
        $id = parse_mysql_query('divelogid.sql'); 
        $pic_class->get_divegallery_info($id['ID'],0,0,0,0);

        $divepics = $pic_class->get_image_link();
        $pics = count($divepics);
        if ($pics > 0) {
            return true;
        } else {
            return false;
        } 
    /*}}}*/
    }

    /**
     * set_dive_pictures formats the links to the pictures if available
     * 
     * @access public
     * @return void
     */
    function set_dive_pictures() {
        global $_config, $t, $_lang, $globals; /*{{{*/
        $result = $this->result; 
        $pic_class = new DivePictures;
        $pic_class->set_divegallery_info_direct($this->user_id);
        $pic_class->get_divegallery_info($result['ID'],0,0,0,0);
        $divepics = $pic_class->get_image_link();
        $pics = count($divepics);

        if ($pics != 0) {
            if (isset($_config['divepics_preview'])) {
                $t->assign('pics2', '1');
                $t->assign('image_link', $divepics);
            } else {
                $t->assign('pics', '1');
                $globals['logid'] = $result['ID'];
                $divepics = parse_mysql_query('divepics.sql');
                
                $t->assign('picpath_web', $_config['web_root']."/".$_config['picpath_web'] . $divepics['Path']);
                $t->assign('divepic_linktit', $_lang['divepic_linktitle_pt1']. "1". $_lang['divepic_linktitle_pt2']. $pics. $_lang['divepic_linktitle_pt3']. $result['Number']);
                $divepic_pt =  $_lang['divepic_pt1']. $pics;
                if ($pics == 1) {
                    $divepic_pt .= $_lang['divepic_pt2s'];
                } else {
                    $divepic_pt .= $_lang['divepic_pt2p'];
                }
                $divepic_pt .= $_lang['divepic_pt3'];
                $t->assign('divepic_pt', $divepic_pt);
                $image_link = array();
                for ($i=1; $i<$pics; $i++) {
                    $image_link[$i-1] =  "<a href=\"". $_config['web_root']."/". $_config['picpath_web'] . $divepics[$i]['Path'];
                    $image_link[$i-1] .= "\" rel=\"lightbox[others]\"\n";
                    $image_link[$i-1] .= "   title=\"". $_lang['divepic_linktitle_pt1']. ($i + 1). $_lang['divepic_linktitle_pt2']. $pics;
                    $image_link[$i-1] .= $_lang['divepic_linktitle_pt3']. $result['Number'] ."\"></a>\n";
                }
                $t->assign('image_link', $image_link);
            }
        }
    /*}}}*/
    }

    /**
     * set_dive_profile  
     * 
     * @access public
     * @return void
     */
    function set_dive_profile() {
        global $t, $_lang, $globals, $_config; /*{{{*/
        $result = $this->result; 
        $profile = $result['Profile'];
        if ($profile && $_config['show_profile'] == true) {
            $t->assign('profile','1');
            $t->assign('get_nr',$this->dive_nr);
            $t->assign('dive_profile_title', $_lang['dive_profile_title'] . $result['Number']);
        }
    /*}}}*/
    }

    /**
     * set_dive_conditions 
     * 
     * @access public
     * @return void
     */
    function set_dive_conditions() {
        global $t, $_config, $_lang, $globals; /*{{{*/
        $result = $this->result; 
        $t->assign('dive_sect_conditions', $_lang['dive_sect_conditions']);

        // Show weather conditions
        $t->assign('logbook_weather', $_lang['logbook_weather']);
        $t->assign('logbook_altitude', $_lang['logbook_altitude']);
        $t->assign('logbook_airtemp', $_lang['logbook_airtemp']);

        if (isset($result['Weather']) && ($result['Weather'] != "")) {
            $t->assign('Weather', $result['Weather']);
        } else {
            $t->assign('Weather','-');
        }

        if (isset($result['Altitude']) && ($result['Altitude'] != "")) {
            $t->assign('Altitude', $result['Altitude']);
        } else {
            $t->assign('Altitude','-');
        }

        if (isset($result['Airtemp']) && ($result['Airtemp'] != "")) {
            if ($_config['temp']) {
                $Airtemp = CelsiusToFahrenh($result['Airtemp'], 0) ."&nbsp;". $_lang['unit_temp_imp'] ;
            } else {
                $Airtemp = $result['Airtemp'] ."&nbsp;". $_lang['unit_temp'] ;
            }
            $t->assign('Airtemp',$Airtemp);
        } else {
            $t->assign('Airtemp','-');
        }

        // Show water conditions
        $t->assign('logbook_water', $_lang['logbook_water']);
        $t->assign('logbook_surface', $_lang['logbook_surface']);
        $t->assign('logbook_uwcurrent', $_lang['logbook_uwcurrent']);
        $t->assign('logbook_watertemp', $_lang['logbook_watertemp']);
        $t->assign('logbook_visibility', $_lang['logbook_visibility']);
        $t->assign('logbook_vishor', $_lang['logbook_vishor']);
        $t->assign('logbook_visver', $_lang['logbook_visver']);

        if (isset($result['Water']) && ($result['Water'] >= 1) && ($result['Water'] <= 3)) {
            $t->assign('Water', $_lang['water'][($result['Water'] - 1)]);
        } else {
            $t->assign('Water','-');
        }

        if (isset($result['Visibility']) && ($result['Visibility'] >= 1) && ($result['Visibility'] <= 3)) {
            $viz = $_lang['visibility'][($result['Visibility'] - 1)];
            $vizimage = '<img src="'.$_config['web_root'].'/images/vis-'.$result['Visibility'].'.gif" alt="'.$viz.'" title="'.$viz.'" border="0" width="50" height="15"> ';
            $t->assign('Visibility', $vizimage.' '.$viz);
        } else {
            $t->assign('Visibility','-');
        }

        if (isset($result['VisHor']) && ($result['VisHor'] != "")) {
            $t->assign('VisHor', htmlentities($result['VisHor']));
        } else {
            $t->assign('VisHor','-');
        }

        if (isset($result['VisVer']) && ($result['VisVer'] != "")) {
            $t->assign('VisVer', htmlentities($result['VisVer']));
        } else {
            $t->assign('VisVer','-');
        }

        if (isset($result['Surface']) && ($result['Surface'] != "")) {
            $t->assign('Surface',$result['Surface']);
        } else {
            $t->assign('Surface','-');
        }

        if (isset($result['UWCurrent']) && ($result['UWCurrent'] != "")) {
            $t->assign('UWCurrent',$result['UWCurrent']);
        } else {
            $t->assign('UWCurrent','-');
        }

        if (isset($result['Watertemp']) && ($result['Watertemp'] != "")) {
            if ($_config['temp']) {
                $Watertemp = CelsiusToFahrenh($result['Watertemp'], 0) ."&nbsp;". $_lang['unit_temp_imp'] ;
            } else {
                $Watertemp = $result['Watertemp'] ."&nbsp;". $_lang['unit_temp'] ;
            }
            $t->assign('Watertemp', $Watertemp);
        } else {
            $t->assign('Watertemp','-');
        }
    /*}}}*/
    }

    /**
     * set_breathing_details 
     * 
     * @access public
     * @return void
     */ 
    function set_breathing_details() {
        global $tanks, $t, $_config, $_lang, $globals; /*{{{*/
        $result = $this->result;

    /**
     * set_tank_details 
     * 
     * @access public
     * @return void
     */ 
    function set_tank_details($i, $result) {
        global $tanks, $t, $_config, $_lang; /*{{{*/

        $tanks[$i]['Set'] = $i + 1;

        // Start with the basic tank details
        //if (isset($result['Tanktype']) && ($result['Tanktype'] != "")) {
        //    $arr_number = $result['Tanktype'] - 1;
        //     if ($arr_number >= 0) {
        if (isset($result['Tanktype']) && $result['Tanktype'] != "" && isset($_lang['tanktype'][$result['Tanktype'] - 1])) {
            $t->assign('Tanktype', $_lang['tanktype'][$result['Tanktype'] - 1]);
            $arr_number = $result['Tanktype'] - 1;
            $tanks[$i]['Tanktype'] = $_lang['tanktype'][$arr_number];
        } else {
            $tanks[$i]['Tanktype'] = '-';
        }

        if (isset($result['Tanksize']) && ($result['Tanksize'] != "")) {
            if ($_config['volume']) {
                if (($result['PresW'] == "") || ($result['PresW'] <= 0)) {
                    $Tanksize = $result['Tanksize'] * 7 ;
                } else {
                    $Tanksize = LitreToCuft(($result['Tanksize'] * $result['PresW']), 0) ;
                }
                $Tanksize .= "&nbsp;". $_lang['unit_volume_imp'] ;
            } else {
                $Tanksize = $result['Tanksize'] ."&nbsp;". $_lang['unit_volume'] ;
            }
            $tanks[$i]['Tanksize'] = $Tanksize;
        } else {
            $tanks[$i]['Tanksize'] = '-';
        }

        if (isset($result['DblTank']) && ($result['DblTank'] != "")) {
            if ($result['DblTank'] == 'True') {
                $tankimagealt = $_lang['dbltank'][1];
                $tankimagefile = "twin_cylinders.gif";
            } else {
                $tankimagealt = $_lang['dbltank'][0];
                $tankimagefile = "single_cylinder.gif";
            }
            $tankimage = '<img src="'.$_config['web_root'].'/images/'.$tankimagefile.'" alt="'.$tankimagealt.'" title="'.$tankimagealt.'" border="0" width="19" height="20"> ';
            $tanks[$i]['DblTankImage'] = $tankimage;
        } else {
            $tanks[$i]['DblTankImage'] = '';
        }

        if (isset($result['PresW']) && ($result['PresW'] != "")) {
            if ($_config['pressure']) {
                $PresW = BarToPsi($result['PresW'], -1) ."&nbsp;". $_lang['unit_pressure_imp'] ;
            } else {
                $PresW = $result['PresW'] ."&nbsp;". $_lang['unit_pressure'] ;
            }
            $tanks[$i]['PresW'] = $PresW;
        } else {
            $tanks[$i]['PresW'] = '-';
        }

        if (isset($result['SupplyType']) && ($result['SupplyType'] != "")) {
            switch ($result['SupplyType']) {
              case '0':
                $supplytypefile = "oc.gif";
                break;
              case '1':
                $supplytypefile = "scr.gif";
                break;
              case '2':
                $supplytypefile = "ccr.gif";
                break;
              default:
                $supplytypefile = "oc.gif";
                break;
            }
            $supplytype = $_lang['supplytype'][$result['SupplyType']];
            $supplytypeimage = '<img src="'.$_config['web_root'].'/images/'.$supplytypefile.'" alt="'.$supplytype.'" title="'.$supplytype.'" border="0" width="19" height="20"> ';
            $tanks[$i]['SupplyTypeImage'] = $supplytypeimage;
            $tanks[$i]['SupplyType'] = $supplytype;
        } else {
            $tanks[$i]['SupplyTypeImage'] = '';
            $tanks[$i]['SupplyType'] = '-';
        }

        // Details of the gas mix used
        if (isset($result['O2']) && ($result['O2'] != "")) {
            $o2 = $result['O2'];
            $tanks[$i]['O2'] = $o2.'%';
            $t->assign('O2', $result['O2'].'%');
        } else {
            if (isset($result['He']) && $result['He'] != "") {
                $t->assign('O2','-');
            } else {
                $t->assign('O2', $_config['default_o2'].'%');
                $o2 = $_config['default_o2'];
                $tanks[$i]['O2'] = $o2.'%';
            }
        }

        if (isset($result['He']) && ($result['He'] != "")) {
            $t->assign('He', $result['He'].'%');
            $he = $result['He'];
            $tanks[$i]['He'] = $result['He'].'%';
        } else {
            $t->assign('He','-');
            $he = 0;
            $tanks[$i]['He'] = '-';
        }

        if (isset($result['MinPPO2']) && ($result['MinPPO2'] != "")) {
            $tanks[$i]['MinPPO2'] = $result['MinPPO2']."&nbsp;".$_lang['unit_pressure'];
        } else {
            $tanks[$i]['MinPPO2'] = '-';
        }

        if (isset($result['MaxPPO2']) &&  ($result['MaxPPO2'] != "")) {
            $maxppo2 = $result['MaxPPO2'];
            if ($maxppo2 == 0) {
                $maxppo2 = $_config['default_maxppo2'];
            }
            $tanks[$i]['MaxPPO2'] = $maxppo2."&nbsp;".$_lang['unit_pressure'];
        } else {
            $maxppo2 = $_config['default_maxppo2'];
            $tanks[$i]['MaxPPO2'] = $maxppo2."&nbsp;".$_lang['unit_pressure'];
        }

        // More details of the gas used
        $gasimage = "gas_air.gif";  // default air
        $gasimagealt = "Air";
        //We have validated values check popular mixtures
		if ($he == 0) {
			if ($o2 == 32) {
				$gasimage = "gas_n32.gif";  // N32
				$gasimagealt = "EAN ".floor($o2);
			} else if ($o2 == 36) {
				$gasimage = "gas_n36.gif";  // N36
				$gasimagealt = "EAN ".floor($o2);
			} else if ($o2 == 50) {
				//ToDo: Add special image
				//$gasimage = "gas_n50.gif";  // N50
				$gasimage = "gas_ean.gif";  // Other EAN
				$gasimagealt = "EAN ".floor($o2);
			} else if ($o2 == 100) {
				$gasimage = "gas_o2.gif";  // oxygen
				$gasimagealt = "Oxygen";
			} else if ($o2 > 21) {
				$gasimage = "gas_ean.gif";  // Other EAN
				$gasimagealt = "EAN ".floor($o2);
			}
        } else {
            $gasimage = "gas_tri.gif";  // Trimix
            $gasimagealt = "Trimix";
            if ($o2 != 0) {
              $gasimagealt .= ' '.floor($o2) .'/'. floor($he) .'/'. (100 - (floor($he) + floor($o2)));
            }
        }
        $gastypeimage = '<img src="'.$_config['web_root'].'/images/'.$gasimage.'" alt="'.$gasimagealt.'" title="'.$gasimagealt.'" border="0" width="19" height="20">';
        $tanks[$i]['GasTypeImage'] = $gastypeimage;
        $tanks[$i]['GasImageAlt'] = $gasimagealt;

        //  Calculate MOD and EAD
        if ($he != 0) { //it is posible my OSTC makes it :D
            //ToDo: look in OSTC source code how it does it
            $t->assign('MOD','-');
            $t->assign('EAD','-');
        } else {
            if ($_config['length']) {
                $mod_imperial = 33 * (($maxppo2 / ($o2 / 100)) - 1);
                $mod = number_format(floor($mod_imperial), 0);
                $ead_imperial = (($mod + 33) * ((1 - ($o2 / 100)) / .79)) - 33;
                $ead = number_format(ceil($ead_imperial), 0);
                $t->assign('MOD', $mod."&nbsp;". $_lang['unit_length_short_imp']);
                $t->assign('EAD', $ead."&nbsp;". $_lang['unit_length_short_imp']);
                $tanks[$i]['MOD'] = $mod."&nbsp;". $_lang['unit_length_short_imp'];
                $tanks[$i]['EAD'] = $ead."&nbsp;". $_lang['unit_length_short_imp'];
            } else {
                $mod_metric = 10 * (($maxppo2 / ($o2 / 100)) - 1);
                $mod = number_format((floor($mod_metric*10)/10), 1);
                $ead_metric = (($mod + 10) * ((1 - ($o2 / 100)) / .79)) - 10;
                $ead = number_format((ceil($ead_metric*10)/10), 1);
                $t->assign('MOD', $mod."&nbsp;". $_lang['unit_length_short']);
                $t->assign('EAD', $ead."&nbsp;". $_lang['unit_length_short']);
                $tanks[$i]['MOD'] = $mod."&nbsp;". $_lang['unit_length_short'];
                $tanks[$i]['EAD'] = $ead."&nbsp;". $_lang['unit_length_short'];
            }
        }
        // Calculate END
        if (isset($result['He']) && ($result['He'] != "")) {
            if ($_config['length']) {
                $end_imperial = (($mod + 33) * (1 - ($result['He'] / 100))) - 33;
                $end = number_format(ceil($end_imperial), 0);
                $tanks[$i]['END'] = $end."&nbsp;". $_lang['unit_length_short_imp'];
            } else {
                $end_metric = (($mod + 10) * (1 - ($result['He'] / 100))) - 10;
                $end = number_format((ceil($end_metric*10)/10), 1);
                $tanks[$i]['END'] = $end."&nbsp;". $_lang['unit_length_short'];
            }
        } else {
            $tanks[$i]['END'] = '-';
        }


/*
        if (isset($this->averagedepth) && ($this->averagedepth != "")) {
            if ($_config['length']) {
                $avg_depth = MetreToFeet($this->averagedepth);
                if ($avg_depth != '-') {
                    $tanks[$i]['unit_length_short'] = $_lang['unit_length_short_imp'];
                } else {
                    $tanks[$i]['unit_length_short'] = "";
                }
            } else {
                $avg_depth = $this->averagedepth;
                if ($avg_depth != '-') {
                    $tanks[$i]['unit_length_short'] = $_lang['unit_length_short'];
                } else {
                    $tanks[$i]['unit_length_short'] = "";
                }
            }
            $tanks[$i]['averagedepth'] = $avg_depth;
        } else {
            $tanks[$i]['averagedepth'] = '-';
        }
*/
            $tanks[$i]['averagedepth'] = '-';
            $tanks[$i]['unit_length_short'] = "";

        // Show pressure details

        if (isset($result['PresS']) && ($result['PresS'] != "")) {
            if ($_config['pressure']) {
                $PresS = BarToPsi($result['PresS'], -1) ."&nbsp;". $_lang['unit_pressure_imp'] ;
            } else {
                $PresS = $result['PresS'] ."&nbsp;". $_lang['unit_pressure'] ;
            }
            $tanks[$i]['PresS'] = $PresS;
        } else {
            $tanks[$i]['PresS'] = '-';
        }

        if (isset($result['PresE']) && ($result['PresE'] != "")) {
            if ($_config['pressure']) {
                $PresE =  BarToPsi($result['PresE'], -1) ."&nbsp;". $_lang['unit_pressure_imp'];
            } else {
                $PresE =  $result['PresE'] ."&nbsp;". $_lang['unit_pressure'] ;
            }
            $tanks[$i]['PresE'] = $PresE;
        } else {
            $tanks[$i]['PresE'] = '-';
        }

        if ((isset($result['PresS']) && ($result['PresS'] != "")) && (isset($result['PresE']) && ($result['PresE'] != ""))) {
            $diff = intval($result['PresS']) - intval($result['PresE']);
            if ($_config['pressure']) {
                $PresSPresE =  BarToPsi($diff, -1) ."&nbsp;". $_lang['unit_pressure_imp'] ;
            } else {
                $PresSPresE = $diff ."&nbsp;". $_lang['unit_pressure'] ;
            }
            $tanks[$i]['PresSPresE'] = $PresSPresE;
        } else {
            $tanks[$i]['PresSPresE'] = "-";
        }
/*
        if ($this->sac != "") {
            $tanks[$i]['sac'] = $this->sac; 
        } else {
            $tanks[$i]['sac'] = ""; 
        }
*/
            $tanks[$i]['sac'] = "-"; 

    /*}}}*/
    }

        // Breathing
        $t->assign('dive_sect_breathing', $_lang['dive_sect_breathing']);

        // Show tank details
        $t->assign('logbook_tankset', $_lang['logbook_tankset']);
        $t->assign('logbook_tanktype', $_lang['logbook_tanktype']);
        $t->assign('logbook_tanksize', $_lang['logbook_tanksize']);
        $t->assign('logbook_presw', $_lang['logbook_presw']);
        $t->assign('logbook_supplytype', $_lang['logbook_supplytype']);

        $t->assign('logbook_o2', $_lang['logbook_o2']);
        $t->assign('logbook_he', $_lang['logbook_he']);
        $t->assign('logbook_minppo2', $_lang['logbook_minppo2']);
        $t->assign('logbook_maxppo2', $_lang['logbook_maxppo2']);

        $t->assign('logbook_mod', $_lang['logbook_mod']);
        $t->assign('logbook_ead', $_lang['logbook_ead']);
        $t->assign('logbook_end', $_lang['logbook_end']);

        $t->assign('logbook_press', $_lang['logbook_press']);
        $t->assign('logbook_prese', $_lang['logbook_prese']);
        $t->assign('logbook_presdiff', $_lang['logbook_presdiff']);

        $t->assign('logbook_avgdepth', $_lang['logbook_avgdepth']);
        $t->assign('logbook_sac', $_lang['logbook_sac'] );

        $t->assign('logbook_gas', $_lang['logbook_gas']);

        set_tank_details(0,$result);

        // See if there are any other tanks used for this dive
        $globals['dive_id'] = $result['ID'];
        $divetanks = parse_mysql_query('tanksdivelist.sql');
        $tankscount = rows_mysql_query();

        if ($tankscount == 1) {
                set_tank_details(1,$divetanks);
        } else {
            for ($i=0; $i<$tankscount; $i++) {
                set_tank_details(($i + 1),$divetanks[$i]);
            }
        }

        $tanksfordive[] = array();
        for ($i=0; $i<$tankscount+1; $i++) {
            $tanksfordive[$i] = array('tanksfordive' => $tanks[$i]);
        }
        $t->assign('tanksfordive', $tanksfordive);

        if ($this->averagedepth != "") {
            if ($_config['length']) {
                $avg_depth = MetreToFeet($this->averagedepth);
                if ($avg_depth != '-') {
                    $t->assign('unit_length_short', $_lang['unit_length_short_imp']);
                } else {
                    $t->assign('unit_length_short', "");
                }
            } else {
                $avg_depth = $this->averagedepth;
                if ($avg_depth != '-') {
                    $t->assign('unit_length_short', $_lang['unit_length_short']);
                } else {
                    $t->assign('unit_length_short', "");
                }
            }
            $t->assign('averagedepth', $avg_depth ) ;
        } else {
            $t->assign('averagedepth','-');
        }

        if ($this->sac != "") {
            $t->assign('sac', $this->sac ); 
        } else {
            $t->assign('sac', "-" ); 
        } 

        // Gas mixture details
        if (isset($result['Gas']) && ($result['Gas'] != "")) {
            $t->assign('Gas', $result['Gas']);
        } else {
            $t->assign('Gas','-');
        }

    /*}}}*/
    }
    
    /**
     * set_dive_details 
     * 
     * @access public
     * @return void
     */
    function set_dive_details() {
        global $t, $_lang, $globals; /*{{{*/
        $result =  $this->result; 
        // Dive Details
        $t->assign('dive_sect_details', $_lang['dive_sect_details'] );

        $t->assign('logbook_entry', $_lang['logbook_entry'] );
        $t->assign('logbook_boat', $_lang['logbook_boat'] );
        $t->assign('logbook_pgstart', $_lang['logbook_pgstart'] );
        $t->assign('logbook_pgend', $_lang['logbook_pgend'] );
        $t->assign('logbook_deco', $_lang['logbook_deco'] );
        $t->assign('logbook_rep', $_lang['logbook_rep'] );
        $t->assign('logbook_surfint', $_lang['logbook_surfint'] );
        $t->assign('logbook_exittime', $_lang['logbook_exittime'] );

        if (isset($result['Entry']) && $result['Entry'] != "" && isset($_lang['entry'][($result['Entry'] - 1)])) {
            $t->assign('Entry', $_lang['entry'][($result['Entry'] - 1)]);
        } else {
            $t->assign('Entry','-');
        }

        if (isset($result['Boat']) && ($result['Boat'] != "")) {
            $t->assign('Boat', $result['Boat'] );
        } else {
            $t->assign('Boat','-');
        }

        if (isset($result['PGStart']) && ($result['PGStart'] != "")) {
            $t->assign('PGStart', $result['PGStart'] );
        } else {
            $t->assign('PGStart','-');
        }

        if (isset($result['PGEnd']) && ($result['PGEnd'] != "")) {
            $t->assign('PGEnd', $result['PGEnd']);
        } else {
            $t->assign('PGEnd','-');
        }

        $t->assign('Deco', ($result['Deco'] == 'True' ? $_lang['yes'] : $_lang['no']) );
        $t->assign('Rep', ($result['Rep'] == 'True' ? $_lang['yes'] : $_lang['no']) );

        if (isset($result['Surfint']) && ($result['Surfint'] != "")) {
            $t->assign('Surfint', $result['Surfint']);
        } else {
            $t->assign('Surfint','-');
        }

        if ((isset($result['Entrytime']) && ($result['Entrytime'] != "")) && (isset($result['Divetime']) && ($result['Divetime'] != ""))) {
            $exittime = date('H:i:s', strtotime($result['Entrytime']) + ($result['Divetime'] * 60));
            $t->assign('ExitTime',$exittime);
        } else {
            $t->assign('ExitTime','-');
        }

        if ($result['Decostops']) {
            $t->assign('Decostops','1');
            $t->assign('logbook_decostops', $_lang['logbook_decostops'] );

            $r = $result['Decostops'];
            $r = htmlentities($r, ENT_QUOTES, "ISO-8859-1");
            $r = str_replace("\r\n", "\n", $r);
            $r = str_replace("\n", "<br>\n", $r);

            $t->assign('stops', $r);
        }
    /*}}}*/
    }

    /**
     * set_equipment 
     * 
     * @access public
     * @return void
     */
    function set_equipment() {
        global $t, $_config, $_lang, $globals; /*{{{*/
        $result =  $this->result; 
        $t->assign('dive_sect_equipment', $_lang['dive_sect_equipment'] );
        $t->assign('logbook_weight', $_lang['logbook_weight'] );
        $t->assign('logbook_divesuit', $_lang['logbook_divesuit'] );
        $t->assign('logbook_computer', $_lang['logbook_computer'] );
	
		$Weight = '-';
        if (isset($result['Weight']) && $result['Weight'] != "") {
            if ($_config['weight']) {
                $Weight = KgToLbs($result['Weight'], 0) ."&nbsp;". $_lang['unit_weight_imp'] ;
            } else {
                $Weight = $result['Weight'] ."&nbsp;". $_lang['unit_weight'] ;
            }
        }
		$t->assign('Weight', $Weight);


        if (isset($result['Divesuit']) && ($result['Divesuit'] != "")) {
            $t->assign('Divesuit', $result['Divesuit'] );
        } else {
            $t->assign('Divesuit', "" );
        }

        if (isset($result['Computer']) && ($result['Computer'] != "")) {
            $t->assign('Computer', $result['Computer'] );
        } else {
            $t->assign('Computer', "") ;
        }

        if (isset($result['UsedEquip']) && ($result['UsedEquip'] != "")) {
            $t->assign('UsedEquip',1);
            $t->assign('logbook_usedequip', $_lang['logbook_usedequip'] );
            $globals['gearlist'] = $result['UsedEquip'];
            $divegear = parse_mysql_query('divegearlist.sql');
            $num_equip = $globals['sql_num_rows'];
            $equip_link[] =  array();

            for ($i=0; $i<$num_equip; $i++) {
                $equip_link[$i] = array (
                        'equipmentnr' => $divegear[$i]['ID'],
                        'divegear' => $divegear[$i]['Object']
                        );
            }
            $t->assign('logbook_place_linktitle', $_lang['logbook_place_linktitle']);  
            $t->assign('equip_link', $equip_link);
        } 
        $t->assign('comma_separated', $_config['comma_separated']);
        $t->assign('comma_separator', $_config['comma_separator']);
    /*}}}*/
    }

    /**
     * set_comments 
     * 
     * @access public
     * @return void
     */
    function set_comments() {
        global $t, $_lang, $globals; /*{{{*/
        $result = $this->result;
        // Show them if we have them
        if (isset($result['Comments']) && ($result['Comments'] != "")) {
            $t->assign('dive_sect_comments', $_lang['dive_sect_comments']);
            $r = $result['Comments'];

            $r = htmlentities($r, ENT_QUOTES, "ISO-8859-1");
            $r = str_replace("\r\n", "\n", $r);
            $r = str_replace("\n", "<br>\n", $r);

            // Make any URLs clickable
            $r = make_clickable($r);

            $t->assign('Comments', $r);
        } else {
            $t->assign('Comments', "");
        }
    /*}}}*/
    }

    /**
     * set_userdefined 
     * 
     * @access public
     * @return void
     */
    function set_userdefined() {
        global $globals, $_config, $t, $_lang; /*{{{*/
        $this->get_userdefined();
        $userdefined = $this->userdefined; 
        $userdefined_count = $this->userdefined_count; 
        // Show userdefined values if we have them
        $t->assign('userdefined_count', $userdefined_count);

        if ($userdefined_count != 0) {
            $t->assign('userdefined_keys',array_keys($userdefined));
            $t->assign('userdefined_values',array_values($userdefined));
        } else {
            $t->assign('userdefined_keys',"");
            $t->assign('userdefined_values',"");
        }
    /*}}}*/
    }

    /**
     * dive_has_photo 
     * 
     * @param mixed $value 
     * @param mixed $row 
     * @access public
     * @return void
     */
    function dive_has_photo($value, $row) {
        global $_config, $_lang;
        if (Divelog::dive_has_pictures($row['Number'])) {
            return '<img src="'.$_config['web_root'].'/images/photo_icon.gif" border="0" alt="'.$_lang['divepic_linktitle'].'" title="'.$_lang['divepic_linktitle'].'">';
        }
    }
    
    /**
     * get_dive_overview returns the dive overview according the defined view preference 
     * 
     * @access public
     * @return void
     */
    function get_dive_overview() {
        global $t, $_lang, $globals, $_config; /*{{{*/

// TODO: Count the dives and display a message if none
        $t->assign('dlog_none', $_lang['dlog_none']);

        /**
         * When view_type = 1 display the ajax grid, if type = 2 display old fashioned table 
         */
        if ($_config['view_type'] == 1) {
            $this->get_dive_overview_grid();
        } elseif ($_config['view_type'] == 2) {
            $this->get_dive_overview_table();
        } else{
            echo '<strong>ERROR: No view_type defined!</strong><br>';
        }
        $t->assign('pagetitle',$_lang['dive_log']);
    /*}}}*/
    }

    /**
     * get_dive_overview_table 
     * 
     * @access public
     * @return void
     */
    function get_dive_overview_table() {
        global $db, $t, $_lang, $globals, $_config; /*{{{*/
        // Get the details of the dives to be listed
        if ($_config['length']) {
            $recentdivelist_query = sql_file('recentdivelist-imp.sql');
        } else {
            $recentdivelist_query = sql_file('recentdivelist.sql');
        }
        $recentdivelist_query .=  " ORDER BY Number DESC";
        $t->assign('dlog_title_number', $_lang['dlog_title_number']);
        $t->assign('dlog_title_divedate', $_lang['dlog_title_divedate']);
        $t->assign('dlog_title_depth', $_lang['dlog_title_depth']);
        $t->assign('dlog_title_divetime', $_lang['dlog_title_divetime']);
        $t->assign('dlog_title_location', $_lang['dlog_title_location']);
        $t->assign('dlog_title_photo', $_lang['dlog_title_photo']);
        $t->assign('divepic_linktitle', $_lang['divepic_linktitle']);

        $t->assign('logbook_profile', $_lang['logbook_profile']);
        $t->assign('logbook_no_profile', $_lang['logbook_no_profile']);

        if (!empty($this->multiuser)) {
            $path = $_config['web_root'].'/index.php/'.$this->user_id.'/list';
        } else {
            $path = $_config['web_root'].'/index.php/list';
        }
        if (empty($this->requested_page)) {
            $cpage = 0;
        } else {
            $cpage = $this->requested_page;
        }
        $pager_options = new TablePager($cpage,$path);
        $paged_data = Pager_Wrapper_MDB2($db, $recentdivelist_query, $pager_options->options);
        $t->assign('dlog_number_title', $_lang['dlog_number_title']);
        if ($_config['length']) {
            $t->assign('unit_length_short', $_lang['unit_length_short_imp']);
        } else {
            $t->assign('unit_length_short', $_lang['unit_length_short']);
        }
        $t->assign('unit_time_short', $_lang['unit_time_short']);
        $t->assign('pages', $paged_data['links']);
        $t->assign('cells', $paged_data['data']);
    /*}}}*/
    }

    /**
     * get_dive_overview_grid 
     * 
     * @access public
     * @return void
     */
    function get_dive_overview_grid() {
        global $db, $t, $_lang, $globals, $_config;
        // Get the details of the dives to be listed
        if ($_config['length']) {
            $recentdivelist_query = sql_file('recentdivelist-imp.sql');
        } else {
            $recentdivelist_query = sql_file('recentdivelist.sql');
        }
        $recentdivelist_query .= " ORDER BY Number DESC";

        $t->assign('dlog_title_number', $_lang['dlog_title_number'] );
        $t->assign('dlog_title_divedate', $_lang['dlog_title_divedate']);
        $t->assign('dlog_title_depth', $_lang['dlog_title_depth'] );
        $t->assign('dlog_title_divetime', $_lang['dlog_title_divetime'] );
        $t->assign('dlog_title_location', $_lang['dlog_title_location'] );
        $t->assign('dlog_title_photo', $_lang['dlog_title_photo'] );

        if (!empty($this->multiuser)) {
            $path = $_config['web_root'].'/index.php/'.$this->user_id.'/list';
        } else {
            $path = $_config['web_root'].'/index.php/list';
        }
        if (empty($this->requested_page)) {
            $cpage = 0;
        } else {
            $cpage = $this->requested_page;
        }
        $data = parse_mysql_query(0,$recentdivelist_query);
        $GridClass = new TableGrid($this->user_id,$data);
        $grid = $GridClass->get_grid_class();

        // $grid = new dataGrid($data,$void); 
        $grid->showColumn('Number', $_lang['dlog_title_number']);
        $grid->setColwidth('Number',"35");
        $grid->showColumn('Divedate', $_lang['dlog_title_divedate']);
        $grid->setColwidth('Divedate',"80");
        $grid->showColumn('Depth', $_lang['dlog_title_depth']);
        $grid->setColwidth('Depth',"60");
        $grid->showColumn('Divetime', $_lang['dlog_title_divetime']);
        $grid->setColwidth('Divetime',"60");
        $grid->showColumn('Place', $_lang['dlog_title_place']);
        $grid->setColwidth('Place',"200");
        $grid->showColumn('City', $_lang['dlog_title_location']);
        $grid->setColwidth('City',"160");
        $grid->showCustomColumn("photo", $_lang['dlog_title_photo']);
        $grid->setColwidth('photo',"30");

        $methodVariable = array($this, 'dive_has_photo'); 
        $grid->setCallbackFunction("photo", $methodVariable);
        $grid->setCallbackFunction("Divedate","convert_date"); 
        $grid->setCallbackFunction("Depth","add_unit_depth");
        $grid->setCallbackFunction("Divetime","add_unit_time");

        $grid->setRowActionFunction("action");
        $grid_ret = $grid->render(TRUE);

        $t->assign('grid_display' ,1);
        $t->assign('grid',$grid_ret );
    }

    /**
     * get_overview_divers 
     * 
     * @access public
     * @return void
     */
    function get_overview_divers() {
        global $t, $_lang, $globals, $_config; /*{{{*/
        $users = new Users();
        $user_list = $users->get_user_data();
        $t->assign('diver_overview',1);
        $t->assign('divers', $user_list);
        $t->assign('file_name','index.php'); 
    /*}}}*/
    }
/*}}}*/
}

/**
 * Divesite contains all functions for displaying the divesite information
 * 
 * @package phpdivinglog
 * @copyright Copyright (C) 2007 Rob Lensen. All rights reserved.
 * @author Rob Lensen <rob@bsdfreaks.nl> 
 * @license LGPL v3 http://www.gnu.org/licenses/lgpl-3.0.txt
 */
class Divesite{
    var $multiuser; /*{{{*/
    var $table_prefix;
    var $user_id;
    var $divesite_nr;
    var $result;
    var $result_countrycity;
    var $country;
    var $city;
    var $dives;
    var $dive_count;
    var $sitelist;
    var $divesite_data;
    var $request_type; // request_type = 0 overview, request_type = 1 details

    /**
     * Divesite default constructor sets some defaults for this class
     * 
     * @access public
     * @return void
     */
    function __construct() {
        $a = func_get_args(); 
        $i = func_num_args(); 
        if ($i == 0) {
            global $_config;
            $this->multiuser = $_config['multiuser'];
        } else {
            if (method_exists($this,$f='__construct'.$i)) { 
                call_user_func_array(array($this,$f),$a); 
            } 
        }
    }

    /**
     * __construct1 construct a Divesite class when divesite ID is given
     * 
     * @param mixed $a1 
     * @access protected
     * @return void
     */
    function __construct1($a1) 
    { 
        global $_config;
        $this->divesite_nr = $a1;
        $this->get_divesite_info();
    } 

    function get_request_type() {
        return $this->request_type;
    }

    function set_divesite_info($request) {
        // We need to extract the info from the request
        /*{{{*/
        if (!$request->diver_choice) {
            if ($request->get_view_request() == 1) {
                $this->request_type = 1;
                $this->divesite_nr = $request->get_site_nr();
            } else {
                $this->request_type = 0;
                $this->requested_page = $request->get_requested_page();
            }
            if ($this->multiuser) {
                $this->user_id = $request->get_user_id();
                $user = new User();
                $user->set_user_id($this->user_id);
                $this->table_prefix = $user->get_table_prefix();
            } else {
                $user = new User();
                $this->table_prefix = $user->get_table_prefix();
                $this->divesite_nr = $request->get_site_nr();
            }
        } else {
            $this->request_type = 3;
        }
    /*}}}*/
    }

    function get_divesite_info() {
        global $globals, $_config; /*{{{*/
        if (!empty($this->divesite_nr)) {
            $this->request_type = 1;
            $globals['placeid'] = $this->divesite_nr;
            $this->result = parse_mysql_query('oneplace.sql');
            $this->result_countrycity = parse_mysql_query('countrycity.sql');
        } else {
            /**
             * If the request type is not already set(by divers choice), set it to overview  
             */
            if ($this->request_type != 3) {
                $this->request_type = 0;
            }
        }
        return $this->result;
    /*}}}*/
    }

    function get_overview_divers() {
        global $t, $_lang, $globals, $_config; /*{{{*/
        $users = new Users();
        $user_list = $users->get_user_data();
        $t->assign('diver_overview',1);
        $t->assign('divers', $user_list);
        $t->assign('file_name','divesite.php');
    /*}}}*/
    }

    /**
     * get_divesite_location_details 
     * 
     * @access public
     * @return void
     */
    function get_divesite_location_details() {
        global $globals, $_config; /*{{{*/
        $countrycity = $this->result_countrycity;
        if (count($countrycity) != 0) {
            if (isset($countrycity['Country']) && ($countrycity['Country'] != "")) {
                // Get the country details from database
                $globals['countryid'] = $countrycity['CountryID'];
                $countrydetails = parse_mysql_query('onecountry.sql');
                if ($globals['sql_num_rows'] == 0) {
                    $this->country = $countrycity['Country'];
                    $this->countryid = $countrycity['CountryID'];
                } else {
                    $this->country = $countrydetails['Country'];
                    $this->countryid = $countrydetails['ID'];
                }
            }

            if (isset($countrycity['City']) && ($countrycity['City'] != "")) {
                // Get the city details from database
                $globals['cityid'] = $countrycity['CityID'];
                $citydetails = parse_mysql_query('onecity.sql');
                if ($globals['sql_num_rows'] == 0) {
                    $this->city = $countrycity['City'];
                    $this->cityid = $countrycity['CityID'];
                } else {
                    $this->city = $citydetails['City'];
                    $this->cityid = $citydetails['ID'];
                }
            }
        }
    /*}}}*/
    }

    /**
     * get_dives_at_location 
     * 
     * @access public
     * @return void
     */
    function get_dives_at_location() {
        global $globals, $_config; /*{{{*/
        // Get the dives at this site from database
        $globals['placeid'] = $this->divesite_nr;
        $this->dives = parse_mysql_query('divelocations.sql');
        $this->dive_count = $globals['sql_num_rows'];
        // Get the site list from database
        $this->sitelist = parse_mysql_query('sitelist.sql');
    /*}}}*/
    }

    /**
     * set_main_divesite_details 
     * 
     * @access public
     * @return void
     */
    function set_main_divesite_details() {
        global $globals, $_config, $t, $_lang; /*{{{*/
        $this->get_divesite_location_details(); 
        // Show main site details
        $result = $this->result;
        $t->assign('pagetitle',$_lang['dive_site_pagetitle'].$result['Place']);
        $t->assign('divesite_id', $this->divesite_nr);
        $t->assign('place_place', $_lang['place_place']);
        $t->assign('place_city', $_lang['place_city']);
        $t->assign('place_country', $_lang['place_country']);
        $t->assign('place_maxdepth', $_lang['place_maxdepth'] );

        if (isset($result['Place']) && ($result['Place'] != "")) {
            $Place = $result['Place'];
        } else {
            $Place = "-";
        }
        $t->assign('Place', $Place);

        if (!empty($this->city)) {
            $t->assign('dive_city_nr', $this->cityid);
            $t->assign('dive_city',$this->city);
            $t->assign('logbook_city_linktitle', $_lang['logbook_city_linktitle']);
        } else {
            $t->assign('dive_city','-');
        }

        if (!empty($this->country)) {
            $t->assign('dive_country_nr', $this->countryid);
            $t->assign('dive_country',$this->country);
            $t->assign('logbook_country_linktitle', $_lang['logbook_country_linktitle']);
        } else {
            $t->assign('dive_country','-');
        }

        $t->assign('place_rating', $_lang['place_rating']);
        if (isset($result['Rating']) && ($result['Rating'] >= 0) && ($result['Rating'] <= 5)) {
            $desc = $_lang['rating'][$result['Rating']];
            $Rating = '<img src="'.$_config['web_root'].'/images/ratings5-'.$result['Rating'].'.gif" alt="'.$desc.'" title="'.$desc.'" border="0" width="84" height="15">';
            $t->assign('Rating', $Rating);
        } else {
            $t->assign('Rating','-');
        }

        if (isset($result['MaxDepth']) && ($result['MaxDepth'] != "")) {
            if ($_config['length']) {
                $MaxDepth = MetreToFeet($result['MaxDepth'], 0) ."&nbsp;". $_lang['unit_length_short_imp'] ;
            } else {
                $MaxDepth = $result['MaxDepth'] ."&nbsp;". $_lang['unit_length_short'];
            }
        } else {
            $MaxDepth = "-";
        }
        $t->assign('MaxDepth',$MaxDepth);

        // Show extra site details
        $t->assign('place_lat', $_lang['place_lat']);
        $t->assign('place_lon', $_lang['place_lon']);
        if (isset($result['MapPath']) && ($result['MapPath'] != "")) {
            $MapPath = $_lang['place_map'];
        } else {
            $MapPath = "&nbsp;";
        }
        $t->assign('place_map', $MapPath);

        if (isset($result['Lat']) && ($result['Lat'] != "")) {
            $Lat = latitude_format($result['Lat']);
        } else {
            $Lat = "-";
        }
        $t->assign('Lat', $Lat);
        $t->assign('LatDec',$result['Lat']); 

        if (isset($result['Lon']) && ($result['Lon'] != "")) {
            $Lon = longitude_format($result['Lon']) ;
        } else {
            $Lon = "-";
        }
        $t->assign('Lon',$Lon);
        $t->assign('LonDec',$result['Lon']);
        if ((isset($result['Lat']) && ($result['Lat'] != "")) && (isset($result['Lon']) && ($result['Lon'] != ""))) {
            $t->assign('site_google_link', $_lang['site_google_link'] .$result['Place']);
        }

        if (isset($result['MapPath']) && ($result['MapPath'] != "")) {
            $maplink_url = "<a href=\"".$_config['web_root']."/". $_config['mappath_web'] . $result['MapPath']."\"  rel=\"lightbox[others]\"\n";
            $maplink_url .=  "   title=\"". $_lang['mappic_linktitle']. $result['Place'];
            $maplink_url .=  "\">". $_lang['mappic'] ."</a>\n";
            $t->assign('maplink_url',$maplink_url);
        } else {
            $t->assign('maplink_url','&nbsp;');
        }
        $t->assign('google_map', $_lang['google_map']);
        $t->assign('place_datum', $_lang['place_datum']);
        $t->assign('datum', $_lang['datum']);

        $t->assign('place_watername', $_lang['place_watername']);
        if (isset($result['WaterName']) && ($result['WaterName'] != "")) {
            $t->assign('WaterName', $result['WaterName'] );
        } else {
            $t->assign('WaterName','-');
        }

        $t->assign('place_water', $_lang['place_water']);
        if ($result['Water'] != 0 && $_lang['water'][($result['Water'] - 1)]) {
            $t->assign('Water', $_lang['water'][($result['Water'] - 1)]);
        } else {
            $t->assign('Water','-');
        }

        $t->assign('place_altitude', $_lang['place_altitude']);
        if (isset($result['Altitude']) && ($result['Altitude'] != "")) {
            $t->assign('Altitude', $result['Altitude'] );
        } else {
            $t->assign('Altitude','-');
        }

        $t->assign('place_difficulty', $_lang['place_difficulty']);
        if (isset($result['Difficulty']) && ($result['Difficulty'] != "")) {
            $t->assign('Difficulty', $result['Difficulty'] );
        } else {
            $t->assign('Difficulty','-');
        }
    /*}}}*/
    }

    /**
     * set_divesite_pictures 
     * 
     * @access public
     * @return void
     */
    function set_divesite_pictures() {
        global $_config, $t, $_lang, $globals; /*{{{*/
        $pic_class = new DivePictures;
        $pic_class->set_divegallery_info_direct($this->user_id);
        $pic_class->get_divegallery_info(0,$this->divesite_nr,0,0,0);
        $divepics = $pic_class->get_image_link();
        $pics = count($divepics);
        if ($pics != 0) {
            if (isset($_config['divepics_preview'])) {
                $t->assign('pics2' , '1');
                $t->assign('image_link', $divepics);
            } else {
                /**
                 *  
                 */
            }
        }
    /*}}}*/
    }

    /**
     * set_dives_at_location 
     * 
     * @access public
     * @return void
     */
    function set_dives_at_location() {
        global $globals, $_config, $t, $_lang; /*{{{*/
        $this->get_dives_at_location();
        // Show site dives if we have them
        if (count($this->dives) == 1) {
            $dives[0] = $this->dives;
        } else {
            $dives = $this->dives;
        }
        if ($this->dive_count != 0) {
            $t->assign('dive_count', $this->dive_count);
            if ($this->dive_count == 1) {
                $t->assign('site_dive_trans', $_lang['site_dive_single']);
            } else {
                $t->assign('site_dive_trans', $_lang['site_dive_plural']);
            }
            for ($i=0; $i<$this->dive_count; $i++) {
                $dives[$i] = $dives[$i]['Number'] ; 
            }
            $t->assign('dlog_number_title', $_lang['dlog_number_title'] );
            $t->assign('dives',$dives);
        } else {
            $t->assign('dlog_number_title', "" );
            $t->assign('dives',"");
        }
        $t->assign('comma_separated', $_config['comma_separated']);
        $t->assign('comma_separator', $_config['comma_separator']);
    /*}}}*/
    }

    /**
     * set_divesite_comments 
     * 
     * @access public
     * @return void
     */
    function set_divesite_comments() {
        global $globals, $_lang, $_config, $t; /*{{{*/
        // Comments
        $result = $this->result;
        // Show them if we have them
        if (isset($result['Comments']) && ($result['Comments'] != "")) {
            $t->assign('site_sect_comments', $_lang['site_sect_comments']);
            $r = $result['Comments'];

            $r = htmlentities($r, ENT_QUOTES, "ISO-8859-1");
            $r = str_replace("\r\n", "\n", $r);
            $r = str_replace("\n", "<br>\n", $r);

            // Handle Google Map URL
            $r = str_replace('[url]','<a href="',$r);
            $r = str_replace('[/url]','" target="_blank" title="'. $_lang['site_google_link']. $result['Place'] .'">'. $_lang['site_google_link'] . $result['Place'] .'</a>',$r);

            // Make any URLs clickable
            $r = make_clickable($r);

            $t->assign('Comments', $r);
        }
    /*}}}*/
    } 

    /**
     * get_divesite_overview 
     * 
     * @access public
     * @return void
     */
    function get_divesite_overview() {
        global $t, $_lang, $globals, $_config; /*{{{*/
        $placetable = $this->table_prefix."Place";
        $logbooktable = $this->table_prefix."Logbook";
        
        if ($_config["length"]) {
           $sql = sql_file("divesite_overview-imp.sql");
        } else {
            $sql = sql_file("divesite_overview.sql");
        }

// TODO: Count the dive sites and display a message if none
        $t->assign('dsite_none', $_lang['dsite_none']);

        /**
         * when view_type = 1 display the ajax grid if type = 2 display old fashioned table 
         */
        if ($_config['view_type'] == 1) {
            $this->get_divesite_overview_grid($sql);
        }
        elseif ($_config['view_type'] == 2) {
            $this->get_divesite_overview_table($sql);
        } else{
            echo '<strong>ERROR: No view_type defined!</strong><br>';
        }
        $t->assign('pagetitle',$_lang['dive_sites']);
    /*}}}*/
    }

    /**
     * get_divesite_overview_table 
     * 
     * @access public
     * @return void
     */
    function get_divesite_overview_table($sql) {
        global $db, $t, $_lang, $globals, $_config; /*{{{*/
        // Get the page header
        // Get the details of the locations to be listed
        $locationlist_query = $sql." ORDER BY Place";
        $t->assign('dsite_title_place', $_lang['dsite_title_place']);
        $t->assign('dsite_title_city', $_lang['dsite_title_city']);
        $t->assign('dsite_title_country', $_lang['dsite_title_country']);
        $t->assign('dsite_title_maxdepth', $_lang['dsite_title_maxdepth']);
        if ($this->multiuser == 1) {
            $path = $_config['web_root'].'/divesite.php/'.$this->user_id.'/list';
        } else {
            $path = $_config['web_root'].'/divesite.php/list';
        }
        if (empty($this->requested_page)) {
            $cpage = 0;
        } else {
            $cpage = $this->requested_page;
        }
        $pager_options = new TablePager($cpage,$path);
        $paged_data = Pager_Wrapper_MDB2($db, $locationlist_query, $pager_options->options);
        $t->assign('logbook_place_linktitle', $_lang['logbook_place_linktitle']);
        $t->assign('dsite_title_place', $_lang['dsite_title_place']);
        $t->assign('dsite_title_city', $_lang['dsite_title_city']);
        $t->assign('dsite_title_country', $_lang['dsite_title_country']);
        $t->assign('dsite_title_maxdepth', $_lang['dsite_title_maxdepth']);
        if ($_config['length']) {
            $t->assign('unit_length_short', $_lang['unit_length_short_imp']);
        } else {
            $t->assign('unit_length_short', $_lang['unit_length_short']);
        }

        $t->assign('pages', $paged_data['links']);
        $t->assign('cells', $paged_data['data']);
    /*}}}*/
    }

    /**
     * get_divesite_overview_grid 
     * 
     * @param mixed $sql 
     * @access public
     * @return void
     */
    function get_divesite_overview_grid($sql) {
        global $t, $_lang, $globals, $_config; /*{{{*/
        $sql .=  " ORDER BY Place ASC";
        $data = parse_mysql_query(0,$sql);;
        $GridClass = new TableGrid($this->user_id,$data);
        $grid = $GridClass->get_grid_class();

        /**
         * Define the table according some info 
         */
        if ($this->multiuser) {
            $url = "/divesite.php".$t->getTemplateVars('sep1').$this->user_id.$t->getTemplateVars('sep2');
        } else {
            $url = "/divesite.php".$t->getTemplateVars('sep2');
        }
        $grid->showColumn('Place', $_lang['dsite_title_place']);
        $grid->setColwidth('Place',"250");
        $grid->showColumn('City', $_lang['dsite_title_city']);
        $grid->setColwidth('City',"200");
        $grid->showColumn('Country', $_lang['dsite_title_country']);
        $grid->setColwidth('Country',"120");
        $grid->showColumn('MaxDepth', $_lang['dsite_title_maxdepth']);
        $grid->setColwidth('MaxDepth',"55");
        $grid->setRowActionFunction("action");
        $grid->setCallbackFunction("MaxDepth","add_unit_depth");

        $grid_ret = $grid->render(TRUE); 
        $t->assign('grid_display' ,1);
        $t->assign('grid',$grid_ret );
    /*}}}*/
    }
/*}}}*/
}

/**
 * Equipment contains all functions for displaying the equipment information
 * 
 * @package phpdivinglog
 * @copyright Copyright (C) 2007 Rob Lensen. All rights reserved.
 * @author Rob Lensen <rob@bsdfreaks.nl> 
 * @license LGPL v3 http://www.gnu.org/licenses/lgpl-3.0.txt
 */
class Equipment{
    var $multiuser; /*{{{*/
    var $user_id;
    var $equipment_nr;
    var $result;
    var $table_prefix;
    var $result_gearlist;
    var $show_equip_service; // if this is set show only equipment which needs service
    var $request_type; // request_type = 0 overview, request_type = 1 details
   
    function Equipment() {
        global $_config;
        $this->multiuser = $_config['multiuser'];
    }
    
    function get_request_type() {
        return $this->request_type;
    }

    function get_equipment_nr() {
        return $this->equipment_nr;
    }
    
    /**
     * set_equipment_info 
     * 
     * @param mixed $request 
     * @access public
     * @return void
     */
    function set_equipment_info($request) {
        // We need to extract the info from the request
        /*{{{*/
        if (!$request->diver_choice) {
            // Find request type
            if ($request->get_view_request() == 1) {
                $this->request_type = 1;
                $this->equipment_nr = $request->get_equipment_nr();
            } else {
                $this->request_type = 0;
                $this->requested_page = $request->get_requested_page();
                //Check if we need to show only the equipment with the service tag
                if (isset($request->special_req) && $request->special_req == 'service') {
                    $this->show_equip_service = 1;
                } else {
                    $this->show_equip_service = 0;
                }
            }
            if ($this->multiuser) {
                $this->user_id = $request->get_user_id();
                $user = new User();
                $user->set_user_id($this->user_id);
                $this->table_prefix = $user->get_table_prefix();
            } else {
                $user = new User();
                $this->table_prefix = $user->get_table_prefix();
            }
        } else {
            $this->request_type = 3;
        }
    /*}}}*/
    }

    /**
     * get_equipment_info 
     * 
     * @access public
     * @return void
     */
    function get_equipment_info() {
        global $_config, $globals; /*{{{*/
        if (!empty($this->equipment_nr)) {
            $this->request_type = 1;
            $globals['gear'] = $this->equipment_nr;
            $this->result = parse_mysql_query('oneequipment.sql');
            $this->result_gearlist = parse_mysql_query('gearlist.sql');
        } else {
           /**
             * If the request type is not already set(by divers choice), set it to overview  
             */
            if ($this->request_type != 3) {
                $this->request_type = 0;
            }
        }
        return $this->result;
    /*}}}*/
    }

    /**
     * get_overview_divers 
     * 
     * @access public
     * @return void
     */
    function get_overview_divers() {
        global $t, $_lang, $globals, $_config; /*{{{*/
        $users = new Users();
        $user_list = $users->get_user_data();
        $t->assign('diver_overview',1);
        $t->assign('divers', $user_list);
        $t->assign('file_name','equipment.php');
    /*}}}*/
    }

    /**
     * set_equipment_service_info 
     * 
     * @access public
     * @return void
     */
    function set_equipment_service_info() {
        global $_config, $globals, $t, $_lang; /*{{{*/
        $this->service = parse_mysql_query('equipservice.sql');
        $this->equipment_service_count = $globals['sql_num_rows'];
        $t->assign('equipment_service_count', $this->equipment_service_count);
        $t->assign('equipment_service_reminder', $_config['equipment_service_reminder']);
        $t->assign('equipment_service_warning', $_lang['equip_service_warning']);
    /*}}}*/
    }

    /**
     * set_main_equipment_details 
     * 
     * @access public
     * @return void
     */
    function set_main_equipment_details() {
        global $t, $_config, $globals, $_lang; /*{{{*/
        $result = $this->result; 

        $t->assign('pagetitle', $_lang['equip_details_pagetitle'].$result['Object']);
        $t->assign('equip_object', $_lang['equip_object'] );
        $t->assign('equip_manufacturer', $_lang['equip_manufacturer']);
        $t->assign('equip_datep', $_lang['equip_datep']);
        $t->assign('equip_dater', $_lang['equip_dater'] );
        $t->assign('equip_datern', $_lang['equip_datern'] );
        $t->assign('equip_o2servicedate', $_lang['equip_o2servicedate'] );
        $t->assign('equip_serial', $_lang['equip_serial'] );
        $t->assign('equip_warranty', $_lang['equip_warranty'] );
        $t->assign('equip_shop', $_lang['equip_shop'] );
        $t->assign('equip_price', $_lang['equip_price']);
        $t->assign('equip_weight', $_lang['equip_weight'] );
        $t->assign('equip_inactive', $_lang['equip_inactive'] );
        $t->assign('equip_service_warning', $_lang['equip_service_warning'] );

        // Show main equipment details
        $t->assign('Object',$result['Object'] );
        $t->assign('Manufacturer', $result['Manufacturer']);

        // Show equipment purchase details
        if (isset($result['DateP']) && ($result['DateP'] != "")) {
            $t->assign('DateP', date($_lang['equip_date_format'], strtotime($result['DateP'])));
        } else {
            $t->assign('DateP','-');
        }

        if (isset($result['DateR']) && ($result['DateR'] != "")) {
            $t->assign('DateR', date($_lang['equip_date_format'], strtotime($result['DateR'])) );
        } else {
            $t->assign('DateR','-');
        }

        $t->assign('equip_datern_warning',false);
        if (isset($result['DateRN']) && ($result['DateRN'] != "")) {
            $t->assign('DateRN', date($_lang['equip_date_format'], strtotime($result['DateRN'])) );
            if (strtotime("now") > (strtotime($result['DateRN']) - ($_config['equipment_service_warning'] * 86400))) {
                $t->assign('equip_datern_warning',true);
            }
        } else {
            $t->assign('DateRN','-');
        }

        $t->assign('equip_o2servicedate_warning',false);
        if (isset($result['O2ServiceDate']) && ($result['O2ServiceDate'] != "")) {
            $t->assign('O2ServiceDate', date($_lang['equip_date_format'], strtotime($result['O2ServiceDate'])) );
            if (strtotime("now") > (strtotime($result['O2ServiceDate']) - ($_config['equipment_service_warning'] * 86400))) {
                $t->assign('equip_o2servicedate_warning',true);
            }
        } else {
            $t->assign('O2ServiceDate','-');
        }

        if (isset($result['Inactive']) && ($result['Inactive'] != "")) {
            if ($result['Inactive'] == 'True') {
                $t->assign('Inactive', '<img src="'.$_config['web_root'].'/images/icon_inactive_16.png" alt="'.$_lang['inactive'][1].'" title="'.$_lang['inactive'][1].'"> '.$_lang['inactive'][1]);
            } else {
                $t->assign('Inactive', '<img src="'.$_config['web_root'].'/images/icon_active_16.png" alt="'.$_lang['inactive'][0].'" title="'.$_lang['inactive'][0].'"> '.$_lang['inactive'][0]);
            }
        } else {
            $t->assign('Inactive','-');
        }

        if (isset($result['Serial']) && ($result['Serial'] != "")) {
            $t->assign('Serial', $result['Serial']);
        } else {
            $t->assign('Serial','-');
        }

        if (isset($result['Warranty']) && ($result['Warranty'] != "")) {
            $t->assign('Warranty', $result['Warranty'] );
        } else {
            $t->assign('Warranty','-');
        }

        if (isset($result['Shop']) && ($result['Shop'] != "")) {
            $t->assign('Shop', $result['Shop']);
        } else {
            $t->assign('Shop','-');
        }

        if (isset($result['Price']) && ($result['Price'] != "")) {
            $t->assign('Price',$_lang['currency_prefix'] .number_format($result['Price'],2) .$_lang['currency_suffix'] );
        } else {
            $t->assign('Price','-');
        }

        if (isset($result['Weight']) && ($result['Weight'] != "")) {
            if ($_config['weight']) {
                $Weight = KgToLbs($result['Weight'], 0) ."&nbsp;". $_lang['unit_weight_imp'] ;
            } else {
                $Weight = $result['Weight'] ."&nbsp;". $_lang['unit_weight'] ;
            }$t->assign('Weight' ,$Weight);
        } else {
            $t->assign('Weight','-');
        }

        // Show the photo
        if (isset($result['PhotoPath']) && ($result['PhotoPath'] != "")) {
            $t->assign('PhotoPath', $result['PhotoPath'] );
            $t->assign('equip_photo', $_lang['equip_photo'] );
            $this->set_equipment_pictures();
            $t->assign('PhotoPathurl',  $_config['equippath_web'] . $result['PhotoPath']);
            $t->assign('equip_photo_linktitle', $_lang['equip_photo_linktitle']. $result['Object']);
            $t->assign('equip_photo_link', $_lang['equip_photo_link'] );
        }

    /*}}}*/
    }

    /**
     * set_equipment_pictures 
     * 
     * @access public
     * @return void
     */
    function set_equipment_pictures() {
        global $_config,$t, $_lang, $globals; /*{{{*/
        $pic_class = new DivePictures;
        $pic_class->set_divegallery_info_direct($this->user_id);
        $pic_class->get_divegallery_info(0,0,$this->equipment_nr,0,0);
        $divepics = $pic_class->get_image_link();
        $pics = count($divepics);
        if ($pics > 0) {
            if (isset($_config['divepics_preview'])) {
                $t->assign('pics2' , '1');
                $t->assign('has_images', '1');
                $t->assign('image_link', $divepics);
            } else {
                /**
                 *  
                 */
                 $t->assign('has_images', '0');
              // $t->assign('image_link', '');
            }
        } else {
            $t->assign('has_images', '0');
        // $t->assign('image_link', '');
        }
    /*}}}*/
    }

    /**
     * set_comments 
     * 
     * @access public
     * @return void
     */
    function set_comments() {
        global $t, $_lang, $globals; /*{{{*/
        $result =  $this->result; 
        // Show them if we have them
        if (isset($result['Comments']) && ($result['Comments'] != "")) {
            $t->assign('equip_sect_comments', $_lang['equip_sect_comments'] );

            $r = $result['Comments'];

            $r = htmlentities($r, ENT_QUOTES, "ISO-8859-1");
            $r = str_replace("\r\n", "\n", $r);
            $r = str_replace("\n", "<br>\n", $r);

            // Make any URLs clickable
            $r = make_clickable($r);

            $t->assign('Comments', $r);
        }
    /*}}}*/
    }

    /**
     * equipment_inactive 
     * 
     * @param mixed $value 
     * @param mixed $row 
     * @access public
     * @return void
     */
    function equipment_inactive($value, $row) {
        global $_config, $_lang;
        if ($value == 'True') {
            return '<img src="'.$_config['web_root'].'/images/icon_inactive_16.png" alt="'.$_lang['inactive'][1].'" title="'.$_lang['inactive'][1].'">';
        } else {
            return '<img src="'.$_config['web_root'].'/images/icon_active_16.png" alt="'.$_lang['inactive'][0].'" title="'.$_lang['inactive'][0].'">';
        }
    }

    /**
     * equip_has_photo 
     * 
     * @param mixed $value 
     * @param mixed $row 
     * @access public
     * @return void
     */
    function equip_has_photo($value, $row) {
        global $_config, $_lang;
        if (isset($row['PhotoPath']) && $row['PhotoPath'] != '') {
            return '<img src="'.$_config['web_root'].'/images/photo_icon.gif" border="0" alt="'.$_lang['equip_photo_linktitle'].$row['Object'].'" title="'.$_lang['equip_photo_linktitle'].$row['Object'].'">';
        } else {
            return '&nbsp;';
        }
    }

    /**
     * equip_sevice_warning 
     * 
     * @param mixed $value 
     * @param mixed $row 
     * @access public
     * @return void
     */
    function equip_service_warning($value, $row) {
        global $_config, $_lang;
        if (isset($row['Service']) && ($row['Service'] == "1")) {
            return '<img src="'.$_config['web_root'].'/images/icon_warning_16.png" 
border="0" width="16" height="16"
alt="'.$_lang['equip_service_warning'].'" title="'.$_lang['equip_service_warning'].'">';
        } else {
            return '&nbsp;';
        }
    }

    /**
     * get_equipment_overview 
     * 
     * @access public
     * @return void
     */
    function get_equipment_overview() {
        global $t, $_lang, $globals, $_config; /*{{{*/

        // TODO: Count the equipment and display a message if none
        $t->assign('equip_none', $_lang['equip_none']);
        $t->assign('pagetitle', $_lang['dive_equip']);

        /**
         * When view_type = 1 display the ajax grid, if type = 2 display old fashioned table 
         */
        if ($_config['view_type'] == 1) {
            $this->get_equipment_overview_grid();
        } elseif ($_config['view_type'] == 2) {
            $this->get_equipment_overview_table();
        } else {
            echo '<strong>ERROR: No view_type defined!</strong><br>';
        }
    /*}}}*/
    }

    /**
     * get_equipment_overview_table 
     * 
     * @access public
     * @return void
     */
    function get_equipment_overview_table() {
        global $db, $t, $_lang, $globals, $_config; /*{{{*/
        if ($this->show_equip_service == 1) {
            $equiplist_query = sql_file('equipservice.sql');
            $t->assign('equipment_service_warning', $_lang['equip_service_warning']);
            $t->assign('pagetitle', $_lang['equip_service_warning']);
        } else {
            $equiplist_query = sql_file('equiplist.sql');
        }
        $t->assign('show_equip_service', $this->show_equip_service);
        // $t->assign('equip_none', $_lang['equip_none'] );
        $t->assign('equip_title_object', $_lang['equip_title_object'] );
        $t->assign('equip_title_manufacturer', $_lang['equip_title_manufacturer'] );
        $t->assign('equip_title_inactive', $_lang['equip_title_inactive'] );
        $t->assign('equip_title_photo', $_lang['equip_title_photo']);
        $t->assign('equip_title_service', $_lang['equip_title_service']);

        $t->assign('logbook_equip_linktitle', $_lang['logbook_equip_linktitle'] );
        $t->assign('equip_photo_linktitle', $_lang['equip_photo_linktitle']);
        $t->assign('equip_inactive_active', $_lang['inactive'][0]);
        $t->assign('equip_inactive_inactive', $_lang['inactive'][1]);

        if (!empty($this->multiuser)) {
            $path = $_config['web_root'].'/equipment.php/'.$this->user_id.'/list';
        } else {
            $path = $_config['web_root'].'/equipment.php/list';
        }
        if (empty($this->requested_page)) {
            $cpage = 0;
        } else {
            $cpage = $this->requested_page;
        }
        $pager_options = new TablePager($cpage,$path);
        $paged_data = Pager_Wrapper_MDB2($db, $equiplist_query, $pager_options->options);
        
        $t->assign('pages', $paged_data['links']);
        $t->assign('cells', $paged_data['data']);
    /*}}}*/
    }

    /**
     * get_equipment_overview_grid 
     * 
     * @access public
     * @return void
     */
    function get_equipment_overview_grid() {
        global $t, $_lang, $globals, $_config; /*{{{*/
        if ($this->show_equip_service == 1) {
            $sql = sql_file('equipservice.sql');
            $t->assign('equipment_service_warning', $_lang['equip_service_warning']);
            $t->assign('pagetitle', $_lang['equip_service_warning']);
        } else {
            $sql = sql_file('equiplist.sql');
        }
        $t->assign('show_equip_service', $this->show_equip_service);

        $data = parse_mysql_query(0,$sql);;
        $GridClass = new TableGrid($this->user_id,$data);
        $grid = $GridClass->get_grid_class();

        /**
         * Define the table according some info 
         */
        if ($this->multiuser) {
            $url = "/divesite.php".$t->getTemplateVars('sep1').$this->user_id.$t->getTemplateVars('sep2');
        } else {
            $url = "/divesite.php".$t->getTemplateVars('sep2');
        }

        $grid->showColumn('Object', $_lang['equip_title_object']);
        $grid->setColwidth('Object',"350");
        $grid->showColumn('Manufacturer', $_lang['equip_title_manufacturer']);
        $grid->setColwidth('Manufacturer',"180");
        $grid->showColumn('Inactive', $_lang['equip_title_inactive']);
        $grid->setColwidth('Inactive',"30");
        $grid->showCustomColumn("photo", $_lang['equip_title_photo']);
        $grid->setColwidth('photo',"25");
        $grid->showCustomColumn("service", $_lang['equip_title_service']);
        $grid->setColwidth('service',"40");

        $methodVariable = array($this, 'equip_has_photo'); 
        $grid->setCallbackFunction("photo", $methodVariable);

        $methodVariable = array($this, 'equipment_inactive'); 
        $grid->setCallbackFunction("Inactive", $methodVariable);

        $methodVariable = array($this, 'equip_service_warning'); 
        $grid->setCallbackFunction("service", $methodVariable);

        $grid->setRowActionFunction("action");
        $grid_ret = $grid->render(TRUE); 
        $t->assign('grid_display' ,1);
        $t->assign('grid',$grid_ret );
    /*}}}*/
    }
/*}}}*/
}

/**
 * Diveshop contains all functions for displaying the diveshop information
 * 
 * @package phpdivinglog
 * @version $Rev$
 * @copyright Copyright (C) 2011 Rob Lensen. All rights reserved.
 * @author Rob Lensen <rob@bsdfreaks.nl> 
 * @license LGPL v3 http://www.gnu.org/licenses/lgpl-3.0.txt
 */
class Diveshop{
    var $multiuser; /*{{{*/
    var $table_prefix;
    var $user_id;
    var $diveshop_nr;
    var $result;
    var $result_countrycity;
    var $country;
    var $city;
    var $dives;
    var $dive_count;
    var $shoplist;
    var $diveshop_data;
    var $request_type; // request_type = 0 overview, request_type = 1 details

    /**
     * Diveshop default constructor sets some defaults for this class
     * 
     * @access public
     * @return void
     */
    function __construct() {
        $a = func_get_args(); 
        $i = func_num_args(); 
        if ($i == 0) {
            global $_config;
            $this->multiuser = $_config['multiuser'];
        } else {
            if (method_exists($this,$f='__construct'.$i)) { 
                call_user_func_array(array($this,$f),$a); 
            } 
        }
    }

    /**
     * __construct1 construct a Diveshop class when diveshop ID is given
     * 
     * @param mixed $a1 
     * @access protected
     * @return void
     */
    function __construct1($a1) 
    { 
        global $_config;
        $this->diveshop_nr = $a1;
        $this->get_diveshop_info();
    } 

    function get_request_type() {
        return $this->request_type;
    }

    function set_diveshop_info($request) {
        // We need to extract the info from the request
        /*{{{*/
        if (!$request->diver_choice) {
            if ($request->get_view_request() == 1) {
                $this->request_type = 1;
                $this->diveshop_nr = $request->get_diveshop_nr();
            } else {
                $this->request_type = 0;
                $this->requested_page = $request->get_requested_page();
            }
            if ($this->multiuser) {
                $this->user_id = $request->get_user_id();
                $user = new User();
                $user->set_user_id($this->user_id);
                $this->table_prefix = $user->get_table_prefix();
            } else {
                $user = new User();
                $this->table_prefix = $user->get_table_prefix();
                $this->diveshop_nr = $request->get_diveshop_nr();
            }
        } else {
            $this->request_type = 3;
        }
    /*}}}*/
    }

    function get_diveshop_info() {
        global $globals, $_config; /*{{{*/
        if (!empty($this->diveshop_nr)) {
            $this->request_type = 1;
            $globals['shopid'] = $this->diveshop_nr;
            $this->result = parse_mysql_query('oneshop.sql');
        } else {
            /**
             * If the request type is not already set (by divers choice), set it to overview  
             */
            if ($this->request_type != 3) {
                $this->request_type = 0;
            }
        }
        return $this->result;
    /*}}}*/
    }

    function get_overview_divers() {
        global $t, $_lang, $globals, $_config; /*{{{*/
        $users = new Users();
        $user_list = $users->get_user_data();
        $t->assign('diver_overview',1);
        $t->assign('divers', $user_list);
        $t->assign('file_name','diveshop.php');
    /*}}}*/
    }

    /**
     * get_dives_with_shop 
     * 
     * @access public
     * @return void
     */
    function get_dives_with_shop() {
        global $globals, $_config; /*{{{*/
        // Get the dives with this shop from database
        $globals['shopid'] = $this->diveshop_nr;
        $this->dives = parse_mysql_query('shopdives.sql');
        $this->dive_count = $globals['sql_num_rows'];
        // Get the shop list from database
        $this->shoplist = parse_mysql_query('shoplist.sql');
    /*}}}*/
    }

    /**
     * get_trips_with_shop 
     * 
     * @access public
     * @return void
     */
    function get_trips_with_shop() {
        global $globals, $_config; /*{{{*/
        // Get the trips with this dive shop from database
        $globals['shopid'] = $this->diveshop_nr;
        $this->trips = parse_mysql_query('shoptrips.sql');
        $this->trip_count = $globals['sql_num_rows'];
        // Get the diveshop list from database
        $this->shoplist = parse_mysql_query('shoplist.sql');
    /*}}}*/
    }

    /**
     * set_main_diveshop_details 
     * 
     * @access public
     * @return void
     */
    function set_main_diveshop_details() {
        global $globals, $_config, $t, $_lang; /*{{{*/
        // Show main shop details
        $result = $this->result;

        if (isset($result['ShopType']) && ($result['ShopType'] != '')) {
            $t->assign('pagetitle',$result['ShopName'].' - '.$result['ShopType']);
        } else {
            $t->assign('pagetitle',$_lang['dive_shop_pagetitle'].$result['ShopName']);
        }

        $t->assign('diveshop_id', $this->diveshop_nr);
        $t->assign('shop_name', $_lang['shop_name']);
        $t->assign('shop_type', $_lang['shop_type']);
        $t->assign('shop_street', $_lang['shop_street']);
        $t->assign('shop_address2', $_lang['shop_address2']);
        $t->assign('shop_zip', $_lang['shop_zip']);
        $t->assign('shop_city', $_lang['shop_city']);
        $t->assign('shop_state', $_lang['shop_state']);
        $t->assign('shop_country', $_lang['shop_country']);
        $t->assign('shop_phone', $_lang['shop_phone']);
        $t->assign('shop_mobile', $_lang['shop_mobile']);
        $t->assign('shop_fax', $_lang['shop_fax']);
        $t->assign('shop_email', $_lang['shop_email']);
        $t->assign('shop_url', $_lang['shop_url']);
        $t->assign('shop_location', $_lang['shop_location']);
        $t->assign('shop_rating', $_lang['shop_rating']);
        $t->assign('shop_sect_activity', $_lang['shop_sect_activity'].$result['ShopName']);

        if (isset($result['ShopName']) && ($result['ShopName'] != "")) {
            $t->assign('ShopName', $result['ShopName']);
        } else {
            $t->assign('ShopName','-');
        }

        if (isset($result['ShopType']) && ($result['ShopType'] != "")) {
            $t->assign('ShopType', $result['ShopType']);
        } else {
            $t->assign('ShopType','-');
        }

        if (isset($result['Street']) && ($result['Street'] != "")) {
            $t->assign('Street', $result['Street']);
        } else {
            $t->assign('Street','');
        }

        if (isset($result['Address2']) && ($result['Address2'] != "")) {
            $t->assign('Address2', $result['Address2']);
        } else {
            $t->assign('Address2','');
        }

        if (isset($result['Zip']) && ($result['Zip'] != "")) {
            $t->assign('Zip', $result['Zip']);
        } else {
            $t->assign('Zip','');
        }

        if (isset($result['City']) && ($result['City'] != "")) {
            $t->assign('City', $result['City']);
        } else {
            $t->assign('City','');
        }

        if (isset($result['State']) && ($result['State'] != "")) {
            $t->assign('State', $result['State']);
        } else {
            $t->assign('State','');
        }

        if (isset($result['Country']) && ($result['Country'] != "")) {
            $t->assign('Country', $result['Country']);
        } else {
            $t->assign('Country','');
        }

        if (isset($result['Phone']) && ($result['Phone'] != "")) {
            $t->assign('Phone', $result['Phone']);
        } else {
            $t->assign('Phone', '-');
        }

        if (isset($result['Mobile']) && ($result['Mobile'] != "")) {
            $t->assign('Mobile', $result['Mobile']);
        } else {
            $t->assign('Mobile', '-');
        }

        if (isset($result['Fax']) && ($result['Fax'] != "")) {
            $t->assign('Fax', $result['Fax']);
        } else {
            $t->assign('Fax', '-');
        }

        if (isset($result['Email']) && ($result['Email'] != "")) {
            $email = '<a href="mailto:'.$result['Email'].'" target="_blank">'.$result['Email'].'</a>';
            $t->assign('Email', $email);
        } else {
            $t->assign('Email', '-');
        }

        if (isset($result['URL']) && ($result['URL'] != "")) {
            $url = '<a href="'.$result['URL'].'" target="_blank">'.$result['URL'].'</a>';
            $t->assign('URL', $url);
        } else {
            $t->assign('URL', '-');
        }

        if (isset($result['Rating']) && ($result['Rating'] >= 0) && ($result['Rating'] <= 5)) {
            $desc = $_lang['rating'][$result['Rating']];
            $Rating = '<img src="'.$_config['web_root'].'/images/ratings5-'.$result['Rating'].'.gif" alt="'.$desc.'" title="'.$desc.'" border="0" width="84" height="15">';
            $t->assign('Rating', $Rating);
        } else {
            $t->assign('Rating','-');
        }

        // Show the photo
        if (isset($result['PhotoPath']) && ($result['PhotoPath'] != "")) {
            $t->assign('PhotoPath', $result['PhotoPath']);
            $t->assign('shop_photo', $_lang['shop_photo']);
            $this->set_diveshop_pictures();
            $t->assign('PhotoPathurl', $_config['shoppath_web'] . $result['PhotoPath']);
            $t->assign('shop_photo_linktitle', $_lang['shop_photo_linktitle']. $result['ShopName']. ' '.$result['ShopType']);
            $t->assign('shop_photo_link', $_lang['shop_photo_link'] );
        }

    /*}}}*/
    }

    /**
     * set_diveshop_pictures 
     * 
     * @access public
     * @return void
     */
    function set_diveshop_pictures() {
        global $_config, $t, $_lang, $globals; /*{{{*/
        $pic_class = new DivePictures;
        $pic_class->set_divegallery_info_direct($this->user_id);
        $pic_class->get_divegallery_info(0,0,0,$this->diveshop_nr,0);
        $divepics = $pic_class->get_image_link();
        $pics = count($divepics);
        if ($pics > 0) {
            if (isset($_config['divepics_preview'])) {
                $t->assign('pics2', '1');
                $t->assign('has_images', '1');
                $t->assign('image_link', $divepics);
            } else {
                /**
                 *  
                 */
                 $t->assign('has_images', '0');
            }
        } else {
                 $t->assign('has_images', '0');
        }
    /*}}}*/
    }

    /**
     * set_dives_with_shop 
     * 
     * @access public
     * @return void
     */
    function set_dives_with_shop() {
        global $globals, $_config, $t, $_lang; /*{{{*/
        $this->get_dives_with_shop();
        // Show shop dives if we have them
        if (count($this->dives) == 1) {
            $dives[0] = $this->dives;
        } else {
            $dives = $this->dives;
        }
        if ($this->dive_count != 0) {
            $t->assign('dive_count', $this->dive_count);
            if ($this->dive_count == 1) {
                $t->assign('shop_dive_trans', $_lang['shop_dive_single']);
            } else {
                $t->assign('shop_dive_trans', $_lang['shop_dive_plural']);
            }
            for ($i=0; $i<$this->dive_count; $i++) {
                $dives[$i] = $dives[$i]['Number'] ; 
            }
            $t->assign('dlog_number_title', $_lang['dlog_number_title'] );
            $t->assign('dives',$dives);
        } else {
            $t->assign('dive_count', $this->dive_count);
            $t->assign('dlog_number_title', "" );
            $t->assign('dives',"");
        }
        $t->assign('comma_separated', $_config['comma_separated']);
        $t->assign('comma_separator', $_config['comma_separator']);
    /*}}}*/
    }

    /**
     * set_trips_with_shop 
     * 
     * @access public
     * @return void
     */
    function set_trips_with_shop() {
        global $globals, $_config, $t, $_lang; /*{{{*/
        $this->get_trips_with_shop();
        // Show shop trips if we have them
        if ($this->trip_count == 1) {
            $trips[0] = $this->trips;
        } else {
            $trips = $this->trips;
        }
        if ($this->trip_count != 0) {
            $t->assign('trip_count', $this->trip_count);
            if ($this->trip_count == 1) {
                $t->assign('shop_trip_trans', $_lang['shop_trip_single']);
            } else {
                $t->assign('shop_trip_trans', $_lang['shop_trip_plural']);
            }
            $t->assign('dtrip_number_title', $_lang['dtrip_number_title'] );
            $t->assign('trips',$trips);
        } else {
            $t->assign('trip_count', $this->trip_count);
            $t->assign('dtrip_number_title', "" );
            $t->assign('trips',"");
        }
        $t->assign('comma_separated', $_config['comma_separated']);
        $t->assign('comma_separator', $_config['comma_separator']);
    /*}}}*/
    }

    /**
     * set_diveshop_comments 
     * 
     * @access public
     * @return void
     */
    function set_diveshop_comments() {
        global $globals, $_config, $t, $_lang; /*{{{*/
        // Comments
        $result = $this->result;
        // Show them if we have them
        if (isset($result['Comments']) && ($result['Comments'] != "")) {
            $t->assign('shop_sect_comments', $_lang['shop_sect_comments']);
            $r = $result['Comments'];

            $r = htmlentities($r, ENT_QUOTES, "ISO-8859-1");
            $r = str_replace("\r\n", "\n", $r);
            $r = str_replace("\n", "<br>\n", $r);

            // Make any URLs clickable
            $r = make_clickable($r);

            $t->assign('Comments', $r);
        }
    /*}}}*/
    } 

    /**
     * shop_has_photo 
     * 
     * @param mixed $value 
     * @param mixed $row 
     * @access public
     * @return void
     */
    function shop_has_photo($value, $row) {
        global $_config, $_lang;
        if ($row['PhotoPath'] != '') {
            return '<img src="'.$_config['web_root'].'/images/photo_icon.gif" border="0" alt="'.$_lang['shop_photo_linktitle'].$row['ShopName'].' '.$row['ShopType'].'" title="'.$_lang['shop_photo_linktitle'].$row['ShopName'].' '.$row['ShopType'].'">';
        }
    }

    /**
     * get_diveshop_overview 
     * 
     * @access public
     * @return void
     */
    function get_diveshop_overview() {
        global $t, $_lang, $globals, $_config; /*{{{*/
        $shoptable = $this->table_prefix."Shop";
        $sql = sql_file("shoplist.sql");

// TODO: Count the shops and display a message if none
        $t->assign('dshop_none', $_lang['dshop_none']);

        /**
         * when view_type = 1 display the ajax grid if type = 2 display old fashioned table 
         */
        if ($_config['view_type'] == 1) {
            $this->get_diveshop_overview_grid($sql);
        }
        elseif ($_config['view_type'] == 2) {
            $this->get_diveshop_overview_table($sql);
        } else{
            echo '<strong>ERROR: No view_type defined!</strong><br>';
        }
        $t->assign('pagetitle',$_lang['dive_shops']);
    /*}}}*/
    }

    /**
     * get_diveshop_overview_table 
     * 
     * @access public
     * @return void
     */
    function get_diveshop_overview_table($sql) {
        global $db, $t, $_lang, $globals, $_config; /*{{{*/
        // Get the page header
        // Get the details of the locations to be listed
        $locationlist_query = $sql;
        $t->assign('dshop_title_shop', $_lang['dshop_title_shop']);
        $t->assign('dshop_title_type', $_lang['dshop_title_type']);
        $t->assign('dshop_title_country', $_lang['dshop_title_country']);
        $t->assign('dshop_title_photo', $_lang['dshop_title_photo']);
        $t->assign('logbook_shop_linktitle', $_lang['logbook_shop_linktitle'] );

        $t->assign('shop_photo_linktitle', $_lang['shop_photo_linktitle'] );

        if ($this->multiuser == 1) {
            $path = $_config['web_root'].'/diveshop.php/'.$this->user_id.'/list';
        } else {
            $path = $_config['web_root'].'/diveshop.php/list';
        }
        if (empty($this->requested_page)) {
            $cpage = 0;
        } else {
            $cpage = $this->requested_page;
        }

        $pager_options = new TablePager($cpage,$path);
        $paged_data = Pager_Wrapper_MDB2($db, $locationlist_query, $pager_options->options);

        $t->assign('pages', $paged_data['links']);
        $t->assign('cells', $paged_data['data']);
    /*}}}*/
    }

    /**
     * get_diveshop_overview_grid 
     * 
     * @param mixed $sql 
     * @access public
     * @return void
     */
    function get_diveshop_overview_grid($sql) {
        global $t, $_lang, $globals, $_config; /*{{{*/
        $data = parse_mysql_query(0,$sql);;
        $GridClass = new TableGrid($this->user_id,$data);
        $grid = $GridClass->get_grid_class();

        /**
         * Define the table according some info 
         */
        if ($this->multiuser) {
            $url = "/diveshop.php".$t->getTemplateVars('sep1').$this->user_id.$t->getTemplateVars('sep2');
        } else {
            $url = "/diveshop.php".$t->getTemplateVars('sep2');
        }

        $grid->showColumn('ShopName', $_lang['dshop_title_shop']);
        $grid->setColwidth('ShopName',"300");
        $grid->showColumn('ShopType', $_lang['dshop_title_type']);
        $grid->setColwidth('ShopType',"100");
        $grid->showColumn('Country', $_lang['dshop_title_country']);
        $grid->setColwidth('Country',"200");
        $grid->setRowActionFunction("action");
        $grid->showCustomColumn("photo", $_lang['dshop_title_photo']);
        $grid->setColwidth('photo',"25");

        $methodVariable = array($this, 'shop_has_photo'); 
        $grid->setCallbackFunction("photo", $methodVariable);

        $grid_ret = $grid->render(TRUE); 
        $t->assign('grid_display' ,1);
        $t->assign('grid',$grid_ret );
    /*}}}*/
    }
/*}}}*/
}


/**
 * Divetrip contains all functions for displaying the divetrip information
 * 
 * @package phpdivinglog
 * @version $Rev$
 * @copyright Copyright (C) 2011 Rob Lensen. All rights reserved.
 * @author Rob Lensen <rob@bsdfreaks.nl> 
 * @license LGPL v3 http://www.gnu.org/licenses/lgpl-3.0.txt
 */
class Divetrip{
    var $multiuser; /*{{{*/
    var $table_prefix;
    var $user_id;
    var $divetrip_nr;
    var $result;
    var $result_shop;
    var $result_country;
    var $country;
    var $shop;
    var $dives;
    var $dive_count;
    var $triplist;
    var $divetrip_data;
    var $request_type; // request_type = 0 overview, request_type = 1 details

    /**
     * Divetrip default constructor sets some defaults for this class
     * 
     * @access public
     * @return void
     */
    function __construct() {
        $a = func_get_args(); 
        $i = func_num_args(); 
        if ($i == 0) {
            global $_config;
            $this->multiuser = $_config['multiuser'];
        } else {
            if (method_exists($this,$f='__construct'.$i)) { 
                call_user_func_array(array($this,$f),$a); 
            } 
        }
    }

    /**
     * __construct1 construct a Divetrip class when divetrip ID is given
     * 
     * @param mixed $a1 
     * @access protected
     * @return void
     */
    function __construct1($a1) 
    { 
        global $_config;
        $this->divetrip_nr = $a1;
        $this->get_divetrip_info();
    } 

    function get_request_type() {
        return $this->request_type;
    }

    function set_divetrip_info($request) {
        // We need to extract the info from the request
        /*{{{*/
        if (!$request->diver_choice) {
            if ($request->get_view_request() == 1) {
                $this->request_type = 1;
                $this->divetrip_nr = $request->get_divetrip_nr();
            } else {
                $this->request_type = 0;
                $this->requested_page = $request->get_requested_page();
            }
            if ($this->multiuser) {
                $this->user_id = $request->get_user_id();
                $user = new User();
                $user->set_user_id($this->user_id);
                $this->table_prefix = $user->get_table_prefix();
            } else {
                $user = new User();
                $this->table_prefix = $user->get_table_prefix();
                $this->divetrip_nr = $request->get_divetrip_nr();
            }
        } else {
            $this->request_type = 3;
        }
    /*}}}*/
    }

    function get_divetrip_info() {
        global $globals, $_config; /*{{{*/
        if (!empty($this->divetrip_nr)) {
            $this->request_type = 1;
            $globals['tripid'] = $this->divetrip_nr;
            $this->result = parse_mysql_query('onetrip.sql');
        } else {
            /**
             * If the request type is not already set (by divers choice), set it to overview  
             */
            if ($this->request_type != 3) {
                $this->request_type = 0;
            }
        }
        return $this->result;
    /*}}}*/
    }

    function get_overview_divers() {
        global $t, $_lang, $globals, $_config; /*{{{*/
        $users = new Users();
        $user_list = $users->get_user_data();
        $t->assign('diver_overview',1);
        $t->assign('divers', $user_list);
        $t->assign('file_name','divetrip.php');
    /*}}}*/
    }

    /**
     * get_dives_on_trip 
     * 
     * @access public
     * @return void
     */
    function get_dives_on_trip() {
        global $globals, $_config; /*{{{*/
        // Get the dives on this trip from database
        $globals['tripid'] = $this->divetrip_nr;
        $this->dives = parse_mysql_query('tripdives.sql');
        $this->dive_count = $globals['sql_num_rows'];
        // Get the trip list from database
        $this->triplist = parse_mysql_query('triplist.sql');
    /*}}}*/
    }

    /**
     * set_main_divetrip_details 
     * 
     * @access public
     * @return void
     */
    function set_main_divetrip_details() {
        global $globals, $_config, $t, $_lang; /*{{{*/
        // $this->get_divetrip_location_details(); 
        // Show main trip details
        $result = $this->result;

        $t->assign('divetrip_id', $this->divetrip_nr);
        $t->assign('trip_name', $_lang['trip_name']);
        $t->assign('trip_shop', $_lang['trip_shop']);
        $t->assign('trip_country', $_lang['trip_country']);
        $t->assign('trip_startdate', $_lang['trip_startdate']);
        $t->assign('trip_enddate', $_lang['trip_enddate']);
        $t->assign('trip_rating', $_lang['trip_rating']);

        if (isset($result['TripName']) && ($result['TripName'] != "")) {
            $t->assign('TripName', $result['TripName']);
            $t->assign('pagetitle', $result['TripName'].$_lang['dive_trip_pagetitle']);
        } else {
            $t->assign('TripName','-');
            $t->assign('pagetitle', $this->divetrip_nr.$_lang['dive_trip_pagetitle']);
        }

        if (isset($result['Rating']) && ($result['Rating'] >= 0) && ($result['Rating'] <= 5)) {
            $desc = $_lang['rating'][$result['Rating']];
            $Rating = '<img src="'.$_config['web_root'].'/images/ratings5-'.$result['Rating'].'.gif" alt="'.$desc.'" title="'.$desc.'" border="0" width="84" height="15">';
            $t->assign('Rating', $Rating);
        } else {
            $t->assign('Rating','-');
        }

        if (isset($result['ShopName']) && ($result['ShopName'] != "")) {
            $t->assign('dive_shop_nr', $result['ShopID']);
            $t->assign('dive_shop_name', $result['ShopName']);
            $t->assign('logbook_shop_linktitle', $_lang['logbook_shop_linktitle']);
            if ($result['ShopType'] != '') {
                $t->assign('trip_shop', $result['ShopType'].':');
            }
        } else {
            $t->assign('dive_shop_nr', '');
            $t->assign('dive_shop_name','-');
        }

        if (isset($result['Country']) && ($result['Country'] != "")) {
            $t->assign('dive_country_nr', $result['CountryID']);
            $t->assign('dive_country_name', $result['Country']);
            $t->assign('logbook_country_linktitle', $_lang['logbook_country_linktitle']);
        } else {
            $t->assign('dive_shop_nr', '');
            $t->assign('dive_shop_name','-');
        }

        if (isset($result['StartDate']) && ($result['StartDate'] != "")) {
            $t->assign('StartDate', date($_lang['logbook_divedate_format'], strtotime($result['StartDate'])));
        } else {
            $t->assign('StartDate','-');
        }

        if (isset($result['EndDate']) && ($result['EndDate'] != "")) {
            $t->assign('EndDate', date($_lang['logbook_divedate_format'], strtotime($result['EndDate'])));
        } else {
            $t->assign('EndDate','-');
        }

        // Show the photo
        if (isset($result['PhotoPath']) && ($result['PhotoPath'] != "")) {
            $t->assign('PhotoPath', $result['PhotoPath']);
            $t->assign('trip_photo', $_lang['trip_photo']);
            $this->set_divetrip_pictures();
            $t->assign('PhotoPathurl', $_config['trippath_web'] . $result['PhotoPath']);
            $t->assign('trip_photo_linktitle', $_lang['trip_photo_linktitle']. $result['TripName']);
            $t->assign('trip_photo_link', $_lang['trip_photo_link'] );
        }

    /*}}}*/
    }

    /**
     * set_divetrip_pictures 
     * 
     * @access public
     * @return void
     */
    function set_divetrip_pictures() {
        global $_config, $t, $_lang, $globals; /*{{{*/
        $pic_class = new DivePictures;
        $pic_class->set_divegallery_info_direct($this->user_id);
        $pic_class->get_divegallery_info(0,0,0,0,$this->divetrip_nr);
        $divepics = $pic_class->get_image_link();
        $pics = count($divepics);
        if ($pics > 0) {
            if (isset($_config['divepics_preview'])) {
                $t->assign('pics2', '1');
                $t->assign('has_images', '1');
                $t->assign('image_link', $divepics);
            } else {
                /**
                 *  
                 */
                 $t->assign('has_images', '0');
            }
        } else {
                 $t->assign('has_images', '0');
        }
    /*}}}*/
    }

    /**
     * set_dives_on_trip 
     * 
     * @access public
     * @return void
     */
    function set_dives_on_trip() {
        global $globals, $_config, $t, $_lang; /*{{{*/
        $this->get_dives_on_trip();
        // Show trip dives if we have them
        if ($this->dive_count == 1) {
            $dives[0] = $this->dives;
        } else {
            $dives = $this->dives;
        }
        if ($this->dive_count != 0) {
            $t->assign('dive_count', $this->dive_count);
            if ($this->dive_count == 1) {
                $t->assign('trip_dive_trans', $_lang['trip_dive_single']);
            } else {
                $t->assign('trip_dive_trans', $_lang['trip_dive_plural']);
            }
            for ($i=0; $i<$this->dive_count; $i++) {
                $dives[$i] = $dives[$i]['Number'] ; 
            }
            $t->assign('dlog_number_title', $_lang['dlog_number_title'] );
            $t->assign('dives',$dives);
        } else {
            $t->assign('dive_count', $this->dive_count);
            $t->assign('dlog_number_title', "" );
            $t->assign('dives',"");
        }
        $t->assign('comma_separated', $_config['comma_separated']);
        $t->assign('comma_separator', $_config['comma_separator']);
    /*}}}*/
    }

    /**
     * set_buddy_details sets the buddy info
     * 
     * @access public
     * @return void
     */
    function set_buddy_details() {
        global $t, $_lang, $globals; /*{{{*/
        $result = $this->result;
        $t->assign('trip_buddy', $_lang['trip_buddy']);

        if (isset($result['BuddyIDs']) && ($result['BuddyIDs'] != "")) {
            // Get the names of the buddies on this trip
            $globals['buddies'] = $result['BuddyIDs'];
            $buddy = parse_mysql_query('buddies.sql');
            $buddycount = rows_mysql_query();
            if ($buddycount == 1) {
                // This works for one buddy
                $buddies[] = $buddy;
            } else {
                // This works for multiple buddies
                $buddies = $buddy;
            }

            $buddynames = '';
            for ($i=0; $i<$buddycount; $i++) {
                if (($i != '0') && (($buddies[$i]['FirstName'] != '') || ($buddies[$i]['LastName'] != ''))) {
                    $buddynames .= ', ';
                }
                if ($buddies[$i]['FirstName'] != '') {
                    $buddynames .= $buddies[$i]['FirstName'];
                    if ($buddies[$i]['LastName'] != '') {
                        $buddynames .= ' '.$buddies[$i]['LastName'];
                    } else {
                        $buddynames .= $buddies[$i]['LastName'];
                    }
                } else {
                    if ($buddies[$i]['LastName'] != '') {
                        $buddynames .= $buddies[$i]['LastName'];
                    }
                }
            }
            $t->assign('buddy', $buddynames);
        } else {
            $t->assign('buddy','-');
        }
    /*}}}*/
    }

    /**
     * set_divetrip_comments 
     * 
     * @access public
     * @return void
     */
    function set_divetrip_comments() {
        global $globals, $_config, $t, $_lang; /*{{{*/
        // Comments
        $result = $this->result;
        // Show them if we have them
        if (isset($result['Comments']) && ($result['Comments'] != "")) {
            $t->assign('trip_sect_comments', $_lang['trip_sect_comments']);
            $r = $result['Comments'];

            $r = htmlentities($r, ENT_QUOTES, "ISO-8859-1");
            $r = str_replace("\r\n", "\n", $r);
            $r = str_replace("\n", "<br>\n", $r);

            // Make any URLs clickable
            $r = make_clickable($r);

            $t->assign('Comments', $r);
        }
    /*}}}*/
    } 

    /**
     * trip_has_photo 
     * 
     * @param mixed $value 
     * @param mixed $row 
     * @access public
     * @return void
     */
    function trip_has_photo($value, $row) {
        global $_config, $_lang;
        if ($row['PhotoPath'] != '') {
            return '<img src="'.$_config['web_root'].'/images/photo_icon.gif" border="0" alt="'.$_lang['trip_photo_icontitle'].$row['TripName'].'" title="'.$_lang['trip_photo_icontitle'].$row['TripName'].'">';
        }
    }

    /**
     * get_divetrip_overview 
     * 
     * @access public
     * @return void
     */
    function get_divetrip_overview() {
        global $t, $_lang, $globals, $_config; /*{{{*/
        $triptable = $this->table_prefix."Trip";
        $sql = sql_file("triplist.sql");

// TODO: Count the dive trips and display a message if none
        $t->assign('dtrip_none', $_lang['dtrip_none']);

        /**
         * when view_type = 1 display the ajax grid if type = 2 display old fashioned table 
         */
        if ($_config['view_type'] == 1) {
            $this->get_divetrip_overview_grid($sql);
        }
        elseif ($_config['view_type'] == 2) {
            $this->get_divetrip_overview_table($sql);
        } else{
            echo '<strong>ERROR: No view_type defined!</strong><br>';
        }
        $t->assign('pagetitle',$_lang['dive_trips']);
    /*}}}*/
    }

    /**
     * get_divetrip_overview_table 
     * 
     * @access public
     * @return void
     */
    function get_divetrip_overview_table($sql) {
        global $db, $t, $_lang, $globals, $_config; /*{{{*/
        // Get the page header
        // Get the details of the trips to be listed
        $locationlist_query = $sql;
        $t->assign('dtrip_title_trip', $_lang['dtrip_title_trip']);
        $t->assign('dtrip_title_shop', $_lang['dtrip_title_shop']);
        $t->assign('dtrip_title_country', $_lang['dtrip_title_country']);
        $t->assign('dtrip_title_photo', $_lang['dtrip_title_photo']);
        $t->assign('logbook_trip_linktitle', $_lang['logbook_trip_linktitle'] );

        $t->assign('trip_photo_icontitle', $_lang['trip_photo_icontitle'] );

        if ($this->multiuser == 1) {
            $path = $_config['web_root'].'/divetrip.php/'.$this->user_id.'/list';
        } else {
            $path = $_config['web_root'].'/divetrip.php/list';
        }
        if (empty($this->requested_page)) {
            $cpage = 0;
        } else {
            $cpage = $this->requested_page;
        }

        $pager_options = new TablePager($cpage,$path);
        $paged_data = Pager_Wrapper_MDB2($db, $locationlist_query, $pager_options->options);

        $t->assign('pages', $paged_data['links']);
        $t->assign('cells', $paged_data['data']);
    /*}}}*/
    }

    /**
     * get_divetrip_overview_grid 
     * 
     * @param mixed $sql 
     * @access public
     * @return void
     */
    function get_divetrip_overview_grid($sql) {
        global $t, $_lang, $globals, $_config; /*{{{*/
        $data = parse_mysql_query(0,$sql);;
        $GridClass = new TableGrid($this->user_id,$data);
        $grid = $GridClass->get_grid_class();

        /**
         * Define the table according some info 
         */
        if ($this->multiuser) {
            $url = "/divetrip.php".$t->getTemplateVars('sep1').$this->user_id.$t->getTemplateVars('sep2');
        } else {
            $url = "/divetrip.php".$t->getTemplateVars('sep2');
        }

        $grid->showColumn('TripName', $_lang['dtrip_title_trip']);
        $grid->setColwidth('ShopName',"250");
        $grid->showColumn('ShopName', $_lang['dtrip_title_shop']);
        $grid->setColwidth('ShopName',"225");
        $grid->showColumn('Country', $_lang['dtrip_title_country']);
        $grid->setColwidth('Country',"125");
        $grid->setRowActionFunction("action");
        $grid->showCustomColumn("photo", $_lang['dtrip_title_photo']);
        $grid->setColwidth('photo',"25");

        $methodVariable = array($this, 'trip_has_photo'); 
        $grid->setCallbackFunction("photo", $methodVariable);

        $grid_ret = $grid->render(TRUE); 
        $t->assign('grid_display' ,1);
        $t->assign('grid',$grid_ret );
    /*}}}*/
    }
/*}}}*/
}


/**
 * Divecountry contains all functions for displaying the divecountry information
 * 
 * @package phpdivinglog
 * @version $Rev$
 * @copyright Copyright (C) 2011 Rob Lensen. All rights reserved.
 * @author Rob Lensen <rob@bsdfreaks.nl> 
 * @license LGPL v3 http://www.gnu.org/licenses/lgpl-3.0.txt
 */
class Divecountry{
    var $multiuser; /*{{{*/
    var $table_prefix;
    var $user_id;
    var $divecountry_nr;
    var $result;
    var $result_shop;
    var $result_country;
    var $country;
    var $cities;
    var $cities_count;
    var $shop;
    var $dives;
    var $dive_count;
    var $trips;
    var $trip_count;
    var $sites;
    var $site_count;
    var $triplist;
    var $divecountry_data;
    var $request_type; // request_type = 0 overview, request_type = 1 details

    /**
     * Divecountry default constructor sets some defaults for this class
     * 
     * @access public
     * @return void
     */
    function __construct() {
        $a = func_get_args(); 
        $i = func_num_args(); 
        if ($i == 0) {
            global $_config;
            $this->multiuser = $_config['multiuser'];
        } else {
            if (method_exists($this,$f='__construct'.$i)) { 
                call_user_func_array(array($this,$f),$a); 
            } 
        }
    }

    /**
     * __construct1 construct a Divecountry class when divecountry ID is given
     * 
     * @param mixed $a1 
     * @access protected
     * @return void
     */
    function __construct1($a1) 
    { 
        global $_config;
        $this->divecountry_nr = $a1;
        $this->get_divecountry_info();
    } 

    function get_request_type() {
        return $this->request_type;
    }

    function set_divecountry_info($request) {
        // We need to extract the info from the request
        /*{{{*/
        if (!$request->diver_choice) {
            if ($request->get_view_request() == 1) {
                $this->request_type = 1;
                $this->divecountry_nr = $request->get_divecountry_nr();
            } else {
                $this->request_type = 0;
                $this->requested_page = $request->get_requested_page();
            }
            if ($this->multiuser) {
                $this->user_id = $request->get_user_id();
                $user = new User();
                $user->set_user_id($this->user_id);
                $this->table_prefix = $user->get_table_prefix();
            } else {
                $user = new User();
                $this->table_prefix = $user->get_table_prefix();
                $this->divecountry_nr = $request->get_divecountry_nr();
            }
        } else {
            $this->request_type = 3;
        }
    /*}}}*/
    }

    function get_divecountry_info() {
        global $globals, $_config; /*{{{*/
        if (!empty($this->divecountry_nr)) {
            $this->request_type = 1;
            $globals['countryid'] = $this->divecountry_nr;
            $this->result = parse_mysql_query('onecountry.sql');
        } else {
            /**
             * If the request type is not already set (by divers choice), set it to overview  
             */
            if ($this->request_type != 3) {
                $this->request_type = 0;
            }
        }
        return $this->result;
    /*}}}*/
    }

    function get_overview_divers() {
        global $t, $_lang, $globals, $_config; /*{{{*/
        $users = new Users();
        $user_list = $users->get_user_data();
        $t->assign('diver_overview',1);
        $t->assign('divers', $user_list);
        $t->assign('file_name','divecountry.php');
    /*}}}*/
    }

    /**
     * get_trips_in_country 
     * 
     * @access public
     * @return void
     */
    function get_trips_in_country() {
        global $globals, $_config; /*{{{*/
        // Get the trips in this country from database
        $globals['countryid'] = $this->divecountry_nr;
        $this->trips = parse_mysql_query('countrytrips.sql');
        $this->trip_count = $globals['sql_num_rows'];
        // Get the divecountry list from database
        $this->countrylist = parse_mysql_query('countrylist.sql');
    /*}}}*/
    }

    /**
     * get_cities_in_country 
     * 
     * @access public
     * @return void
     */
    function get_cities_in_country() {
        global $globals, $_config; /*{{{*/
        // Get the cities in this country from database
        $globals['countryid'] = $this->divecountry_nr;
        $this->cities = parse_mysql_query('countrycities.sql');
        $this->cities_count = $globals['sql_num_rows'];
        // Get the divecountry list from database
        $this->countrylist = parse_mysql_query('countrylist.sql');
    /*}}}*/
    }

    /**
     * get_sites_in_country 
     * 
     * @access public
     * @return void
     */
    function get_sites_in_country() {
        global $globals, $_config; /*{{{*/
        // Get the sites in this country from database
        $globals['countryid'] = $this->divecountry_nr;
        $this->sites = parse_mysql_query('countrysites.sql');
        $this->site_count = $globals['sql_num_rows'];
        // Get the divecountry list from database
        $this->countrylist = parse_mysql_query('countrylist.sql');
    /*}}}*/
    }

    /**
     * get_dives_in_country 
     * 
     * @access public
     * @return void
     */
    function get_dives_in_country() {
        global $globals, $_config; /*{{{*/
        // Get the dives in this country from database
        $globals['countryid'] = $this->divecountry_nr;
        $this->dives = parse_mysql_query('countrydives.sql');
        $this->dive_count = $globals['sql_num_rows'];
        // Get the divecountry list from database
        $this->countrylist = parse_mysql_query('countrylist.sql');
    /*}}}*/
    }

    /**
     * set_main_divecountry_details 
     * 
     * @access public
     * @return void
     */
    function set_main_divecountry_details() {
        global $globals, $_config, $t, $_lang; /*{{{*/
        // $this->get_divecountry_location_details(); 
        // Show main country details
        $result = $this->result;

        $t->assign('pagetitle',$_lang['country_details_pagetitle'].$result['Country']);

        $t->assign('divecountry_id', $this->divecountry_nr);
        $t->assign('country_name', $_lang['country_name']);
        $t->assign('country_gmt', $_lang['country_gmt']);
        $t->assign('country_currency', $_lang['country_currency']);
        $t->assign('country_rate', $_lang['country_rate']);
        $t->assign('country_flag', $_lang['country_flag']);
        $t->assign('country_sect_activity', $_lang['country_sect_activity'].$result['Country']);

        if (isset($result['Country']) && ($result['Country'] != "")) {
            $t->assign('Country', $result['Country']);
        } else {
            $t->assign('Country','-');
        }

        if (isset($result['Gmt']) && ($result['Gmt'] != "")) {
            $Gmt = $_lang['country_gmt_show'];
            if ($result['Gmt'] > 0) {
                $v = abs($result['Gmt']);
                $h = floor($v/3600);
                $m = floor(($v - ($h * 3600)) / 60);
                $Gmt .= ' +'. sprintf('%02u',$h) . ':' . sprintf('%02u',$m);
            } elseif ($result['Gmt'] < 0) {
                $v = abs($result['Gmt']);
                $h = floor($v/3600);
                $m = floor(($v - ($h * 3600)) / 60);
                $Gmt .= ' -'. sprintf('%02u',$h) . ':' . sprintf('%02u',$m);
            }
            $t->assign('Gmt', $Gmt);
        } else {
            $t->assign('Gmt','-');
        }

        if (isset($result['Currency']) && ($result['Currency'] != "")) {
            $t->assign('Currency', $result['Currency']);
        } else {
            $t->assign('Currency','-');
        }

        if (isset($result['CurFactor']) && ($result['CurFactor'] != "")) {
            $t->assign('CurFactor', $result['CurFactor']);
        } else {
            $t->assign('CurFactor','-');
        }

        // Show the flag
        if (isset($result['FlagPath']) && ($result['FlagPath'] != "")) {
            $t->assign('FlagPath', $result['FlagPath']);
            $t->assign('FlagPathurl', $_config['flagpath_web'] . $result['FlagPath']);
            $t->assign('country_flag_linktitle', $_lang['country_flag_linktitle']. $result['Country']);
        } else {
            $t->assign('FlagPath','');
            $t->assign('FlagPathurl','');
            $t->assign('country_flag_linktitle','');
        }

    /*}}}*/
    }

    /**
     * set_trips_in_country 
     * 
     * @access public
     * @return void
     */
    function set_trips_in_country() {
        global $globals, $_config, $t, $_lang; /*{{{*/
        $this->get_trips_in_country();
        // Show country trips if we have them
        if ($this->trip_count == 1) {
            $trips[0] = $this->trips;
        } else {
            $trips = $this->trips;
        }
        if ($this->trip_count != 0) {
            $t->assign('trip_count', $this->trip_count);
            if ($this->trip_count == 1) {
                $t->assign('country_trip_trans', $_lang['country_trip_single']);
            } else {
                $t->assign('country_trip_trans', $_lang['country_trip_plural']);
            }
            $t->assign('dtrip_number_title', $_lang['dtrip_number_title'] );
            $t->assign('trips',$trips);
        } else {
            $t->assign('trip_count', $this->trip_count);
            $t->assign('dtrip_number_title', "" );
            $t->assign('trips',"");
        }
        $t->assign('comma_separated', $_config['comma_separated']);
        $t->assign('comma_separator', $_config['comma_separator']);
    /*}}}*/
    }

    /**
     * set_cities_in_country 
     * 
     * @access public
     * @return void
     */
    function set_cities_in_country() {
        global $globals, $_config, $t, $_lang; /*{{{*/
        $this->get_cities_in_country();
        // Show country cities if we have them
        if ($this->cities_count == 1) {
            $cities[0] = $this->cities;
        } else {
            $cities = $this->cities;
        }
        if ($this->cities_count != 0) {
            $t->assign('cities_count', $this->cities_count);
            if ($this->cities_count == 1) {
                $t->assign('country_cities_trans', $_lang['country_cities_single']);
            } else {
                $t->assign('country_cities_trans', $_lang['country_cities_plural']);
            }
            $t->assign('dcity_number_title', $_lang['dcity_number_title'] );
            $t->assign('cities',$cities);
        } else {
            $t->assign('cities_count', $this->cities_count);
            $t->assign('dcity_number_title', "" );
            $t->assign('cities',"");
        }
        $t->assign('comma_separated', $_config['comma_separated']);
        $t->assign('comma_separator', $_config['comma_separator']);
    /*}}}*/
    }

    /**
     * set_sites_in_country 
     * 
     * @access public
     * @return void
     */
    function set_sites_in_country() {
        global $globals, $_config, $t, $_lang; /*{{{*/
        $this->get_sites_in_country();
        // Show country sites if we have them
        if ($this->site_count == 1) {
            $sites[0] = $this->sites;
        } else {
            $sites = $this->sites;
        }
        if ($this->site_count != 0) {
            $t->assign('site_count', $this->site_count);
            if ($this->site_count == 1) {
                $t->assign('country_site_trans', $_lang['country_site_single']);
            } else {
                $t->assign('country_site_trans', $_lang['country_site_plural']);
            }
            $t->assign('dsite_number_title', $_lang['dsite_number_title'] );
            $t->assign('sites',$sites);
        } else {
            $t->assign('site_count', $this->site_count);
            $t->assign('dsite_number_title', "" );
            $t->assign('sites',"");
        }
        $t->assign('comma_separated', $_config['comma_separated']);
        $t->assign('comma_separator', $_config['comma_separator']);
    /*}}}*/
    }

    /**
     * set_dives_in_country 
     * 
     * @access public
     * @return void
     */
    function set_dives_in_country() {
        global $globals, $_config, $t, $_lang; /*{{{*/
        $this->get_dives_in_country();
        // Show country dives if we have them
        if ($this->dive_count == 1) {
            $dives[0] = $this->dives;
        } else {
            $dives = $this->dives;
        }
        if ($this->dive_count != 0) {
            $t->assign('dive_count', $this->dive_count);
            if ($this->dive_count == 1) {
                $t->assign('country_dive_trans', $_lang['country_dive_single']);
            } else {
                $t->assign('country_dive_trans', $_lang['country_dive_plural']);
            }
            for ($i=0; $i<$this->dive_count; $i++) {
                $dives[$i] = $dives[$i]['Number'] ; 
            }
            $t->assign('dlog_number_title', $_lang['dlog_number_title'] );
            $t->assign('dives',$dives);
        } else {
            $t->assign('dive_count', $this->dive_count);
            $t->assign('dlog_number_title', "" );
            $t->assign('dives',"");
        }
        $t->assign('comma_separated', $_config['comma_separated']);
        $t->assign('comma_separator', $_config['comma_separator']);
    /*}}}*/
    }

    /**
     * set_divecountry_comments 
     * 
     * @access public
     * @return void
     */
    function set_divecountry_comments() {
        global $globals, $_config, $t, $_lang; /*{{{*/
        // Comments
        $result = $this->result;
        // Show them if we have them
        if (isset($result['Comments']) && ($result['Comments'] != "")) {
            $t->assign('country_sect_comments', $_lang['country_sect_comments']);
            $r = $result['Comments'];

            $r = htmlentities($r, ENT_QUOTES, "ISO-8859-1");
            $r = str_replace("\r\n", "\n", $r);
            $r = str_replace("\n", "<br>\n", $r);

            // Make any URLs clickable
            $r = make_clickable($r);

            $t->assign('Comments', $r);
        }
    /*}}}*/
    } 

    /**
     * get_divecountry_overview 
     * 
     * @access public
     * @return void
     */
    function get_divecountry_overview() {
        global $t, $_lang, $globals, $_config; /*{{{*/
        $countrytable = $this->table_prefix."Country";
        $sql = sql_file("countrylist.sql");

// TODO: Count the countries and display a message if none
        $t->assign('country_none', $_lang['country_none']);

        /**
         * when view_type = 1 display the ajax grid if type = 2 display old fashioned table 
         */
        if ($_config['view_type'] == 1) {
            $this->get_divecountry_overview_grid($sql);
        }
        elseif ($_config['view_type'] == 2) {
            $this->get_divecountry_overview_table($sql);
        } else{
            echo '<strong>ERROR: No view_type defined!</strong><br>';
        }
        $t->assign('pagetitle',$_lang['dive_countries']);
    /*}}}*/
    }

    /**
     * get_divecountry_overview_table 
     * 
     * @access public
     * @return void
     */
    function get_divecountry_overview_table($sql) {
        global $db, $t, $_lang, $globals, $_config; /*{{{*/
        // Get the page header
        // Get the details of the countries to be listed
        $countrylist_query = $sql;
        $t->assign('country_title_country', $_lang['country_title_country']);
        $t->assign('country_title_count', $_lang['country_title_count']);
        $t->assign('logbook_country_linktitle', $_lang['logbook_country_linktitle']);

        if ($this->multiuser == 1) {
            $path = $_config['web_root'].'/divecountry.php/'.$this->user_id.'/list';
        } else {
            $path = $_config['web_root'].'/divecountry.php/list';
        }
        if (empty($this->requested_page)) {
            $cpage = 0;
        } else {
            $cpage = $this->requested_page;
        }

        $pager_options = new TablePager($cpage,$path);
        $paged_data = Pager_Wrapper_MDB2($db, $countrylist_query, $pager_options->options);

        $t->assign('pages', $paged_data['links']);
        $t->assign('cells', $paged_data['data']);
    /*}}}*/
    }

    /**
     * get_divecountry_overview_grid 
     * 
     * @param mixed $sql 
     * @access public
     * @return void
     */
    function get_divecountry_overview_grid($sql) {
        global $t, $_lang, $globals, $_config; /*{{{*/
        $data = parse_mysql_query(0,$sql);;
        $GridClass = new TableGrid($this->user_id,$data);
        $grid = $GridClass->get_grid_class();

        /**
         * Define the table according some info 
         */
        if ($this->multiuser) {
            $url = "/divecountry.php".$t->getTemplateVars('sep1').$this->user_id.$t->getTemplateVars('sep2');
        } else {
            $url = "/divecountry.php".$t->getTemplateVars('sep2');
        }

        $grid->showColumn('Country', $_lang['country_title_country']);
        $grid->setColwidth('Country',"575");
        $grid->showColumn('Dives', $_lang['country_title_count']);
        $grid->setColwidth('Dives',"50");
        $grid->setRowActionFunction("action");

        $grid_ret = $grid->render(TRUE); 
        $t->assign('grid_display' ,1);
        $t->assign('grid',$grid_ret );
    /*}}}*/
    }
/*}}}*/
}




/**
 * Divecity contains all functions for displaying the dive city information
 * 
 * @package phpdivinglog
 * @version $Rev$
 * @copyright Copyright (C) 2011 Rob Lensen. All rights reserved.
 * @author Rob Lensen <rob@bsdfreaks.nl> 
 * @license LGPL v3 http://www.gnu.org/licenses/lgpl-3.0.txt
 */
class Divecity{
    var $multiuser; /*{{{*/
    var $table_prefix;
    var $user_id;
    var $divecity_nr;
    var $result;
    var $result_sites;
    var $result_dives;
    var $city;
    var $city_count;
    var $sites;
    var $sites_count;
    var $dives;
    var $dives_count;
    var $citylist;
    var $divecity_data;
    var $request_type; // request_type = 0 overview, request_type = 1 details

    /**
     * Divecity default constructor sets some defaults for this class
     * 
     * @access public
     * @return void
     */
    function __construct() {
        $a = func_get_args(); 
        $i = func_num_args(); 
        if ($i == 0) {
            global $_config;
            $this->multiuser = $_config['multiuser'];
        } else {
            if (method_exists($this,$f='__construct'.$i)) { 
                call_user_func_array(array($this,$f),$a); 
            } 
        }
    }

    /**
     * __construct1 construct a Divecity class when divecity ID is given
     * 
     * @param mixed $a1 
     * @access protected
     * @return void
     */
    function __construct1($a1) 
    { 
        global $_config;
        $this->divecity_nr = $a1;
        $this->get_divecity_info();
    } 

    function get_request_type() {
        return $this->request_type;
    }

    function set_divecity_info($request) {
        // We need to extract the info from the request
        /*{{{*/
        if (!$request->diver_choice) {
            if ($request->get_view_request() == 1) {
                $this->request_type = 1;
                $this->divecity_nr = $request->get_divecity_nr();
            } else {
                $this->request_type = 0;
                $this->requested_page = $request->get_requested_page();
            }
            if ($this->multiuser) {
                $this->user_id = $request->get_user_id();
                $user = new User();
                $user->set_user_id($this->user_id);
                $this->table_prefix = $user->get_table_prefix();
            } else {
                $user = new User();
                $this->table_prefix = $user->get_table_prefix();
                $this->divecity_nr = $request->get_divecity_nr();
            }
        } else {
            $this->request_type = 3;
        }
    /*}}}*/
    }

    function get_divecity_info() {
        global $globals, $_config; /*{{{*/
        if (!empty($this->divecity_nr)) {
            $this->request_type = 1;
            $globals['cityid'] = $this->divecity_nr;
            $this->result = parse_mysql_query('onecity.sql');
        } else {
            /**
             * If the request type is not already set (by divers choice), set it to overview  
             */
            if ($this->request_type != 3) {
                $this->request_type = 0;
            }
        }
        return $this->result;
    /*}}}*/
    }

    function get_overview_divers() {
        global $t, $_lang, $globals, $_config; /*{{{*/
        $users = new Users();
        $user_list = $users->get_user_data();
        $t->assign('diver_overview',1);
        $t->assign('divers', $user_list);
        $t->assign('file_name','divecity.php');
    /*}}}*/
    }

    /**
     * get_sites_in_city 
     * 
     * @access public
     * @return void
     */
    function get_sites_in_city() {
        global $globals, $_config; /*{{{*/
        // Get the sites in this city from database
        $globals['cityid'] = $this->divecity_nr;
        $this->sites = parse_mysql_query('citysites.sql');
        $this->site_count = $globals['sql_num_rows'];
        // Get the divecity list from database
        $this->citylist = parse_mysql_query('citylist.sql');
    /*}}}*/
    }

    /**
     * get_dives_in_city 
     * 
     * @access public
     * @return void
     */
    function get_dives_in_city() {
        global $globals, $_config; /*{{{*/
        // Get the dives in this city from database
        $globals['cityid'] = $this->divecity_nr;
        $this->dives = parse_mysql_query('citydives.sql');
        $this->dive_count = $globals['sql_num_rows'];
        // Get the divecity list from database
        $this->citylist = parse_mysql_query('citylist.sql');
    /*}}}*/
    }

    /**
     * set_main_divecity_details 
     * 
     * @access public
     * @return void
     */
    function set_main_divecity_details() {
        global $globals, $_config, $t, $_lang; /*{{{*/
        // $this->get_divecity_location_details(); 
        // Show main city details
        $result = $this->result;

        $t->assign('divecity_id', $this->divecity_nr);
        $t->assign('city_name', $_lang['city_name']);
        $t->assign('city_type', $_lang['city_type']);
        $t->assign('city_country', $_lang['city_country']);
        $t->assign('city_map', $_lang['city_map']);
        $t->assign('city_sect_activity', $_lang['city_sect_activity'].$result['City']);

        if (isset($result['City']) && ($result['City'] != "")) {
            $t->assign('City', $result['City']);
        } else {
            $t->assign('City','-');
        }

        if (isset($result['Type']) && ($result['Type'] >= 0) && ($result['Type'] <= 5)) {
            $Type = $_lang['citytype'][$result['Type']];
            $t->assign('Type', $Type);
            $t->assign('pagetitle',$result['City'].' - '.$Type);
        } else {
            $t->assign('Type','-');
            $t->assign('pagetitle',$_lang['city_details_pagetitle'].$result['City']);
        }

        // Show the map
        if (isset($result['MapPath']) && ($result['MapPath'] != "")) {
            $t->assign('MapPath', $result['MapPath']);
            $t->assign('MapPathurl', $_config['mappath_web'] . $result['MapPath']);
            $t->assign('city_map_linktitle', $_lang['city_map_linktitle'] . $result['City']);
        } else {
            $t->assign('MapPath','');
            $t->assign('MapPathurl','');
            $t->assign('city_map_linktitle','');
        }

    /*}}}*/
    }

    /**
     * set_sites_in_city 
     * 
     * @access public
     * @return void
     */
    function set_sites_in_city() {
        global $globals, $_config, $t, $_lang; /*{{{*/
        $this->get_sites_in_city();
        // Show city sites if we have them
        if ($this->site_count == 1) {
            $sites[0] = $this->sites;
        } else {
            $sites = $this->sites;
        }
        if ($this->site_count != 0) {
            $t->assign('site_count', $this->site_count);
            if ($this->site_count == 1) {
                $t->assign('city_site_trans', $_lang['city_site_single']);
            } else {
                $t->assign('city_site_trans', $_lang['city_site_plural']);
            }
            $t->assign('dcity_number_title', $_lang['dcity_number_title'] );
            $t->assign('sites',$sites);
        } else {
            $t->assign('site_count', $this->site_count);
            $t->assign('dcity_number_title', "" );
            $t->assign('sites',"");
        }
        $t->assign('comma_separated', $_config['comma_separated']);
        $t->assign('comma_separator', $_config['comma_separator']);
    /*}}}*/
    }

    /**
     * set_dives_in_city 
     * 
     * @access public
     * @return void
     */
    function set_dives_in_city() {
        global $globals, $_config, $t, $_lang; /*{{{*/
        $this->get_dives_in_city();
        // Show city dives if we have them
        if ($this->dive_count == 1) {
            $dives[0] = $this->dives;
        } else {
            $dives = $this->dives;
        }
        if ($this->dive_count != 0) {
            $t->assign('dive_count', $this->dive_count);
            if ($this->dive_count == 1) {
                $t->assign('city_dive_trans', $_lang['city_dive_single']);
            } else {
                $t->assign('city_dive_trans', $_lang['city_dive_plural']);
            }
            for ($i=0; $i<$this->dive_count; $i++) {
                $dives[$i] = $dives[$i]['Number'] ; 
            }
            $t->assign('dlog_number_title', $_lang['dlog_number_title']);
            $t->assign('dives',$dives);
        } else {
            $t->assign('dive_count', $this->dive_count);
            $t->assign('dlog_number_title', "");
            $t->assign('dives',"");
        }
        $t->assign('comma_separated', $_config['comma_separated']);
        $t->assign('comma_separator', $_config['comma_separator']);
    /*}}}*/
    }

    /**
     * set_divecity_comments 
     * 
     * @access public
     * @return void
     */
    function set_divecity_comments() {
        global $globals, $_config, $t, $_lang; /*{{{*/
        // Comments
        $result = $this->result;
        // Show them if we have them
        if (isset($result['Comments']) && ($result['Comments'] != "")) {
            $t->assign('city_sect_comments', $_lang['city_sect_comments']);
            $r = $result['Comments'];

            $r = htmlentities($r, ENT_QUOTES, "ISO-8859-1");
            $r = str_replace("\r\n", "\n", $r);
            $r = str_replace("\n", "<br>\n", $r);

            // Make any URLs clickable
            $r = make_clickable($r);

            $t->assign('Comments', $r);
        }
    /*}}}*/
    } 

    /**
     * city_type_convert 
     * 
     * @param mixed $value 
     * @param mixed $row 
     * @access public
     * @return void
     */
    function city_type_convert($value, $row) {
        global $_config, $_lang;
        if (($row['Type'] != '') && ($row['Type'] >= 0) && ($row['Type'] <= 5)) {
            return $_lang['citytype'][$row['Type']];
        } else {
            return '-';
        }
    }

    /**
     * get_divecity_overview 
     * 
     * @access public
     * @return void
     */
    function get_divecity_overview() {
        global $t, $_lang, $globals, $_config; /*{{{*/
        $citytable = $this->table_prefix."City";
        $sql = sql_file("citylist.sql");

// TODO: Count the cities and display a message if none
        $t->assign('city_none', $_lang['city_none']);

        /**
         * when view_type = 1 display the ajax grid if type = 2 display old fashioned table 
         */
        if ($_config['view_type'] == 1) {
            $this->get_divecity_overview_grid($sql);
        }
        elseif ($_config['view_type'] == 2) {
            $this->get_divecity_overview_table($sql);
        } else{
            echo '<strong>ERROR: No view_type defined!</strong><br>';
        }
        $t->assign('pagetitle',$_lang['dive_cities']);
    /*}}}*/
    }

    /**
     * get_divecity_overview_table 
     * 
     * @access public
     * @return void
     */
    function get_divecity_overview_table($sql) {
        global $db, $t, $_lang, $globals, $_config; /*{{{*/
        // Get the page header
        // Get the details of the cities to be listed
        $citylist_query = $sql;
        $t->assign('city_title_city', $_lang['city_title_city']);
        $t->assign('city_title_type', $_lang['city_title_type']);
        $t->assign('city_title_country', $_lang['city_title_country']);
        $t->assign('city_title_dives', $_lang['city_title_count']);
        $t->assign('logbook_city_linktitle', $_lang['logbook_city_linktitle'] );

        if ($this->multiuser == 1) {
            $path = $_config['web_root'].'/divecity.php/'.$this->user_id.'/list';
        } else {
            $path = $_config['web_root'].'/divecity.php/list';
        }
        if (empty($this->requested_page)) {
            $cpage = 0;
        } else {
            $cpage = $this->requested_page;
        }

        $pager_options = new TablePager($cpage,$path);
        $paged_data = Pager_Wrapper_MDB2($db, $citylist_query, $pager_options->options);

        $t->assign('pages', $paged_data['links']);
        $t->assign('cells', $paged_data['data']);
        $t->assign('citytypes', $_lang['citytype']);
    /*}}}*/
    }

    /**
     * get_divecity_overview_grid 
     * 
     * @param mixed $sql 
     * @access public
     * @return void
     */
    function get_divecity_overview_grid($sql) {
        global $t, $_lang, $globals, $_config; /*{{{*/
        $data = parse_mysql_query(0,$sql);
        $GridClass = new TableGrid($this->user_id,$data);
        $grid = $GridClass->get_grid_class();
        /**
         * Define the table according some info 
         */
        if ($this->multiuser) {
            $url = "/divecity.php".$t->getTemplateVars('sep1').$this->user_id.$t->getTemplateVars('sep2');
        } else {
            $url = "/divecity.php".$t->getTemplateVars('sep2');
        }

        $grid->showColumn('City', $_lang['city_title_city']);
        $grid->setColwidth('City',"290");
        $grid->showCustomColumn("type", $_lang['city_title_type']);
        $grid->setColwidth('Type',"100");
        $grid->showColumn('Country', $_lang['city_title_country']);
        $grid->setColwidth('Country',"195");
        $grid->showColumn('Dives', $_lang['city_title_count']);
        $grid->setColwidth('Dives',"40");
        $grid->setRowActionFunction("action");

        $methodVariable = array($this, 'city_type_convert'); 
        $grid->setCallbackFunction("type", $methodVariable);

        $grid_ret = $grid->render(TRUE); 
        $t->assign('grid_display' ,1);
        $t->assign('grid',$grid_ret );
    /*}}}*/
    }
/*}}}*/
}


/**
 * Divestats contains all functions for displaying all divestatistics
 * 
 * @package phpdivinglog
 * @copyright Copyright (C) 2007 Rob Lensen. All rights reserved.
 * @author Rob Lensen <rob@bsdfreaks.nl> 
 * @license LGPL v3 http://www.gnu.org/licenses/lgpl-3.0.txt
 */
class Divestats{
    var $multiuser; /*{{{*/
    var $user_id;
    var $username;
    var $result;
    var $table_prefix;
    var $divestats;
    var $request_type;
    var $shoredives;
    var $boatdives;
    var $nightdives;
    var $driftdives;
    var $deepdives;
    var $cavedives;
    var $wreckdives;
    var $photodives;
    var $saltwaterdives;
    var $freshwaterdives;
    var $brackishdives;
    var $decodives;
    var $nodecodives;
    var $repdives;
    var $norepdives;
    var $singletankdives;
    var $doubletankdives;
    var $DivedateMinNr;
    var $DivedateMaxNr;
    var $DivetimeMinNr;
    var $DivetimeMaxNr;
    var $DepthMinNr;
    var $DepthMaxNr;
    var $WatertempMinNr;
    var $WatertempMaxNr;
    var $AirempMinNr;
    var $AirtempMaxNr;
    var $depthrange;
    var $depthrange1_per;
    var $depthrange2_per;
    var $depthrange3_per;
    var $depthrange4_per;
    var $depthrange5_per;
    var $end;
    var $divecert;
    var $number_cert;
    var $LastEntryTime;
    var $LastDivePlace;
    var $LastDiveID;
    var $LastCity;
    var $LastCountry;

   /**
     * Divestats default constructor 
     * 
     * @access public
     * @return void
     */
    function Divestats() {
        global $_config;
        $this->multiuser = $_config['multiuser'];
    }

    /**
     * set_divestats_info 
     * 
     * @param mixed $request 
     * @access public
     * @return void
     */
    function set_divestats_info($request) {
        // We need to extract the info from the request 
        /*{{{*/
        if (!$request->diver_choice) {
            if ($this->multiuser) {
                $this->user_id = $request->get_user_id();
                $user = new User();
                $user->set_user_id($this->user_id);
                $this->table_prefix = $user->get_table_prefix();
                $this->username = $user->get_username();
            } else {
                $this->user_id = $request->get_user_id();
                $user = new User();
                // $user->set_user_id($this->user_id);
                $this->table_prefix = $user->get_table_prefix();
                $this->username = $user->get_username();
            }
        } else {
            $this->request_type = 3;
        }
    /*}}}*/
    }

    /**
     * get_divestats_info 
     * 
     * @access public
     * @return void
     */
    function get_divestats_info() {
        global $globals, $_config; /*{{{*/
        if (($this->multiuser && !empty($this->user_id)) || !$this->multiuser ) {
            // Get number of dives
            $count = parse_mysql_query('divecount.sql');
            $this->end = $count['COUNT(*)'];

            $this->divestats = parse_mysql_query('divestats.sql');
            $divestats = $this->divestats;
            // Get the number of shore dives
            $globals['stats'] = "Entry = 1";
            $divestatsother = parse_mysql_query('divestatsother.sql');
            $this->shoredives = $divestatsother['Count'];

            // Get the number of boat dives
            $globals['stats'] = "Entry = 2";
            $divestatsother = parse_mysql_query('divestatsother.sql');
            $this->boatdives = $divestatsother['Count'];

            // Get the number of night dives
            $globals['stats'] = "Divetype = '3' OR Divetype LIKE '%,3' OR Divetype LIKE '%,3,%' OR Divetype LIKE '3,%'";
            $divestatsother = parse_mysql_query('divestatsother.sql');
            $this->nightdives = $divestatsother['Count'];

            // Get the number of drift dives
            $globals['stats'] = "Divetype = '4' OR Divetype LIKE '%,4' OR Divetype LIKE '%,4,%' OR Divetype LIKE '4,%'";
            $divestatsother = parse_mysql_query('divestatsother.sql');
            $this->driftdives = $divestatsother['Count'];

            // Get the number of deep dives
            $globals['stats'] = "Divetype = '5' OR Divetype LIKE '%,5' OR Divetype LIKE '%,5,%' OR Divetype LIKE '5,%'";
            $divestatsother = parse_mysql_query('divestatsother.sql');
            $this->deepdives = $divestatsother['Count'];

            // Get the number of cave dives
            $globals['stats'] = "Divetype = '6' OR Divetype LIKE '%,6' OR Divetype LIKE '%,6,%' OR Divetype LIKE '6,%'";
            $divestatsother = parse_mysql_query('divestatsother.sql');
            $this->cavedives = $divestatsother['Count'];

            // Get the number of wreck dives
            $globals['stats'] = "Divetype = '7' OR Divetype LIKE '%,7' OR Divetype LIKE '%,7,%' OR Divetype LIKE '7,%'";
            $divestatsother = parse_mysql_query('divestatsother.sql');
            $this->wreckdives = $divestatsother['Count'];

            // Get the number of photo dives
            $globals['stats'] = "Divetype = '8' OR Divetype LIKE '%,8' OR Divetype LIKE '%,8,%' OR Divetype LIKE '8,%'";
            $divestatsother = parse_mysql_query('divestatsother.sql');
            $this->photodives = $divestatsother['Count'];

            // Get the number of saltwater dives
            $globals['stats'] = "Water = '1'";
            $divestatsother = parse_mysql_query('divestatsother.sql');
            $this->saltwaterdives = $divestatsother['Count'];

            // Get the number of freshwater dives
            $globals['stats'] = "Water = '2'";
            $divestatsother = parse_mysql_query('divestatsother.sql');
            $this->freshwaterdives = $divestatsother['Count'];

            // Get the number of brackish dives
            $globals['stats'] = "Water = 3";
            $divestatsother = parse_mysql_query('divestatsother.sql');
            $this->brackishdives = $divestatsother['Count'];

            // Get the number of deco dives
            $globals['stats'] = "Deco = 'True'";
            $divestatsother = parse_mysql_query('divestatsother.sql');
            $this->decodives = $divestatsother['Count'];
            $this->nodecodives = $this->end - $this->decodives;

            // Get the number of rep dives
            $globals['stats'] = "Rep = 'True'";
            $divestatsother = parse_mysql_query('divestatsother.sql');
            $this->repdives = $divestatsother['Count'];
            $this->norepdives = $this->end - $this->repdives;

            // Get the number of double tank dives
            $globals['stats'] = "DblTank = 'True'";
            $divestatsother = parse_mysql_query('divestatsother.sql');
            $this->doubletankdives = $divestatsother['Count'];

            // Get the number of single tank dives
            $globals['stats'] = "DblTank = 'False'";
            $divestatsother = parse_mysql_query('divestatsother.sql');
            $this->singletankdives = $divestatsother['Count'];

            // Get the number of OC dives
            $globals['stats'] = "SupplyType = '0'";
            $divestatsother = parse_mysql_query('divestatsother.sql');
            $this->ocdives = $divestatsother['Count'];

            // Get the number of SCR dives
            $globals['stats'] = "SupplyType = '1'";
            $divestatsother = parse_mysql_query('divestatsother.sql');
            $this->scrdives = $divestatsother['Count'];

            // Get the number of CCR dives
            $globals['stats'] = "SupplyType = 2";
            $divestatsother = parse_mysql_query('divestatsother.sql');
            $this->ccrdives = $divestatsother['Count'];

            // Get dive number for first dive
            $globals['stats'] = "Divedate = '" . $divestats['DivedateMin'] . "'";
            $divestatsnr = parse_mysql_query('divestatsnr.sql');
            // Check the number of dives on the first day of diving, if more than 1 get the first
            if ($globals['sql_num_rows'] > 1) {
                $this->DivedateMinNr = $divestatsnr[0]['Number'];
            } else {
                $this->DivedateMinNr = $divestatsnr['Number'];
            }

            // Get dive number for last dive
            $globals['stats'] = "Divedate = '" . $divestats['DivedateMax'] . "'";
            $divestatsnr = parse_mysql_query('divestatsnr.sql');
            // Some principle as first dive
            if ($globals['sql_num_rows'] > 1) {
                $this->DivedateMaxNr = $divestatsnr[count($divestatsnr)-1]['Number'];
            } else {
                $this->DivedateMaxNr = $divestatsnr['Number'];
            }

            // Get dive number for sortest dive
            $globals['stats'] = "Divetime = '" . $divestats['DivetimeMin'] . "'";
            $divestatsnr = parse_mysql_query('divestatsnr.sql');
            if ($globals['sql_num_rows'] > 1) {
                $this->DivetimeMinNr = $divestatsnr[0]['Number'];
            } else {
                $this->DivetimeMinNr = $divestatsnr['Number'];
            }
            // Get dive number for deepest dive
            $globals['stats'] = "Divetime = '" . $divestats['DivetimeMax'] . "'";
            $divestatsnr = parse_mysql_query('divestatsnr.sql');
            if ($globals['sql_num_rows'] > 1) {
                $this->DivetimeMaxNr = $divestatsnr[0]['Number'];
            } else {
                $this->DivetimeMaxNr = $divestatsnr['Number'];
            }
            // Get dive number for shallowest dive
            $globals['stats'] = "Depth = '" . $divestats['DepthMin'] . "'";
            $divestatsnr = parse_mysql_query('divestatsnr.sql');
            if ($globals['sql_num_rows'] > 1) {
                $this->DepthMinNr = $divestatsnr[0]['Number'];
            } else {
                $this->DepthMinNr = $divestatsnr['Number'];
            }
            // Get dive number for deepest dive
            $globals['stats'] = "Depth = '" . $divestats['DepthMax'] . "'";
            $divestatsnr = parse_mysql_query('divestatsnr.sql');
            if ($globals['sql_num_rows'] > 1) {
                $this->DepthMaxNr = $divestatsnr[0]['Number'];
            } else {
                $this->DepthMaxNr = $divestatsnr['Number'];
            }
            // Get dive number for coldest water dive
            $globals['stats'] = "Watertemp = '" . $divestats['WatertempMin'] . "'";
            $divestatsnr = parse_mysql_query('divestatsnr.sql');
            if ($globals['sql_num_rows'] > 1) {
                $this->WatertempMinNr = $divestatsnr[0]['Number'];
            } else {
                $this->WatertempMinNr = $divestatsnr['Number'];
            }
            // Get dive number for warmest water dive
            $globals['stats'] = "Watertemp = '" . $divestats['WatertempMax'] . "'";
            $divestatsnr = parse_mysql_query('divestatsnr.sql');
            if ($globals['sql_num_rows'] > 1) {
                $this->WatertempMaxNr = $divestatsnr[0]['Number'];
            } else {
                $this->WatertempMaxNr = $divestatsnr['Number'];
            }
            // Get dive number for coldest air dive
            $globals['stats'] = "Airtemp = '" . $divestats['AirtempMin'] . "'";
            $divestatsnr = parse_mysql_query('divestatsnr.sql');
            if ($globals['sql_num_rows'] > 1) {
                $this->AirtempMinNr = $divestatsnr[0]['Number'];
            } else {
                $this->AirtempMinNr = $divestatsnr['Number'];
            }
            // Get dive number for warmest air dive
            $globals['stats'] = "Airtemp = '" . $divestats['AirtempMax'] . "'";
            $divestatsnr = parse_mysql_query('divestatsnr.sql');
            if ($globals['sql_num_rows'] > 1) {
                $this->AirtempMaxNr = $divestatsnr[0]['Number'];
            } else {
                $this->AirtempMaxNr = $divestatsnr['Number'];
            }
            // Get the number of 1st depth range dives
            $globals['stats'] = "Depth <= 18";
            $divestatsother = parse_mysql_query('divestatsother.sql');
            $this->depthrange[0] = $divestatsother['Count'];
            $this->depthrange1_per = round(($this->depthrange[0] / $this->end) * 100);

            // Get the number of 2nd depth range dives
            $globals['stats'] = "Depth > 18 AND Depth <= 30";
            $divestatsother = parse_mysql_query('divestatsother.sql');
            $this->depthrange[1] = $divestatsother['Count'];
            $this->depthrange2_per =  round(($this->depthrange[1] / $this->end) * 100);

            // Get the number of 3rd depth range dives
            $globals['stats'] = "Depth > 30 AND Depth <= 40";
            $divestatsother = parse_mysql_query('divestatsother.sql');
            $this->depthrange[2] = $divestatsother['Count'];
            $this->depthrange3_per =  round(($this->depthrange[2] / $this->end) * 100);

            // Get the number of 4th depth range dives
            $globals['stats'] = "Depth > 40 AND Depth <= 55";
            $divestatsother = parse_mysql_query('divestatsother.sql');
            $this->depthrange[3] = $divestatsother['Count'];
            $this->depthrange4_per =  round(($this->depthrange[3] / $this->end) * 100);

            // Get the number of 5th depth range dives
            $globals['stats'] = "Depth > 55";
            $divestatsother = parse_mysql_query('divestatsother.sql');
            $this->depthrange[4] = $divestatsother['Count'];
            $this->depthrange5_per = round(($this->depthrange[4] / $this->end) * 100);

            $this->divecert = parse_mysql_query('brevetlist.sql');
            $this->number_cert = $globals['sql_num_rows'];

        } else {
            /**
             * If the request type is not already set (by divers choice), set it to overview  
             */
            if ($this->request_type != 3) {
                $this->request_type = 0;
            }
        }
    /*}}}*/
    }

    /**
     * get_lastdive_info 
     * 
     * @access public
     * @return void
     */
    function get_lastdive_info() {
       global $globals, $_config; /*{{{*/
        if (($this->multiuser && !empty($this->user_id)) || !$this->multiuser ) {
            $lastdive = parse_mysql_query('lastdive.sql');
            $this->LastEntryTime = $lastdive['Entrytime'];
            $this->LastDivePlace = $lastdive['Place'];
            $this->LastDiveID= $lastdive['PlaceID'];
            $this->LastCity = $lastdive['City'];
            $this->LastCityID = $lastdive['CityID'];
            $this->LastCountry = $lastdive['Country'];
            $this->LastCountryID = $lastdive['CountryID'];
        }
    /*}}}*/
    }
    /**
     * set_all_statistics 
     * 
     * @access public
     * @return void
     */
    function set_all_statistics() {
        $this->set_dive_statistics(); /*{{{*/
        $this->set_dive_certifications(); 
    /*}}}*/
    }

    /**
     * get_overview_divers 
     * 
     * @access public
     * @return void
     */
    function get_overview_divers() {
        global $t, $_lang, $globals, $_config; /*{{{*/
        $users = new Users();
        $user_list = $users->get_user_data();
        $t->assign('diver_overview',1);
        $t->assign('divers', $user_list);
        $t->assign('file_name','divestats.php'); 
    /*}}}*/
    }

    /**
     * set_dive_statistics 
     * 
     * @access public
     * @return void
     */
    function set_dive_statistics() {
        global $globals, $_config, $t, $_lang; /*{{{*/
        $divestats = $this->divestats; 

        // Dive Log, Dive Sites, Dive Statistics
        $t->assign('dive_log_linktitle', $_lang['dive_log_linktitle']);
        $t->assign('dive_log', $_lang['dive_log']);
        $t->assign('dive_sites_linktitle', $_lang['dive_sites_linktitle']);
        $t->assign('dive_sites',$_lang['dive_sites']);
        $t->assign('dive_equip_linktitle', $_lang['dive_equip_linktitle']);
        $t->assign('dive_equip',$_lang['dive_equip']);
        $t->assign('dive_stats_linktitle', $_lang['dive_stats_linktitle']);
        $t->assign('dive_stats', $_lang['dive_stats']);
        $t->assign('stats_sect_stats', $this->username ."'s " .$_lang['stats_sect_stats']);
 
        $t->assign('dive_tab_stats', $_lang['dive_tab_stats']);
        $t->assign('dive_tab_certs', $_lang['dive_tab_certs']);
     
        // Show overall details
        $t->assign('stats_totaldives', $_lang['stats_totaldives'] );
        $t->assign('stats_divedatemax',$_lang['stats_divedatemax']);
        $t->assign('stats_divedatemin',$_lang['stats_divedatemin'] );
        $t->assign('end',$this->end );
        $t->assign('DivedateMax', date($_lang['logbook_divedate_format'], strtotime($divestats['DivedateMax'])));
        $t->assign('DivedateMaxNr',$this->DivedateMaxNr);
        $t->assign('dlog_number_title',$_lang['dlog_number_title']);
        $t->assign('dsite_number_title',$_lang['dsite_number_title']);
        $t->assign('dcountry_number_title',$_lang['dcountry_number_title']);
        $t->assign('dcity_number_title',$_lang['dcity_number_title']);
        $t->assign('DivedateMin', date($_lang['logbook_divedate_format'], strtotime($divestats['DivedateMin'])));
        $t->assign('DivedateMinNr', $this->DivedateMinNr);

        // Show dive length details
        $t->assign('stats_totaltime', $_lang['stats_totaltime']);
        $t->assign('stats_divetimemax', $_lang['stats_divetimemax'] );
        $t->assign('stats_divetimemin', $_lang['stats_divetimemin'] );
        $t->assign('stats_divetimeavg', $_lang['stats_divetimeavg'] );

        $total_abt = floor($divestats['BottomTime']/60) .":". ($divestats['BottomTime']%60) ." ". $_lang['stats_totaltime_units'];
        $t->assign('total_abt',$total_abt );
        $t->assign('DivetimeMax', $divestats['DivetimeMax']);
        $t->assign('unit_time', $_lang['unit_time']);
        $t->assign('DivetimeMaxNr', $this->DivetimeMaxNr );
        $t->assign('DivetimeMin', $divestats['DivetimeMin']);
        $t->assign('DivetimeMinNr', $this->DivetimeMinNr);
        $t->assign('DivetimeAvg', round($divestats['DivetimeAvg'],0) );

        // Show dive depth details
        $t->assign('stats_depthmax', $_lang['stats_depthmax'] );
        $t->assign('stats_depthmin', $_lang['stats_depthmin'] );
        $t->assign('stats_depthavg', $_lang['stats_depthavg'] );

        if ($_config['length']) {
            $DepthMax = MetreToFeet($divestats['DepthMax'], 0) ."&nbsp;". $_lang['unit_length_short_imp'];
        } else {
            $DepthMax =  $divestats['DepthMax'] ."&nbsp;". $_lang['unit_length'];
        }
        $t->assign('DepthMax', $DepthMax);
        $t->assign('DepthMaxNr', $this->DepthMaxNr);

        if ($_config['length']) {
            $DepthMin =  MetreToFeet($divestats['DepthMin'], 0) ."&nbsp;". $_lang['unit_length_short_imp'];
        } else {
            $DepthMin =  $divestats['DepthMin'] ."&nbsp;". $_lang['unit_length'];
        }
        $t->assign('DepthMin',$DepthMin);
        $t->assign('DepthMinNr', $this->DepthMinNr );

        if ($_config['length']) {
            $DepthAvg =  MetreToFeet($divestats['DepthAvg'], 0) ."&nbsp;". $_lang['unit_length_short_imp'];
        } else {
            $DepthAvg =  round($divestats['DepthAvg'], 1) ."&nbsp;". $_lang['unit_length'];
        }
        $t->assign('DepthAvg', $DepthAvg);

        // Show dive depth table
        $t->assign('stats_depth1m',  $_config['length'] ? $_lang['stats_depth1i'] : $_lang['stats_depth1m']);
        $t->assign('depthrange1',$this->depthrange );
        $t->assign('depthrange1_per' , $this->depthrange1_per);

        $t->assign('stats_depth2m',  $_config['length'] ? $_lang['stats_depth2i'] : $_lang['stats_depth2m']);
        $t->assign('depthrange2',  $this->depthrange[1]  );
        $t->assign('depthrange2_per', $this->depthrange2_per);

        $t->assign('stats_depth3m',  $_config['length'] ? $_lang['stats_depth3i'] : $_lang['stats_depth3m']) ;
        $t->assign('depthrange3', $this->depthrange[2]);
        $t->assign('depthrange3_per', $this->depthrange3_per);

        $t->assign('stats_depth4m',  $_config['length'] ? $_lang['stats_depth4i'] : $_lang['stats_depth4m']);
        $t->assign('depthrange4', $this->depthrange[3]);
        $t->assign('depthrange4_per', $this->depthrange4_per );

        $t->assign('stats_depth5m',  $_config['length'] ? $_lang['stats_depth5i'] : $_lang['stats_depth5m']);
        $t->assign('depthrange5', $this->depthrange[4]);
        $t->assign('depthrange5_per', $this->depthrange5_per);

        // Show water temp details
        $t->assign('stats_watertempmin', $_lang['stats_watertempmin']);
        $t->assign('stats_watertempmax', $_lang['stats_watertempmax'] );
        $t->assign('stats_watertempavg', $_lang['stats_watertempavg'] );

        $t->assign('stats_decodives', $_lang['stats_decodives'] );
        $t->assign('stats_repdives', $_lang['stats_repdives'] );

        if ($_config['temp']) {
            $WatertempMin =  CelsiusToFahrenh($divestats['WatertempMin'], 0) ."&nbsp;". $_lang['unit_temp_imp'];
        } else {
            $WatertempMin =  $divestats['WatertempMin'] ."&nbsp;". $_lang['unit_temp'];
        }
        $t->assign('WatertempMin', $WatertempMin);
        $t->assign('WatertempMinNr', $this->WatertempMinNr );

        if ($_config['temp']) {
            $WatertempMax =  CelsiusToFahrenh($divestats['WatertempMax'], 0) ."&nbsp;". $_lang['unit_temp_imp'];
        } else {
            $WatertempMax =  $divestats['WatertempMax'] ."&nbsp;". $_lang['unit_temp'];
        }
        $t->assign('WatertempMax', $WatertempMax);
        $t->assign('WatertempMaxNr', $this->WatertempMaxNr );

        if ($_config['temp']) {
            $WatertempAvg =  CelsiusToFahrenh($divestats['WatertempAvg'], 0) ."&nbsp;". $_lang['unit_temp_imp'];
        } else {
            $WatertempAvg =  round($divestats['WatertempAvg'],1) ."&nbsp;". $_lang['unit_temp'];
        }
        $t->assign('WatertempAvg', $WatertempAvg);

        // Show air temp details
        $t->assign('stats_airtempmin', $_lang['stats_airtempmin']);
        $t->assign('stats_airtempmax', $_lang['stats_airtempmax'] );
        $t->assign('stats_airtempavg', $_lang['stats_airtempavg'] );

        if ($_config['temp']) {
            $AirtempMin =  CelsiusToFahrenh($divestats['AirtempMin'], 0) ."&nbsp;". $_lang['unit_temp_imp'];
        } else {
            $AirtempMin =  $divestats['AirtempMin'] ."&nbsp;". $_lang['unit_temp'];
        }
        $t->assign('AirtempMin', $AirtempMin);
        $t->assign('AirtempMinNr', $this->AirtempMinNr );

        if ($_config['temp']) {
            $AirtempMax =  CelsiusToFahrenh($divestats['AirtempMax'], 0) ."&nbsp;". $_lang['unit_temp_imp'];
        } else {
            $AirtempMax =  $divestats['AirtempMax'] ."&nbsp;". $_lang['unit_temp'];
        }
        $t->assign('AirtempMax', $AirtempMax);
        $t->assign('AirtempMaxNr', $this->AirtempMaxNr );

        if ($_config['temp']) {
            $AirtempAvg =  CelsiusToFahrenh($divestats['AirtempAvg'], 0) ."&nbsp;". $_lang['unit_temp_imp'];
        } else {
            $AirtempAvg =  round($divestats['AirtempAvg'],1) ."&nbsp;". $_lang['unit_temp'];
        }
        $t->assign('AirtempAvg', $AirtempAvg);

        // Show deco and rep details
        $t->assign('stats_decodives', $_lang['stats_decodives'] );
        $t->assign('stats_nodecodives', $_lang['stats_nodecodives'] );
        $t->assign('stats_repdives',$_lang['stats_repdives'] );
        $t->assign('stats_norepdives',$_lang['stats_norepdives'] );

        $decodives_per = round(($this->decodives / $this->end) * 100) ;
        $nodecodives_per = round(($this->nodecodives / $this->end) * 100) ;
        $repdives_per = round(($this->repdives / $this->end) * 100) ;
        $norepdives_per = round(($this->norepdives / $this->end) * 100) ;
        $t->assign('decodives', $this->decodives);
        $t->assign('nodecodives', $this->nodecodives);
        $t->assign('repdives', $this->repdives);
        $t->assign('norepdives', $this->norepdives);
        $t->assign('decodives_per', $decodives_per);
        $t->assign('nodecodives_per', $nodecodives_per);
        $t->assign('repdives_per', $repdives_per);
        $t->assign('norepdives_per', $norepdives_per);

        // Show water type details
        $t->assign('stats_saltwaterdives', $_lang['stats_saltwaterdives'] );
        $t->assign('stats_freshwaterdives', $_lang['stats_freshwaterdives']);
        $t->assign('stats_brackishdives', $_lang['stats_brackishdives'] );
        $t->assign('saltwaterdives', $this->saltwaterdives );
        $t->assign('saltwaterdives_per', round(($this->saltwaterdives / $this->end) * 100) );
        $t->assign('freshwaterdives', $this->freshwaterdives);
        $t->assign('freshwaterdives_per', round(($this->freshwaterdives / $this->end) * 100) );
        $t->assign('brackishdives', $this->brackishdives);
        $t->assign('brackishdives_per', round(($this->brackishdives / $this->end) * 100) );

        // Show more dive type details
        $t->assign('stats_deepdives' ,$_lang['stats_deepdives'] );
        $t->assign('stats_cavedives', $_lang['stats_cavedives']);
        $t->assign('stats_wreckdives', $_lang['stats_wreckdives']);
        $t->assign('stats_photodives', $_lang['stats_photodives']);

        $t->assign('deepdives', $this->deepdives );
        $t->assign('deepdives_per', round(($this->deepdives / $this->end) * 100));
        $t->assign('cavedives', $this->cavedives );
        $t->assign('cavedives_per', round(($this->cavedives / $this->end) * 100));
        $t->assign('wreckdives', $this->wreckdives );
        $t->assign('wreckdives_per', round(($this->wreckdives / $this->end) * 100));
        $t->assign('photodives', $this->photodives);
        $t->assign('photodives_per', round(($this->photodives / $this->end) * 100));

        // Show dive type details
        $t->assign('stats_shoredives', $_lang['stats_shoredives']);
        $t->assign('stats_boatdives', $_lang['stats_boatdives']);
        $t->assign('stats_nightdives', $_lang['stats_nightdives']);
        $t->assign('stats_driftdives', $_lang['stats_driftdives']);
        $t->assign('shoredives', $this->shoredives);
        $t->assign('shoredives_per', round(($this->shoredives / $this->end) * 100));
        $t->assign('boatdives', $this->boatdives );
        $t->assign('boatdives_per', round(($this->boatdives / $this->end) * 100));
        $t->assign('nightdives', $this->nightdives );
        $t->assign('nightdives_per', round(($this->nightdives / $this->end) * 100));
        $t->assign('driftdives', $this->driftdives );
        $t->assign('driftdives_per', round(($this->driftdives / $this->end) * 100));

        // Show if double or single tanks used
        $t->assign('stats_singletankdives', $_lang['stats_singletankdives']);
        $t->assign('singletankdives', $this->singletankdives);
        $t->assign('singletankdives_per', round(($this->singletankdives / $this->end) * 100));
        $t->assign('stats_doubletankdives', $_lang['stats_doubletankdives']);
        $t->assign('doubletankdives', $this->doubletankdives);
        $t->assign('doubletankdives_per', round(($this->doubletankdives / $this->end) * 100));

        // Show circuit type details
        $t->assign('stats_ocdives', $_lang['stats_ocdives'] );
        $t->assign('stats_scrdives', $_lang['stats_scrdives']);
        $t->assign('stats_ccrdives', $_lang['stats_ccrdives'] );
        $t->assign('ocdives', $this->ocdives );
        $t->assign('ocdives_per', round(($this->ocdives / $this->end) * 100) );
        $t->assign('scrdives', $this->scrdives);
        $t->assign('scrdives_per', round(($this->scrdives / $this->end) * 100) );
        $t->assign('ccrdives', $this->ccrdives);
        $t->assign('ccrdives_per', round(($this->ccrdives / $this->end) * 100) );

    /*}}}*/
    }

    /**
     * set_lastdive_info 
     * 
     * @access public
     * @return void
     */
    function set_lastdive_info() {
        global $globals, $_config, $t, $_lang; /*{{{*/
        $t->assign('LastEntryTime', $this->LastEntryTime);
        $t->assign('LastDivePlace',$this->LastDivePlace);
        $t->assign('LastDiveID',$this->LastDiveID);
        $t->assign('LastCity',$this->LastCity);
        $t->assign('LastCityID',$this->LastCityID);
        $t->assign('LastCountry',$this->LastCountry);
        $t->assign('LastCountryID',$this->LastCountryID);
    /*}}}*/
    }

    /**
     * set_dive_certifications 
     * 
     * @access public
     * @return void
     */
    function set_dive_certifications() {
        global $globals, $_config, $t, $_lang; /*{{{*/
        /**
         * Declare the variables otherwise an error is displayed
         */
        $userpath_web = "";
        $title = "";
        $Scan1Path = "";
        $cert_scan_front = "";
        $Scan2Path = "";
        $cert_scan_back = "";

        $t->assign('user_show_certs', $_config['user_show_certs'] );
        $t->assign('cert_none', $_lang['cert_none'] );
        $t->assign('stats_sect_certs', $this->username."'s ".$_lang['stats_sect_certs'] );

        if ($_config['user_show_certs']) {
        if ($this->number_cert != 0) {
            $divecert = $this->divecert;
            $t->assign('count',$this->number_cert);
            $t->assign('stats_sect_certs', $this->username."'s ".$_lang['stats_sect_certs'] );

            // Show dive certification titles
            $t->assign('cert_brevet', $_lang['cert_brevet'] );
            $t->assign('cert_org', $_lang['cert_org'] );
            $t->assign('cert_certdate', $_lang['cert_certdate'] );
            $t->assign('cert_number', $_lang['cert_number'] );
            $t->assign('cert_instructor', $_lang['cert_instructor'] );

            for ($i=0; $i<count($divecert); $i++) {
                if (isset($divecert[$i]['Brevet']) && ($divecert[$i]['Brevet'] != "")) {
                    $Brevet = $divecert[$i]['Brevet'];
                } else {
                    $Brevet = "-";
                }
                if (isset($divecert[$i]['Org']) && ($divecert[$i]['Org'] != "")) {
                    $Org = $divecert[$i]['Org'];
                } else {
                    $Org = "-";
                }
                if (isset($divecert[$i]['CertDate']) && ($divecert[$i]['CertDate'] != "")) {
                    $CertDate = date($_lang['logbook_divedate_format'], strtotime($divecert[$i]['CertDate']));
                } else {
                    $CertDate = "-";
                }
                if (isset($divecert[$i]['Number']) && ($divecert[$i]['Number'] != "")) {
                    $Number = $divecert[$i]['Number'];
                } else {
                    $Number = "-";
                }
                if (isset($divecert[$i]['Instructor']) && ($divecert[$i]['Instructor'] != "")) {
                    $Instructor = $divecert[$i]['Instructor'];
                } else {
                    $Instructor = "-";
                }
                if ((isset($divecert[$i]['Scan1Path']) && ($divecert[$i]['Scan1Path'] != "")) || (isset($divecert[$i]['Scan2Path']) && ($divecert[$i]['Scan2Path'] != ""))) {
                    $title = $divecert[$i]['Org'] . " " .$divecert[$i]['Brevet']; 
                    $userpath_web = $_config['userpath_web'] ;
                    $cert_scan_front = "";
                    $cert_scan_back = "";
                    if (!empty($divecert[$i]['Scan1Path'])) {
                        $Scan1Path = array();
                        $Scan1Path = $divecert[$i]['Scan1Path'];
                        $cert_scan_front = $_lang['cert_scan_front'];
                    } else {
                        $Scan1Path = array();
                        $Scan1Path = '';
                        $cert_scan_front = '';
                    }
                    if (!empty($divecert[$i]['Scan2Path'])) {
                        $Scan2Path = array();
                        $Scan2Path = $divecert[$i]['Scan2Path'] ;
                        $cert_scan_back = $_lang['cert_scan_back'];
                    } else {
                        $Scan2Path = array();
                        $Scan2Path = '';
                        $cert_scan_front = '';
                    }
                } 

                $rowdata[$i] = array (
                        'brevet' => $Brevet , 'org' => $Org , 'certdate' => $CertDate , 'number' => $Number , 'instructor' => $Instructor ,
                        'userpath_web' => $userpath_web , 'title' => $title , 'scan1path' => $Scan1Path , 'cert_scan_front' => $cert_scan_front ,
                        'scan2path' => $Scan2Path , 'cert_scan_back' => $cert_scan_back );

            }
            $t->assign('cells',$rowdata);
        } else {
            $t->assign('cert_none', $_lang['cert_none'] );
        }
        }
    /*}}}*/
    }
/*}}}*/
}

/**
 * DivePictures 
 * 
 * @package phpdivinglog
 * @version $Rev$
 * @copyright Copyright (C) 2008 Rob Lensen. All rights reserved.
 * @author Rob Lensen <rob@bsdfreaks.nl> 
 * @license LGPL v3 http://www.gnu.org/licenses/lgpl-3.0.txt
 */
class DivePictures{
/*{{{*/
    var $multiuser;
    var $user_id;
    var $table_prefix;
    var $username;
    var $request_type;
    var $image_link;
    var $images_for_resize;
    var $number_images_resize;
    var $num_images_from_dive;
    var $requested_page;

    /**
     * DivePictures 
     * 
     * @access public
     * @return void
     */
    function DivePictures() {
        global $_config;
        $this->multiuser = $_config['multiuser'];
    }
    
    /**
     * set_divegallery_info 
     * 
     * @param mixed $request 
     * @access public
     * @return void
     */
    function set_divegallery_info($request) {
        // We need to extract the info from the request 
        /*{{{*/
        if (!$request->diver_choice) {
            if ($this->multiuser) {
                $this->user_id = $request->get_user_id();
                $user = new User();
                $user->set_user_id($this->user_id);
                $this->table_prefix = $user->get_table_prefix();
                $this->username = $user->get_username();
            } else {
                $this->user_id = $request->get_user_id();
                $user = new User();
                // $user->set_user_id($this->user_id);
                $this->table_prefix = $user->get_table_prefix();
                $this->username = $user->get_username();
            }
            $this->requested_page = $request->get_requested_page();
        } else {
            $this->request_type = 3;
        }
    /*}}}*/
    }
    
    /**
     * set_divegallery_info_direct 
     * 
     * @param mixed $user_id 
     * @param mixed $table_prefix 
     * @access public
     * @return void
     */
    function set_divegallery_info_direct($user_id) {
        if ($this->multiuser) {/*{{{*/
            $this->user_id = $user_id;
            $user = new User();
            $user->set_user_id($this->user_id);
            $this->table_prefix = $user->get_table_prefix();
            $this->username = $user->get_username();
        } else {
            $this->user_id = $user_id;
            $user = new User();
            $this->table_prefix = $user->get_table_prefix();
            $this->username = $user->get_username();
        }/*}}}*/
    }
    
    /**
     * get_divegallery_info 
     * 
     * @param int $dive_id 
     * @access public
     * @return void
     */
    function get_divegallery_info($dive_id = 0, $site_id = 0, $equipment_nr = 0, $shop_id = 0, $trip_id = 0) {
        global $globals, $_config, $_lang, $t;/*{{{*/
        //if (($this->multiuser && !empty($this->user_id)) || !$this->multiuser ) {
            if ($dive_id == 0 && $site_id == 0 && $equipment_nr == 0 && $shop_id == 0 && $trip_id == 0) {
                $divepics = parse_mysql_query('divepicsall.sql');
                $base_path = $_config['picpath_web'];
            } elseif ($dive_id != 0) {
                $globals['logid'] = $dive_id;
                $divepics = parse_mysql_query('divepics.sql');
                $base_path = $_config['picpath_web'];
            } elseif ($site_id !=0) {
                $globals['id'] = $site_id;
                $base_path = $_config['mappath_web'];
                $divepics = parse_mysql_query('divepics_map.sql');
            } elseif ($equipment_nr !=0) {
                $globals['id'] = $equipment_nr;
                $base_path = $_config['equippath_web'];
                $divepics_temp = parse_mysql_query('divepics_equip.sql');
                if (count($divepics_temp) == 2) {
                    $divepics[0] = $divepics_temp;
                } else {
                    $divepics = $divepics_temp;
                }
            } elseif ($shop_id !=0) {
                $globals['id'] = $shop_id;
                $base_path = $_config['shoppath_web'];
                $divepics_temp = parse_mysql_query('divepics_shop.sql');
                if (count($divepics_temp) == 2) {
                    $divepics[0] = $divepics_temp;
                } else {
                    $divepics = $divepics_temp;
                }
            } elseif ($trip_id !=0) {
                $globals['id'] = $trip_id;
                $base_path = $_config['trippath_web'];
                $divepics_temp = parse_mysql_query('divepics_trip.sql');
                if (count($divepics_temp) == 2) {
                    $divepics[0] = $divepics_temp;
                } else {
                    $divepics = $divepics_temp;
                }
            }
            
            $pics = count($divepics);
            if ($pics != 0 && !empty($divepics[0]['Path']) )  {
                $this->image_link = array();
                $this->num_images_from_dive = array();
                $a =1;
                for ($i=0; $i<$pics; $i++) {
                    $img_url =  $base_path . $divepics[$i]['Path'];
                    if (file_exists($img_url)) {
                        $img_thumb_url = $base_path .'thumb_' . $divepics[$i]['Path'];
                        // $img_title = $_lang['divepic_linktitle_pt1']. ($a). $_lang['divepic_linktitle_pt2']. $pics;
                        // $img_title .= $_lang['divepic_linktitle_pt3']  ;
                        $img_title = $divepics[$i]['Description'];
                        $img_date = $this->get_exif_data($img_url);
                        $dive_nr = 0;
                        $site_nr = 0;
                        $shop_nr = 0;
                        $trip_nr = 0;

                        if (isset($divepics[$i]['Number'])) {
                            $dive_nr = $divepics[$i]['Number'];
                        }
                        if (isset($divepics[$i]['PlaceID'])) {
                            $site_nr = $divepics[$i]['PlaceID'];
                            $divesite = new Divesite($site_nr);
                            if (isset($divesite->result['Place'])) {
                                $divesite_name = $divesite->result['Place'];
                            } else {
                                $divesite_name = '';
                            }
                        } else {
                            $divesite_name = '';
                        }
                        $this->image_link[] = array(
                                'img_url' => $img_url, 
                                'img_thumb_url' => $img_thumb_url , 
                                'img_title' => $img_title,
                                'dive_nr' => $dive_nr,
                                'site_nr' => $site_nr,
                                'site_name' => $divesite_name,
                                'img_date'    => $img_date,
                                'resize' => false,
                                'thumb' => false
                                );
                        if (array_key_exists($dive_nr,$this->num_images_from_dive)) {
                            $this->num_images_from_dive[$dive_nr]++;
                        } else {
                            $this->num_images_from_dive[$dive_nr] = 1;
                        }
                        $a++;
                    }
                }
                if (isset($_config['enable_resize'])) {
                    $t->assign('pics_resized','1');
                    /**
                     * Check if the images are correctly sized 
                     */
                    $toberesized = array();
                    $tobethumbed = array();
                    $this->number_images_resize = 0;
                    for ($i=0 ; $i < count($this->image_link) ; $i++) {
                        /**
                         *  Check normal image
                         */
                        if (filesize($this->image_link[$i]['img_url']) < 1812000 ) {
                            $size = array();
                            $size =getimagesize($this->image_link[$i]['img_url']);
                            /**
                             * Make an array of the resized images 
                             */
                            if ($size[0] <= $_config['pic-width']) {
                                // echo "<strong>ERROR: No resize!</strong><br>";
                            } else {
                                $this->image_link[$i]['resize'] = true;
                                $this->number_images_resize++;
                            }
                            /**
                             * Make array of the beresized thumbs
                             */
                            if (!file_exists($this->image_link[$i]['img_thumb_url'])) {
                                $this->image_link[$i]['thumb'] = true;
                                $this->number_images_resize++;
                            } else {
                                $size_thumb = getimagesize($this->image_link[$i]['img_thumb_url']);
                                if ($size_thumb[0]<=$_config['thumb-width'] && $size_thumb[1]<=$_config['thumb-width']) {
                                    // echo "<strong>ERROR: No thumb!</strong><br>";
                                } else {
                                    $this->image_link[$i]['thumb'] = true;
                                    $this->number_images_resize++;
                                }
                            }
                        }
                    }

                }
            }
//        } else {
//            /**
//             * if the request type is not already set(by divers choice), set it to overview  
//             */
//            if ($this->request_type != 3) {
//                $this->request_type = 0;
//            }
//        }
        /*}}}*/
    }

    /**
     * resizer 
     * 
     * @access public
     * @return void
     */
    function resizer($ref = 0) {
        global $_config; /*{{{*/
        // $host  = $_SERVER['HTTP_HOST'];
        // $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        $extra = 'resize.php?ref='.$ref;
        // header("Location: http://$host$uri/$extra");
        $url = $_config['web_root'];
        header("Location: $url/$extra");
        exit;
    /*}}}*/
    }

    /**
     * resize_needed 
     * 
     * @access public
     * @return void
     */
    function resize_needed() {
        if ($this->number_images_resize > 0) { /*{{{*/
            return true;
        } else {
            return false;
        }
    /*}}}*/
    }

    /**
     * return_total_images_for_resizing 
     * 
     * @access public
     * @return void
     */
    function return_total_images_for_resizing() {
        return $this->number_images_resize;
    }

    /**
     * return_array_images_for_resize 
     * 
     * @access public
     * @return void
     */
    function return_array_images_for_resize() {
        $temp = array(); /*{{{*/
        for ($i=0 ; $i < count($this->image_link) ; $i++) {
            $temp[] = array_filter($this->image_link[$i]);
        }   
        for ($a=0 ; $a < count($temp) ; $a++) {
            if (array_key_exists('thumb',$temp[$a]) || array_key_exists('resize',$temp[$a])) {
                $this->images_for_resize[] = $temp[$a];
            }
        }
        return $this->images_for_resize;
    /*}}}*/
    }

    /**
     * get_image_link 
     * 
     * @access public
     * @return void
     */
    function get_image_link() {
        return $this->image_link;
    }
    /**
     * get_exif_data
     * 
     * @acces public
     * @return array with exif data
     */

    function get_exif_data($file) {
        global $_config, $t, $_lang, $globals;/*{{{*/
        if (isset($_config["get_exif_data"]) && function_exists('exif_read_data')) {
            $exif_date = exif_read_data ( $file ,'IFD0'  ); 
            $edate = $exif_date['DateTime']; 
        } else {
            $edate = "";
        }
        return $edate;/*}}}*/
    }

    /**
     * set_all_dive_pictures 
     * 
     * @access public
     * @return void
     */
    function set_all_dive_pictures() {
    /*{{{*/
        global $_config,$t, $_lang, $globals;
        
        if (!empty($this->multiuser)) {
            $path = $_config['web_root'].'/divegallery.php/'.$this->user_id.'/list';
        } else {
            $path = $_config['web_root'].'/divegallery.php/list';
        }
        if (empty($this->requested_page)) {
            $cpage = 0;
        } else {
            $cpage = $this->requested_page;
        }
        $pager = new TablePager($cpage,$path);
        $pager->set_tablepager_itemdata($this->image_link);
        $pager->create_pager();

        $t->assign('pics2', '1');
        $t->assign('image_link', $pager->return_pager_data());
        $t->assign('pager_links', $pager->return_pager_links());
        $t->assign(
                    'page_numbers', array(
                    'current' => $pager->return_getCurrentPageID(),
                    'total'   => $pager->return_numPages()
                        )
                   );
        $t->assign('num_images_from_dive', $this->num_images_from_dive);
        $t->assign('divepic_dive_linktitle', $_lang['logbook_dive_linktitle'] );
        $t->assign('divepic_place_linktitle', $_lang['logbook_place_linktitle'] );
        $t->assign('logbook_place', $_lang['logbook_place'] );
        $t->assign('divepic_dive_number', $_lang['divepic_dive_number'] );
        $t->assign('dive_details_pagetitle', $_lang['dive_details_pagetitle'] );/*}}}*/
    }
    
    /*}}}*/
}


/**
 * Tank get all tanks which belong to a dive 
 * 
 * @package phpdivinglog
 * @version $Rev$
 * @copyright Copyright (C) 2011 Rob Lensen. All rights reserved.
 * @author Rob Lensen <rob@bsdfreaks.nl> 
 * @license LGPL v3 http://www.gnu.org/licenses/lgpl-3.0.txt
 */
class Tank{
    var $multiuser; /*{{{*/
    var $table_prefix;
    var $user_id;
    var $divetank_nr;
    var $dive_nr;
    var $result;
    var $request_type; // request_type = 0 overview, request_type = 1 details

    /**
     * Divesite default constructor sets some defaults for this class
     * 
     * @access public
     * @return void
     */
    function __construct($tank_id = 0 , $dive_nr = 0) {
        if ($tank_id !=0) {
            // Class is called to get info about a tank
            $this->get_divetank_info();

        } elseif ($dive_nr != 0) {
            // Class is called to get all tanks from a dive
        } else {
            // Class is called without arguments
            // @TODO maybe a tank list?
        }
    
    }

    function get_request_type() {
        return $this->request_type;
    }

    /**
     * get_equipment_info 
     * 
     * @access public
     * @return void
     */
    function get_divetank_info() {
        global $_config, $globals; /*{{{*/
        if (!empty($this->divetank_nr)) {
            $this->request_type = 1;
            $globals['tank_id'] = $this->divetank_nr;
            $this->result = parse_mysql_query('onetank.sql');
        } else {
           /**
             * If the request type is not already set (by divers choice), set it to overview  
             */
            if ($this->request_type != 3) {
                $this->request_type = 0;
            }
        }
        return $this->result;
    /*}}}*/
    }

    /**
     * get_tanks_from_dive checks if there are more tanks associated with a dive nr
     * 
     * @access public
     * @return void
     */
    function get_tanks_from_dive() {
        global $t, $_config, $_lang, $globals; /*{{{*/
        $globals['dive_nr'] = $this->dive_nr;
        $divetanks = parse_mysql_query('tanksdivelist.sql');
        $num_tanks = $globals['sql_num_rows'];
        if ($num_tanks > 0) {
            // Get the info for each tank
        }
    /*}}}*/
    }


/*}}}*/
}

/**
 * AppInfo 
 * 
 * @package phpdivinglog
 * @copyright Copyright (C) 2007 Rob Lensen. All rights reserved.
 * @author Rob Lensen <rob@bsdfreaks.nl> 
 * @license LGPL v3 http://www.gnu.org/licenses/lgpl-3.0.txt
 */
class AppInfo{ /*{{{*/
    var $table_prefix;
    var $user_id;
    var $DivelogVersion;
    var $Divelogname;
    var $Appname;
    var $phpDivelogVersion;
    var $Authors;

    /**
     * AppInfo 
     * 
     * @access public
     * @return void
     */
    function AppInfo($request) {
        global $_lang, $_config;
        if ($_config['multiuser']) {
            $this->user_id = $request->get_user_id();
            $user = new User();
            $user->set_user_id($this->user_id);
            $this->table_prefix = $user->get_table_prefix();
        } else {
            // if prefix is set get it.
            if (isset($_config['table_prefix']))
                $this->table_prefix = $_config['table_prefix'];
        }
        $this->phpDivelogVersion = $_config['app_version'];
        $this->Appname = $_config['app_name'];
        $dbinfo = parse_mysql_query('dbinfo.sql');
        $this->Divelogname = $dbinfo['PrgName'];
        $this->DivelogVersion = $dbinfo['DBVersion'];
        $this->dbversion = $_lang['dbversion'];
        $this->and = $_lang['and'];
        $this->app_url = $_config['app_url'];
        $this->dlog_url = $_config['dlog_url'];
        $this->dlog_version = $_config['dlog_version'];
    }

    /**
     * SetAppInfo 
     * 
     * @access public
     * @return void
     */
    function SetAppInfo() {
        global $_config, $t, $_lang;
        $t->assign('poweredby', $_lang['poweredby']);
        $t->assign('Divelogname', $this->Divelogname);
        $t->assign('DivelogVersion', $this->DivelogVersion);
        $t->assign('Appname', $this->Appname);
        $t->assign('phpDivelogVersion', $this->phpDivelogVersion);
        $t->assign('dbversion', $this->dbversion);
        $t->assign('and', $this->and);
        $t->assign('app_url', $this->app_url);
        $t->assign('dlog_url', $this->dlog_url);
        $t->assign('dlog_version', $this->dlog_version);
    }
/*}}}*/
}
