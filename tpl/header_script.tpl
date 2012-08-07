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
    hs.graphicsDir = '{/literal}{$web_root}{literal}/includes/highslide/graphics/';
    hs.align = 'center';
    hs.transitions = ['expand', 'crossfade'];
    hs.outlineType = 'rounded-white';
    hs.fadeInOut = true;

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

{* No images based tabs - variable width *}
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script type="text/javascript" src="{$app_path}/js/jquery.tools.min.js"></script>
{if isset($profile)} 
<script type="text/javascript" src="{$app_path}/includes/jqplot/jquery.jqplot.min.js"></script>
<script type="text/javascript" src="{$app_path}/includes/jqplot/plugins/jqplot.canvasTextRenderer.min.js"></script>
<script type="text/javascript" src="{$app_path}/includes/jqplot/plugins/jqplot.canvasAxisLabelRenderer.min.js"></script>
<script type="text/javascript" src="{$app_path}/includes/jqplot/plugins/jqplot.highlighter.min.js"></script>
<script type="text/javascript" src="{$app_path}/includes/jqplot/plugins/jqplot.cursor.min.js"></script>
{/if}
{if isset($piechart_display)}
<script type="text/javascript" src="{$app_path}/includes/jqplot/jquery.jqplot.min.js"></script>
<script type="text/javascript" src="{$app_path}/includes/jqplot/plugins/jqplot.pieRenderer.min.js"></script>
{/if}
<!-- tab styling -->
<link rel="stylesheet" type="text/css" href="{$app_path}/includes/tabs-no-images.css">

