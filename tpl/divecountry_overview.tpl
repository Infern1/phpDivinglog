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
      <td colspan="2">{$pages}</td>
    </tr>
{/if}
    <tr class="divelogtitle">
    	<td width="575" valign="bottom">{$country_title_country}</td>
    	<td width="50" valign="bottom">{$country_title_count}</td>
    </tr>
</thead>
{if $pages != ''}
  <tfoot>
    <tr>
      <td colspan="2">{$pages}</td>
    </tr>
  </tfoot>
{/if}
<tbody>
{section name=cell_data loop=$cells }
    <tr class="diveoverview">
{if isset($multiuser_id)}
        <td><a href="{$app_path}/{$base_page}{$sep1}{$multiuser_id}{$sep2}{$cells[cell_data].id}" 
          title="{$cells[cell_data].country} {$logbook_country_linktitle}">{$cells[cell_data].country}</a></td>
{else}
        <td><a href="{$app_path}/{$base_page}{$sep2}{$cells[cell_data].id}" 
          title="{$cells[cell_data].country} {$logbook_country_linktitle}">{$cells[cell_data].country}</a></td>
{/if}
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
