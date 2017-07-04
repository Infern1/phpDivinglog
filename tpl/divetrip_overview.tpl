<h1>{$pagetitle}</h1>
<!-- Include links_overview -->
{include file='links_overview.tpl'}
<!-- End include links_overview -->
{* Loop through the array *}
<table class="hover" id="divetrip" cellspacing="0" cellpadding="0" width="100%">
<thead>
   <tr class="divelogtitle">
    	<td width="250" valign="bottom">{$dtrip_title_trip}</td>
    	<td width="225" valign="bottom">{$dtrip_title_shop}</td>
    	<td width="125" valign="bottom">{$dtrip_title_country}</td>
    	<td width="25" valign="bottom">{$dtrip_title_photo}</td>
    </tr>
</thead>
<tbody>
{section name=cell_data loop=$cells }
    <tr class="diveoverview">
{if isset($multiuser_id)}
        <td><a href="{$app_path}/{$base_page}{$sep1}{$multiuser_id}{$sep2}{$cells[cell_data].ID}" 
          title="{$cells[cell_data].TripName} {$logbook_trip_linktitle}">{$cells[cell_data].TripName}</a></td>
{else}
        <td><a href="{$app_path}/{$base_page}{$sep2}{$cells[cell_data].ID}" 
          title="{$cells[cell_data].TripName} {$logbook_trip_linktitle}">{$cells[cell_data].TripName}</a></td>
{/if}
        <td>{$cells[cell_data].ShopName}</td>
        <td>{$cells[cell_data].Country}</td>
{if $cells[cell_data].PhotoPath != ''}
        <td><img src="{$app_path}/images/photo_icon.gif" border="0" alt="{$trip_photo_icontitle}{$cells[cell_data].TripName}" title="{$trip_photo_icontitle}{$cells[cell_data].TripName}"></td>
{else}
        <td>&nbsp;</td>
{/if}
    </tr>
{/section}
</tbody>
</table>
{include file='datatable.tpl' tablename='divetrip'}
{*	Show the links *}
<!-- Include links_overview -->
{include file='links_overview.tpl'}
<!-- End include links_overview -->
