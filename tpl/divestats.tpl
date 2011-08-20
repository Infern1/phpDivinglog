{include file='header.tpl'}
<div id="content">
<!-- Include links_overview -->
	{include file='links_overview.tpl'}
<!-- End include links_overview -->

{if isset($diver_overview)}
{include file='diver_overview.tpl'}
{else}

  <table class="details" cellspacing="0" cellpadding="0" width="100%">
    <colgroup>
      <col width="25%">
      <col width="25%">
      <col width="25%">
     <col width="25%">
    </colgroup>

    <tr class="divesection">
      <td colspan="4">{$pagetitle}</td>
    </tr>
  </table>

<!-- the tabs -->
<ul class="css-tabs">
  <li><a href="#">{$dive_tab_stats}</a></li>
  <li><a href="#">{$dive_tab_certs}</a></li>
</ul>

<!-- tab "panes" -->
<div class="css-panes">

<!-- pane 1 -->
  <div>
  <table class="details" cellspacing="0" cellpadding="0" width="100%">
    <colgroup>
      <col width="25%">
      <col width="25%">
      <col width="25%">
     <col width="25%">
    </colgroup>

{*
<tr><td colspan="4" class="spacing">&nbsp;</td></tr>
*}
<tr class="divesection">
 <td colspan="4">{$stats_sect_stats}</td>
</tr>

{* Show overall details *}
<tr class="divetitle">
 <td>{$stats_totaldives}</td>
 <td>{$stats_divedatemax}</td>
 <td>{$stats_divedatemin}</td>
 <td>&nbsp;</td>
</tr>

<tr class="divedetails">
 <td>{$end}</td>
 {if isset($multiuser_id)}
 <td>{$DivedateMax}&nbsp;&nbsp;<a href="{$app_path}/index.php{$sep1}{$multiuser_id}{$sep2}{$DivedateMaxNr}"
title="{$dlog_number_title}{$DivedateMaxNr}">{$DivedateMaxNr}</a></td>
 <td>{$DivedateMin}&nbsp;&nbsp;<a href="{$app_path}/index.php{$sep1}{$multiuser_id}{$sep2}{$DivedateMinNr}"
title="{$dlog_number_title}{$DivedateMinNr}">{$DivedateMinNr}</a></td>
 {else}
 <td>{$DivedateMax}&nbsp;&nbsp;<a href="{$app_path}/index.php{$sep2}{$DivedateMaxNr}" 
title="{$dlog_number_title}{$DivedateMaxNr}">{$DivedateMaxNr}</a></td>
 <td>{$DivedateMin}&nbsp;&nbsp;<a href="{$app_path}/index.php{$sep2}{$DivedateMinNr}" 
title="{$dlog_number_title}{$DivedateMinNr}">{$DivedateMinNr}</a></td>
 {/if}
 <td>&nbsp;</td>
</tr>

{* Show dive length details *}
<tr class="divetitle">
 <td>{$stats_totaltime}</td>
 <td>{$stats_divetimemax}</td>
 <td>{$stats_divetimemin}</td>
 <td>{$stats_divetimeavg}</td>
</tr>

<tr class="divedetails">
 <td>{$total_abt}</td>
 {if isset($multiuser_id)}
 <td>{$DivetimeMax}&nbsp;{$unit_time}&nbsp;&nbsp;<a href="{$app_path}/index.php{$sep1}{$multiuser_id}{$sep2}{$DivetimeMaxNr}"  title="{$dlog_number_title}{$DivetimeMaxNr}">{$DivetimeMaxNr}</a></td>
 <td>{$DivetimeMin}&nbsp;{$unit_time}&nbsp;&nbsp;<a href="{$app_path}/index.php{$sep1}{$multiuser_id}{$sep2}{$DivetimeMinNr}"  title="{$dlog_number_title}{$DivetimeMinNr}">{$DivetimeMinNr}</a></td>
 {else}
 <td>{$DivetimeMax}&nbsp;{$unit_time}&nbsp;&nbsp;<a href="{$app_path}/index.php{$sep2}{$DivetimeMaxNr}" title="{$dlog_number_title} {$DivetimeMaxNr}">{$DivetimeMaxNr}</a></td>
 <td>{$DivetimeMin}&nbsp;{$unit_time}&nbsp;&nbsp;<a href="{$app_path}/index.php{$sep2}{$DivetimeMinNr}" title="{$dlog_number_title} {$DivetimeMinNr}">{$DivetimeMinNr}</a></td>
 {/if}
 <td>{$DivetimeAvg}&nbsp;{$unit_time}</td>
