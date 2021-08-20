<!-- Start the output -->
{if isset($no_id)}
ERROR no user id set!
{else}

<div class="rightHDR">LLOYD BORRETT'S<br>
DIVING LOG<br>
SUMMARY</div>

<!-- Total dives -->
<p class="rightLIST"><span class="small">{$stats_totaldives}</span><br>
<b>{$DivedateMaxNr}</b></p>

<!-- Total bottom time -->
<p class="rightLIST"><span class="small">{$stats_totaltime}</span><br>
<b>{$total_abt}</b></p>

<!-- Last dive -->
<p class="rightLIST"><span class="small">{$stats_divedatemax}</span><br>
<b>{$LastEntryTime}</b><br>
<b>{$DivedateMax}</b><br>
{if isset($LastDiveID)}
<b><a href="{$app_path}/divesite.php{$sep1}{$LastDiveID}" title="{$dsite_number_title}{$LastDiveID} - {$LastDivePlace}">{$LastDivePlace}</a></b><br>
{/if} 
{if $LastCity != ''}
<b>{$LastCity}</b><br>
{/if}
{if $LastCountry != ''}
<b><a href="{$app_path}/divecountry.php{$sep1}{$LastCountryID}" title="{$dcountry_number_title}{$LastCountryID} - {$LastCountry}">{$LastCountry}</a></b><br>
{/if} 
[<b>
<a href="{$app_path}/index.php{$sep2}{$DivedateMaxNr}" 
 title="{$dlog_number_title}{$DivedateMaxNr}">{$DivedateMaxNr}</a>
</b>]</p>

{* Get the certification details *}
{if isset($count)}
<p class="rightLIST"><span class="small">{$cert_brevet}<b>
{* Show dive certification titles *}
{* Loop through the array *}
{section name=cell_data loop=$cells }
<br>{$cells[cell_data].brevet}
{/section}
</b></p>
{/if}

{* Equipment service reminder *}
{if $equipment_service_reminder and ($equipment_service_count > 0)}
<p class="rightLIST"><img src="{$app_path}/images/equipment_service.png"
height="50" width="50" border="0"
alt="{$equipment_service_warning}" title="{$equipment_service_warning}"></p>
{/if}

{include file='links_sum.tpl'}
{include file='app_info.tpl'}
{/if}
