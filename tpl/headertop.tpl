<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="Content-Language" content="en-us">
    <title>phpDivingLog - {$pagetitle} </title>

 <link rel="stylesheet" type="text/css" media="screen" href="{$app_path}/includes/divelog.css">
 <link rel="stylesheet" type="text/css" media="screen" href="{$app_path}/includes/highslide.css"> 
{literal}
<style type="text/css">
.highslide {
    cursor: url({/literal}{$web_root}{literal}/highslide/graphics/zoomin.cur), pointer;
}
.highslide-loading {
    background-image: url({/literal}{$web_root}{literal}/highslide/graphics/loader.white.gif);
}
a.highslide-full-expand {
    background: url({/literal}{$web_root}{literal}/highslide/graphics/fullexpand.gif) no-repeat;
}
.controlbar {   
    background: url({/literal}{$web_root}{literal}/highslide/graphics/controlbar4.gif);
}
.controlbar a:hover {
    background-image: url({/literal}{$web_root}{literal}/highslide/graphics/controlbar4-hover.gif);
}
</style>
{/literal}
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
hs.registerOverlay(
        {
thumbnailId: null,
overlayId: 'controlbar',
position: 'top right',
hideOnMouseOut: true
}
);
hs.graphicsDir = '{/literal}{$web_root}/{literal}highslide/graphics/';
hs.outlineType = 'rounded-white';
// Tell Highslide to use the thumbnail's title for captions
hs.captionEval = 'this.thumb.title';
</script>
{/literal}
{$grid_header}

</head>

  <body> 