</tr>

{* Show dive depth details *}
<tr class="divetitle">
 <td>&nbsp;</td>
 <td>{$stats_depthmax}</td>
 <td>{$stats_depthmin}</td>
 <td>{$stats_depthavg}</td>
</tr>

<tr class="divedetails">
 <td>&nbsp;</td>
 {if isset($multiuser_id)}
 <td>{$DepthMax}&nbsp;&nbsp;<a href="{$app_path}/index.php{$sep1}{$multiuser_id}{$sep2}{$DepthMaxNr}"  
title="{$dlog_number_title}{$DepthMaxNr}">{$DepthMaxNr}</a></td>
 <td>{$DepthMin}&nbsp;&nbsp;<a href="{$app_path}/index.php{$sep1}{$multiuser_id}{$sep2}{$DepthMinNr}"  
title="{$dlog_number_title}{$DepthMinNr}">{$DepthMinNr}</a></td>
 {else}
 <td>{$DepthMax}&nbsp;&nbsp;<a href="{$app_path}/index.php{$sep2}{$DepthMaxNr}"  title="{$dlog_number_title}{$DepthMaxNr}">{$DepthMaxNr}</a></td>
 <td>{$DepthMin}&nbsp;&nbsp;<a href="{$app_path}/index.php{$sep2}{$DepthMinNr}"  title="{$dlog_number_title}{$DepthMinNr}">{$DepthMinNr}</a></td>
 {/if}
 <td>{$DepthAvg}</td>
</tr>

{* Show dive depth tablea *}
<tr class="divedetails">
 <td colspan="4" align="center">
 {if isset($multiuser_id)}
  <img src="{$app_path}/drawpiechart.php{$sep1}{$multiuser_id}{$sep2}{$get_nr}" border="0" alt="" title="">
 {else}
  <img src="{$app_path}/drawpiechart.php" align="center" border="0" alt="" title="">
 {/if}
 </td>
</tr>

{* Show water temp details *}
<tr class="divetitle">
 <td>{$stats_watertempmin}</td>
 <td>{$stats_watertempmax}</td>
 <td>{$stats_watertempavg}</td>
 <td>&nbsp;</td>
</tr>

<tr class="divedetails">
 {if isset($multiuser_id)}
<td>{$WatertempMin}&nbsp;&nbsp;<a href="{$app_path}/index.php{$sep1}{$multiuser_id}{$sep2}{$WatertempMinNr}" title="{$dlog_number_title} {$WatertempMinNr}">{$WatertempMinNr}</a></td>
<td>{$WatertempMax}&nbsp;&nbsp;<a href="{$app_path}/index.php{$sep1}{$multiuser_id}{$sep2}{$WatertempMaxNr}" title="{$dlog_number_title} {$WatertempMaxNr}">{$WatertempMaxNr}</a></td>
<td>{$WatertempAvg}</td>
 {else}
<td>{$WatertempMin}&nbsp;&nbsp;<a href="{$app_path}/index.php{$sep2}{$WatertempMinNr}" title="{$dlog_number_title} {$WatertempMinNr}">{$WatertempMinNr}</a></td>
<td>{$WatertempMax}&nbsp;&nbsp;<a href="{$app_path}/index.php{$sep2}{$WatertempMaxNr}" title="{$dlog_number_title} {$WatertempMaxNr}">{$WatertempMaxNr}</a></td>
<td>{$WatertempAvg}</td>
 {/if}
 <td>&nbsp;</td>
</tr>

{* Show air temp details *}
<tr class="divetitle">
 <td>{$stats_airtempmin}</td>
 <td>{$stats_airtempmax}</td>
 <td>{$stats_airtempavg}</td>
 <td>&nbsp;</td>
</tr>

<tr class="divedetails">
 {if isset($multiuser_id)}
