<!-- Include links_details -->
{include file='links_details.tpl'}
<!-- End include links_details -->
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
  <li><a href="#">{$dive_tab_logbook}</a></li>
  <li><a href="#">{$dive_tab_breathing}</a></li>
{if $Comments != ''}
  <li><a href="#">{$dive_tab_comments}</a></li>
{/if}
{if isset($pics) || isset($pics2)}
  <li><a href="#">{$dive_tab_photos}</a></li>
{/if}
{if isset($profile)}
  <li><a href="#">{$dive_tab_profile}</a></li>
{/if}
{if $userdefined_count == '1'}
  <li><a href="#">{$dive_tab_userdefined}</a></li>
{/if}
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

{* Show Dive Information *}

    {* Show main dive details *}
    <tr class="divetitle">
      <td>{$logbook_divedate}</td>
      <td>{$logbook_entrytime}</td>
      <td>{$logbook_divetime}</td>
      <td>{$logbook_depth}</td>
    </tr>

    <tr class="divedetails">
    {if $dive_date != ''}
      <td>{$dive_date}</td>
    {else}
      <td>-</td>
    {/if} 

    {if $entry_time != ''}
      <td>{$entry_time}</td>
    {else}
      <td>-</td>
    {/if} 

    {if $dive_time != ''}
      <td>{$dive_time}</td>
    {else}
      <td>-</td>
    {/if} 

    {if $dive_depth != ''}
      <td>{$dive_depth}</td>
    {else}
      <td>-</td>
    {/if} 
    </tr>

    {*	Show dive location details *}
    <tr class="divetitle">
      <td colspan="2">{$logbook_place}</td>
      <td colspan="2">{$logbook_city}</td>
    </tr>

    <tr class="divedetails">
    {if isset($dive_site_nr)} 
      {if isset($multiuser_id)}
      <td colspan="2"><a href="{$app_path}/divesite.php{$sep1}{$multiuser_id}{$sep2}{$dive_site_nr}" title="{$dive_place} {$logbook_place_linktitle}">{$dive_place}</a></td>
      {else}
      <td colspan="2"><a href="{$app_path}/divesite.php{$sep2}{$dive_site_nr}" title="{$dive_place} {$logbook_place_linktitle}">{$dive_place}</a></td>
      {/if}
    {else}
      <td colspan="2">-</td>
    {/if} 

    {if isset($dive_city_nr)} 
      {if isset($multiuser_id)}
      <td colspan="2"><a href="{$app_path}/divecity.php{$sep1}{$multiuser_id}{$sep2}{$dive_city_nr}" title="{$dive_city} {$logbook_city_linktitle}">{$dive_city}</a></td>
      {else}
      <td colspan="2"><a href="{$app_path}/divecity.php{$sep2}{$dive_city_nr}" title="{$dive_city} {$logbook_city_linktitle}">{$dive_city}</a></td>
      {/if}
    {else}
      <td colspan="2">-</td>
    {/if}
    </tr>

    <tr class="divetitle">
      <td colspan="2">{$logbook_country}</td>
      <td colspan="2">{$logbook_divemaster}</td>
    </tr>

    <tr class="divedetails">

    {if isset($dive_country_nr)} 
      {if isset($multiuser_id)}
      <td colspan="2"><a href="{$app_path}/divecountry.php{$sep1}{$multiuser_id}{$sep2}{$dive_country_nr}" title="{$dive_country} {$logbook_country_linktitle}">{$dive_country}</a></td>
      {else}
      <td colspan="2"><a href="{$app_path}/divecountry.php{$sep2}{$dive_country_nr}" title="{$dive_country} {$logbook_country_linktitle}">{$dive_country}</a></td>
      {/if}
    {else}
      <td colspan="2">-</td>
    {/if} 

    {if $divemaster != ''} 
      <td colspan="2">{$divemaster}</td>
    {else}
      <td colspan="2">-</td>
    {/if}
    </tr>
    
    <tr class="divetitle">
      <td colspan="2">{$dive_shop_head}</td>
      <td colspan="2">{$dive_trip_head}</td>
    </tr>

    <tr class="divedetails">
    {if $dive_shop_name != "-"} 
      {if isset($multiuser_id)}
      <td colspan="2"><a href="{$app_path}/diveshop.php{$sep1}{$multiuser_id}{$sep2}{$dive_shop_nr}" title="{$dive_shop_name} {$logbook_shop_linktitle}">{$dive_shop_name}</a></td>
      {else}
      <td colspan="2"><a href="{$app_path}/diveshop.php{$sep2}{$dive_shop_nr}" title="{$dive_shop_name} {$logbook_shop_linktitle}">{$dive_shop_name}</a></td>
      {/if}
    {else}
      <td colspan="2">-</td>
    {/if}

    {if $dive_trip_name != '-'}
      {if isset($multiuser_id)}
      <td colspan="2"><a href="{$app_path}/divetrip.php{$sep1}{$multiuser_id}{$sep2}{$dive_trip_nr}" title="{$dive_trip_name} {$logbook_shop_linktitle}">{$dive_trip_name}</a></td>
      {else}
      <td colspan="2"><a href="{$app_path}/divetrip.php{$sep2}{$dive_trip_nr}" title="{$dive_trip_name} {$logbook_shop_linktitle}">{$dive_trip_name}</a></td>
      {/if}
    {else}
      <td colspan="2">-</td>
    {/if}
    </tr>

    {* Show buddy details *}
    {if $buddy != ''}
    <tr class="divetitle">
      <td colspan="4">{$logbook_buddy}</td>
    </tr>
    <tr class="divedetails">
      <td colspan="4">{$buddy}</td>
    </tr>
    {/if}

    {* Dive Details Section *}
    {*
    <tr>
      <td colspan="4" class="spacing">&nbsp;</td>
    </tr>
    *}
    <tr class="divesection">
      <td colspan="4">{$dive_sect_details}</td>
    </tr>

    <tr class="divetitle">
      <td>{$logbook_entry}</td>
      <td colspan="2">{$logbook_boat}</td>
      <td>&nbsp;</td>
    </tr>

    <tr class="divedetails">
    {if $Entry != ''}
      <td>{$Entry}</td>
    {else}
      <td>-</td>
    {/if}

    {if $Boat != ''}
      <td colspan="2">{$Boat}</td>
    {else}
      <td colspan="2">-</td>
    {/if}

      <td>&nbsp;</td>
    </tr>

    <tr class="divetitle">
      <td>{$logbook_pgstart}</td>
      <td>{$logbook_entrytime}</td>
      <td>{$logbook_exittime}</td>
      <td>{$logbook_pgend}</td>
    </tr>

    <tr class="divedetails">
    {if $PGStart != ''}
      <td style="border-bottom:thick double #000000;">{$PGStart}</td>
    {else}
      <td style="border-bottom:thick double #000000;">-</td>
    {/if}

    {if $entry_time != ''}
      <td>{$entry_time}</td>
    {else}
      <td>-</td>
    {/if} 

    {if $ExitTime != ''}
      <td>{$ExitTime}</td>
    {else }
      <td>-</td>
    {/if}

    {if $PGEnd != ''}
      <td style="border-bottom:thick double #000000;">{$PGEnd}</td>
    {else}
      <td style="border-bottom:thick double #000000;">-</td>
    {/if}
    </tr>

    <tr class="divetitle">
      <td style="border-right:thick double #000000;">{$logbook_altitude}</td>
      <td>{$logbook_rep}</td>
      <td style="border-right:thick double #000000;">{$logbook_surfint}</td>
      <td>&nbsp;</td>
    </tr>

    <tr class="divedetails">
    {if $Altitude != ''}
      <td style="border-right:thick double #000000;">{$Altitude}</td>
    {else}
      <td style="border-right:thick double #000000;">-</td>
    {/if}

    {if $Rep != ''}
      <td>{$Rep}</td>
    {else}
      <td>-</td>
    {/if}

    {if $Surfint != ''}
      <td style="border-right:thick double #000000;">{$Surfint}</td>
    {else }
      <td style="border-right:thick double #000000;">-</td>
    {/if}

      <td>&nbsp;</td>
    </tr>

    <tr class="divetitle">
      <td style="border-right:thick double #000000;">{$logbook_depth}</td>
      <td>&nbsp;</td>
      <td style="border-right:thick double #000000;">&nbsp;</td>
      <td>{$logbook_avgdepth}</td>
    </tr>

    <tr class="divedetails">
    {if $dive_depth != ''}
      <td style="border-right:thick double #000000;">{$dive_depth}</td>
    {else}
      <td style="border-right:thick double #000000;">-</td>
    {/if}

      <td style="border-bottom:thick double #000000;">&nbsp;</td>

      <td style="border-bottom:thick double #000000; border-right:thick double #000000;">&nbsp;</td>

    {if $averagedepth != ''}
      <td>{$averagedepth} {$unit_length_short}</td>
    {else}
      <td>-</td>
    {/if}
    </tr>

    <tr class="divetitle">
      <td>&nbsp;</td>
      <td colspan="2" style="text-align:center;">{$logbook_divetime}</td>
      <td>{$logbook_deco}</td>
    </tr>

    <tr class="divedetails">
      <td>&nbsp;</td>

    {if $dive_time != ''}
      <td colspan="2" style="text-align:center;">{$dive_time}</td>
    {else}
      <td colspan="2" style="text-align:center;">-</td>
    {/if} 

    {if $Deco != ''}
      <td>{$Deco}</td>
    {else}
      <td>-</td>
    {/if}
    </tr>

    {if isset($Decostops) }
    <tr>
      <td colspan="4" class="spacing">&nbsp;</td>
    </tr>
    <tr class="divesection">
      <td colspan="4">{$logbook_decostops}</td>
    </tr>
    <tr class="divedetails">
      <td colspan="4">{$stops}</td>
    </tr>
    {/if}

    {* Conditions Section *}
    {*
    <tr>
      <td colspan="4" class="spacing">&nbsp;</td>
    </tr>
    *}
    <tr class="divesection">
      <td colspan="4">{$dive_sect_conditions}</td>
    </tr>

    {* Show weather conditions *}
    <tr class="divetitle">
      <td>{$logbook_weather}</td>
      <td>{$logbook_airtemp}</td>
      <td>{$logbook_watertemp}</td>
      <td>&nbsp;</td>
    </tr>

    <tr class="divedetails">
    {if $Weather != ''}
      <td>{$Weather}</td>
    {else}
      <td>-</td>
    {/if}

    {if $Airtemp != ''}
      <td>{$Airtemp}</td>
    {else}
      <td>-</td>
    {/if}

    {if $Watertemp != ''}
      <td>{$Watertemp}</td>
    {else}
      <td>-</td>
    {/if}
      <td>&nbsp;</td>
    </tr>

    {* Show water conditions *}
    <tr class="divetitle">
      <td>{$logbook_water}</td>
      <td>{$logbook_surface}</td>
      <td>{$logbook_uwcurrent}</td>
      <td>&nbsp;</td>
    </tr>

    <tr class="divedetails">
    {if $Water != ''}
      <td>{$Water}</td>
    {else}
      <td>-</td>
    {/if}

    {if $Surface != ''}
      <td>{$Surface}</td>
    {else}
      <td>-</td>
    {/if}

    {if $UWCurrent != ''}
      <td>{$UWCurrent}</td>
    {else}
      <td>-</td>
    {/if}
      <td>&nbsp;</td>
    </tr>

    {* Show water visibility *}
    <tr class="divetitle">
      <td>{$logbook_visibility}</td>
      <td>{$logbook_vishor}</td>
      <td>{$logbook_visver}</td>
    </tr>

    <tr class="divedetails">
    {if $Visibility != ''}
      <td>{$Visibility}</td>
    {else}
      <td>-</td>
    {/if}

    {if $VisHor != ''}
      <td>{$VisHor}</td>
    {else}
      <td>-</td>
    {/if}

    {if $VisVer != ''}
      <td>{$VisVer}</td>
    {else}
      <td>-</td>
    {/if}
      <td>&nbsp;</td>
    </tr>

    {* Equipment Section *}
    {*
    <tr>
      <td colspan="4" class="spacing">&nbsp;</td>
    </tr>
    *}
    <tr class="divesection">
      <td colspan="4">{$dive_sect_equipment}</td>
    </tr>
    <tr class="divetitle">
      <td>{$logbook_weight}</td>
      <td>{$logbook_divesuit}</td>
      <td colspan="2" >{$logbook_computer}</td>
    </tr>

    <tr class="divedetails">
    {if $Weight != ''}
      <td>{$Weight}</td>
    {else}
      <td>-</td>
    {/if}

    {if $Divesuit != ''}
      <td>{$Divesuit}</td>
    {else}
      <td>-</td>
    {/if}

    {if $Computer != ''}
      <td colspan="2">{$Computer}</td>
    {else}
      <td>-</td>
    {/if}
    </tr>

    {if isset($UsedEquip)}
    <tr class="divetitle">
      <td colspan="4">{$logbook_usedequip}</td>
    </tr>
    <tr class="divedetails">
      <td colspan="4">
      {foreach from=$equip_link key=id item=i name=equipment}
        {if isset($multiuser_id)}
        <a 
