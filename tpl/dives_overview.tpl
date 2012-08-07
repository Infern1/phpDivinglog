<h1>{$pagetitle}</h1>
<!-- Include links_overview -->
{include file='links_overview.tpl'}
<!-- End include links_overview -->
{if isset($grid_display)}
    {$grid}
{else}
{* Loop through the array *}
<table class="divetable" cellspacing="0" cellpadding="0" width="100%">
  <thead>
{if $pages != ''}
    <tr>
      <td colspan="5">{$pages}</td>
    </tr>
{/if}
    <tr class="divelogtitle">
        <td width="55" valign="bottom" >{$dlog_title_number}</td>
        <td width="75" valign="bottom">{$dlog_title_divedate}</td>
        <td width="60" valign="bottom" >{$dlog_title_depth}</td>
        <td width="60" valign="bottom" >{$dlog_title_divetime}</td>
        <td width="375" valign="bottom">{$dlog_title_location}</td>
    </tr>
  </thead>
{if $pages != ''}
  <tfoot>
    <tr>
      <td colspan="5">{$pages}</td>
    </tr>
  </tfoot>
{/if}
  <tbody>
{section name=cell_data loop=$cells }
    <tr class="diveoverview">
        <td>
{if isset($multiuser_id)}
{if $cells[cell_data].profile == '1'}
<img src="{$app_path}/images/profile.gif" border="0" alt="{$logbook_profile}" title="{$logbook_profile}">
{else}
<img src="{$app_path}/images/no_profile.gif" border="0" alt="{$logbook_no_profile}" title="{$logbook_no_profile}">
{/if}
<a href="{$app_path}/index.php{$sep1}{$multiuser_id}{$sep2}{$cells[cell_data].number}" 
title="{$dlog_number_title}{$cells[cell_data].number}" >{$cells[cell_data].number}</a></td>
{else}
{if $cells[cell_data].profile == '1'}
<img src="{$app_path}/images/profile.gif" border="0" alt="{$logbook_profile}" title="{$logbook_profile}">
{else}
<img src="{$app_path}/images/no_profile.gif" border="0" alt="{$logbook_no_profile}" title="{$logbook_no_profile}">
{/if}
<a href="{$app_path}/index.php{$sep2}{$cells[cell_data].number}" 
title="{$dlog_number_title}{$cells[cell_data].number}" >{$cells[cell_data].number}</a></td>
{/if}
        <td>{$cells[cell_data].divedate}</td>
        <td>{$cells[cell_data].depth|commify:2} {$unit_length_short}</td>
        <td>{$cells[cell_data].divetime} {$unit_time_short} </td>
        <td>{$cells[cell_data].place}, {$cells[cell_data].city}</td>
    </tr>
{/section}
  </tbody>
</table>
{/if}
    {* Show the links *}
	<!-- Include links_overview -->
	{include file='links_overview.tpl'}
    <!-- End include links_overview -->
