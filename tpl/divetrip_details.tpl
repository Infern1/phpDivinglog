<!-- Include links_details -->
{include file='links_details.tpl'}
<!-- End include links_details -->
  <table class="details" cellspacing="0" cellpadding="0" width="100%">
    <tr class="divesection">
      <td colspan="4">{$pagetitle}</td>
    </tr>

    {* Show main trip details *}
    <tr class="divetitle">
      <td colspan="2">{$trip_name}</td>
      <td colspan="2">{$trip_rating}</td>
    </tr>
    <tr class="divedetails">
      <td colspan="2">{$TripName}</td>
      <td colspan="2">{$Rating}</td>
    </tr>

    <tr class="divetitle">
      <td colspan="2">{$trip_shop}</td>
      <td colspan="2">{$trip_country}</td>
    </tr>
    <tr class="divedetails">
    {if $dive_shop_nr != ""} 
      {if isset($multiuser_id)}
      <td colspan="2"><a href="{$app_path}/diveshop.php{$sep1}{$multiuser_id}{$sep2}{$dive_shop_nr}" title="{$dive_shop_name} {$logbook_shop_linktitle}">{$dive_shop_name}</a></td>
      {else}
      <td colspan="2"><a href="{$app_path}/diveshop.php{$sep2}{$dive_shop_nr}" title="{$dive_shop_name} {$logbook_shop_linktitle}">{$dive_shop_name}</a></td>
      {/if}
    {else}
      <td>-</td>
    {/if} 
      <td colspan="2">{$Country}</td>
    </tr>

    <tr class="divetitle">
      <td colspan="2">{$trip_startdate}</td>
      <td colspan="2">{$trip_enddate}</td>
    </tr>
    <tr class="divedetails">
      <td colspan="2">{$StartDate}</td>
      <td colspan="2">{$EndDate}</td>
    </tr>

    {if isset($pics2)}
    {* Show pics *}
    <tr class="divetitle">
      <td colspan="4">{$trip_photo}</td>
    </tr>

    <tr class="divedetails">
      <td colspan="4">
    {foreach from=$image_link key=id item=i name=images}
        <a id="thumb" href="{$web_root}/{$i.img_url}" 
class="highslide" onclick="return hs.expand(this)" 
title="{$i.img_title}"><img 
src="{$web_root}/{$i.img_url}" 
alt="{$i.img_title}" 
title="{$i.img_title}" 
height="{$thumb_height}" width="{$thumb_width}"></a>
        <div class='highslide-caption'>
        {$i.img_title}
        </div>
   {/foreach}
      </td>
    </tr>
    {/if}

    {* Show trip dives if we have them *}
    {if $dive_count != 0}
    <tr class="divetitle">
      <td colspan="4">{$dive_count} {$trip_dive_trans}</td>
    </tr>

    <tr class="divedetails">
      <td colspan="4">
      {foreach from=$dives item=dive}
        {if isset($multiuser_id)}
        <a href="{$app_path}/index.php{$sep1}{$multiuser_id}{$sep2}{$dive}"
title="{$dlog_number_title}{$dive}">{$dive}</a>
        {else}
        <a href="{$app_path}/index.php{$sep2}{$dive}"
title="{$dlog_number_title}{$dive}">{$dive}</a>
        {/if}
      {/foreach}
      </td>
    </tr>
    {/if}

    {* Show buddy details *}
    {if $buddy != ''}
    <tr class="divetitle">
      <td colspan="4">{$trip_buddy}</td>
    </tr>
    <tr class="divedetails">
      <td colspan="4">{$buddy}</td>
    </tr>
    {/if}

    {* Comments *}
    {* Show them if we have them *}
    {if isset($Comments)}
    <tr class="divesection">
      <td colspan="4">{$trip_sect_comments}</td>
    </tr>
    <tr class="divedetails">
      <td colspan="4">{$Comments}</td>
    </tr>
    {/if}

    <tr class="divesection">
      <td colspan="4">&nbsp;</td>
    </tr>
  </table>

<!-- Include links_details -->
{include file='links_details.tpl'}
<!-- End include links_details -->
