<!-- Include links_overview -->
{include file='links_overview.tpl'}
<!-- End include links_overview -->
{if isset($grid_display)}
{$grid}
{else}
<table class="divetable" cellspacing="0" cellpadding="0" width="100%">
<thead>
    <tr class="divelogtitle">
        <td width="55%" valign="bottom">{$equip_title_object}</td>
        <td width="35%" valign="bottom">{$equip_title_manufacturer}</td>
        <td width="5%" valign="bottom">{$equip_title_inactive}</td>
        <td width="5%" valign="bottom">{$equip_title_photo}</td>
    </tr>
</thead>
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
