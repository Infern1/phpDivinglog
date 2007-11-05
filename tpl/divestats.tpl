{include file='header.tpl'}
<div id="content">
<!-- Include links_overview -->
	{include file='links_overview.tpl'}
<!-- End include links_overview -->
{if isset($diver_overview)}
{include file='diver_overview.tpl'}
{else}
<table class="divetable" cellspacing="0" cellpadding="0" width="100%">

<tr><td colspan="4" class="spacing">&nbsp;</td></tr>

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

<tr class="divecontent">
 <td>{$end}</td>
 {if isset($multiuser_id)}
<td>{$DivedateMax}&nbsp;&nbsp;<a href="{$app_path}/index.php/{$multiuser_id}/{$DivedateMaxNr}"
title="{$dlog_number_title}{$DivedateMaxNr}">{$DivedateMaxNr}</a></td>
<td>{$DivedateMin}&nbsp;&nbsp;<a href="{$app_path}/index.php/{$multiuser_id}/{$DivedateMinNr}"
title="{$dlog_number_title}{$DivedateMinNr}">{$DivedateMinNr}</a></td>

{else}
 <td>{$DivedateMax}&nbsp;&nbsp;<a href="{$app_path}/index.php/{$DivedateMaxNr}" 
 title="{$dlog_number_title}{$DivedateMaxNr}">{$DivedateMaxNr}</a></td>
 <td>{$DivedateMin}&nbsp;&nbsp;<a href="{$app_path}/index.php/{$DivedateMinNr}" 
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

<tr class="divecontent">
 <td>{$total_abt}</td>
 {if isset($multiuser_id)}
<td>{$DivetimeMax}&nbsp;{$unit_time}&nbsp;&nbsp;<a href="{$app_path}/index.php/{$multiuser_id}/{$DivetimeMaxNr}"  title="{$dlog_number_title}{$DivetimeMaxNr}">{$DivetimeMaxNr}</a></td>
<td>{$DivetimeMin}&nbsp;{$unit_time}&nbsp;&nbsp;<a href="{$app_path}/index.php/{$multiuser_id}/{$DivetimeMinNr}"  title="{$dlog_number_title}{$DivetimeMinNr}">{$DivetimeMinNr}</a></td>
{else}
<td>{$DivetimeMax}&nbsp;{$unit_time}&nbsp;&nbsp;<a href="{$app_path}/index.php/{$DivetimeMaxNr}"  title="{$dlog_number_title} {$DivetimeMaxNr}">{$DivetimeMaxNr}</a></td>
<td>{$DivetimeMin}&nbsp;{$unit_time}&nbsp;&nbsp;<a href="{$app_path}/index.php/{$DivetimeMinNr}"  title="{$dlog_number_title} {$DivetimeMinNr}">{$DivetimeMinNr}</a></td>
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

<tr class="divecontent">
 <td>&nbsp;</td>
 {if isset($multiuser_id)}
<td>{$DepthMax}&nbsp;&nbsp;<a href="{$app_path}/index.php/{$multiuser_id}/{$DepthMaxNr}"  
title="{$dlog_number_title}{$DepthMaxNr}">{$DepthMaxNr}</a></td>
<td>{$DepthMin}&nbsp;&nbsp;<a href="{$app_path}/index.php/{$multiuser_id}/{$DepthMinNr}"  
title="{$dlog_number_title}{$DepthMinNr}">{$DepthMinNr}</a></td>
 {else}
<td>{$DepthMax}&nbsp;&nbsp;<a href="{$app_path}/index.php/{$DepthMaxNr}"  title="{$dlog_number_title}{$DepthMaxNr}">{$DepthMaxNr}</a></td>
 <td>{$DepthMin}&nbsp;&nbsp;<a href="{$app_path}/index.php/{$DepthMinNr}"  title="{$dlog_number_title}{$DepthMinNr}">{$DepthMinNr}</a></td>

 {/if}
 <td>{$DepthAvg}</td>
</tr>

{* Show dive depth tablea *}
<tr class="divecontent">
 <td>&nbsp;</td>
 <td align="right">{$stats_depth1m}&nbsp;&nbsp;</td>
 <td>{$depthrange1} ({$depthrange1_per}%)</td>
 <td>&nbsp;</td>
</tr>
<tr class="divecontent">
 <td>&nbsp;</td>
 <td align="right">{$stats_depth2m}&nbsp;&nbsp;</td>
 <td>{$depthrange2} ({$depthrange2_per}%)</td>
 <td>&nbsp;</td>
</tr>
<tr class="divecontent">
 <td>&nbsp;</td>
 <td align="right">{$stats_depth3m}&nbsp;&nbsp;</td>
 <td>{$depthrange3} ({$depthrange3_per}%)</td>
 <td>&nbsp;</td>
