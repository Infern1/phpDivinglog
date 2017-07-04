<h1>{$pagetitle}</h1>
<!-- Include links_details -->
{include file='links_details.tpl'}
<!-- End include links_details -->
  <table class="details" cellspacing="0" cellpadding="0" width="100%">
    <tr class="divesection">
      <td colspan="4">{$pagetitle}</td>
    </tr>

    {* Show main city details *}
    <tr class="divetitle">
      <td colspan="2">{$city_name}</td>
      <td colspan="2">{$city_type}</td>
    </tr>

    <tr class="divedetails">
    {if $City != ''}
      <td colspan="2">{$City}</td>
    {else}
      <td colspan="2">-</td>
    {/if}

    {if $Type != ''}
      <td colspan="2">{$Type}</td>
    {else}
      <td colspan="2">-</td>
    {/if}
    </tr>

    <tr class="divetitle">
      <td colspan="4">{$city_map}</td>
    </tr>

    <tr class="divedetails">
    {if $MapPathurl != ''}
      <td colspan="4"><img src="{$app_path}/{$MapPathurl}" border="0" 
        alt="{$city_map_linktitle}" title="{$city_map_linktitle}"></td>
    {else}
      <td colspan="4">-</td>
    {/if}
    </tr>

    {* Comments *}
    {* Show them if we have them *}
    {if isset($Comments)}
    <tr class="divesection">
      <td colspan="4">{$city_sect_comments}</td>
    </tr>
    <tr class="divedetails">
      <td colspan="4">{$Comments}</td>
    </tr>
    {/if}

    <tr class="divesection">
      <td colspan="4">{$city_sect_activity}</td>
    </tr>

    {* Show city sites if we have them *}
    {if $site_count != 0}
    <tr class="divetitle">
      <td colspan="4">{$site_count} {$city_site_trans}</td>
    </tr>

    <tr class="divedetails">
      <td colspan="4">
      {foreach from=$sites item=site}
        {if isset($multiuser_id)}
        <a href="{$app_path}/divesite.php{$sep1}{$multiuser_id}{$sep2}{$site.PlaceID}"
title="{$dsite_number_title}{$site.PlaceID} - {$site.Place}">{if $site.Place == ''}{$site.PlaceID}{else}{$site.Place}{/if}</a>{if !$site@last}{if $comma_separated}{$comma_separator}{else}&nbsp;{/if}{/if}
        {else}
        <a href="{$app_path}/divesite.php{$sep2}{$site.PlaceID}"
title="{$dsite_number_title}{$site.PlaceID} - {$site.Place}">{if $site.Place == ''}{$site.PlaceID}{else}{$site.Place}{/if}</a>{if !$site@last}{if $comma_separated}{$comma_separator}{else}&nbsp;{/if}{/if}
        {/if}
      {/foreach}
      </td>
    </tr>
    {/if}

    {* Show city dives if we have them *}
    {if $dive_count != 0}
    <tr class="divetitle">
      <td colspan="4">{$dive_count} {$city_dive_trans}</td>
    </tr>

    <tr class="divedetails">
      <td colspan="4">
      {foreach from=$dives item=dive}
        {if isset($multiuser_id)}
        <a href="{$app_path}/index.php{$sep1}{$multiuser_id}{$sep2}{$dive}"
title="{$dlog_number_title}{$dive}">{$dive}</a>{if !$dive@last}{if $comma_separated}{$comma_separator}{else}&nbsp;{/if}{/if}
        {else}
        <a href="{$app_path}/index.php{$sep2}{$dive}"
title="{$dlog_number_title}{$dive}">{$dive}</a>{if !$dive@last}{if $comma_separated}{$comma_separator}{else}&nbsp;{/if}{/if}
        {/if}
      {/foreach}
      </td>
    </tr>
    {/if}

    <tr class="divesection">
      <td colspan="4">&nbsp;</td>
    </tr>
  </table>

<!-- Include links_details -->
{include file='links_details.tpl'}
<!-- End include links_details -->
