<h1>{$pagetitle}</h1>
<!-- Include links_overview -->
{include file='links_overview.tpl'}
<!-- End include links_overview -->
{* Loop through the array *}
<table id='divetable' class="hover" cellspacing="0" cellpadding="0" width="100%">
<thead>
    <tr class="divelogtitle">
    	<td width="250" valign="bottom">{$dsite_title_place}</td>
    	<td width="200" valign="bottom">{$dsite_title_city}</td>
    	<td width="125" valign="bottom">{$dsite_title_country}</td>
    	<td width="50" valign="bottom">{$dsite_title_maxdepth}</td>
    </tr>
</thead>
<tbody>
{section name=cell_data loop=$cells }
<tr class="diveoverview">
<td><a href="{$app_path}/{$base_page}{$sep2}{$cells[cell_data].ID}" 
        title="{$cells[cell_data].place} {$logbook_place_linktitle}">{$cells[cell_data].Place}</a></td>
           <td>{$cells[cell_data].City}</td>
           <td>{$cells[cell_data].Country}</td>
{if $cells[cell_data].MaxDepth != ''}
           <td>{$cells[cell_data].MaxDepth} {$unit_length_short}</td>
{else}
           <td>-</td>
{/if}
</tr>
{/section}
</tbody>
</table>
{*	Show the links *}
<!-- Include links_overview -->
{include file='datatable.tpl' tablename='divetable'}

{include file='links_overview.tpl'}
<!-- End include links_overview -->
