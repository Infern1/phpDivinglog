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
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script type="text/javascript" src="{$app_path}/js/jquery.tools.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.15/datatables.min.js"></script>
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
