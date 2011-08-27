<!-- Include links_overview -->
{include file='links_overview.tpl'}
<!-- End include links_overview -->
{if isset($grid_display)}
<!-- display the grid -->
{$grid}
{else}
{* Loop through the array *}
<table class="divetable" cellspacing="0" cellpadding="0" width="100%">
<thead>
    <tr class="divelogtitle">
    	<td valign="bottom">{$dshop_title_country}</td>
    </tr>
</thead>
<tbody>
{section name=cell_data loop=$cells }
    <tr class="divecontent">
{if isset($multiuser_id)}
        <td><a href="{$app_path}/{$base_page}{$sep1}{$multiuser_id}{$sep2}{$cells[cell_data].id}" 
          title="{$cells[cell_data].trip} {$logbook_trip_linktitle}">{$cells[cell_data].trip}</a></td>
{else}
        <td><a href="{$app_path}/{$base_page}{$sep2}{$cells[cell_data].id}" 
          title="{$cells[cell_data].shop} {$logbook_trip_linktitle}">{$cells[cell_data].trip}</a></td>
{/if}
        <td>{$cells[cell_data].shop}</td>
        <td>{$cells[cell_data].country}</td>
    </tr>
{/section}
</tbody>
</table>
{$pages}
{/if}
{*	Show the links *}
<!-- Include links_overview -->
{include file='links_overview.tpl'}
<!-- End include links_overview -->
