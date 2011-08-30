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
    	<td valign="bottom" colspan="2">{$city_title_city}</td>
        <td valign="bottom">{$city_title_country}</td>
        <td valign="bottom">{$city_title_dives}</td>
    </tr>
</thead>
<tbody>
{section name=cell_data loop=$cells }
    <tr class="divecontent">
{if isset($multiuser_id)}
        <td colspan="2"><a href="{$app_path}/{$base_page}{$sep1}{$multiuser_id}{$sep2}{$cells[cell_data].id}" 
          title="{$cells[cell_data].city} {$logbook_city_linktitle}">{$cells[cell_data].city}</a></td>
{else}
        <td colspan="2"><a href="{$app_path}/{$base_page}{$sep2}{$cells[cell_data].id}" 
          title="{$cells[cell_data].shop} {$logbook_city_linktitle}">{$cells[cell_data].city}</a></td>
{/if}
        <td>{$cells[cell_data].country}</td>
        <td>{$cells[cell_data].dives}</td>
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
