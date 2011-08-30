<!-- Include links_details -->
{include file='links_details.tpl'}
<!-- End include links_details -->
  <table class="details" cellspacing="0" cellpadding="0" width="100%">
    <tr class="divesection">
      <td colspan="4">{$pagetitle}</td>
    </tr>

    {* Show main country details *}
    <tr class="divetitle">
      <td colspan="2">{$city_name}</td>
      <td colspan="2">{$city_map}</td>
    </tr>

    <tr class="divedetails">
    {if $Country != ''}
      <td colspan="2">{$City}</td>
    {else}
      <td colspan="2">-</td>
    {/if}

    {if $MapPathurl != ''}
      <td colspan="2" rowspan="5"><img src="{$app_path}/{$MapPathurl}" border="0" title="{$city_flag_linktitle}"></td>
    {else}
      <td colspan="2" rowspan="5">-</td>
    {/if}
    </tr>

    <tr class="divetitle">
      <td colspan="2">{$city_type}</td>
    </tr>

    <tr class="divedetails">
    {if $Type != ''}
      <td colspan="2">{$Type}</td>
    {else}
      <td colspan="2">-</td>
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
        <a href="{$app_path}/divesite.php{$sep1}{$multiuser_id}{$sep2}{$site}"
title="{$dsite_number_title}{$site}">{$site}</a>
        {else}
        <a href="{$app_path}/divesite.php{$sep2}{$site}"
title="{$dsite_number_title}{$site}">{$site}</a>
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
title="{$dlog_number_title}{$dive}">{$dive}</a>
        {else}
        <a href="{$app_path}/index.php{$sep2}{$dive}"
title="{$dlog_number_title}{$dive}">{$dive}</a>
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
