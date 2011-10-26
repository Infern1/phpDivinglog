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
    	<td width="250" valign="bottom">{$dtrip_title_trip}</td>
    	<td width="225" valign="bottom">{$dtrip_title_shop}</td>
    	<td width="125" valign="bottom">{$dtrip_title_country}</td>
    	<td width="25" valign="bottom">{$dtrip_title_photo}</td>
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
          title="{$cells[cell_data].tripname} {$logbook_trip_linktitle}">{$cells[cell_data].tripname}</a></td>
{else}
        <td><a href="{$app_path}/{$base_page}{$sep2}{$cells[cell_data].id}" 
          title="{$cells[cell_data].tripname} {$logbook_trip_linktitle}">{$cells[cell_data].tripname}</a></td>
{/if}
        <td>{$cells[cell_data].shopname}</td>
        <td>{$cells[cell_data].country}</td>
{if $cells[cell_data].photopath != ''}
        <td><img src="{$app_path}/images/photo_icon.gif" border="0" alt="{$trip_photo_icontitle}{$cells[cell_data].tripname}" title="{$trip_photo_icontitle}{$cells[cell_data].tripname}"></td>
{else}
        <td>&nbsp;</td>
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
