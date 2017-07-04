<h1>{$pagetitle}</h1>
<!-- Include links_overview -->
{include file='links_overview.tpl'}
<!-- End include links_overview -->
{* Loop through the array *}
<table id='divesites' class="hover" cellspacing="0" cellpadding="0" width="100%">
<thead>
   <tr class="divelogtitle">
    	<td valign="bottom" width="300">{$city_title_city}</td>
        <td valign="bottom" width="80">{$city_title_type}</td>
        <td valign="bottom" width="205">{$city_title_country}</td>
        <td valign="bottom" width="40">{$city_title_dives}</td>
    </tr>
</thead>
<tbody>
{section name=cell_data loop=$cells }
    <tr class="diveoverview">
{if isset($multiuser_id)}
        <td><a href="{$app_path}/{$base_page}{$sep1}{$multiuser_id}{$sep2}{$cells[cell_data].ID}" 
          title="{$cells[cell_data].City} {$logbook_city_linktitle}">{$cells[cell_data].City}</a></td>
{else}
        <td><a href="{$app_path}/{$base_page}{$sep2}{$cells[cell_data].ID}" 
          title="{$cells[cell_data].City} {$logbook_city_linktitle}">{$cells[cell_data].City}</a></td>
{/if}
{if ($cells[cell_data].Type >= 0) && ($cells[cell_data].Type <= 5)}
        <td>{$citytypes[$cells[cell_data].Type]}</td>
{else}
        <td>-</td>
{/if}
        <td>{$cells[cell_data].Country}</td>
        <td>{$cells[cell_data].Dives}</td>
    </tr>
{/section}
</tbody>
</table>
{*	Show the links *}
{include file='datatable.tpl' tablename='divesites'}
<!-- Include links_overview -->
{include file='links_overview.tpl'}
<!-- End include links_overview -->
