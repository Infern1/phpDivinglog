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
    	<td width="300" valign="bottom">{$dshop_title_shop}</td>
    	<td width="100" valign="bottom">{$dshop_title_type}</td>
    	<td width="200" valign="bottom">{$dshop_title_country}</td>
    	<td width="25" valign="bottom">{$dshop_title_photo}</td>
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
          title="{$cells[cell_data].shopname} {$cells[cell_data].shoptype} {$logbook_shop_linktitle}">{$cells[cell_data].shopname}</a></td>
{else}
        <td><a href="{$app_path}/{$base_page}{$sep2}{$cells[cell_data].id}" 
          title="{$cells[cell_data].shopname} {$cells[cell_data].shoptype} {$logbook_shop_linktitle}">{$cells[cell_data].shopname}</a></td>
{/if}
        <td>{$cells[cell_data].shoptype}</td>
        <td>{$cells[cell_data].country}</td>
{if $cells[cell_data].photopath != ''}
        <td><img src="{$app_path}/images/photo_icon.gif" border="0" alt="{$shop_photo_linktitle}{$cells[cell_data].shopname} {$cells[cell_data].shoptype}" title="{$shop_photo_linktitle}{$cells[cell_data].shopname} {$cells[cell_data].shoptype}"></td>
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