href="{$app_path}/equipment.php{$sep1}{$multiuser_id}{$sep2}{$i.equipmentnr}" title="{$i.divegear} {$logbook_place_linktitle}">{$i.divegear}</a>{if !$i@last}{if $comma_separated}{$comma_separator}{else}&nbsp;{/if}{/if}
        {else}
         <a href="{$app_path}/equipment.php{$sep2}{$i.equipmentnr}" 
title="{$i.divegear} {$logbook_place_linktitle}">{$i.divegear}</a>{if !$i@last}{if $comma_separated}{$comma_separator}{else}&nbsp;{/if}{/if}
        {/if}
      {/foreach}
      </td>
    </tr>
    {/if}

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

    {* Breathing Section *}
    {*
    <tr>
      <td colspan="4" class="spacing">&nbsp;</td>
    </tr>
    <tr class="divesection">
      <td colspan="4">{$dive_sect_breathing}</td>
    </tr>
    *}

    {foreach from=$tanksfordive key=id item=i name=tanks}

    {if $i.tanksfordive['Set'] != '1'}
    <tr class="divedetails">
      <td colspan="4"><hr></td>
    </tr>
    {/if}

    <tr class="divedetails">
      <td colspan="4">{$logbook_tankset}{$i.tanksfordive['Set']}</td>
    </tr>

    {* Show tank details *}
    <tr class="divetitle">
      <td>{$logbook_tanktype}</td>
      <td>{$logbook_tanksize}</td>
      <td>{$logbook_presw}</td>
      <td>{$logbook_supplytype}</td>
    </tr>

    <tr class="divedetails">
    {if $i.tanksfordive['Tanktype'] != ''}
      <td>{$i.tanksfordive['Tanktype']}</td>
    {else}
      <td>-</td>
    {/if}

    {if $i.tanksfordive['Tanksize'] != ''}
      <td>{$i.tanksfordive['DblTankImage']}{$i.tanksfordive['Tanksize']}</td>
    {else}
      <td>-</td>
    {/if}

    {if $i.tanksfordive['PresW'] != ''}
      <td>{$i.tanksfordive['PresW']}</td>
    {else}
      <td>-</td>
    {/if}

    {if $i.tanksfordive['SupplyType'] != ''}
      <td>{$i.tanksfordive['SupplyTypeImage']}{$i.tanksfordive['SupplyType']}</td>
    {else}
      <td>-</td>
    {/if}
    </tr>
 
    <tr class="divetitle">
      <td>{$logbook_o2}</td>
      <td>{$logbook_he}</td>
      <td>{$logbook_minppo2}</td>
      <td>{$logbook_maxppo2}</td>
    </tr>

    <tr class="divedetails">
    {if $i.tanksfordive['O2'] != ''}
      <td>{$i.tanksfordive['O2']}</td>
    {else}
      <td>-</td>
    {/if}

    {if $i.tanksfordive['He'] != ''}
      <td>{$i.tanksfordive['He']}</td>
    {else}
      <td>-</td>
    {/if}

    {if $i.tanksfordive['MinPPO2'] != ''}
      <td>{$i.tanksfordive['MinPPO2']}</td>
    {else}
      <td>-</td>
    {/if}

    {if $i.tanksfordive['MaxPPO2'] != ''}
      <td>{$i.tanksfordive['MaxPPO2']}</td>
    {else}
      <td>-</td>
    {/if}
    </tr>
 
    <tr class="divetitle">
      <td rowspan="2">{$i.tanksfordive['GasTypeImage']} {$i.tanksfordive['GasImageAlt']}</td>
      <td>{$logbook_mod}</td>
      <td>{$logbook_ead}</td>
      <td>{$logbook_end}</td>
    </tr>

    <tr class="divedetails">
    {if $i.tanksfordive['MOD'] != ''}
      <td>{$i.tanksfordive['MOD']}</td>
    {else}
      <td>-</td>
    {/if}

    {if $i.tanksfordive['EAD'] != ''}
      <td>{$i.tanksfordive['EAD']}</td>
    {else}
      <td>-</td>
    {/if}

    {if $i.tanksfordive['END'] != ''}
      <td>{$i.tanksfordive['END']}</td>
    {else}
      <td>-</td>
    {/if}
    </tr>

    {* Show pressure details *}
    <tr class="divetitle">
      <td>{$logbook_press}</td>
      <td>{$logbook_prese}</td>
      <td>{$logbook_presdiff}</td>
      <td>&nbsp;</td>
    </tr>

    <tr class="divedetails">
    {if $i.tanksfordive['PresS'] != ''}
      <td>{$i.tanksfordive['PresS']}</td>
    {else}
      <td>-</td>
    {/if}

    {if $i.tanksfordive['PresE'] != ''}
      <td>{$i.tanksfordive['PresE']}</td>
    {else }
      <td>-</td>
    {/if}
	
    {if $i.tanksfordive['PresSPresE'] != ''}
      <td>{$i.tanksfordive['PresSPresE']}</td>
    {else }
      <td>-</td>
    {/if}

      <td>&nbsp;</td>
    </tr>

    {* Show avg depth and sac details for tank set *}
    <tr class="divetitle">
      <td>{$logbook_avgdepth}</td>
      <td>{$logbook_sac}</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>

    <tr class="divedetails">
    {if $i.tanksfordive['averagedepth'] != ''}
      <td>{$i.tanksfordive['averagedepth']} {$i.tanksfordive['unit_length_short']}</td>
    {else}
      <td>-</td>
    {/if}

    {if $i.tanksfordive['sac'] != ''}
      <td>{$i.tanksfordive['sac']}</td>
    {else}
      <td>-</td>
    {/if}
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
 
    {/foreach}

    <tr class="divedetails">
      <td colspan="4"><hr></td>
    </tr>

    <tr class="divetitle">
      <td>{$logbook_avgdepth}</td>
      <td>{$logbook_sac}</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>

    <tr class="divedetails">
    {if $averagedepth != ''}
      <td>{$averagedepth} {$unit_length_short}</td>
    {else}
      <td>-</td>
    {/if}

    {if $sac != ''}
      <td>{$sac}</td>
    {else}
      <td>-</td>
    {/if}

      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>

    <tr class="divetitle">
      <td colspan="4">{$logbook_gas}</td>
    </tr>

    <tr class="divedetails">
    {if $Gas != ''}
      <td colspan="4">{$Gas}</td>
    {else}
      <td>-</td>
    {/if}
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


