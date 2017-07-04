<h1>{$pagetitle}</h1>
<!-- Include links_overview -->
{include file='links_overview.tpl'}
<!-- End include links_overview -->
{* Loop through the array *}
<table class="hover" id="country" cellspacing="0" cellpadding="0" width="100%">
<thead>
    <tr class="divelogtitle">
    	<td width="575" valign="bottom">{$country_title_country}</td>
    	<td width="50" valign="bottom">{$country_title_count}</td>
    </tr>
</thead>
<tbody>
{section name=cell_data loop=$cells }
    <tr class="diveoverview">
{if isset($multiuser_id)}
        <td><a href="{$app_path}/{$base_page}{$sep1}{$multiuser_id}{$sep2}{$cells[cell_data].ID}" 
          title="{$cells[cell_data].country} {$logbook_country_linktitle}">{$cells[cell_data].Country}</a></td>
{else}
        <td><a href="{$app_path}/{$base_page}{$sep2}{$cells[cell_data].ID}" 
          title="{$cells[cell_data].Country} {$logbook_country_linktitle}">{$cells[cell_data].Country}</a></td>
{/if}
        <td>{$cells[cell_data].Dives}</td>
    </tr>
{/section}
</tbody>
</table>
{include file='datatable.tpl' tablename='country'}

{*	Show the links *}
<!-- Include links_overview -->
{include file='links_overview.tpl'}
<!-- End include links_overview -->
