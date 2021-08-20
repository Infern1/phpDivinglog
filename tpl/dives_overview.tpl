<h1>{$pagetitle}</h1>
<!-- Include links_overview -->
{include file='links_overview.tpl'}
<!-- End include links_overview -->
{if isset($grid_display)}
    {$grid}
{else}
{* Loop through the array *}
<table id="divelog" class="hover" cellspacing="0" cellpadding="0" width="100%">
  <thead>
    <tr class="divelogtitle">
        <td width="55" valign="bottom" >{$dlog_title_number}</td>
        <td width="75" valign="bottom">{$dlog_title_divedate}</td>
        <td width="60" valign="bottom" >{$dlog_title_depth}</td>
        <td width="60" valign="bottom" >{$dlog_title_divetime}</td>
        <td width="375" valign="bottom">{$dlog_title_location}</td>
    </tr>
  </thead>
  <tbody>
{section name=cell_data loop=$cells }
    <tr class="diveoverview">
        <td>
{if $cells[cell_data].Profile == '1'}
<img src="{$app_path}/images/profile.gif" border="0" alt="{$logbook_profile}" title="{$logbook_profile}">
{else}
<img src="{$app_path}/images/no_profile.gif" border="0" alt="{$logbook_no_profile}" title="{$logbook_no_profile}">
{/if}
<a href="{$app_path}/index.php{$sep2}{$cells[cell_data].Number}" 
title="{$dlog_number_title}{$cells[cell_data].Number}" >{$cells[cell_data].Number}</a></td>

        <td>{$cells[cell_data].Divedate}</td>
        <td>{$cells[cell_data].Depth|commify:2} {$unit_length_short}</td>
        <td>{$cells[cell_data].Divetime} {$unit_time_short} </td>
        <td>{$cells[cell_data].Place}, {$cells[cell_data].City}</td>
    </tr>
{/section}
  </tbody>
</table>
{/if}
{include file='datatable.tpl' tablename='divelog' order='[ 0, \'desc\' ]'}

    {* Show the links *}
	<!-- Include links_overview -->
	{include file='links_overview.tpl'}
    <!-- End include links_overview -->