<!-- pane 3 -->
{if $Comments != ''}
  <div>
  <table class="details" cellspacing="0" cellpadding="0" width="100%">
    <colgroup>
      <col width="25%">
      <col width="25%">
      <col width="25%">
     <col width="25%">
    </colgroup>

    {if $Comments != ''}
    {*
    <tr class="divesection">
      <td colspan="4">{$dive_sect_comments|default:'&nbsp;'}</td>
    </tr>
    *}
    <tr class="divedetails">
      <td colspan="4">{$Comments}</td>
    </tr>
    {else} 
    <tr class="divedetails">
      <td colspan="4"><p>No comments.</p></td>
    </tr>
    {/if}
    <tr class="divesection">
      <td colspan="4">&nbsp;</td>
    </tr>
  </table>
  </div>
{/if}


<!-- pane 4 -->
{if isset($pics) || isset($pics2)}
  <div>
  <table class="details" cellspacing="0" cellpadding="0" width="100%">
    <colgroup>
      <col width="25%">
      <col width="25%">
      <col width="25%">
     <col width="25%">
    </colgroup>

    {* Dive pictures *}
    {if isset($pics) || isset($pics2)}
    {if isset($pics)}
    <tr><td colspan="4" class="spacing">&nbsp;</td></tr>
    <tr>
      <td colspan="4">
        <p class="centeredImage"><a href="{$picpath_web}"
