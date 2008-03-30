<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="Content-Language" content="en-us">
    <title>phpDivingLog - {$pagetitle} </title>

 <link rel="stylesheet" type="text/css" media="screen" href="{$app_path}/includes/divelog.css">
<!-- <link rel="stylesheet" type="text/css" media="screen" href="{$app_path}/includes/highslide.css"> -->

<!--
<script type="text/javascript" src="{$app_path}/includes/lightbox/prototype.js"></script>
 <script type="text/javascript" src="{$app_path}/includes/lightbox/scriptaculous.js?load=effects"></script>
{if isset($multiuser_id)}
<script type="text/javascript" src="{$app_path}/includes/lightbox/lightbox_mu.js"></script>
<link rel="stylesheet" type="text/css" media="screen" href="{$app_path}/includes/lightbox/lightbox_mu.css">
{else}
<script type="text/javascript" src="{$app_path}/includes/lightbox/lightbox.js"></script>
<link rel="stylesheet" type="text/css" media="screen" href="{$app_path}/includes/lightbox/lightbox.css">
{/if}
-->
<script type="text/javascript" src="{$app_path}/includes/misc.js"></script>
<script type="text/javascript" src="{$app_path}/highslide/highslide.js"></script>
{literal}
<script type="text/javascript">
function open_url(index, link){
window.location.href = '{/literal}{$web_root}{literal}' + link + index  ;
}
    // remove the registerOverlay call to disable the controlbar
    hs.graphicsDir = '{/literal}{$web_root}/{literal}highslide/graphics/';
</script>
{/literal}
{$grid_header}
{literal}
<style type="text/css">
* {
    font-family: Verdana, Helvetica;
    font-size: 10pt;
}
.highslide {
    cursor: url(highslide/graphics/zoomin.cur), pointer;
    outline: none;
}
.highslide-active-anchor img {
    visibility: hidden;
}
.highslide img {
    border: 2px solid gray;
}
.highslide:hover img {
    border: 2px solid white;
}
.highslide-wrapper {
    background: white;
}
.highslide-image {
    border: 10px solid white;
}
.highslide-image-blur {
}
.highslide-caption {
    display: none;
    border: 5px solid white;
    border-top: none;
    padding: 5px;
    background-color: white;
}
.highslide-loading {
    display: block;
    color: black;
    font-size: 8pt;
    font-family: sans-serif;
    font-weight: bold;
    text-decoration: none;
    padding: 2px;
    border: 1px solid black;
    background-color: white;
    
    padding-left: 22px;
    background-image: url(highslide/graphics/loader.white.gif);
    background-repeat: no-repeat;
    background-position: 3px 1px;
}
a.highslide-credits,
a.highslide-credits i {
    padding: 2px;
    color: silver;
    text-decoration: none;
    font-size: 10px;
}
a.highslide-credits:hover,
a.highslide-credits:hover i {
    color: white;
    background-color: gray;
}

a.highslide-full-expand {
    background: url(highslide/graphics/fullexpand.gif) no-repeat;
    display: block;
    margin: 0 10px 10px 0;
    width: 34px;
    height: 34px;
}

/* These must always be last */
.highslide-display-block {
    display: block;
}
.highslide-display-none {
    display: none;
}
</style>
{/literal}
</head>

  <body> 