</tr>
<tr class="divecontent">
 <td>&nbsp;</td>
 <td align="right">{$stats_depth4m}&nbsp;&nbsp;</td>
 <td>{$depthrange4} ({$depthrange4_per}%)</td>
 <td>&nbsp;</td>
</tr>
<tr class="divecontent">
 <td>&nbsp;</td>
 <td align="right">{$stats_depth5m}&nbsp;&nbsp;</td>
 <td>{$depthrange5} ({$depthrange5_per}%)</td>
 <td>&nbsp;</td>
</tr>

{* Show water temp details *}
<tr class="divetitle">
 <td>{$stats_watertempmin}</td>
 <td>{$stats_watertempmax}</td>
 <td>{$stats_decodives}</td>
 <td>{$stats_repdives}</td>
</tr>

<tr class="divecontent">
 {if isset($multiuser_id)}
<td>{$WatertempMin}&nbsp;&nbsp;<a href="{$app_path}/index.php/{$multiuser_id}/{$WatertempMinNr}" title="{$dlog_number_title} {$WatertempMinNr}">{$WatertempMinNr}</a></td>
<td>{$WatertempMax}&nbsp;&nbsp;<a href="{$app_path}/index.php/{$multiuser_id}/{$WatertempMaxNr}" title="{$dlog_number_title} {$WatertempMaxNr}">{$WatertempMaxNr}</a></td>
 {else}
<td>{$WatertempMin}&nbsp;&nbsp;<a href="{$app_path}/index.php/{$WatertempMinNr}" title="{$dlog_number_title} {$WatertempMinNr}">{$WatertempMinNr}</a></td>
<td>{$WatertempMax}&nbsp;&nbsp;<a href="{$app_path}/index.php/{$WatertempMaxNr}" title="{$dlog_number_title} {$WatertempMaxNr}">{$WatertempMaxNr}</a></td>
 {/if}
 <td>{$decodives} ({$decodives_per}%)</td>
 <td>{$repdives} ({$repdives_per}%)</td>
</tr>

{*  Show water type details *}
<tr class="divetitle">
 <td>{$stats_saltwaterdives}</td>
 <td>{$stats_freshwaterdives}</td>
 <td>{$stats_brackishdives}</td>
 <td>&nbsp;</td>
</tr>

<tr class="divecontent">
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

<tr class="divecontent">
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

<tr class="divecontent">
 <td>{$deepdives} ({$deepdives_per}%)</td>
 <td>{$cavedives} ({$cavedives_per}%)</td>
 <td>{$wreckdives} ({$wreckdives_per}%)</td>
 <td>{$photodives} ({$photodives_per}%)</td>
</tr>


</table>

{* Dive Certifications *}
{* Get the certification details *}
{if isset($count)}

	<table class="divetable" cellspacing="0" cellpadding="0" width="100%">
	<tr><td colspan="4" class="spacing">&nbsp;</td></tr>
	<tr class="divesection"><td colspan="5">{$stats_sect_certs}</td></tr>

	{* Show dive certification titles *}
	<tr class="divetitle">
	 <td>{$cert_brevet}</td>
	 <td>{$cert_org}</td>
	 <td>{$cert_certdate}</td>
	 <td>{$cert_number}</td>
	 <td>{$cert_instructor}</td>
	</tr>

    {* Loop through the array *}
    {section name=cell_data loop=$cells }
        <tr class="divecontent">
            <td>{$cells[cell_data].brevet}</td>
			<td>{$cells[cell_data].org}</td>
			<td>{$cells[cell_data].certdate}</td>
			<td>{$cells[cell_data].number}</td>
			<td>{$cells[cell_data].instructor}</td>
		</tr>
		{if isset($cells[cell_data].scan1path) or  isset($cells[cell_data].scan2path)}
			<tr class="divecontent">
			 <td colspan="5" align="center">
			{if isset($cells[cell_data].scan1path)}
                <img src="{$app_path}/{$cells[cell_data].userpath_web}{$cells[cell_data].scan1path}" title="{$cells[cell_data].title} {$cells[cell_data].cert_scan_front}" alt="{$cells[cell_data].title} {$cells[cell_data].cert_scan_front}">
			{/if}
			{if isset($cells[cell_data].scan2path)}
				<img src="{$app_path}/{$cells[cell_data].userpath_web}{$cells[cell_data].scan2path}" title="{$cells[cell_data].title} {$cells[cell_data].cert_scan_back}" alt="{$cells[cell_data].title} {$cells[cell_data].cert_scan_back}">
			{/if}
			<br>
			 &nbsp;</td>
			</tr>
        {/if}
    {/section}
    {/if}
	</tr>
	</table>
{/if}
<!-- Include links_overview -->
	{include file='links_overview.tpl'}
<!-- End include links_overview -->
</div>
{include file='footer.tpl'}