rel="lightbox[others]" title="{$divepic_linktit}">{$divepic_pt}</a>
        {foreach from=$image_link item=foo}
          {$foo}
        {/foreach}
        </p>
      </td>
    </tr>
    {/if}

    {if isset($pics2)}
    <tr>
      <td colspan="4" class="spacing">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="4">
        <p class="centeredImage">
          {foreach from=$image_link key=id item=i name=images}
            {if isset($pics_resized)}
            <a id="thumb{$id}" href="{$web_root}/{$i.img_url}" 
class="highslide" onclick="return hs.expand(this)" 
title="{$i.img_title}"><img 
src="{$web_root}/{$i.img_thumb_url}" 
alt="Highslide JS" 
title="{$i.img_title}" 
height="{$thumb_height}" width="{$thumb_width}" ></a>
            {else}
            <a id="thumb{$id}" href="{$web_root}/{$i.img_url}" 
class="highslide" onclick="return hs.expand(this)" 
title="{$i.img_title}"><img
src="{$web_root}/imagesize.php?w={$thumb_width}&img={$i.img_url}" 
alt="Highslide JS" 
title="{$i.img_title}" 
height="{$thumb_height}" width="{$thumb_width}" ></a>
            {/if}
          {/foreach}
        </p>
      </td>
    </tr>
    {/if}
    {else} 
    <tr>
      <td colspan="4">No photos.</td>
    </tr>
    {/if}
    <tr class="divesection">
      <td colspan="4">&nbsp;</td>
    </tr>
  </table>
  </div>
{/if}


