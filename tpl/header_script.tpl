{literal}
<style type="text/css">
.highslide {
    cursor: url({/literal}{$web_root}{literal}/includes/highslide/graphics/zoomin.cur), pointer;
}
.highslide-loading {
    background-image: url({/literal}{$web_root}{literal}/includes/highslide/graphics/loader.white.gif);
}
.highslide-controls {
    background: url({/literal}{$web_root}{literal}/includes/highslide/graphics/controlbar-white.gif) 0 -90px no-repeat;
}
.highslide-controls ul {
    background: url({/literal}{$web_root}{literal}/includes/highslide/graphics/controlbar-white.gif) right -90px no-repeat;
}
.highslide-controls a {
    background-image: url({/literal}{$web_root}{literal}/includes/highslide/graphics/controlbar-white.gif);
}
</style>
{/literal}
<script type="text/javascript" src="{$app_path}/includes/misc.js"></script>
<script type="text/javascript" src="{$app_path}/includes/highslide/highslide-with-gallery.js"></script>
{literal}
<script type="text/javascript">
function open_url(index, link){
window.location.href = '{/literal}{$web_root}{literal}' + link + index  ;
}
    hs.graphicsDir = '{/literal}{$web_root}/{literal}highslide/graphics/';
    hs.align = 'center';
    hs.transitions = ['expand', 'crossfade'];
    hs.outlineType = 'rounded-white';
    hs.fadeInOut = true;
    //hs.dimmingOpacity = 0.75;

    // Add the controlbar
    if (hs.addSlideshow) hs.addSlideshow({
        //slideshowGroup: 'group1',
        interval: 5000,
        repeat: false,
        useControls: true,
        fixedControls: 'fit',
        overlayOptions: {
            opacity: .75,
            position: 'bottom center',
            hideOnMouseOut: true
        }
    });
</script>
{/literal}

