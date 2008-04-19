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
</table>
{* Show photos per dive *}
              {foreach from=$image_link key=id item=i name=images}
    <div class="thumbwrapper">
                   {if isset($pics_resized)}
<a id="thumb{$id}" href="{$web_root}/{$i.img_url}" class="highslide" onclick="return hs.expand(this)">
    <img src="{$web_root}/{$i.img_thumb_url}" alt="Highslide JS" title="Click" height="80" width="120" >
</a>
                    {else}
<a id="thumb{$id}" href="{$web_root}/{$i.img_url}" class="highslide" onclick="return hs.expand(this)">
    <img src="{$web_root}/imagesize.php?w=100&img={$i.img_url}" alt="Highslide JS" title="Click " height="80" width="120" >
</a>
                    {/if}
                    <div class='highslide-caption'>
 {$dive_details_pagetitle} {if isset($multiuser_id)}
<a href="{$app_path}/index.php{$sep1}{$multiuser_id}{$sep2}{$i.dive_nr}" title="{$dlog_number_title}{$i.dive_nr}">{$i.dive_nr}</a>
                    {else}
<a href="{$app_path}/index.php{$sep2}{$i.dive_nr}" title="{$dlog_number_title}{$i.dive_nr}">{$i.dive_nr}</a>
                    {/if}<br>
                    {$logbook_place}  {if isset($multiuser_id)}
<a href="{$app_path}/divesite.php{$sep1}{$multiuser_id}{$sep2}{$i.site_nr}" title="{$dlog_number_title}{$i.site_nr}">{$i.site_nr}</a>
                   {else}
<a href="{$app_path}/divesite.php{$sep2}{$i.site_nr}" title="{$dlog_number_title}{$i.site_nr}">{$i.site_nr}</a>
                   {/if}<br>
                {$i.img_title} 
                   </div>
                    <p>{$dive_details_pagetitle} {if isset($multiuser_id)}
<a href="{$app_path}/index.php{$sep1}{$multiuser_id}{$sep2}{$i.dive_nr}" title="{$dlog_number_title}{$i.dive_nr}">{$i.dive_nr}</a>
                    {else}
<a href="{$app_path}/index.php{$sep2}{$i.dive_nr}" title="{$dlog_number_title}{$i.dive_nr}">{$i.dive_nr}</a>
                    {/if}<br>
                    {$logbook_place}  {if isset($multiuser_id)}
<a href="{$app_path}/divesite.php{$sep1}{$multiuser_id}{$sep2}{$i.site_nr}" title="{$dlog_number_title}{$i.site_nr}">{$i.site_nr}</a>
                   {else}
<a href="{$app_path}/divesite.php{$sep2}{$i.site_nr}" title="{$dlog_number_title}{$i.site_nr}">{$i.site_nr}</a>
                   {/if}
 </p>
        </div>
    {/foreach}

{if $page_numbers.total > 1}
<table width="100%" cellspacing="0" cellpadding="0">
    <tr>
	    <td colspan="5" align="center">{$pager_links}</td>
    </tr>
</table>
<br>
{/if}

<table width="100%" cellspacing="0" cellpadding="0">
    <tr class="divesection">
	    <td colspan="5">&nbsp;</td>
    </tr>
</table>

<div id="controlbar" class="highslide-overlay controlbar">
    <a href="#" class="previous" onclick="return hs.previous(this)" title="Previous (left arrow key)"></a>
    <a href="#" class="next" onclick="return hs.next(this)" title="Next (right arrow key)"></a>
    <a href="#" class="highslide-move" onclick="return false" title="Click and drag to move"></a>
    <a href="#" class="close" onclick="return hs.close(this)" title="Close"></a>
</div>

{/if}
<!-- Include links_overview -->
	{include file='links_overview.tpl'}
<!-- End include links_overview -->
</div>
{include file='footer.tpl'}

