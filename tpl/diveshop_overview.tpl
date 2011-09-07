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
    	<td width="50%" valign="bottom">{$dshop_title_shop}</td>
    	<td width="15%" valign="bottom">{$dshop_title_type}</td>
    	<td width="30%" valign="bottom">{$dshop_title_country}</td>
    	<td width="5%" valign="bottom">{$dshop_title_photo}</td>
    </tr>
</thead>
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
        <td><img src="{$app_path}/images/photo_icon.gif" border="0" alt="" title=""></td>
{else}
        <td>&nbsp;</td>
{/if}
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