<td>{$AirtempMin}&nbsp;&nbsp;<a href="{$app_path}/index.php{$sep1}{$multiuser_id}{$sep2}{$AirtempMinNr}" title="{$dlog_number_title} {$AirtempMinNr}">{$AirtempMinNr}</a></td>
<td>{$AirtempMax}&nbsp;&nbsp;<a href="{$app_path}/index.php{$sep1}{$multiuser_id}{$sep2}{$AirtempMaxNr}" title="{$dlog_number_title} {$AirtempMaxNr}">{$AirtempMaxNr}</a></td>
<td>{$AirtempAvg}</td>
 {else}
<td>{$AirtempMin}&nbsp;&nbsp;<a href="{$app_path}/index.php{$sep2}{$AirtempMinNr}" title="{$dlog_number_title} {$AirtempMinNr}">{$AirtempMinNr}</a></td>
<td>{$AirtempMax}&nbsp;&nbsp;<a href="{$app_path}/index.php{$sep2}{$AirtempMaxNr}" title="{$dlog_number_title} {$AirtempMaxNr}">{$AirtempMaxNr}</a></td>
<td>{$AirtempAvg}</td>
 {/if}
 <td>&nbsp;</td>
</tr>

{* Show Deco and rep details *}
<tr class="divetitle">
 <td>{$stats_decodives}</td>
 <td>{$stats_nodecodives}</td>
 <td>{$stats_repdives}</td>
 <td>{$stats_norepdives}</td>
</tr>

<tr class="divedetails">
 <td>{$decodives} ({$decodives_per}%)</td>
 <td>{$nodecodives} ({$nodecodives_per}%)</td>
 <td>{$repdives} ({$repdives_per}%)</td>
 <td>{$norepdives} ({$norepdives_per}%)</td>
</tr>

{*  Show water type details *}
<tr class="divetitle">
 <td>{$stats_saltwaterdives}</td>
 <td>{$stats_freshwaterdives}</td>
 <td>{$stats_brackishdives}</td>
 <td>&nbsp;</td>
</tr>

<tr class="divedetails">
 <td>{$saltwaterdives} ({$saltwaterdives_per}%)</td>
 <td>{$freshwaterdives} ({$freshwaterdives_per}%)</td>
 <td>{$brackishdives} ({$brackishdives_per})%)</td>
 <td>&nbsp;</td>
</tr>

{* Show dive type details *}
<tr class="divetitle">
 <td>{$stats_shoredives}</td>
 <td>{$stats_boatdives}</td>
 <td>{$stats_nightdives}</td>
 <td>{$stats_driftdives}</td>
</tr>

<tr class="divedetails">
 <td>{$shoredives} ({$shoredives_per}%)</td>
 <td>{$boatdives} ({$boatdives_per}%)</td>
 <td>{$nightdives} ({$nightdives_per}%)</td>
 <td>{$driftdives} ({$driftdives_per}%)</td>
</tr>

{*  Show more dive type details *}
<tr class="divetitle">
 <td>{$stats_deepdives}</td>
 <td>{$stats_cavedives}</td>
 <td>{$stats_wreckdives}</td>
 <td>{$stats_photodives}</td>
</tr>

<tr class="divedetails">
 <td>{$deepdives} ({$deepdives_per}%)</td>
 <td>{$cavedives} ({$cavedives_per}%)</td>
 <td>{$wreckdives} ({$wreckdives_per}%)</td>
 <td>{$photodives} ({$photodives_per}%)</td>
</tr>

{*  Show single and double tank details *}
<tr class="divetitle">
 <td>{$stats_singletankdives}</td>
 <td>{$stats_doubletankdives}</td>
 <td>&nbsp;</td>
 <td>&nbsp;</td>
</tr>

<tr class="divedetails">
 <td>{$singletankdives} ({$singletankdives_per}%)</td>
 <td>{$doubletankdives} ({$doubletankdives_per}%)</td>
 <td>&nbsp;</td>
 <td>&nbsp;</td>
</tr>

{*  Show supply type details *}
<tr class="divetitle">
 <td>{$stats_ocdives}</td>
 <td>{$stats_scrdives}</td>
 <td>{$stats_ccrdives}</td>
 <td>&nbsp;</td>
</tr>

<tr class="divedetails">
 <td>{$ocdives} ({$ocdives_per}%)</td>
 <td>{$scrdives} ({$scrdives_per}%)</td>
 <td>{$ccrdives} ({$ccrdives_per}%)</td>
 <td>&nbsp;</td>
