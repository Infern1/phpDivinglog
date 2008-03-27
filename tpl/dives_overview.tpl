 <!-- Include links_overview -->
{include file='links_overview.tpl'}
<!-- End include links_overview -->
{if isset($grid_display)}
    {$grid}
    {else}
{* Loop through the array *}

<table class="divetable" cellspacing="0" cellpadding="0" width="100%">
    <thead>
		<tr class="divelogtitle">
		    <td width="8%" valign="bottom" >{$dlog_title_number}</td>
		    <td width="15%" valign="bottom">{$dlog_title_divedate}</td>
		    <td width="15%" valign="bottom" >{$dlog_title_depth}</td>
		    <td width="10%" valign="bottom" >{$dlog_title_divetime}</td>
		    <td valign="bottom">{$dlog_title_location}</td>
		</tr>
    </thead>
    <tbody>
{section name=cell_data loop=$cells  }
<tr class="divecontent">
<td>
{if isset($multiuser_id)}
<a href="{$app_path}/index.php{$sep1}{$multiuser_id}{$sep2}{$cells[cell_data].number}" 
title="{$dlog_number_title}{$cells[cell_data].number}" >{$cells[cell_data].number}</a></td>
{else}
<a href="{$app_path}/index.php{$sep2}{$cells[cell_data].number}" 
title="{$dlog_number_title}{$cells[cell_data].number}" >{$cells[cell_data].number}</a></td>
{/if}
<td>{$cells[cell_data].divedate}</td>
<td>{$cells[cell_data].depth} {$unit_length_short}</td>
<td>{$cells[cell_data].divetime} {$unit_time_short} </td>
<td>{$cells[cell_data].place} {$cells[cell_data].city}</td>
</tr>
{/section}
</tbody>
</table>
{$pages}
{/if}
    {* Show the links *}
	<!-- Include links_overview -->
	{include file='links_overview.tpl'}
    <!-- End include links_overview -->
