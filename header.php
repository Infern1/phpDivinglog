<?php
/**
 * Some things that need to be in the header for phpDivinglog 
 */

?>

<?php
 /**
  * Embedding for wordpress 
  */
require_once('/usr/local/www/rob.lensen.nu/www/wp-blog-header.php');
require_once( ABSPATH . WPINC . '/registration-functions.php');

add_action('wp_head', 'get_divelog_header');

function get_divelog_header(){
    global $t ;
    //, $_config;
    //$t->display('header_meta.tpl');
    //$t->display('header_title.tpl');
    $t->display('header_css.tpl');
    $t->display('header_script.tpl');
}

get_header(); 

?>
<body>
<div id="rap"> <!--div rap starts-->
<div id="masthead"> <!--div masthead starts-->
<h1 id="header"><a href="<?php bloginfo('url'); ?>"><?php bloginfo('name'); ?></a></h1>
<div class="slogan"><?php bloginfo('description'); ?></div>
</div> <!--div masthead ends-->
<div id="hmenu"> <!--div hmenu starts-->
<div id="hnav"> <!--div hnav starts-->

<?php include (TEMPLATEPATH . '/horizontal.php'); ?>

</div> <!--div hnav ends-->
</div> <!--div hmenu ends-->
<div id="main"> <!--div main starts-->

<div id="content"> <!--div content starts-->
<div class="post">


