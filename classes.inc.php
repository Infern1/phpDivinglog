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
    var $request_uri;/*{{{*/
    var $request_file_depth;
    var $multiuser;
    var $user_id;
    var $equipment_nr;
    var $dive_nr;
    var $site_nr;
    var $diver_choice;

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
    function HandleRequest(){
        global $_config, $t;
        $this->multiuser = $_config['multiuser'];

        if($_config['query_string']){
            if($this->multiuser){
                $t->assign('sep1','?user_id=');
                $t->assign('sep2','&id=');
                $t->assign('list','&view=list');
            } else {
                //$t->assign('sep1','?user_id=');
                $t->assign('sep2','?id=');
                $t->assign('list','?view=list');
            }
        } else {
            $t->assign('sep1',"/");
            $t->assign('sep2',"/");
            $t->assign('list','/list');
        }            
    }

    /**
     * set_request_uri 
     * 
     * @param mixed $uri 
     * @access public
     * @return void
     */
    function set_request_uri($uri){
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
    function set_file_depth($depth = 0){
        global $_config;
        $app_depth  = count_all((split("/", $_config['abs_url_path'])));
        $this->request_file_depth = $depth + $app_depth;
    }
    function get_user_id(){
        return $this->user_id;
    }
    function get_dive_nr(){
        return $this->dive_nr;
    }
    function get_site_nr(){
        return $this->site_nr;
    }
    function get_equipment_nr(){
        return $this->equipment_nr;
    }
    function get_request_type(){
        return $this->get_request_type;
    }
    function get_view_request(){
        return $this->view_request;
    }
    function get_requested_page(){
        return $this->requested_page;
    }
    function get_multiuser(){
        return $this->multiuser;
    }
    /**
     * handle_url will handle the url according the type of setup. 
     * The url handling differs between single and multiuser setup 
     * 
     * @access public
     * @return void
     */
    function handle_url(){
        global $_config, $t;
        if($this->multiuser){
            // The url should contain a least one select person, other wise return to the person chooser
            // Get the last two parts of the array
            $split_request = GetRequestVar($this->request_uri, $this->request_file_depth);
            //check if the user is set, otherwise show person chooser
            if(isset($split_request[1]) && Users::is_valid_user($split_request[1]) ){
                $file_req = $split_request[0];
                $this->user_id = check_number($split_request[1]);
                if(isset($split_request[2]) && $split_request[2] == 'list'  ){
                    $this->view_request = 0;
                    if(isset($split_request[3])){
                        $this->requested_page = $split_request[3];
                    }
                }elseif(isset($split_request[2])) {
                    $t->assign('dive_detail' ,1);
                    $this->view_request = 1;    
                } else {
                    $this->diver_choice = true;
                }
                switch($file_req) {
                    case "index.php":
                        if($this->view_request == 1)
                            $this->dive_nr = check_number($split_request[2]);
                        $this->request_type = 1;
                        break;
                    case 'divesite.php':
                        if($this->view_request == 1)
                            $this->site_nr = check_number($split_request[2]);
                        $this->request_type = 2;
                        break;
                    case 'equipment.php':
                        if($this->view_request == 1)
                            $this->equipment_nr = check_number($split_request[2]);
                        $this->request_type = 3;
                        break;
                    case 'divestats.php':
                        $this->request_type = 4;
                        break;
                    case 'divegallery.php':
                        $this->request_type = 6;
                        break;
                     case 'resize.php':
                        $this->request_type = 7;
                        break;
                     case 'drawprofile.php':
                        $this->request_type = 5;
                        if($this->view_request == 1)
                            $this->dive_nr = check_number($split_request[2]);
                        break;
                    case 'drawpiechart.php':
                        $this->request_type = 5;
                        $this->diver_choice = false;
                        break;                   
                    case 'divesummary.php':
                        $this->request_type = 5;
                        $this->diver_choice = false;
                        break;
                    default:
                        $this->diver_choice = true;
                        $this->request_type = 1;
                    break;
                }
            }else {
                $this->diver_choice = true;
            }
            //print_r($this);

        }
        else {
            //Find what the client wants to see and if a record is requested set it
            $split_request = GetRequestVar($this->request_uri, $this->request_file_depth);
            if(!empty($split_request[0])){
                if(isset($split_request[1]) && $split_request[1] == 'list' ){
                    $this->view_request = 0;
                    if(isset($split_request[2]) ){
                        $this->requested_page = $split_request[2];
                    }
                } elseif(isset($split_request[1])) {
                    $t->assign('dive_detail' ,1);
                    $this->view_request = 1;    
                }

                $file_req = $split_request[0];
                $id = 0;
                if(!empty($split_request[1])){
                    $id = check_number($split_request[1]);
                }

                switch($file_req) {
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
                    case 'divegallery.php':
                        $this->user_id = $id;
                        $this->request_type = 6;
                        break;
                    case 'resize.php':
                        $this->user_id = $id;
                        $this->request_type = 7;
                        break;
                    case 'drawprofile.php':
                        $this->dive_nr = $id;
                        $this->request_type = 5;
                        break;
                    case 'divesummary.php':
                        $this->user_id = $id;
                        $this->request_type = 4;
                        break;
                    default:
                        //defaults to main page
                        break;
                }
            }
        }
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
    var $user_id;/*{{{*/
    var $table_prefix;
    var $username;
    var $multiuser;
    
    
    /**
     * User 
     * 
     * @access public
     * @return void
     */
    function User(){
        global $_config;
        $this->multiuser = $_config['multiuser'];
        if(!$this->multiuser){
            if(isset($_config['table_prefix'])){
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
    function set_user_id($id){
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
    function get_username(){
        global $_config, $globals;
        set_config_table_prefix($this->table_prefix);
        $user_data = parse_mysql_query('personal.sql');
        reset_config_table_prefix();
        $this->username = $user_data[0]['Firstname'] .' '.$user_data[0]['Lastname'];
        return $this->username;
    }
    /**
     * set_table_prefix 
     * 
     * @param mixed $id 
     * @access public
     * @return void
     */
    function set_table_prefix($id){
        global $_config;
        //Get the prefix from the config file
        if(isset($_config['user_prefix'][$id])){
            $this->table_prefix = $_config['user_prefix'][$id];
        }

    }

    function get_table_prefix(){
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
    var $language;/*{{{*/
    var $gridtable;

    /**
     * Grid 
     * 
     * @access public
     * @return void
     */
    function TableGrid($user_id = 0){
        global $_config;
        $this->language = $_config['language'];
        $objGrid = new datagrid;
        $objGrid->friendlyHTML(); 
        $objGrid->conectadb($_config['database_server']  , $_config['database_username'], $_config['database_password'], $_config['database_db']); 
        $objGrid -> pathtoimages($_config['abs_url_path']. "/images/");
        $objGrid->datarows($_config['max_list']);
        if($_config['query_string']){
            $objGrid->methodForm('GET');
            $objGrid->linkparam("user_id=".$user_id."&id=list");
        }
        $this->gridtable  =& $objGrid;
        $this->SetGridLanguage();
    }
    
    /**
     * get_grid caputure the output of the grid and return the html to the class
     * 
     * @param mixed $objGrid 
     * @access public
     * @return void
     */
    function get_grid($objGrid){
        ob_start();
        $objGrid->grid();
        $objGrid -> desconectar(); 
        $grid = ob_get_clean();
        return $grid;
    }

    function get_grid_class(){
        return $this->gridtable;
    }
    /**
     * SetGridLanguage 
     * 
     * @param  void
     * @access public
     * @return void
     */
    function SetGridLanguage(){
        global $_lang, $_config;/*{{{*/
        switch ($this->language){
            case 'english' :
                // Do nothing since default is english for phpmydatgrid
                break;
            case 'nederlands': case 'dutch' :
                $this->gridtable->language("ne");
                break;
            case 'deutch': case 'german' :
                $this->gridtable->language("de");
                break;
           case 'espa.ol': case 'es' :
                $this->gridtable->language("es");
                break;
            case 'francais': case 'fr' :
                $this->gridtable->language("fr");
                break;
            case 'italian' : case 'it' :
                $this->gridtable->language("it");
                break;
            case '.e.tina': case 'cs' :
                $this->gridtable->language("cs");
                break;
            case 'portuguese' : case 'portugese' :
                $this->gridtable->language("pt");
                break;
            default:
                if(!isset($_lang['grid_cancel'])){
                    echo "<center><strong>ERROR no language found for the grid (".$_config['language']. ") fix your language file</strong></center><br>";
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
   
    }/*}}}*/
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
    /**
     * TablePager default constructor which creates the defaults 
     * 
     * @access public
     * @return void
     */
    function TablePager($cpage, $path){
        global $_config;
        if($_config['query_string']){
            $pager_options = array( 
                    'mode' => 'Sliding', 
                    'perPage' => $_config['max_list'], 
                    'append' => true,
                    'currentPage' => $cpage,
                    'path' => '' ,
                    'fileName' => '%d',
                    'delta' => 2, ); 
        } else {
            $pager_options = array( 
                    'mode' => 'Sliding', 
                    'perPage' => $_config['max_list'], 
                    'append' => false,
                    'currentPage' => $cpage,
                    'path' => $path ,
                    'fileName' => '%d',
                    'delta' => 2, ); 
        }
        $this->options = $pager_options;
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
    function Users(){
        global $_config, $t;
        //Get the user_ids and put them in a array
        if($_config['multiuser']){
            $this->user_ids = array_keys($_config['user_prefix']);
        }
    }
    /**
     * get_divers  sets the divers which are defined in the config file
     * @todo maybe users should be getted from the database
     * @access public
     * @return void
     */
    function get_divers(){
        global $_config, $t;
        //Get the user_ids and put them in a array
        if($_config['multiuser']){
            $this->user_ids = array_keys($_config['user_prefix']);
        }
    }

    /**
     * return_divers returns an array of the divers, more easy. 
     * 
     * @access public
     * @return void
     */
    function return_divers(){
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
    function is_valid_user($id){
        global $_config, $t;
        $user_ids =  array_keys($_config['user_prefix']);
        if(in_array($id, $user_ids)){
            //user is in, return true
            return true;
        } else {
            return false;
        }
    }

    function get_table_prefix(){
        global $_config;
        //We have the ids so just get the prefix
        foreach($this->user_ids as $id){
            $this->table_prefixes[] = $_config['user_prefix'][$id];
        }
    }

    function set_user_data(){
        global $_config;
        //Get the personal info of each user from the DB
        foreach($this->table_prefixes as $prefix){
            //First set the prefix
            $_config['table_prefix'] = $prefix;
            //Get the data
            $user_data[] = array_shift(parse_mysql_query('personal.sql'));
        }
        $i=0;
        foreach($this->user_ids as $id){
            $this->user_array[] = array_merge($user_data[$i], array( "ID" => $id));
            $i++;
        }
        //Reset the prefix
        unset($_config['table_prefix']);

    }
    function get_user_data(){
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
    function TopLevelMenu($request = 0 ){
        global $_config, $t;
        //Get the user_ids and put them in a array
        if($_config['multiuser']){
            $this->multiuser = true;
        }
        if($this->multiuser){
            $this->user_id = $request->get_user_id();
            $user = new User();
            $user->set_user_id($this->user_id);
            $this->table_prefix = $user->get_table_prefix();
        }
        else {
            // if prefix is set get it.
            if(isset($_config['table_prefix']))
                $this->table_prefix = $_config['table_prefix'];
        }

    }
    /**
     * get_std_links gets the links for the details screen 
     * 
     * @access public
     * @return void
     */
    function get_std_links(){
        global $t, $_lang;/*{{{*/
        //	Dive Log, Dive Sites, Dive Statistics
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
        if($this->multiuser){
            $t->assign('multiuser_id', $this->user_id);
        }/*}}}*/

    }
    /**
     * get_ovv_links sets the links in the overview screen
     * 
     * @access public
     * @return void
     */
    function get_ovv_links(){
        global $t, $_lang;/*{{{*/
        // Start filling the data in the links_overview.tpl file
        $t->assign('base_page','index.php');
        //	Dive Sites, Dive Statistics
        $t->assign('diver_choice_linktitle', $_lang['diver_choice_linktitle']);
        $t->assign('diver_choice', $_lang['diver_choice']);
        $t->assign('dive_log',$_lang['dive_log']);
        $t->assign('dive_log_linktitle', $_lang['dive_log_linktitle']);
        $t->assign('dive_sites_linktitle', $_lang['dive_sites_linktitle']);
        $t->assign('dive_sites', $_lang['dive_sites'] );
        $t->assign('dive_equip_linktitle', $_lang['dive_equip_linktitle']);
        $t->assign('dive_equip', $_lang['dive_equip'] );
        $t->assign('dive_stats_linktitle', $_lang['dive_stats_linktitle']);
        $t->assign('dive_stats', $_lang['dive_stats'] );

        if($this->multiuser){
            $t->assign('multiuser_id', $this->user_id);
        }
        //	Get the page header
        $pagetitle = $_lang['dive_log'];
        $t->assign('pagetitle',$pagetitle);/*}}}*/

    }

    /**
     * get_nav_links according the requested page
     * 
     * @param mixed $request
     * @access public
     * @return void
     */
    function get_nav_links($request){

        global $t, $globals,  $_lang, $_config;
        if($request->request_type == 1){
            set_config_table_prefix($this->table_prefix);
            $divelist = parse_mysql_query('divelist.sql');
            reset_config_table_prefix();
            for($i=0; $i<count($divelist); $i++) {
                $divelist[$i] = $divelist[$i]['Number'];
            }
            $get_nr = $request->get_dive_nr();
            $thisdive = array_search($get_nr, $divelist);
            if (array_search($get_nr, $divelist) != count($divelist)-1 ) {
                $t->assign('divenr','1');
                $t->assign('first_dive',$divelist[count($divelist)-1]);
                $t->assign('first_dive_linktitle', $_lang['first_dive_linktitle']);
                $t->assign('first',$_lang['first']);
                $t->assign('next_dive',$divelist[$thisdive+1]);
                $t->assign('previous_dive_linktitle', $_lang['previous_dive_linktitle']);
                $t->assign('previous', $_lang['previous']);

            } 
            //	Next, Last
            if (array_search($get_nr, $divelist) != 0 ) {
                $t->assign('divenr_not_null','1');	
                $t->assign('next_dive_nr',$divelist[$thisdive-1]);
                $t->assign('next_dive_linktitle', $_lang['next_dive_linktitle']);
                $t->assign('next', $_lang['next']);
                $t->assign('last_dive_nr',  $divelist[0]);
                $t->assign('last_dive_linktitle', $_lang['last_dive_linktitle']);
                $t->assign('last', $_lang['last'] );
            }
        }
        elseif($request->request_type == 2){
            //	First, Previous, Next, Last links and Dive #
            set_config_table_prefix($this->table_prefix);
            $sitelist = parse_mysql_query('sitelist.sql');
            reset_config_table_prefix();
            $last = count($sitelist) - 1;
            $position = -1;
            for ($i=0; $i<count($sitelist); $i++) {
                if ($sitelist[$i]['ID'] == $globals['placeid']) {
                    $position = $i;
                }
            }

            //	First, Previous
            if ($position != 0 ) {
                $t->assign('position',$position);
                $t->assign('first_site_id', $sitelist[0]['ID']);
                $t->assign('first_site_linktitle', $_lang['first_site_linktitle']);
                $t->assign('first', $_lang['first']);
                $t->assign('previous_site_id', $sitelist[$position - 1]['ID']);
                $t->assign('previous_site_linktitle', $_lang['previous_site_linktitle']);
                $t->assign('previous', $_lang['previous']);
            }

            //	Next, Last
            if ($position != $last) {
                $t->assign('divesite_not_null','1');
                $t->assign('next_divesite_nr', $sitelist[$position + 1]['ID']);
                $t->assign('next_site_linktitle', $_lang['next_site_linktitle']);
                $t->assign('next', $_lang['next'] );
                $t->assign('last_divesite_nr', $sitelist[$last]['ID']);
                $t->assign('last_site_linktitle', $_lang['last_site_linktitle'] );
                $t->assign('last', $_lang['last'] );
            } 
        } elseif($request->request_type == 3){
            set_config_table_prefix($this->table_prefix);
            $gearlist = parse_mysql_query('gearlist.sql');
            reset_config_table_prefix();
            $last = count($gearlist) - 1;
            $position = -1;
            for ($i=0; $i<count($gearlist); $i++) {
                if ($gearlist[$i]['ID'] == $globals['gear']) {
                    $position = $i;
                }
            }    
            //	First, Previous
            if ($position != 0 ) {
                $t->assign('equipment_first','1');
                $t->assign('first_eq_id', $gearlist[0]['ID']);
                $t->assign('first_equip_linktitle', $_lang['first_equip_linktitle']);
                $t->assign('first', $_lang['first']);
                $t->assign('previous_eq_id', $gearlist[$position - 1]['ID']);
                $t->assign('previous_equip_linktitle', $_lang['previous_equip_linktitle']);
                $t->assign('previous', $_lang['previous']);
            }

            //	Dive Log, Dive Sites, Dive Statistics
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

            //	Next, Last
            if ($position != $last) {
                $t->assign('equipment_not_null','1');
                $t->assign('next_eq_id', $gearlist[$position + 1]['ID']);
                $t->assign('next_equip_linktitle', $_lang['next_equip_linktitle']);
                $t->assign('next', $_lang['next'] );
                $t->assign('last_eq_id', $gearlist[$last]['ID']);
                $t->assign('last_equip_linktitle', $_lang['last_equip_linktitle'] );
                $t->assign('last', $_lang['last'] );
            } 
            //End filling the links section


        }

    }/*}}}*/
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
    var $multiuser;/*{{{*/
    var $user_id;
    var $dive_nr;
    var $result;
    var $table_prefix;
    var $profile_info;
    var $averagedepth;
    var $requested_page;
    var $sac;
    var $request_type; // request_type = 0 overview request_type = 1 details
   
    /**
     * Divelog default constructor sets some defaults for this class
     * 
     * @access public
     * @return void
     */
    function Divelog(){
        global $_config;
        $this->multiuser = $_config['multiuser'];
    }
    
    function get_request_type(){
        return $this->request_type;
    }
    /**
     * set_divelog_info sets the basics for this class from the url requested 
     * 
     * @param mixed $request 
     * @access public
     * @return void
     */
    function set_divelog_info($request){
        //We need to extract the info from the request/*{{{*/
        if(!$request->diver_choice){
            //Find request type
            if($request->get_view_request() == 1){
                $this->request_type = 1;
                $this->dive_nr = $request->get_dive_nr();
            } else {
                $this->request_type = 0;
                $this->requested_page = $request->get_requested_page();
            }
            if($this->multiuser){
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
    function get_divelog_info(){
        global $globals, $_config;/*{{{*/
        if(!empty($this->dive_nr)){
            $this->request_type = 1;
            //	Get the dive details from database
            $globals['divenr'] = $this->dive_nr;
            set_config_table_prefix($this->table_prefix);
            $this->result = parse_mysql_query('onedive.sql');
            reset_config_table_prefix();
            //Set profile data
            //	Calculate average depth and SAC for this dive
            $result = $this->result;
            $profile = $result[0]['Profile'];
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
             * If the request type is not already set(by divers choice), set it to overview  
             */
            if($this->request_type != 3){
                $this->request_type = 0;
            }
        }
        return $this->result;/*}}}*/
    }

    /**
     * get_main_dive_details 
     * 
     * @access public
     * @return void
     */
    function set_main_dive_details(){
        global $t,  $_config, $_lang;/*{{{*/
	    $result =  $this->result; 
        
        $t->assign('pagetitle',$_lang['dive_details_pagetitle'].$result[0]['Number']);
        $t->assign('logbook_divedate', $_lang['logbook_divedate'] );
	    $t->assign('logbook_entrytime', $_lang['logbook_entrytime'] );
	    $t->assign('logbook_divetime', $_lang['logbook_divetime'] );
	    $t->assign('logbook_depth',$_lang['logbook_depth'] );

        if ($result[0]['Divedate'] == "") {
            $t->assign('dive_date','-');	
        } else {
            $t->assign('dive_date', date($_lang['logbook_divedate_format'], strtotime($result[0]['Divedate'])) );
        }
        if ($result[0]['Entrytime'] == "") {
            $t->assign('entry_time','-');	
        } else {
            $t->assign('entry_time',$result[0]['Entrytime'] );
        }
        if ($result[0]['Divetime'] == "") {
            $t->assign('dive_time','-');	
        } else {
            $t->assign('dive_time', $result[0]['Divetime'] ."&nbsp;". $_lang['unit_time'] );
        }
        if ($result[0]['Depth'] == "") {
            $t->assign('dive_depth','-');	
        } else {
            if ($_config['length']) {
                $t->assign('dive_depth', MetreToFeet($result[0]['Depth'], 0) ."&nbsp;". $_lang['unit_length_imp'] );
            } else {
                $t->assign('dive_depth', $result[0]['Depth'] ."&nbsp;". $_lang['unit_length']);
            }
        }

        //	Show dive location details
        $t->assign('logbook_place', $_lang['logbook_place'] );
        $t->assign('logbook_city', $_lang['logbook_city'] );
        $t->assign('logbook_country', $_lang['logbook_country'] );
        if (!empty($result[0]['PlaceID'])) {
            $t->assign('dive_site_nr', $result[0]['PlaceID'] );
            $t->assign('dive_place', $result[0]['Place'] );
            $t->assign('logbook_place_linktitle', $_lang['logbook_place_linktitle']);
        }
        if (!empty($result[0]['City'])){
            $t->assign('dive_city', $result[0]['City'] );
        }
        if (!empty($result[0]['Country'])){
            $t->assign('dive_country',$result[0]['Country']);
        }/*}}}*/
    }

    /**
     * set_buddy_details sets the buddy info
     * 
     * @access public
     * @return void
     */
    function set_buddy_details(){
        global $t, $_lang;/*{{{*/
	    $result =  $this->result; 
        if ($result[0]['Buddy'] != "") {
            $t->assign('buddy', $result[0]['Buddy'] );
            $t->assign('logbook_buddy', $_lang['logbook_buddy'] );
        }/*}}}*/
    }

    /**
     * set_dive_pictures formats the links to the pictures if available
     * 
     * @access public
     * @return void
     */
    function set_dive_pictures(){
        global $_config,$t, $_lang, $globals;/*{{{*/
        $result =  $this->result; 
        $pic_class = new DivePictures;
        $pic_class->set_divegallery_info_direct($this->user_id);
        $pic_class->get_divegallery_info($result[0]['ID']);
        $divepics = $pic_class->get_image_link();
        $pics = count($divepics);

        if ($pics != 0) {
            if(isset($_config['divepics_preview'])){
                $t->assign('pics2' , '1');
                $t->assign('image_link', $divepics);
            } else {
                $t->assign('pics' , '1');
                set_config_table_prefix($this->table_prefix);
                $globals['logid'] = $result[0]['ID'];
                $divepics = parse_mysql_query('divepics.sql');
                reset_config_table_prefix();
                
                $t->assign('picpath_web', $_config['web_root']."/".$_config['picpath_web'] . $divepics[0]['Path']);
                $t->assign('divepic_linktit', $_lang['divepic_linktitle_pt1']. "1". $_lang['divepic_linktitle_pt2']. $pics. $_lang['divepic_linktitle_pt3']. $result[0]['Number']);
                $divepic_pt =  $_lang['divepic_pt1']. $pics;
                if ($pics == 1) {
                    $divepic_pt .= $_lang['divepic_pt2s'];
                } else {
                    $divepic_pt .= $_lang['divepic_pt2p'];
                }
                $divepic_pt .= $_lang['divepic_pt3'];
                $t->assign('divepic_pt', $divepic_pt);
                $image_link = array();
                for($i=1; $i<$pics; $i++) {
                    $image_link[$i-1] =  "<a href=\"". $_config['web_root']."/". $_config['picpath_web'] . $divepics[$i]['Path'];
                    $image_link[$i-1] .= "\" rel=\"lightbox[others]\"\n";
                    $image_link[$i-1] .= "   title=\"". $_lang['divepic_linktitle_pt1']. ($i + 1). $_lang['divepic_linktitle_pt2']. $pics;
                    $image_link[$i-1] .= $_lang['divepic_linktitle_pt3']. $result[0]['Number'] ."\"></a>\n";
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
    function set_dive_profile(){
        global $t, $_lang, $globals, $_config;/*{{{*/
        $result =  $this->result; 
        $profile = $result[0]['Profile'];
        if($profile && $_config['show_profile'] == true) {
            $t->assign('profile','1');
            $t->assign('get_nr',$this->dive_nr);
            $t->assign('dive_profile_title', $_lang['dive_profile_title'] . $result[0]['Number'] );
            $t->assign('dive_profile_title', $_lang['dive_profile_title'] . $result[0]['Number'] );
        }/*}}}*/
    }

    /**
     * set_dive_conditions 
     * 
     * @access public
     * @return void
     */
    function set_dive_conditions(){
        global $t, $_config, $_lang, $globals;/*{{{*/
        $result =  $this->result; 
        $t->assign('dive_sect_conditions', $_lang['dive_sect_conditions']);

        //	Show weather conditions
        $t->assign('logbook_weather', $_lang['logbook_weather'] );
        $t->assign('logbook_visibility', $_lang['logbook_visibility']);
        $t->assign('logbook_altitude', $_lang['logbook_altitude']);
        $t->assign('logbook_airtemp', $_lang['logbook_airtemp'] );


        if ($result[0]['Weather'] != "") {
            $t->assign('Weather', $result[0]['Weather']);
        }
        if ( $result[0]['Visibility'] != 0 && $_lang['visibility'][($result[0]['Visibility'] - 1)]) {
            $t->assign('Visibility', $_lang['visibility'][($result[0]['Visibility'] - 1)]);
        }
        if ($result[0]['Altitude'] != "") {
            $t->assign('Altitude', $result[0]['Altitude'] );
        }
        if ($result[0]['Airtemp'] != "") {

            if ($_config['temp']) {
                $Airtemp = CelsiusToFahrenh($result[0]['Airtemp'], 0) ."&nbsp;". $_lang['unit_temp_imp'] ;
            } else {
                $Airtemp = $result[0]['Airtemp'] ."&nbsp;". $_lang['unit_temp'] ;
            }
            $t->assign('Airtemp',$Airtemp);
        }

        //	Show water conditions
        $t->assign('logbook_water', $_lang['logbook_water']);
        $t->assign('logbook_surface', $_lang['logbook_surface']);
        $t->assign('logbook_uwcurrent', $_lang['logbook_uwcurrent']);
        $t->assign('logbook_watertemp', $_lang['logbook_watertemp']);

        if ( $result[0]['Water'] != 0 && $_lang['water'][($result[0]['Water'] - 1)]) {
            $t->assign('Water', $_lang['water'][($result[0]['Water'] - 1)]);
        }

        if ($result[0]['Surface'] != "") {
            $t->assign('Surface',$result[0]['Surface'] );
        }
        if ($result[0]['UWCurrent'] != "") {
            $t->assign('UWCurrent',$result[0]['UWCurrent'] );
        }
        if ($result[0]['Watertemp'] != "") 
        {
            if ($_config['temp']) {
                $Watertemp = CelsiusToFahrenh($result[0]['Watertemp'], 0) ."&nbsp;". $_lang['unit_temp_imp'] ;
            } else {
                $Watertemp = $result[0]['Watertemp'] ."&nbsp;". $_lang['unit_temp'] ;
            }
            $t->assign('Watertemp', $Watertemp);
        }/*}}}*/
    }

    /**
     * set_breathing_details 
     * 
     * @access public
     * @return void
     */ 
    function set_breathing_details(){
        global $t,$_config, $_lang, $globals;/*{{{*/
        $result =  $this->result; 
        // Breathing
        $t->assign('dive_sect_breathing', $_lang['dive_sect_breathing'] );
        //	Show tank details
        $t->assign('logbook_tanktype', $_lang['logbook_tanktype'] );
        $t->assign('logbook_tanksize', $_lang['logbook_tanksize'] );
        $t->assign('logbook_gas', $_lang['logbook_gas'] );
        $t->assign('logbook_avgdepth', $_lang['logbook_avgdepth'] );

        if (isset($result[0]['Tanktype'] ) ) {
            $arr_number = $result[0]['Tanktype'] - 1;
            if($arr_number >= 0)
                $t->assign('Tanktype', $_lang['tanktype'][$arr_number]);
        }

        if ($result[0]['Tanksize'] != "") {
            if ($_config['volume']) {
                $Tanksize =  LitreToCuft($result[0]['Tanksize'], 0) ."&nbsp;". $_lang['unit_volume_imp'] ;
            } else {
                $Tanksize = $result[0]['Tanksize'] ." ". $_lang['unit_volume'] ;
            }
            $t->assign('Tanksize',$Tanksize);
        }
        if ($result[0]['Gas'] != "") {
            $t->assign('Gas', $result[0]['Gas'] );
        }
        if ($this->averagedepth != "") {
            if($_config['length']){
                $avg_depth = MetreToFeet($this->averagedepth);
                if($avg_depth != '-'){
                    $t->assign('unit_length_short', $_lang['unit_length_short_imp']);
                }
            } else {
                $avg_depth = $this->averagedepth;
                if($avg_depth != '-'){
                    $t->assign('unit_length_short', $_lang['unit_length_short']);
                }
            }
            $t->assign('averagedepth', $avg_depth ) ;

        }

        //	Show pressure details
        $t->assign('logbook_press', $_lang['logbook_press']);
        $t->assign('logbook_prese', $_lang['logbook_prese']);
        $t->assign('logbook_presdiff', $_lang['logbook_presdiff']);
        $t->assign('logbook_sac', $_lang['logbook_sac'] );

        if ($result[0]['PresS'] != "") {
            if ($_config['pressure']) {
                $PresS = BarToPsi($result[0]['PresS'], -1) ."&nbsp;". $_lang['unit_pressure_imp'] ;
            } else {
                $PresS = $result[0]['PresS'] ."&nbsp;". $_lang['unit_pressure'] ;
            }
            $t->assign('PresS', $PresS);
        }
        if ($result[0]['PresE'] != "") {
            if ($_config['pressure']) {
                $PresE =  BarToPsi($result[0]['PresE'], -1) ."&nbsp;". $_lang['unit_pressure_imp'];
            } else {
                $PresE =  $result[0]['PresE'] ."&nbsp;". $_lang['unit_pressure'] ;
            }
            $t->assign('PresE' ,$PresE);
        }
        if (($result[0]['PresS'] != "") || ($result[0]['PresE'] != "")) {
            $diff = intval($result[0]['PresS']) - intval($result[0]['PresE']);
            if ($_config['pressure']) {
                $PresSPresE =  BarToPsi($diff, -1) ."&nbsp;". $_lang['unit_pressure_imp'] ;
            } else {
                $PresSPresE = $diff ."&nbsp;". $_lang['unit_pressure'] ;
            }
            $t->assign('PresSPresE', $PresSPresE);
        }
        if ($this->sac != "") {
            $t->assign('sac', $this->sac ); 
        }/*}}}*/
    }
    
    /**
     * set_dive_details 
     * 
     * @access public
     * @return void
     */
    function set_dive_details(){
        global $t, $_lang, $globals;/*{{{*/
        $result =  $this->result; 
        // Dive Details
        $t->assign('dive_sect_details', $_lang['dive_sect_details'] );

        $t->assign('logbook_entry', $_lang['logbook_entry'] );
        $t->assign('logbook_deco', $_lang['logbook_deco'] );
        $t->assign('logbook_rep', $_lang['logbook_rep'] );
        $t->assign('logbook_surfint', $_lang['logbook_surfint'] );

        if ($_lang['entry'][($result[0]['Entry'] - 1)]) {
            $t->assign('Entry', $_lang['entry'][($result[0]['Entry'] - 1)]);
        }

        $t->assign('Deco', ($result[0]['Deco'] == 'True' ? $_lang['yes'] : $_lang['no']) );
        $t->assign('Rep', ($result[0]['Rep'] == 'True' ? $_lang['yes'] : $_lang['no']) );
        if ($result[0]['Surfint'] != "") {
            $t->assign('Surfint', $result[0]['Surfint'] );
        }

        if ($result[0]['Decostops']) {
            $t->assign('Decostops','1');
            $t->assign('logbook_decostops', $_lang['logbook_decostops'] );

            $r = $result[0]['Decostops'];
            $r = str_replace("\r\n", "<br>\n", $r);
            $t->assign('stops', $r);
        }/*}}}*/
    }

    /**
     * set_equipment 
     * 
     * @access public
     * @return void
     */
    function set_equipment(){
        global $t,$_config, $_lang, $globals;/*{{{*/
        $result =  $this->result; 
        $t->assign('dive_sect_equipment', $_lang['dive_sect_equipment'] );
        $t->assign('logbook_weight', $_lang['logbook_weight'] );
        $t->assign('logbook_divesuit', $_lang['logbook_divesuit'] );
        $t->assign('logbook_computer', $_lang['logbook_computer'] );

        if ($result[0]['Weight'] != "") {
            if ($_config['weight']) {
                $Weight = KgToLbs($result[0]['Weight'], 0) ."&nbsp;". $_lang['unit_weight_imp'] ;
            } else {
                $Weight = $result[0]['Weight'] ."&nbsp;". $_lang['unit_weight'] ;
            }$t->assign('Weight' ,$Weight);
        }
        if ($result[0]['Divesuit'] != "") {
            $t->assign('Divesuit', $result[0]['Divesuit'] );
        }
        if ($result[0]['Computer'] != "") {
            $t->assign('Computer', $result[0]['Computer'] );
        }
        if ($result[0]['UsedEquip'] != "") {
            $t->assign('UsedEquip',1);
            $t->assign('logbook_usedequip', $_lang['logbook_usedequip'] );
            $globals['gearlist'] = $result[0]['UsedEquip'];
            set_config_table_prefix($this->table_prefix);
            $divegear = parse_mysql_query('divegearlist.sql');
            reset_config_table_prefix();
            $num_equip = count($divegear);
            for($i=0; $i<$num_equip; $i++) {
                if($this->multiuser){
                    $equip_link[$i] = '<a href="'.$_config['web_root'].'/equipment.php/'.$this->user_id.'/'. $divegear[$i]['ID'] .'" title="'. $divegear[$i]['Object'] .' ';
                } else {
                    $equip_link[$i] = '<a href="'.$_config['web_root'].'/equipment.php/'. $divegear[$i]['ID'] .'" title="'. $divegear[$i]['Object'] .' ';
                }
                $equip_link[$i] .=   $_lang['logbook_place_linktitle'] .'">';
                $equip_link[$i] .= $divegear[$i]['Object'] ."</a>";
            }
            $t->assign('equip_link', $equip_link);
        }/*}}}*/
    }

    /**
     * set_comments 
     * 
     * @access public
     * @return void
     */
    function set_comments(){
        global $t, $_lang, $globals;/*{{{*/
        $result =  $this->result; 
        //	Show them if we have them
        if ($result[0]['Comments'] != "") {

            $t->assign('dive_sect_comments', $_lang['dive_sect_comments']);
            /*		
                    $r = new rtf( $result[0]['Comments']);
                    $r->output("html");
                    $r->parse();
                    if( count( $r->err) == 0) {
                    $find = '<div align="left">';
                    $str = str_replace ($find, '', strip_tags($r->out, '<div>'));
                    $str = str_replace ('</div>', '<br />', $str);
                    echo $str;
                    }
             */
            $r = $result[0]['Comments'];
            $r = str_replace("\r\n", "<br>\n", $r);
            $t->assign('Comments', $r);
        }/*}}}*/
    }
    
    /**
     * get_dive_overview returns the dive overview according the defined view preference 
     * 
     * @access public
     * @return void
     */
    function get_dive_overview(){
        global $t, $_lang, $globals, $_config;/*{{{*/

        /**
         * When view_type = 1 display the ajax grid if type = 2 display old fashioned table 
         */
        if($_config['view_type'] == 1){
            $this->get_dive_overview_grid();
        }
        elseif($_config['view_type'] == 2){
            $this->get_dive_overview_table();
        }
        else{
            echo 'no view_type defined!';
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
    function get_dive_overview_table(){
        global $db, $t, $_lang, $globals, $_config;/*{{{*/
        set_config_table_prefix($this->table_prefix);
        //    Get the details of the dives to be listed
        //$recentdivelist = parse_mysql_query('recentdivelist.sql');
        if ($_config['length']){
            $recentdivelist_query = sql_file('recentdivelist-imp.sql');
        } else {
            $recentdivelist_query = sql_file('recentdivelist.sql');
        }
            $recentdivelist_query .=  " ORDER BY Number DESC";
        reset_config_table_prefix();
		$t->assign('dlog_title_number', $_lang['dlog_title_number'] );
		$t->assign('dlog_title_divedate', $_lang['dlog_title_divedate']);
		$t->assign('dlog_title_depth', $_lang['dlog_title_depth'] );
		$t->assign('dlog_title_divetime', $_lang['dlog_title_divetime'] );
		$t->assign('dlog_title_location', $_lang['dlog_title_location'] );

        if(!empty($this->multiuser)){
            $path = $_config['web_root'].'/index.php/'.$this->user_id.'/list';
        } else {
            $path = $_config['web_root'].'/index.php/list';
        }
        if(empty($this->requested_page)){
            $cpage = 0;
        } else {
            $cpage = $this->requested_page;
        }
        $pager_options = new TablePager($cpage,$path);
        $paged_data = Pager_Wrapper_MDB2($db, $recentdivelist_query, $pager_options->options);
        $t->assign('dlog_number_title', $_lang['dlog_number_title']);
        if($_config['length']){
            $t->assign('unit_length_short' , $_lang['unit_length_short_imp']);
        } else {
            $t->assign('unit_length_short' , $_lang['unit_length_short']);
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
    function get_dive_overview_grid(){
        global $t, $_lang, $globals, $_config;/*{{{*/
        $GridClass = new TableGrid($this->user_id);
        $objGrid = $GridClass->get_grid_class();
        $objGrid -> tabla ($this->table_prefix."Logbook");
        set_config_table_prefix($this->table_prefix);
        if ($_config['length']){
            $recentdivelist_query = sql_file('recentdivelist-imp.sql');
        } else {
            $recentdivelist_query = sql_file('recentdivelist.sql');
        }
        reset_config_table_prefix();
        if($this->multiuser){
            $url =  "/index.php".$t->get_template_vars('sep1').$this->user_id.$t->get_template_vars('sep2');
        } else {
            $url =  "/index.php".$t->get_template_vars('sep2');
        }
        $objGrid -> keyfield("Number"); 
        $t->assign('grid_header' , $objGrid -> getHeader(NULL, $_config['abs_url_path']. '/js/dgscripts.js', $_config['abs_url_path']. '/includes/dgstyle.css'));
        //        $objGrid -> paginationmode('links');
        $objGrid->message['display'] = $_lang['display_rows_dives'];
        $objGrid -> orderby("Number", "DESC"); 
        //Total width should be 700px 5+100+80+250+400
        if($this->multiuser){
            $objGrid -> FormatColumn("Number", $_lang['dlog_title_number'], 5, 5, 5, "20", "center","link:open_url(%s\,'$url'),Number"); 
        } else{
            $objGrid -> FormatColumn("Number", $_lang['dlog_title_number'], 5, 5, 5, "20", "center","link:open_url(%s\,'$url'),Number"); 

        }
        $objGrid -> FormatColumn("Divedate", $_lang['dlog_title_divedate'], 2, 4, 0, "60", "center", "date:dmy:-" ); 
        if($_config['length']){
            $objGrid -> FormatColumn("Depth", $_lang['dlog_title_depth'], 12, 12, 0, "60", "center","sign:".$_lang['unit_length_short_imp']  );
        } else {
            $objGrid -> FormatColumn("Depth", $_lang['dlog_title_depth'], 12, 12, 0, "60", "center","sign:".$_lang['unit_length_short']  );
        }
        $objGrid -> FormatColumn("Divetime", $_lang['dlog_title_divetime'], 2, 4, 0, "60", "center","sign:".$_lang['unit_time_short'] );
        $objGrid -> FormatColumn("Place", $_lang['dlog_title_place'], 180, 100, 0, "300", "left"); 
        $objGrid -> FormatColumn("City", $_lang['dlog_title_location'], 180, 100, 0, "184", "left"); 
        //$objGrid -> FormatColumn("image_1","Img", "25", "0", "3","20","center","image:images/photo_icon.gif");    
  
        $objGrid->sqlstatement($recentdivelist_query);
        
        $grid = $GridClass->get_grid($objGrid);
        $t->assign('grid_display' ,1);
        $t->assign('grid',$grid );
/*}}}*/
    }

    /**
     * get_overview_divers 
     * 
     * @access public
     * @return void
     */
    function get_overview_divers(){
        global $t, $_lang, $globals, $_config;/*{{{*/
        $users = new Users();
        $user_list = $users->get_user_data();
        $t->assign('diver_overview',1);
        $t->assign('divers', $user_list);
        $t->assign('file_name','index.php'); /*}}}*/
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
    var $multiuser;/*{{{*/
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
    var $request_type; // request_type = 0 overview request_type = 1 details

    /**
     * Divesite default constructor sets some defaults for this class
     * 
     * @access public
     * @return void
     */
    function Divesite(){
        global $_config;
        $this->multiuser = $_config['multiuser'];
    }

    function get_request_type(){
        return $this->request_type;
    }
    function set_divesite_info($request){
        //We need to extract the info from the request/*{{{*/
        if(!$request->diver_choice){
            if($request->get_view_request() == 1){
                $this->request_type = 1;
                $this->divesite_nr = $request->get_site_nr();
            } else {
                $this->request_type = 0;
                $this->requested_page = $request->get_requested_page();
            }
            if($this->multiuser){
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
    function get_divesite_info(){
        global $globals, $_config;/*{{{*/
        if(!empty($this->divesite_nr)){
            $this->request_type = 1;
            $globals['placeid'] = $this->divesite_nr;
            set_config_table_prefix($this->table_prefix);
            $this->result = parse_mysql_query('oneplace.sql');
            $this->result_countrycity = parse_mysql_query('countrycity.sql');
            reset_config_table_prefix();
        } else {
            /**
             * If the request type is not already set(by divers choice), set it to overview  
             */
            if($this->request_type != 3){
                $this->request_type = 0;
            }
        }
        return $this->result;/*}}}*/
    }

    function get_overview_divers(){
        global $t, $_lang, $globals, $_config;/*{{{*/
        $users = new Users();
        $user_list = $users->get_user_data();
        $t->assign('diver_overview',1);
        $t->assign('divers', $user_list);
        $t->assign('file_name','divesite.php'); /*}}}*/
    }
    /**
     * get_divesite_location_details 
     * 
     * @access public
     * @return void
     */
    function get_divesite_location_details(){
        global $globals, $_config;/*{{{*/
        $countrycity = $this->result_countrycity;
        set_config_table_prefix($this->table_prefix);
        if (count($countrycity) != 0) {
            if ($countrycity[0]['Country'] != "") {
                //			Get the country details from database
                $globals['countryid'] = $countrycity[0]['CountryID'];
                $countrydetails = parse_mysql_query('onecountry.sql');
                if (count($countrydetails) == 0) {
                    $this->country = $countrycity[0]['Country'];
                } else {
                    $this->country = $countrydetails[0]['Country'];
                }
            }

            if ($countrycity[0]['City'] != "") {
                //			Get the city details from database
                $globals['cityid'] = $countrycity[0]['CityID'];
                $citydetails = parse_mysql_query('onecity.sql');
                if (count($citydetails) == 0) {
                    $this->city = $countrycity[0]['City'];
                } else {
                    $this->city = $citydetails[0]['City'];
                }
            }
            reset_config_table_prefix();

        }/*}}}*/
    }

    /**
     * get_dives_at_location 
     * 
     * @access public
     * @return void
     */
    function get_dives_at_location(){
        global $globals, $_config;/*{{{*/
        //	Get the dives at this site from database
        $globals['placeid'] = $this->divesite_nr;
        set_config_table_prefix($this->table_prefix);
        $this->dives = parse_mysql_query('divelocations.sql');
        $this->dive_count = count($this->dives);
        //	Get the site list from database
        $this->sitelist = parse_mysql_query('sitelist.sql');
        reset_config_table_prefix();
        /*}}}*/
    }

    /**
     * set_main_divesite_details 
     * 
     * @access public
     * @return void
     */
    function set_main_divesite_details(){
        global $globals, $_config, $t , $_lang;/*{{{*/
        $this->get_divesite_location_details(); 
        //	Show main site details
        $result = $this->result;
        $t->assign('pagetitle',$_lang['dive_site_pagetitle'].$result[0]['Place']);
        $t->assign('divesite_id', $this->divesite_nr);
        $t->assign('place_place',   $_lang['place_place']);
        $t->assign('place_city',    $_lang['place_city']);
        $t->assign('place_country', $_lang['place_country']);
        $t->assign('place_maxdepth',$_lang['place_maxdepth'] );

        if ($result[0]['Place'] == "") {
            $Place = "-";
        } else {
            $Place = $result[0]['Place'];
        }
        $t->assign('Place',$Place);
        $t->assign('city', $this->city );
        $t->assign('country', $this->country);
        if ($result[0]['MaxDepth'] == "") {
            $MaxDepth = "-";
        } else {
            if ($_config['length']) {
                $MaxDepth = MetreToFeet($result[0]['MaxDepth'], 0) ."&nbsp;". $_lang['unit_length_short_imp'] ;
            } else {
                $MaxDepth = $result[0]['MaxDepth'] ."&nbsp;". $_lang['unit_length_short'];
            }
        }
        $t->assign('MaxDepth',$MaxDepth);

        //	Show extra site details
        $t->assign('place_lat', $_lang['place_lat']);
        $t->assign('place_lon', $_lang['place_lon']);
        if ($result[0]['MapPath'] == "") {
            $MapPath = "&nbsp;";
        } else {
            $MapPath = $_lang['place_map'];
        }
        $t->assign('place_map', $MapPath);

        if ($result[0]['Lat'] == "") {
            $Lat = "-";
        } else {
            $Lat = latitude_format($result[0]['Lat']);
        }
        $t->assign('Lat', $Lat);
        $t->assign('LatDec',$result[0]['Lat']); 

        if ($result[0]['Lon'] == "") {
            $Lon = "-";
        } else {
            $Lon = longitude_format($result[0]['Lon']) ;
        }
        $t->assign('Lon',$Lon);
        $t->assign('LonDec',$result[0]['Lon']);
        if (($result[0]['Lat'] != "") || (($result[0]['Lon'] != ""))) {
            $t->assign('site_google_link', $_lang['site_google_link'] .$result[0]['Place']);
        }
        if ($result[0]['MapPath'] == "") {
            $t->assign('maplink_url','&nbsp;');
        } else {
            $maplink_url = "<a href=\"".$_config['web_root']."/". $_config['mappath_web'] . $result[0]['MapPath']."\"  rel=\"lightbox[others]\"\n";
            $maplink_url .=  "   title=\"". $_lang['mappic_linktitle']. $result[0]['Place'];
            $maplink_url .=  "\">". $_lang['mappic'] ."</a>\n";
            $t->assign('maplink_url',$maplink_url);
        }/*}}}*/
    }

    /**
     * set_dives_at_location 
     * 
     * @access public
     * @return void
     */
    function set_dives_at_location(){
        global $globals, $_config, $t, $_lang;/*{{{*/
        $this->get_dives_at_location();
        //	Show site dives if we have them
        $dives = $this->dives;
        if ($this->dive_count != 0) {
            $t->assign('dive_count', $this->dive_count);
            if ($this->dive_count == 1) {
                $t->assign('site_dive_trans', $_lang['site_dive_single']);
            } else {
                $t->assign('site_dive_trans', $_lang['site_dive_plural']);
            }
            for($i=0; $i<$this->dive_count; $i++) {
                $dives[$i] = $dives[$i]['Number'] ; 
            }
            $t->assign('dlog_number_title', $_lang['dlog_number_title'] );
            $t->assign('dives',$dives);
        }/*}}}*/
    }

    /**
     * set_divesite_comments 
     * 
     * @access public
     * @return void
     */
    function set_divesite_comments(){
        global $globals, $_config, $t,$_lang;/*{{{*/
        //	Comments
        $result = $this->result;
        //	Show them if we have them
        if ($result[0]['Comments'] != "") {
            $t->assign('site_sect_comments', $_lang['site_sect_comments']);
            $r = $result[0]['Comments'];
            $r = str_replace("\r\n", "<br>\n", $r);

            //		Handle Google Map URL
            $r = str_replace('[url]','<a href="',$r);
            $r = str_replace('[/url]','" target="_blank" title="'. $_lang['site_google_link']. $result[0]['Place'] .'">'. $_lang['site_google_link'] . $result[0]['Place'] .'</a>',$r);

            $t->assign('Comments', $r);
        }
    }/*}}}*/

    /**
     * get_divesite_overview 
     * 
     * @access public
     * @return void
     */
    function get_divesite_overview(){
        global $t, $_lang, $globals, $_config;/*{{{*/
        $placetable = $this->table_prefix."Place";
        $logbooktable = $this->table_prefix."Logbook";
        
        if($_config["length"]){
            $maxdep = "ROUND($placetable.MaxDepth*3.2808,1) as MaxDepth";
        } else {
             $maxdep = "$placetable.MaxDepth as MaxDepth";
        }
       
        $sql = "SELECT  $placetable.ID AS ID, 
                    $logbooktable.Country AS Country, 
                    $placetable.Place AS Place, 
                    $logbooktable.City AS City,
                    $maxdep
                    FROM $placetable INNER JOIN $logbooktable ON $placetable.ID = $logbooktable.PlaceID
                    GROUP BY ID";

        /**
         * When view_type = 1 display the ajax grid if type = 2 display old fashioned table 
         */
        if($_config['view_type'] == 1){
            $this->get_divesite_overview_grid($sql);
        }
        elseif($_config['view_type'] == 2){
            $this->get_divesite_overview_table($sql);
        }
        else{
            echo 'no view_type defined!';
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
    function get_divesite_overview_table($sql){
        global $db, $t, $_lang, $globals, $_config;/*{{{*/
        set_config_table_prefix($this->table_prefix);
        //    Get the page header
        //    Get the details of the locations to be listed
        $locationlist_query = $sql." ORDER BY Place";
		$t->assign('dsite_title_place',   $_lang['dsite_title_place']);
		$t->assign('dsite_title_city',    $_lang['dsite_title_city']);
		$t->assign('dsite_title_country', $_lang['dsite_title_country']);
		$t->assign('dsite_title_maxdepth', $_lang['dsite_title_maxdepth']);
        if($this->multiuser == 1){
            $path = $_config['web_root'].'/divesite.php/'.$this->user_id.'/list';
        } else {
            $path = $_config['web_root'].'/divesite.php/list';
        }
        if(empty($this->requested_page)){
            $cpage = 0;
        } else {
            $cpage = $this->requested_page;
        }
        $pager_options = new TablePager($cpage,$path);
        $paged_data = Pager_Wrapper_MDB2($db, $locationlist_query, $pager_options->options);
        $t->assign('dsite_title_place',   $_lang['dsite_title_place']);
            $t->assign('dsite_title_city',    $_lang['dsite_title_city']);
            $t->assign('dsite_title_country', $_lang['dsite_title_country']);
            $t->assign('dsite_title_maxdepth', $_lang['dsite_title_maxdepth']);
            $t->assign('pages', $paged_data['links']);
            $t->assign('cells', $paged_data['data']);/*}}}*/
        }
        /**
         * get_divesite_overview_grid 
         * 
         * @param mixed $sql 
         * @access public
         * @return void
         */
        function get_divesite_overview_grid($sql){
            global $t, $_lang, $globals, $_config;/*{{{*/
            $GridClass = new TableGrid($this->user_id);
            $objGrid = $GridClass->get_grid_class();

            /**
             * Define the table according some info 
             */
            $objGrid -> tabla ($this->table_prefix."Place");
            if($this->multiuser){
                $url =  "/divesite.php".$t->get_template_vars('sep1').$this->user_id.$t->get_template_vars('sep2');
            } else {
                $url =  "/divesite.php".$t->get_template_vars('sep2');
            }
            $objGrid -> keyfield("ID"); 
            $t->assign('grid_header' , $objGrid -> getHeader(NULL, $_config['abs_url_path']. '/js/dgscripts.js', $_config['abs_url_path']. '/includes/dgstyle.css'));
            $objGrid -> orderby("Place", "ASC"); 
            $objGrid->message['display'] = $_lang['display_rows_divesites'];
            $objGrid->sqlstatement($sql);
            if($this->multiuser){
                $objGrid -> FormatColumn("Place", $_lang['dsite_title_place'], 0, 0, 1,"250" , "left","link:open_url(%s\,'$url'),ID");  
            } else{
                $objGrid -> FormatColumn("Place", $_lang['dsite_title_place'], 0, 0, 1,"250" , "left","link:open_url(%s\,'$url'),ID"); 
            }
            $objGrid -> FormatColumn("Country", $_lang['dsite_title_country'], 180, 100, 4, "100", "left" ); 
            $objGrid -> FormatColumn("City", $_lang['dsite_title_city'], 180, 100, 4, "200", "left" ); 
            $objGrid -> FormatColumn("MaxDepth", $_lang['dsite_title_maxdepth'], 12, 12, 0, "80", "left","sign:".$_lang['unit_length_short']  );
            $grid = $GridClass->get_grid($objGrid);
            $t->assign('grid_display' ,1);
            $t->assign('grid',$grid );
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
    var $request_type; // request_type = 0 overview request_type = 1 details
   
    function Equipment(){
        global $_config;
        $this->multiuser = $_config['multiuser'];
    }
    
    function get_request_type(){
        return $this->request_type;
    }

    function get_equipment_nr(){
        return $this->equipment_nr;
    }
    
    /**
     * set_equipment_info 
     * 
     * @param mixed $request 
     * @access public
     * @return void
     */
    function set_equipment_info($request){
        //We need to extract the info from the request/*{{{*/
        if(!$request->diver_choice){
            //Find request type
            if($request->get_view_request() == 1){
                $this->request_type = 1;
                $this->equipment_nr = $request->get_equipment_nr();
            } else {
                $this->request_type = 0;
                $this->requested_page = $request->get_requested_page();
            }
            if($this->multiuser){
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
    function get_equipment_info(){
        global $_config,  $globals;/*{{{*/
        if(!empty($this->equipment_nr)){
            $this->request_type = 1;
            $globals['gear'] = $this->equipment_nr;
            set_config_table_prefix($this->table_prefix);
            $this->result = parse_mysql_query('oneequipment.sql');
            $this->result_gearlist = parse_mysql_query('gearlist.sql');
            reset_config_table_prefix();
        } else {
           /**
             * If the request type is not already set(by divers choice), set it to overview  
             */
            if($this->request_type != 3){
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
    function get_overview_divers(){
        global $t, $_lang, $globals, $_config;/*{{{*/
        $users = new Users();
        $user_list = $users->get_user_data();
        $t->assign('diver_overview',1);
        $t->assign('divers', $user_list);
        $t->assign('file_name','equipment.php'); /*}}}*/
    }

    function set_main_equipment_details(){
        global $t, $_config,$globals,  $_lang;/*{{{*/
	    $result =  $this->result; 
        $t->assign('pagetitle',$_lang['equip_details_pagetitle'].$result[0]['Object']);
        $t->assign('equip_object',$_lang['equip_object'] );
        $t->assign('equip_manufacturer', $_lang['equip_manufacturer']);
        $t->assign('equip_shop', $_lang['equip_shop'] );
        $t->assign('equip_datep',$_lang['equip_datep']);
        $t->assign('equip_price',$_lang['equip_price']);

        //	Show main equipment details
        $t->assign('Object',$result[0]['Object'] );
        $t->assign('Manufacturer', $result[0]['Manufacturer']);

        //	Show equipment purchase details
        $t->assign('Shop',$result[0]['Shop']);

        if ($result[0]['DateP'] != "") {
            $t->assign('DateP', date($_lang['equip_date_format'], strtotime($result[0]['DateP'])));
        }
        if ($result[0]['Price'] != "") {
            $t->assign('Price',$_lang['currency_prefix'] .number_format($result[0]['Price'],2) .$_lang['currency_suffix'] );
        }

        //	Show the rest of the details
        $t->assign('equip_serial',$_lang['equip_serial'] );
        $t->assign('equip_warranty',$_lang['equip_warranty'] );
        $t->assign('equip_dater',	$_lang['equip_dater'] );
        if ($result[0]['PhotoPath'] != "") {
            $t->assign('PhotoPath', $result[0]['PhotoPath'] );
            $t->assign('equip_photo', $_lang['equip_photo'] );
        }

        $t->assign('Serial', $result[0]['Serial']);
        $t->assign('Warranty',$result[0]['Warranty'] );

        if ($result[0]['DateR'] != "") {
            $t->assign('DateR', date($_lang['equip_date_format'], strtotime($result[0]['DateR'])) );
        }
        if ($result[0]['PhotoPath'] != "") {
            $t->assign('PhotoPathurl',  $_config['equippath_web'] . $result[0]['PhotoPath']);
            $t->assign('equip_photo_linktitle', $_lang['equip_photo_linktitle']. $result[0]['Object']);
            $t->assign('equip_photo_link', $_lang['equip_photo_link'] );
        }
/*}}}*/
    }

    /**
     * set_comments 
     * 
     * @access public
     * @return void
     */
    function set_comments(){
        global $t, $_lang, $globals;/*{{{*/
        $result =  $this->result; 
        //	Show them if we have them
        //	Show them if we have them
        if ($result[0]['Comments'] != "") {
            $t->assign('equip_sect_comments', $_lang['equip_sect_comments'] );

            $r = $result[0]['Comments'];
            $r = str_replace("\r\n", "<br>\n", $r);
            $t->assign('Comments', $r);
        }/*}}}*/
    }
    /**
     * get_equipment_overview 
     * 
     * @access public
     * @return void
     */
    function get_equipment_overview(){
        global $t, $_lang, $globals, $_config;/*{{{*/

        /**
         * When view_type = 1 display the ajax grid if type = 2 display old fashioned table 
         */
        if($_config['view_type'] == 1){
            $this->get_equipment_overview_grid();
        }
        elseif($_config['view_type'] == 2){
            $this->get_equipment_overview_table();
        }
        else{
            echo 'no view_type defined!';
        }
        $t->assign('pagetitle',$_lang['dive_equip']);
        /*}}}*/
    }

    /**
     * get_equipment_overview_table 
     * 
     * @access public
     * @return void
     */
    function get_equipment_overview_table(){
        global $db, $t, $_lang, $globals, $_config;/*{{{*/
        set_config_table_prefix($this->table_prefix);
        $equiplist_query = sql_file('equiplist.sql');
        reset_config_table_prefix();
        //    $t->assign('equip_none', $_lang['equip_none'] );
        $t->assign('equip_title_object', $_lang['equip_title_object'] );
        $t->assign('equip_title_manufacturer', $_lang['equip_title_manufacturer'] );
        $t->assign('logbook_place_linktitle', $_lang['logbook_place_linktitle'] );
        if(!empty($this->multiuser)){
            $path = $_config['web_root'].'/equipment.php/'.$this->user_id.'/list';
        } else {
            $path = $_config['web_root'].'/equipment.php/list';
        }
        if(empty($this->requested_page)){
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
    function get_equipment_overview_grid(){
        global $t, $_lang, $globals, $_config;/*{{{*/
        $GridClass = new TableGrid($this->used_id);
        $objGrid = $GridClass->get_grid_class();
        $objGrid -> tabla ($this->table_prefix."Equipment");
         if($this->multiuser){
            $url =  "/equipment.php".$t->get_template_vars('sep1'). $this->user_id.$t->get_template_vars('sep2');
        } else {
            $url =  "/equipment.php".$t->get_template_vars('sep2');
        }
         $objGrid->message['display'] = $_lang['display_rows_equipment'];
         $objGrid -> keyfield("ID");
       $t->assign('grid_header' , $objGrid -> getHeader(NULL, $_config['abs_url_path']. '/js/dgscripts.js', $_config['abs_url_path']. '/includes/dgstyle.css'));
        $objGrid -> orderby("Manufacturer", "ASC"); 
        //        $objGrid -> FormatColumn("ID", $_lang['equip_title_object'], 5, 5, 5, "5", "center","link:open_url(%s\,'$url'),ID"); 
        if($this->multiuser){
            $objGrid -> FormatColumn("Object", $_lang['equip_title_object'], 180, 100, 0, "300", "left" ,"link:open_url(%s\,'$url'),ID"); 
        }else{
            $objGrid -> FormatColumn("Object", $_lang['equip_title_object'], 180, 100, 0, "300", "left" ,"link:open_url(%s\,'$url'),ID"); 

        }
        $objGrid -> FormatColumn("Manufacturer", $_lang['equip_title_manufacturer'], 180, 100, 0, "150", "left"); 
        $grid = $GridClass->get_grid($objGrid);
        $t->assign('grid_display' ,1);
        $t->assign('grid',$grid );
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
    var $repdives;
    var $DivedateMinNr;
    var $DivedateMaxNr;
    var $DivetimeMinNr;
    var $DivetimeMaxNr;
    var $DepthMinNr;
    var $DepthMaxNr;
    var $WatertempMinNr;
    var $WatertempMaxNr;
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
    function Divestats(){
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
    function set_divestats_info($request){
        //We need to extract the info from the request/*{{{*/
        if(!$request->diver_choice){
            if($this->multiuser){
                $this->user_id = $request->get_user_id();
                $user = new User();
                $user->set_user_id($this->user_id);
                $this->table_prefix = $user->get_table_prefix();
                $this->username = $user->get_username();
            } else {
                $this->user_id = $request->get_user_id();
                $user = new User();
                //$user->set_user_id($this->user_id);
                $this->table_prefix = $user->get_table_prefix();
                $this->username = $user->get_username();
            }
        } else {
            $this->request_type = 3;
        }/*}}}*/
    }

    /**
     * get_divestats_info 
     * 
     * @access public
     * @return void
     */
    function get_divestats_info(){
        global $globals, $_config;/*{{{*/
        if(($this->multiuser && !empty($this->user_id)) || !$this->multiuser ){
            set_config_table_prefix($this->table_prefix);
            // Get number of dives
            $count = parse_mysql_query('divecount.sql');
            $this->end = $count[0]['COUNT(*)'];

            $this->divestats = parse_mysql_query('divestats.sql');
            $divestats = $this->divestats;
            // Get the number of shore dives
            $globals['stats'] = "Entry = 1";
            $divestatsother = parse_mysql_query('divestatsother.sql');
            $this->shoredives = $divestatsother[0]['Count'];

            // Get the number of boat dives
            $globals['stats'] = "Entry = 2";
            $divestatsother = parse_mysql_query('divestatsother.sql');
            $this->boatdives = $divestatsother[0]['Count'];

            // Get the number of night dives
            $globals['stats'] = "Divetype = '3' OR Divetype LIKE '%,3' OR Divetype LIKE '%,3,%' OR Divetype LIKE '3,%'";
            $divestatsother = parse_mysql_query('divestatsother.sql');
            $this->nightdives = $divestatsother[0]['Count'];

            // Get the number of drift dives
            $globals['stats'] = "Divetype = '4' OR Divetype LIKE '%,4' OR Divetype LIKE '%,4,%' OR Divetype LIKE '4,%'";
            $divestatsother = parse_mysql_query('divestatsother.sql');
            $this->driftdives = $divestatsother[0]['Count'];

            // Get the number of deep dives
            $globals['stats'] = "Divetype = '5' OR Divetype LIKE '%,5' OR Divetype LIKE '%,5,%' OR Divetype LIKE '5,%'";
            $divestatsother = parse_mysql_query('divestatsother.sql');
            $this->deepdives = $divestatsother[0]['Count'];

            // Get the number of cave dives
            $globals['stats'] = "Divetype = '6' OR Divetype LIKE '%,6' OR Divetype LIKE '%,6,%' OR Divetype LIKE '6,%'";
            $divestatsother = parse_mysql_query('divestatsother.sql');
            $this->cavedives = $divestatsother[0]['Count'];

            // Get the number of wreck dives
            $globals['stats'] = "Divetype = '7' OR Divetype LIKE '%,7' OR Divetype LIKE '%,7,%' OR Divetype LIKE '7,%'";
            $divestatsother = parse_mysql_query('divestatsother.sql');
            $this->wreckdives = $divestatsother[0]['Count'];

            // Get the number of photo dives
            $globals['stats'] = "Divetype = '8' OR Divetype LIKE '%,8' OR Divetype LIKE '%,8,%' OR Divetype LIKE '8,%'";
            $divestatsother = parse_mysql_query('divestatsother.sql');
            $this->photodives = $divestatsother[0]['Count'];

            // Get the number of saltwater dives
            $globals['stats'] = "Water = '1'";
            $divestatsother = parse_mysql_query('divestatsother.sql');
            $this->saltwaterdives = $divestatsother[0]['Count'];

            // Get the number of freshwater dives
            $globals['stats'] = "Water = '2'";
            $divestatsother = parse_mysql_query('divestatsother.sql');
            $this->freshwaterdives = $divestatsother[0]['Count'];

            // Get the number of brackish dives
            $globals['stats'] = "Water = 3";
            $divestatsother = parse_mysql_query('divestatsother.sql');
            $this->brackishdives = $divestatsother[0]['Count'];

            // Get the number of deco dives
            $globals['stats'] = "Deco = 'True'";
            $divestatsother = parse_mysql_query('divestatsother.sql');
            $this->decodives = $divestatsother[0]['Count'];

            // Get the number of rep dives
            $globals['stats'] = "Rep = 'True'";
            $divestatsother = parse_mysql_query('divestatsother.sql');
            $this->repdives = $divestatsother[0]['Count'];

            // Get dive number for first dive
            $globals['stats'] = "Divedate = '" . $divestats[0]['DivedateMin'] . "'";
            $divestatsnr = parse_mysql_query('divestatsnr.sql');
            $this->DivedateMinNr = $divestatsnr[0]['Number'];

            // Get dive number for last dive
            $globals['stats'] = "Divedate = '" . $divestats[0]['DivedateMax'] . "'";
            $divestatsnr = parse_mysql_query('divestatsnr.sql');
            $this->DivedateMaxNr = $divestatsnr[count($divestatsnr)-1]['Number'];

            // Get dive number for sortest dive
            $globals['stats'] = "Divetime = '" . $divestats[0]['DivetimeMin'] . "'";
            $divestatsnr = parse_mysql_query('divestatsnr.sql');
            $this->DivetimeMinNr = $divestatsnr[0]['Number'];

            // Get dive number for deepest dive
            $globals['stats'] = "Divetime = '" . $divestats[0]['DivetimeMax'] . "'";
            $divestatsnr = parse_mysql_query('divestatsnr.sql');
            $this->DivetimeMaxNr = $divestatsnr[0]['Number'];

            // Get dive number for shallowest dive
            $globals['stats'] = "Depth = '" . $divestats[0]['DepthMin'] . "'";
            $divestatsnr = parse_mysql_query('divestatsnr.sql');
            $this->DepthMinNr = $divestatsnr[0]['Number'];

            // Get dive number for deepest dive
            $globals['stats'] = "Depth = '" . $divestats[0]['DepthMax'] . "'";
            $divestatsnr = parse_mysql_query('divestatsnr.sql');
            $this->DepthMaxNr = $divestatsnr[0]['Number'];

            // Get dive number for coldest dive
            $globals['stats'] = "Watertemp = '" . $divestats[0]['WatertempMin'] . "'";
            $divestatsnr = parse_mysql_query('divestatsnr.sql');
            $this->WatertempMinNr = $divestatsnr[0]['Number'];

            // Get dive number for warmest dive
            $globals['stats'] = "Watertemp = '" . $divestats[0]['WatertempMax'] . "'";
            $divestatsnr = parse_mysql_query('divestatsnr.sql');
            $this->WatertempMaxNr = $divestatsnr[0]['Number'];

            // Get the number of 1st depth range dives
            $globals['stats'] = "Depth <= 18";
            $divestatsother = parse_mysql_query('divestatsother.sql');
            $this->depthrange[0] = $divestatsother[0]['Count'];
            $this->depthrange1_per = round(($this->depthrange[0] / $this->end) * 100);

            // Get the number of 2nd depth range dives
            $globals['stats'] = "Depth > 18 AND Depth <= 30";
            $divestatsother = parse_mysql_query('divestatsother.sql');
            $this->depthrange[1] = $divestatsother[0]['Count'];
            $this->depthrange2_per =  round(($this->depthrange[1] / $this->end) * 100);

            // Get the number of 3rd depth range dives
            $globals['stats'] = "Depth > 30 AND Depth <= 40";
            $divestatsother = parse_mysql_query('divestatsother.sql');
            $this->depthrange[2] = $divestatsother[0]['Count'];
            $this->depthrange3_per =  round(($this->depthrange[2] / $this->end) * 100);

            // Get the number of 4th depth range dives
            $globals['stats'] = "Depth > 40 AND Depth <= 55";
            $divestatsother = parse_mysql_query('divestatsother.sql');
            $this->depthrange[3] = $divestatsother[0]['Count'];
            $this->depthrange4_per =  round(($this->depthrange[3] / $this->end) * 100);

            // Get the number of 5th depth range dives
            $globals['stats'] = "Depth > 55";
            $divestatsother = parse_mysql_query('divestatsother.sql');
            $this->depthrange[4] = $divestatsother[0]['Count'];
            $this->depthrange5_per = round(($this->depthrange[4] / $this->end) * 100);

            $this->divecert = parse_mysql_query('brevetlist.sql');
            $this->number_cert = count($this->divecert);

            reset_config_table_prefix();
        } else {
            /**
             * If the request type is not already set(by divers choice), set it to overview  
             */
            if($this->request_type != 3){
                $this->request_type = 0;
            }
        }/*}}}*/
    }

    /**
     * get_lastdive_info 
     * 
     * @access public
     * @return void
     */
    function get_lastdive_info(){
       global $globals, $_config;/*{{{*/
        if(($this->multiuser && !empty($this->user_id)) || !$this->multiuser ){
            set_config_table_prefix($this->table_prefix);
            $lastdive = parse_mysql_query('lastdive.sql');
            $this->LastEntryTime = $lastdive[0]['Entrytime'];
            $this->LastDivePlace = $lastdive[0]['Place'];
            $this->LastDiveID= $lastdive[0]['PlaceID'];
            $this->LastCity = $lastdive[0]['City'];
            $this->LastCountry = $lastdive[0]['Country'];
            reset_config_table_prefix();
        }/*}}}*/
    }
    /**
     * set_all_statistics 
     * 
     * @access public
     * @return void
     */
    function set_all_statistics(){
        $this->set_dive_statistics();/*{{{*/
        $this->set_dive_certifications();/*}}}*/
    }

    /**
     * get_overview_divers 
     * 
     * @access public
     * @return void
     */
    function get_overview_divers(){
        global $t, $_lang, $globals, $_config;/*{{{*/
        $users = new Users();
        $user_list = $users->get_user_data();
        $t->assign('diver_overview',1);
        $t->assign('divers', $user_list);
        $t->assign('file_name','divestats.php'); /*}}}*/
    }

    /**
     * set_dive_statistics 
     * 
     * @access public
     * @return void
     */
    function set_dive_statistics(){
        global $globals, $_config, $t , $_lang;/*{{{*/
        $divestats = $this->divestats; 
        //	Dive Log, Dive Sites, Dive Statistics
        $t->assign('dive_log_linktitle', $_lang['dive_log_linktitle']);
        $t->assign('dive_log', $_lang['dive_log']);
        $t->assign('dive_sites_linktitle', $_lang['dive_sites_linktitle']);
        $t->assign('dive_sites',$_lang['dive_sites']);
        $t->assign('dive_equip_linktitle', $_lang['dive_equip_linktitle']);
        $t->assign('dive_equip',$_lang['dive_equip']);
        $t->assign('dive_stats_linktitle', $_lang['dive_stats_linktitle']);
        $t->assign('dive_stats', $_lang['dive_stats']);
        $t->assign('stats_sect_stats', $this->username .' ' .$_lang['stats_sect_stats']);
     
        // Show overall details
        $t->assign('stats_totaldives', $_lang['stats_totaldives'] );
        $t->assign('stats_divedatemax',$_lang['stats_divedatemax']);
        $t->assign('stats_divedatemin',$_lang['stats_divedatemin'] );
        $t->assign('end',$this->end );
        $t->assign('DivedateMax', date($_lang['logbook_divedate_format'], strtotime($divestats[0]['DivedateMax'])));
        $t->assign('DivedateMaxNr',$this->DivedateMaxNr);
        $t->assign('dlog_number_title',$_lang['dlog_number_title']);
        $t->assign('DivedateMin', date($_lang['logbook_divedate_format'], strtotime($divestats[0]['DivedateMin'])));
        $t->assign('DivedateMinNr', $this->DivedateMinNr);

        // Show dive length details
        $t->assign('stats_totaltime', $_lang['stats_totaltime']);
        $t->assign('stats_divetimemax', $_lang['stats_divetimemax'] );
        $t->assign('stats_divetimemin', $_lang['stats_divetimemin'] );
        $t->assign('stats_divetimeavg', $_lang['stats_divetimeavg'] );

        $total_abt = floor($divestats[0]['BottomTime']/60) .":". ($divestats[0]['BottomTime']%60) ." ". $_lang['stats_totaltime_units'] ;
        $t->assign('total_abt',$total_abt );
        $t->assign('DivetimeMax', $divestats[0]['DivetimeMax']);
        $t->assign('unit_time', $_lang['unit_time']);
        $t->assign('DivetimeMaxNr', $this->DivetimeMaxNr );
        $t->assign('DivetimeMin', $divestats[0]['DivetimeMin']);
        $t->assign('DivetimeMinNr', $this->DivetimeMinNr);
        $t->assign('DivetimeAvg', round($divestats[0]['DivetimeAvg'],0) );

        // Show dive depth details
        $t->assign('stats_depthmax', $_lang['stats_depthmax'] );
        $t->assign('stats_depthmin', $_lang['stats_depthmin'] );
        $t->assign('stats_depthavg', $_lang['stats_depthavg'] );

        if ($_config['length']) {
            $DepthMax = MetreToFeet($divestats[0]['DepthMax'], 0) ."&nbsp;". $_lang['unit_length_short_imp'];
        } else {
            $DepthMax =  $divestats[0]['DepthMax'] ."&nbsp;". $_lang['unit_length'];
        }
        $t->assign('DepthMax', $DepthMax);
        $t->assign('DepthMaxNr', $this->DepthMaxNr);

        if ($_config['length']) {
            $DepthMin =  MetreToFeet($divestats[0]['DepthMin'], 0) ."&nbsp;". $_lang['unit_length_short_imp'];
        } else {
            $DepthMin =  $divestats[0]['DepthMin'] ."&nbsp;". $_lang['unit_length'];
        }
        $t->assign('DepthMin',$DepthMin);
        $t->assign('DepthMinNr', $this->DepthMinNr );

        if ($_config['length']) {
            $DepthAvg =  MetreToFeet($divestats[0]['DepthAvg'], 0) ."&nbsp;". $_lang['unit_length_short_imp'];
        } else {
            $DepthAvg =  round($divestats[0]['DepthAvg'], 1) ."&nbsp;". $_lang['unit_length'];
        }
        $t->assign('DepthAvg', $DepthAvg);

        // Show dive depth table
        $t->assign('stats_depth1m',  $_config['length'] ? $_lang['stats_depth1i'] : $_lang['stats_depth1m']);
        $t->assign('depthrange1',$this->depthrange[0] );
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
        $t->assign('stats_decodives', $_lang['stats_decodives'] );
        $t->assign('stats_repdives',$_lang['stats_repdives'] );

        if ($_config['temp']) {
            $WatertempMin =  CelsiusToFahrenh($divestats[0]['WatertempMin'], 0) ."&nbsp;". $_lang['unit_temp_imp'];
        } else {
            $WatertempMin =  $divestats[0]['WatertempMin'] ."&nbsp;". $_lang['unit_temp'];
        }
        $t->assign('WatertempMin', $WatertempMin);
        $t->assign('WatertempMinNr', $this->WatertempMinNr );

        if ($_config['temp']) {
            $WatertempMax =  CelsiusToFahrenh($divestats[0]['WatertempMax'], 0) ."&nbsp;". $_lang['unit_temp_imp'];
        } else {
            $WatertempMax =  $divestats[0]['WatertempMax'] ."&nbsp;". $_lang['unit_temp'];
        }
        $t->assign('WatertempMax', $WatertempMax);
        $t->assign('WatertempMaxNr', $this->WatertempMaxNr );
        $decodives_per =  round(($this->decodives / $this->end) * 100) ;
        $repdives_per =   round(($this->repdives / $this->end) * 100) ;
        $t->assign('decodives',$this->decodives);
        $t->assign('repdives', $this->repdives);
        $t->assign('decodives_per',$decodives_per);
        $t->assign('repdives_per', $repdives_per);

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
        $t->assign('stats_photodives' , $_lang['stats_photodives']);

        $t->assign('deepdives', $this->deepdives );
        $t->assign('deepdives_per' , round(($this->deepdives / $this->end) * 100));
        $t->assign('cavedives', $this->cavedives );
        $t->assign('cavedives_per', round(($this->cavedives / $this->end) * 100));
        $t->assign('wreckdives' , $this->wreckdives );
        $t->assign('wreckdives_per' , round(($this->wreckdives / $this->end) * 100));
        $t->assign('photodives', $this->photodives);
        $t->assign('photodives_per' , round(($this->photodives / $this->end) * 100));

        // Show dive type details
        $t->assign('stats_shoredives', $_lang['stats_shoredives'] );
        $t->assign('stats_boatdives',  $_lang['stats_boatdives'] );
        $t->assign('stats_nightdives',  $_lang['stats_nightdives']);
        $t->assign('stats_driftdives', $_lang['stats_driftdives'] );
        $t->assign('shoredives', $this->shoredives );
        $t->assign('shoredives_per', round(($this->shoredives / $this->end) * 100));
        $t->assign('boatdives', $this->boatdives );
        $t->assign('boatdives_per' , round(($this->boatdives / $this->end) * 100));
        $t->assign('nightdives', $this->nightdives );
        $t->assign('nightdives_per', round(($this->nightdives / $this->end) * 100));
        $t->assign('driftdives' , $this->driftdives );
        $t->assign('driftdives_per' , round(($this->driftdives / $this->end) * 100));/*}}}*/
    }

    /**
     * set_lastdive_info 
     * 
     * @access public
     * @return void
     */
    function set_lastdive_info(){
        global $globals, $_config, $t , $_lang;/*{{{*/
        $t->assign('LastEntryTime', $this->LastEntryTime);
        $t->assign('LastDivePlace',$this->LastDivePlace);
        $t->assign('LastDiveID',$this->LastDiveID);
        $t->assign('LastCity',$this->LastCity);
        $t->assign('LastCountry',$this->LastCountry);/*}}}*/
    }
    /**
     * set_dive_certifications 
     * 
     * @access public
     * @return void
     */
    function set_dive_certifications(){
        global $globals, $_config, $t , $_lang;/*{{{*/
        if ($this->number_cert != 0) {
            $divecert = $this->divecert;
            $t->assign('count',$this->number_cert);
            $t->assign('stats_sect_certs',$this->username. ' '. $_lang['stats_sect_certs'] );

            // Show dive certification titles
            $t->assign('cert_brevet', $_lang['cert_brevet'] );
            $t->assign('cert_org', $_lang['cert_org'] );
            $t->assign('cert_certdate', $_lang['cert_certdate'] );
            $t->assign('cert_number', $_lang['cert_number'] );
            $t->assign('cert_instructor', $_lang['cert_instructor'] );

            for ($i=0; $i<count($divecert); $i++) {
                if ($divecert[$i]['Brevet'] == "") {
                    $Brevet = "-";
                } else {
                    $Brevet =  $divecert[$i]['Brevet'] ;
                }
                if ($divecert[$i]['Org'] == "") {
                    $Org = "-";
                } else {
                    $Org =  $divecert[$i]['Org'] ;
                }
                if ($divecert[$i]['CertDate'] == "") {
                    $CertDate = "-";
                } else {
                    $CertDate = date($_lang['logbook_divedate_format'], strtotime($divecert[$i]['CertDate'])) ;
                }
                if ($divecert[$i]['Number'] == "") {
                    $Number = "-";
                } else {
                    $Number = $divecert[$i]['Number'] ;
                }
                if ($divecert[$i]['Instructor'] == "") {
                    $Instructor = "-";
                } else {
                    $Instructor = $divecert[$i]['Instructor'] ;
                }
                if (($divecert[$i]['Scan1Path'] != "") || ($divecert[$i]['Scan2Path'] != "")) {
                    $title = $divecert[$i]['Org'] . " " .$divecert[$i]['Brevet']; 
                    $userpath_web = $_config['userpath_web'] ;
                    $Scan1Path = array();
                    $Scan2Path = array();
                    $cert_scan_front = "";
                    $cert_scan_back ="";
                    if (!empty($divecert[$i]['Scan1Path'])) {
                        $Scan1Path =  $divecert[$i]['Scan1Path'];
                        $cert_scan_front =  $_lang['cert_scan_front'];
                    }
                    if (!empty($divecert[$i]['Scan2Path'])) {
                        $Scan2Path =  $divecert[$i]['Scan2Path'] ;
                        $cert_scan_back = $_lang['cert_scan_back'];
                    }
                }
                $rowdata[$i] = array (
                        'brevet' => $Brevet , 'org' => $Org , 'certdate' => $CertDate , 'number' => $Number , 'instructor' => $Instructor ,
                        'userpath_web' => $userpath_web , 'title' => $title , 'scan1path' => $Scan1Path , 'cert_scan_front' => $cert_scan_front ,
                        'scan2path' => $Scan2Path , 'cert_scan_back' => $cert_scan_back );

            }
            $t->assign('cells',$rowdata);
        }/*}}}*/
    }/*}}}*/
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

    var $multiuser;
    var $user_id;
    var $table_prefix;
    var $username;
    var $request_type;
    var $image_link;
    var $images_for_resize;
    var $number_images_resize;

    /**
     * DivePictures 
     * 
     * @access public
     * @return void
     */
    function DivePictures(){
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
    function set_divegallery_info($request){
        //We need to extract the info from the request/*{{{*/
        if(!$request->diver_choice){
            if($this->multiuser){
                $this->user_id = $request->get_user_id();
                $user = new User();
                $user->set_user_id($this->user_id);
                $this->table_prefix = $user->get_table_prefix();
                $this->username = $user->get_username();
            } else {
                $this->user_id = $request->get_user_id();
                $user = new User();
                //$user->set_user_id($this->user_id);
                $this->table_prefix = $user->get_table_prefix();
                $this->username = $user->get_username();
            }
        } else {
            $this->request_type = 3;
        }/*}}}*/
    }
    
    /**
     * set_divegallery_info_direct 
     * 
     * @param mixed $user_id 
     * @param mixed $table_prefix 
     * @access public
     * @return void
     */
    function set_divegallery_info_direct($user_id){
        if($this->multiuser){
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
        }
    }
    
    /**
     * get_divegallery_info 
     * 
     * @param int $dive_id 
     * @access public
     * @return void
     */
    function get_divegallery_info($dive_id = 0){
        global $globals, $_config,$_lang,$t;
        if(($this->multiuser && !empty($this->user_id)) || !$this->multiuser ){
            set_config_table_prefix($this->table_prefix);
            if($dive_id == 0){
                $divepics = parse_mysql_query('divepicsall.sql');
            } else {
                $globals['logid'] = $dive_id;
                $divepics = parse_mysql_query('divepics.sql');
            }
            $pics = count($divepics);

            if ($pics != 0) {
                $this->image_link = array();
                for($i=0; $i<$pics; $i++) {
                    $img_url =  $_config['picpath_web'] . $divepics[$i]['Path'];
                    if(file_exists($img_url)){
                        $img_thumb_url = $_config['picpath_web'] .'thumb_' . $divepics[$i]['Path'];
                        $img_title = $_lang['divepic_linktitle_pt1']. ($i + 1). $_lang['divepic_linktitle_pt2']. $pics;
                        $img_title .= $_lang['divepic_linktitle_pt3']. $result[0]['Number'] ;
                        $this->image_link[] =  array(
                                'img_url' => $img_url, 
                                'img_thumb_url' => $img_thumb_url , 
                                'img_title' => $img_title,
                                'resize' => false,
                                'thumb' => false
                                );
                    }
                }

                if(isset($_config['enable_resize'])){
                    $t->assign('pics_resized','1');
                    /**
                     * Check if the images are correctly sized 
                     */
                    $toberesized = array();
                    $tobethumbed = array();
                    $this->number_images_resize = 0;
                    for($i=0 ; $i < count($this->image_link) ; $i++){
                        /**
                         *  Check normal image
                         */
                        if(filesize($this->image_link[$i][img_url]) < 512000  ){
                            $size = array();
                            $size =getimagesize($this->image_link[$i][img_url]);
                            /**
                             * Make an array of the resized images 
                             */
                            if($size[0] <= $_config['pic-width']){
                                //echo "No resize <br>";
                            } else {
                                $this->image_link[$i][resize] = true;
                                $this->number_images_resize++;
                            }
                            /**
                             * Make array of the beresized thumbs
                             */
                            if(!file_exists($this->image_link[$i][img_thumb_url])){
                                $this->image_link[$i][thumb] = true;
                                $this->number_images_resize++;
                            } else {
                                $size_thumb = getimagesize($this->image_link[$i][img_thumb_url]);
                                if($size_thumb[0]<=$_config['thumb-width'] && $size_thumb[1]<=$_config['thumb-width']){
                                    //echo "No thumb <br>";
                                } else {
                                    $this->image_link[$i][thumb] = true;
                                    $this->number_images_resize++;
                                }

                            }
                        }
                    }

                }
            }
            reset_config_table_prefix();
        } else {
            /**
             * if the request type is not already set(by divers choice), set it to overview  
             */
            if($this->request_type != 3){
                $this->request_type = 0;
            }
        }

    }

    /**
     * resizer 
     * 
     * @access public
     * @return void
     */
    function resizer($ref = 0){
        $host  = $_SERVER['HTTP_HOST'];
        $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        $extra = 'resize.php?ref='.$ref;
        header("Location: http://$host$uri/$extra");
        exit;
    }

    /**
     * resize_needed 
     * 
     * @access public
     * @return void
     */
    function resize_needed(){
        if($this->number_images_resize > 0){
            return true;
        } else {
            return false;
        }
    }

    /**
     * return_total_images_for_resizing 
     * 
     * @access public
     * @return void
     */
    function return_total_images_for_resizing(){
        return $this->number_images_resize;
    }

    //function check_thumb($var){

    /**
     * return_array_images_for_resize 
     * 
     * @access public
     * @return void
     */
    function return_array_images_for_resize(){
        $temp = array();
        for($i=0 ; $i < count($this->image_link) ; $i++){
     
            $temp[] = array_filter($this->image_link[$i]);
        }   
        for($a = 0 ; $a < count($temp) ; $a++){
            if(array_key_exists('thumb',$temp[$a]) || array_key_exists('resize',$temp[$a])){
                $this->images_for_resize[] = $temp[$a];
            }
        }
        //print_r($this->images_for_resize);
        return $this->images_for_resize;
    }

    /**
     * get_image_link 
     * 
     * @access public
     * @return void
     */
    function get_image_link(){
        return $this->image_link;
    }

    /**
     * set_all_dive_pictures 
     * 
     * @access public
     * @return void
     */
    function set_all_dive_pictures(){
        global $_config,$t, $_lang, $globals;
        $t->assign('pics2' , '1');
        $t->assign('image_link', $this->image_link);
    }
}

/**
 * AppInfo 
 * 
 * @package phpdivinglog
 * @copyright Copyright (C) 2007 Rob Lensen. All rights reserved.
 * @author Rob Lensen <rob@bsdfreaks.nl> 
 * @license LGPL v3 http://www.gnu.org/licenses/lgpl-3.0.txt
 */
class AppInfo{
/*{{{*/
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
    function AppInfo($request){
        global $_config;
        if($_config['multiuser']){
            $this->user_id = $request->get_user_id();
            $user = new User();
            $user->set_user_id($this->user_id);
            $this->table_prefix = $user->get_table_prefix();
        }
        else {
            // if prefix is set get it.
            if(isset($_config['table_prefix']))
                $this->table_prefix = $_config['table_prefix'];
        }
        $this->phpDivelogVersion = $_config['app_version'];
        $this->Appname = $_config['app_name'];
        set_config_table_prefix($this->table_prefix);
        $dbinfo = parse_mysql_query('dbinfo.sql');
        reset_config_table_prefix();
        $this->Divelogname = $dbinfo[0]['PrgName'];
        $this->DivelogVersion = $dbinfo[0]['DBVersion'];
    }

    /**
     * SetAppInfo 
     * 
     * @access public
     * @return void
     */
    function SetAppInfo(){
        global $_config, $t, $_lang;
        $t->assign('poweredby', $_lang['poweredby']);
        $t->assign('Divelogname', $this->Divelogname);
        $t->assign('DivelogVersion', $this->DivelogVersion);
        $t->assign('Appname', $this->Appname);
        $t->assign('phpDivelogVersion', $this->phpDivelogVersion);
    }/*}}}*/
}
