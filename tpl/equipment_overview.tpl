{if $show_equip_service == 1}
<h1><img src="{$app_path}/images/equipment_service.png"
height="50" width="50" border="0"
alt="{$equipment_service_warning}" title="{$equipment_service_warning}">
{$equipment_service_warning}</h1>
{else}
<h1>{$pagetitle}</h1>
{/if}
<!-- Include links_overview -->
{include file='links_overview.tpl'}
<!-- End include links_overview -->
{if isset($grid_display)}
{$grid}
{else}
<table class="divetable" cellspacing="0" cellpadding="0" width="100%">
<thead>
{if $pages != ''}
    <tr>
      <td colspan="5">{$pages}</td>
    </tr>
{/if}
    <tr class="divelogtitle">
        <td width="350" valign="bottom">{$equip_title_object}</td>
        <td width="180" valign="bottom">{$equip_title_manufacturer}</td>
        <td width="30" valign="bottom">{$equip_title_inactive}</td>
        <td width="25" valign="bottom">{$equip_title_photo}</td>
        <td width="40" valign="bottom">{$equip_title_service}</td>
    </tr>
</thead>
{if $pages != ''}
  <tfoot>
    <tr>
      <td colspan="5">{$pages}</td>
    </tr>
  </tfoot>
{/if}
<tbody>
{* Loop through the array *}
{section name=cell_data loop=$cells }
<tr class="diveoverview">
{if isset($multiuser_id)}
<td><a href="{$app_path}/{$base_page}{$sep1}{$multiuser_id}{$sep2}{$cells[cell_data].id}" 
             title="{$cells[cell_data].object} {$logbook_equip_linktitle}" >{$cells[cell_data].object}</a></td>
{else}
<td><a href="{$app_path}/{$base_page}{$sep2}{$cells[cell_data].id}" 
             title="{$cells[cell_data].object} {$logbook_equip_linktitle}" >{$cells[cell_data].object}</a></td>
{/if}
<td>{$cells[cell_data].manufacturer}</td>
{if $cells[cell_data].inactive == 'True'}
<td><img src="{$app_path}/images/icon_inactive_16.png" border="0" alt="{$equip_inactive_inactive}" title="{$equip_inactive_inactive}"></td>
{else}
<td><img src="{$app_path}/images/icon_active_16.png" border="0" alt="{$equip_inactive_active}" title="{$equip_inactive_active}"></td>
{/if}
{if $cells[cell_data].photopath != ''}
<td><img src="{$app_path}/images/photo_icon.gif" border="0" alt="{$equip_photo_linktitle}{$cells[cell_data].object}" title="{$equip_photo_linktitle}{$cells[cell_data].object}"></td>
{else}
<td>&nbsp;</td>
{/if}
{if $cells[cell_data].service == '1'}
<td><img src="{$app_path}/images/icon_warning_16.png" 
border="0" width="16" height="16"
alt="{$equip_service_warning}" title="{$equip_service_warning}"></td>
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
