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
      <td colspan="4" class="divelog-navigation">{$pages}</td>
    </tr>
{/if}
    <tr class="divelogtitle">
    	<td valign="bottom" width="300">{$city_title_city}</td>
        <td valign="bottom" width="80">{$city_title_type}</td>
        <td valign="bottom" width="205">{$city_title_country}</td>
        <td valign="bottom" width="40">{$city_title_dives}</td>
    </tr>
</thead>
{if $pages != ''}
  <tfoot>
    <tr>
      <td colspan="4" class="divelog-navigation">{$pages}</td>
    </tr>
  </tfoot>
{/if}
<tbody>
{section name=cell_data loop=$cells }
    <tr class="diveoverview">
{if isset($multiuser_id)}
        <td><a href="{$app_path}/{$base_page}{$sep1}{$multiuser_id}{$sep2}{$cells[cell_data].id}" 
          title="{$cells[cell_data].city} {$logbook_city_linktitle}">{$cells[cell_data].city}</a></td>
{else}
        <td><a href="{$app_path}/{$base_page}{$sep2}{$cells[cell_data].id}" 
          title="{$cells[cell_data].city} {$logbook_city_linktitle}">{$cells[cell_data].city}</a></td>
{/if}
{if ($cells[cell_data].type >= 0) && ($cells[cell_data].type <= 5)}
        <td>{$citytypes[$cells[cell_data].type]}</td>
{else}
        <td>-</td>
{/if}
        <td>{$cells[cell_data].country}</td>
        <td>{$cells[cell_data].dives}</td>
    </tr>
{/section}
</tbody>
</table>
{/if}
{*	Show the links *}
<!-- Include links_overview -->
{include file='links_overview.tpl'}
<!-- End include links_overview -->
