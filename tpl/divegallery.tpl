{include file='header.tpl'}
<div id="content">
<!-- Include links_overview -->
	{include file='links_overview.tpl'}
<!-- End include links_overview -->
{if isset($diver_overview)}
{include file='diver_overview.tpl'}
{else}
<table class="divetable" cellspacing="0" cellpadding="0" width="100%">

<tr><td colspan="4" class="spacing">&nbsp;</td></tr>

<tr class="divesection">
 <td colspan="4">{$gallery_title}</td>
</tr>

{* Show photos per dive *}
	<div>
    	<tr><td colspan="4" class="spacing">&nbsp;</td></tr>
        <tr><td colspan="4">
		        <p class="centeredImage">
      {foreach from=$image_link key=id item=i name=images}
                {if isset($pics_resized)}
             <a id="thumb{$id}" href="{$web_root}/{$i.img_url}" class="highslide" onclick="return hs.expand(this)">
                    <img src="{$web_root}/{$i.img_thumb_url}" alt="Highslide JS" title="{$i.img_title}" height="80" width="120" /></a>
                {else}
             <a id="thumb{$id}" href="{$web_root}/{$i.img_url}" class="highslide" onclick="return hs.expand(this)">
                    <img src="{$web_root}/imagesize.php?w=100&img={$i.img_url}" alt="Highslide JS" title="{$i.img_title}" height="80" width="120" /></a>
                {/if}
            {/foreach}
 		</p>
		</td></tr>
<div id="controlbar" class="highslide-overlay controlbar">
    <a href="#" class="previous" onclick="return hs.previous(this)" title="Previous (left arrow key)"></a>
    <a href="#" class="next" onclick="return hs.next(this)" title="Next (right arrow key)"></a>
    <a href="#" class="highslide-move" onclick="return false" title="Click and drag to move"></a>
    <a href="#" class="close" onclick="return hs.close(this)" title="Close"></a>

</div>
</div>

<tr class="divecontent">
    <tr class="divesection">
	    <td colspan="5">&nbsp;</td>
	</tr>
</table>
{/if}
<!-- Include links_overview -->
	{include file='links_overview.tpl'}
<!-- End include links_overview -->
</div>
{include file='footer.tpl'}

