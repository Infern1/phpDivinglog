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
//hs.captionEval = 'this.thumb.title';
</script>
{/literal}
{$grid_header}

