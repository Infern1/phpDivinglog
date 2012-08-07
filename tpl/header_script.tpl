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
{if !isset($embed)}
{literal}
<script type="text/javascript">
function ajax_request($url) {
}

</script>
{/literal}

{else}
{literal}
<script type="text/javascript">
function ajax_request($url) {
	jQuery('#content').load($url, function() {
		update_links();
	});

	
}

function update_links(){
	jQuery(".divelogcrumbs a").each(function (index) {
		jQuery(this).click(function(event){
			var href = jQuery(this).attr('href') ;
			ajax_request(href);
			//alert("As you can see, the link no longer took you to jquery.com");
			event.preventDefault();
		});
	});
	
	jQuery(".diveoverview a").each(function (index) {
		jQuery(this).click(function(event){
			var href = jQuery(this).attr('href') ;
			ajax_request(href);
			event.preventDefault();
		});
	});	
}

jQuery(document).ready(function(){
	update_links();
});
</script>
{/literal}

{/if}

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

{* Images based tabs - fixed width
<script type="text/javascript" src="{$app_path}/js/jquery.tools.min.js"></script>
<!-- tab styling -->
<link rel="stylesheet" type="text/css" href="{$app_path}/includes/tabs.css">
{literal}
<!-- tab pane styling -->
<style type="text/css">
/* tab pane styling */
.panes div {
	display:none;		
	padding:15px 10px;
	border:1px solid #999;
	border-top:0;
	height:100px;
	font-size:14px;
	background-color:#fff;
}
</style>
{/literal}
*}
{* No images based tabs - variable width *}
<script type="text/javascript" src="{$app_path}/js/jquery.tools.min.js"></script>
<!-- tab styling -->
<link rel="stylesheet" type="text/css" href="{$app_path}/includes/tabs-no-images.css">

