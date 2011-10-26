<h1>{$pagetitle}</h1>
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
{if $pages != ''}
    <tr>
      <td colspan="4">{$pages}</td>
    </tr>
{/if}
    <tr class="divelogtitle">
    	<td width="250" valign="bottom">{$dsite_title_place}</td>
    	<td width="200" valign="bottom">{$dsite_title_city}</td>
    	<td width="125" valign="bottom">{$dsite_title_country}</td>
    	<td width="50" valign="bottom">{$dsite_title_maxdepth}</td>
    </tr>
</thead>
{if $pages != ''}
  <tfoot>
    <tr>
      <td colspan="4">{$pages}</td>
    </tr>
  </tfoot>
{/if}
<tbody>
{section name=cell_data loop=$cells }
<tr class="diveoverview">
{if isset($multiuser_id)}
<td><a href="{$app_path}/{$base_page}{$sep1}{$multiuser_id}{$sep2}{$cells[cell_data].id}" 
        title="{$cells[cell_data].place} {$logbook_place_linktitle}">{$cells[cell_data].place}</a></td>
{else}
<td><a href="{$app_path}/{$base_page}{$sep2}{$cells[cell_data].id}" 
        title="{$cells[cell_data].place} {$logbook_place_linktitle}">{$cells[cell_data].place}</a></td>
{/if}
           <td>{$cells[cell_data].city}</td>
           <td>{$cells[cell_data].country}</td>
{if $cells[cell_data].maxdepth != ''}
           <td>{$cells[cell_data].maxdepth} {$unit_length_short}</td>
{else}
           <td>-</td>
{/if}
</tr>
{/section}
</tbody>
</table>
{/if}
{*	Show the links *}
<!-- Include links_overview -->
{include file='links_overview.tpl'}
<!-- End include links_overview -->
