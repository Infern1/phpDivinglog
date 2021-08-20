<h1>{$pagetitle}</h1>
<!-- Include links_overview -->
{include file='links_overview.tpl'}
<!-- End include links_overview -->
{* Loop through the array *}
<table class="hover" id="diveshop" cellspacing="0" cellpadding="0" width="100%">
<thead>
    <tr class="divelogtitle">
    	<td width="300" valign="bottom">{$dshop_title_shop}</td>
    	<td width="100" valign="bottom">{$dshop_title_type}</td>
    	<td width="200" valign="bottom">{$dshop_title_country}</td>
    	<td width="25" valign="bottom">{$dshop_title_photo}</td>
    </tr>
</thead>
<tbody>
{section name=cell_data loop=$cells }
    <tr class="diveoverview">
        <td><a href="{$app_path}/{$base_page}{$sep2}{$cells[cell_data].ID}" 
          title="{$cells[cell_data].ShopName} {$cells[cell_data].ShopType} {$logbook_shop_linktitle}">{$cells[cell_data].ShopName}</a></td>
        <td>{$cells[cell_data].ShopType}</td>
        <td>{$cells[cell_data].Country}</td>
{if $cells[cell_data].PhotoPath != ''}
        <td><img src="{$app_path}/images/photo_icon.gif" border="0" alt="{$shop_photo_linktitle}{$cells[cell_data].ShopName} {$cells[cell_data].ShopType}" title="{$shop_photo_linktitle}{$cells[cell_data].ShopName} {$cells[cell_data].ShopType}"></td>
{else}
        <td>&nbsp;</td>
{/if}
    </tr>
{/section}
</tbody>
</table>
{include file='datatable.tpl' tablename='diveshop'}

{*	Show the links *}
<!-- Include links_overview -->
{include file='links_overview.tpl'}
<!-- End include links_overview -->