</tr>

    {*
    <tr>
      <td colspan="4" class="spacing">&nbsp;</td>
    </tr>
    <tr class="divesection">
      <td colspan="4">&nbsp;</td>
    </tr>
    *}
  </table>
  </div>


<!-- pane 2 -->
  <div>
  <table class="details" cellspacing="0" cellpadding="0" width="100%">
    <colgroup>
      <col width="25%">
      <col width="25%">
      <col width="25%">
     <col width="25%">
    </colgroup>

{* Dive Certifications *}
{* Get the certification details *}
{if isset($count)}

{*
<tr>
 <td colspan="4" class="spacing">&nbsp;</td>
</tr>
*}
<tr class="divesection">
 <td colspan="4">{$stats_sect_certs}</td>
</tr>

{* Loop through the array *}
{section name=cell_data loop=$cells }

{* Show dive certification titles *}
<tr class="divetitle">
 <td colspan="2">{$cert_org}</td>
 <td colspan="2">{$cert_brevet}</td>
</tr>

<tr class="divedetails">
 <td colspan="2">{$cells[cell_data].org}</td>
 <td colspan="2">{$cells[cell_data].brevet}</td>
</tr>

<tr class="divetitle">
 <td>{$cert_certdate}</td>
 <td>{$cert_number}</td>
 <td colspan="2">{$cert_instructor}</td>
</tr>

<tr class="divedetails">
 <td>{$cells[cell_data].certdate}</td>
 <td>{$cells[cell_data].number}</td>
 <td colspan="2">{$cells[cell_data].instructor}</td>
</tr>

{if ($cells[cell_data].scan1path != '') or ($cells[cell_data].scan2path != '')}
<tr class="divedetails">
 <td colspan="4" align="center">
{if $cells[cell_data].scan1path != ''}
  <img src="{$app_path}/{$cells[cell_data].userpath_web}{$cells[cell_data].scan1path}" title="{$cells[cell_data].title} {$cells[cell_data].cert_scan_front}" alt="{$cells[cell_data].title} {$cells[cell_data].cert_scan_front}">
{/if}
{if $cells[cell_data].scan2path != ''}
  <img src="{$app_path}/{$cells[cell_data].userpath_web}{$cells[cell_data].scan2path}" title="{$cells[cell_data].title} {$cells[cell_data].cert_scan_back}" alt="{$cells[cell_data].title} {$cells[cell_data].cert_scan_back}">
{/if}
 </td>
</tr>
{/if}

<tr>
 <td colspan="4" class="spacing"><hr></td>
</tr>
{/section}
{else}
    <tr class="divedetails">
      <td colspan="4">No certifications available.</td>
    </tr>
{/if}
</table>
  </div>

</div>

{* {literal}
<!-- This JavaScript snippet activates those tabs -->
<script type="text/javascript">

// perform JavaScript after the document is scriptable.
$(function() {
	// setup ul.tabs to work as tabs for each div directly under div.panes
	$("ul.tabs").tabs("div.panes > div");
});
</script>
{/literal} *}

{literal}
<!-- activate tabs with JavaScript -->
<script type="text/javascript">
$(function() {
	// :first selector is optional if you have only one tabs on the page
	$(".css-tabs:first").tabs(".css-panes:first > div");
});
</script>
{/literal}

{/if}

{* Show the program deails *}
<table class="details" cellspacing="0" cellpadding="0" width="100%">
<tr class="divedetails"><td class="spacing">&nbsp;</td></tr>
<tr class="divedetails">
 <td>{$poweredby} <a href="{$dlog_url}" target="_blank"
title="{$Divelogname} web site">{$Divelogname}</a> {$dlog_version}
{$dbversion}{$DivelogVersion}{$and}
<a href="{$app_url}" target="_blank"
title="{$Appname} web site">{$Appname}</a>
{$phpDivelogVersion}</td>
</tr>
<tr class="divedetails"><td class="spacing">&nbsp;</td></tr>
<tr class="divesection">
 <td colspan="4">&nbsp;</td>
</tr>
</table>

<!-- Include links_overview -->
	{include file='links_overview.tpl'}
<!-- End include links_overview -->
</div>
{include file='footer.tpl'}

