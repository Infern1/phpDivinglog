<!-- Start the output -->
{if isset($no_id)  }
ERROR no user id set!
{else}


    <div class="rightHDR"> LLOYD'S DIVING LOG<br> SUMMARY </div>

<!-- Total dives -->
   <p class="rightLIST">
   <span class="small"> {$stats_totaldives}</span><br>
   <b>{$DivedateMaxNr}</b>
   </p>

<!-- Total bottom time -->
<p class="rightLIST">
<span class="small">{$stats_totaltime}</span><br>
<b>{$total_abt} {$stats_totaltime_units}</b></p>

<!-- Last dive -->
<p class="rightLIST">
<span class="small">{$stats_divedatemax}</span><br>
 <b>{$LastEntryTime}</b><br>
 <b>{$DivedateMax}</b><br>
{if isset($LastDiveID)}
    <b><a href="{$app_path}/divesite.php{$sep1}{$LastDiveID}" title="{$LastDivePlace}  {$logbook_place_linktitle}">{$LastDivePlace}</a></b>
    <br>
{/if} 
{if $LastCity != ''}
    <b>{$LastCity}</b><br>
{/if}

{if $LastCountry != ''}
    <b>{$LastCountry}</b><br>
{/if} 
[
{if isset($multiuser_id)}
<a href="{$app_path}/index.php{$sep1}{$multiuser_id}{$sep2}{$DivedateMaxNr}"
title="{$dlog_number_title}{$DivedateMaxNr}">{$DivedateMaxNr}</a>
{else}
<a href="{$app_path}/index.php{$sep2}{$DivedateMaxNr}" 
 title="{$dlog_number_title}{$DivedateMaxNr}">{$DivedateMaxNr}</a>
{/if}
]

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

{include file='links_sum.tpl'}
{include file='app_info.tpl'}
{/if}