<!-- pane 5 -->
{if isset($profile)}
  <div>
  <table class="details" cellspacing="0" cellpadding="0" width="100%">
    <colgroup>
      <col width="25%">
      <col width="25%">
      <col width="25%">
     <col width="25%">
    </colgroup>

    {* Dive profile *}
    {if isset($profile)}
    <tr>
      <td colspan="4" class="spacing">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="4">
        <p class="centeredImage">
        {if isset($multiuser_id)}
        <img src="{$app_path}/drawprofile.php{$sep1}{$multiuser_id}{$sep2}{$get_nr}" 
alt="{$dive_profile_title}" 
title="{$dive_profile_title}">
        {else}
        <img src="{$app_path}/drawprofile.php{$sep2}{$get_nr}" 
alt="{$dive_profile_title}" 
title="{$dive_profile_title}">
        {/if}
        </p>
      </td>
    </tr>
    {else} 
    <tr>
      <td colspan="4">No profile.</td>
    </tr>
    {/if}

    {*
    <tr class="divesection">
      <td colspan="4">&nbsp;</td>
    </tr>
    *}
  </table>
  </div>
{/if}


<!-- pane 5 -->
{* User-defined fields *}
{if $userdefined_count == '1'}
  <div>
  <table class="details" cellspacing="0" cellpadding="0" width="100%">
    <colgroup>
      <col width="25%">
      <col width="25%">
      <col width="25%">
      <col width="25%">
    </colgroup>

    {*
    <tr>
      <td colspan="4" class="spacing">&nbsp;</td>
    </tr>
    *}

    {for $foo=2 to 11} 
    {* Display the fields *}
    <tr class="divetitle">
      <td colspan="4">
        {$userdefined_keys[$foo]}
      </td>
    </tr>
    <tr class="divedetails">
      <td colspan="4">
      {if $userdefined_values[$foo] != ''}
        {$userdefined_values[$foo]}
      {else}
        -
      {/if}
      </td>
    </tr>
    {/for}

    {*
    <tr class="divesection">
      <td colspan="4">&nbsp;</td>
    </tr>
    *}
  </table>
  </div>
{/if}

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


{if isset($pics2)}
<div id="controlbar" class="highslide-overlay controlbar">
    <a href="#" class="previous" onclick="return hs.previous(this)" 
title="Previous (left arrow key)"></a>
    <a href="#" class="next" onclick="return hs.next(this)" 
title="Next (right arrow key)"></a>
    <a href="#" class="highslide-move" onclick="return false" 
title="Click and drag to move"></a>
    <a href="#" class="close" onclick="return hs.close(this)" 
title="Close"></a>
</div>
{/if}

{* Show the links again *}
    <!-- Include links_details -->
{include file='links_details.tpl'}
<!-- End include links_details -->
