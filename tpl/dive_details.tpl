<!-- Include links_details -->
{include file='links_details.tpl'}
<!-- End include links_details -->
   <table class="divetable" cellspacing="0" cellpadding="0" width="100%">
   <colgroup>
   <col width="25%">
   <col width="25%">
   <col width="25%">
   <col width="25%">
   </colgroup>
    <tr class="divesection">
	    <td colspan="4">&nbsp;</td>
	</tr>

{* Show Dive Information *}

    {* Show main dive details *}
	<tr class="divetitle">
	    <td >{$logbook_divedate}</td>
	    <td >{$logbook_entrytime}</td>
	    <td >{$logbook_divetime}</td>
	    <td >{$logbook_depth}</td>
	</tr>

    <tr class="divecontent">
	    <td>{$dive_date}</td>
		<td>{$entry_time}</td>
		<td>{$dive_time}</td>
        <td>{$dive_depth}</td>
    </tr>

    {*	Show dive location details *}
    <tr class="divetitle">
	    <td>{$logbook_place}</td>
	    <td colspan="2">{$logbook_city}</td>
	    <td>{$logbook_country}</td>
	</tr>

	<tr class="divecontent">
        {if isset($dive_site_nr)} 
            {if isset($multiuser_id)}
            <td><a href="{$app_path}/divesite.php{$sep1}{$multiuser_id}{$sep2}{$dive_site_nr}" title="{$dive_place} {$logbook_place_linktitle}">{$dive_place}</a></td>
            {else}
            <td><a href="{$app_path}/divesite.php{$sep2}{$dive_site_nr}" title="{$dive_place} {$logbook_place_linktitle}">{$dive_place}</a></td>
            {/if}
        {else}
            <td>-</td>
        {/if} 
        {if isset($dive_city)} 
        <td colspan="2">{$dive_city}</td>
        {else}
        <td>-</td>
        {/if}
        {if isset($dive_country)} 
        <td>{$dive_country}</td>
        {else}
        <td>-</td>
        {/if}
	</tr>

    {* 	Show buddy details *}
	{if isset($buddy)}
        <tr class="divetitle">
		    <td colspan="4">{$logbook_buddy}</td>
        </tr>
        <tr class="divecontent">
		    <td colspan="4">{$buddy}</td>
		</tr>
	{/if}

    {* Dive pictures *}
    {if isset($pics)}
		<tr><td colspan="4" class="spacing">&nbsp;</td></tr>
        <tr><td colspan="4">
		        <p class="centeredImage"><a href="{$picpath_web}"  rel="lightbox[others]"  title="{$divepic_linktit}">{$divepic_pt}</a>
            {foreach from=$image_link item=foo}
                {$foo}
            {/foreach}
		</p>
		</td></tr>

    {/if}
    {if isset($pics2)}
<!--	<div>-->
    	<tr><td colspan="4" class="spacing">&nbsp;</td></tr>
        <tr><td colspan="4">
		        <p class="centeredImage">
             {foreach from=$image_link key=id item=i name=images}
                {if isset($pics_resized)}
             <a id="thumb{$id}" href="{$web_root}/{$i.img_url}" class="highslide" onclick="return hs.expand(this)">
                    <img src="{$web_root}/{$i.img_thumb_url}" alt="Highslide JS" title="{$i.img_title}" height="{$thumb_height}"
                    width="{$thumb_width}" ></a>
                {else}
             <a id="thumb{$id}" href="{$web_root}/{$i.img_url}" class="highslide" onclick="return hs.expand(this)">
                    <img src="{$web_root}/imagesize.php?w=100&img={$i.img_url}" alt="Highslide JS" title="{$i.img_title}" height="{$thumb_height}" width="{$thumb_height}" ></a>
                {/if}
            {/foreach}
		</p>
		</td></tr>
<!--</div> -->
    {/if}

    {* Dive profile *}
    {if isset($profile)}
		<tr><td colspan="4" class="spacing">&nbsp;</td></tr>
        <tr><td colspan="4">
            <p class="centeredImage">
            {if isset($multiuser_id)}
		        <img src="{$app_path}/drawprofile.php{$sep1}{$multiuser_id}{$sep2}{$get_nr}"  alt="{$dive_profile_title}" title="{$dive_profile_title}">
            {else}
                <img src="{$app_path}/drawprofile.php{$sep2}{$get_nr}"  alt="{$dive_profile_title}" title="{$dive_profile_title}">
            {/if}
		    </p>
		    </td>
        </tr>
    {/if}
    {*  Conditions *}
	    <tr><td colspan="4" class="spacing">&nbsp;</td></tr>
        <tr class="divesection">
            <td colspan="4">{$dive_sect_conditions}</td>
	    </tr>

    {* 	Show weather conditions *}
	    <tr class="divetitle">
	        <td>{$logbook_weather}</td>
	        <td>{$logbook_visibility}</td>
	        <td>{$logbook_altitude}</td>
	        <td>{$logbook_airtemp}</td>
	    </tr>
	
    <tr class="divecontent">
	{if isset($Weather)}
	    <td>{$Weather}</td>
    {else}
        <td>-</td>
	{/if}
    {if isset($Visibility)}
	    <td>{$Visibility}</td>
	{else}
	    <td>-</td>
    {/if}

    {if isset($Altitude)}
        <td>{$Altitude}</td>
	{else}
        <td>-</td>
    {/if}

	{if isset($Airtemp)}
        <td>{$Airtemp}</td>
    {else}
        <td>-</td>
    {/if}
	</tr>

    {* Show water conditions *}
	<tr class="divetitle">
	    <td>{$logbook_water}</td>
	    <td>{$logbook_surface}</td>
	    <td>{$logbook_uwcurrent}</td>
	    <td>{$logbook_watertemp}</td>
	</tr>
	<tr class="divecontent">
	{if isset($Water)}
        <td>{$Water}</td>
	{else}
        <td>-</td>
    {/if}
	{if isset($Surface)}
        <td>{$Surface}</td>
    {else}
        <td>-</td>
    {/if}
    {if isset($UWCurrent)}
        <td>{$UWCurrent}</td>
	{else}
        <td>-</td>
    {/if}
	{if isset($Watertemp)}
	    <td>{$Watertemp}</td>
	{else}
        <td>-</td>
    {/if}
	</tr>

    {* Breathing *}
	<tr>
        <td colspan="4" class="spacing">&nbsp;</td>
    </tr>
    <tr class="divesection">
	    <td colspan="4">{$dive_sect_breathing}</td>
	</tr>

    {* Show tank details *}
	<tr class="divetitle">
	    <td>{$logbook_tanktype}</td>
	    <td>{$logbook_tanksize}</td>
	    <td>{$logbook_gas}</td>
	    <td>{$logbook_avgdepth}</td>
	</tr>

	<tr class="divecontent">
	{ if isset($Tanktype)}
        <td>{$Tanktype}</td>
	{else}
        <td>-</td>
    {/if}

	{if isset($Tanksize)}
        <td>{$Tanksize}</td>
    {else}
        <td>-</td>
    {/if}

	{if isset($Gas)}
	    <td>{$Gas}</td>
	{else}
        <td>-</td>
    {/if}

	{if isset($averagedepth)}
	    <td>{$averagedepth} {$unit_length_short}</td>
    {else}
        <td>-</td>
    {/if}
	</tr>

    {* Show pressure details *}
	<tr class="divetitle">
	    <td>{$logbook_press}</td>
	    <td>{$logbook_prese}</td>
	    <td>{$logbook_presdiff}</td>
	    <td>{$logbook_sac}</td>
	</tr>

	<tr class="divecontent">
	{if isset($PresS)}
        <td>{$PresS}</td>
    {else}
        <td>-</td>
    {/if}
    { if isset($PresE)}
		<td>{$PresE}</td>
	{else }
        <td>-</td>
    {/if}
	
    {if isset($PresSPresE)}
        <td>{$PresSPresE}</td>
	{else }
        <td>-</td>
    {/if}
	{if isset($sac)}
	    <td>{$sac}</td>
	{else}
	    <td>-</td>
	
    {/if}
	</tr>

    {* Dive Details *}
	<tr>
        <td colspan="4" class="spacing">&nbsp;</td>
    </tr>
    <tr class="divesection">
        <td colspan="4">{$dive_sect_details}</td>
    </tr>
    <tr class="divetitle">
        <td>{$logbook_entry}</td>
        <td>{$logbook_deco}</td>
        <td>{$logbook_rep}</td>
        <td>{$logbook_surfint}</td>
    </tr>

    <tr class="divecontent">
        {if isset($Entry)}
        <td>{$Entry}</td>
        {else}
		<td>-</td>
        {/if}

	    <td>{$Deco}</td>
	    <td>{$Rep}</td>
	{if isset($Surfint)}
	    <td>{$Surfint}</td>
	{else }
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
        <tr>
            <td colspan="4">{$stops}</td>
        </tr>
	{/if}

    {* Equipment *}
	<tr>
        <td colspan="4" class="spacing">&nbsp;</td>
    </tr>
    <tr class="divesection">
	    <td colspan="4">{$dive_sect_equipment}</td>
	</tr>
    <tr class="divetitle">
	    <td>{$logbook_weight}</td>
	    <td>{$logbook_divesuit}</td>
	    <td colspan="2" >{$logbook_computer}</td>
	</tr>

	<tr class="divecontent">
	{ if isset($Weight)}
        <td>{$Weight}</td>
	{else }
        <td>-</td>
    {/if}

	{if isset($Divesuit)}
		<td>{$Divesuit}</td>
	{else}
        <td>-</td>
    {/if}

	{if isset($Computer)}
	    <td colspan="2">{$Computer}</td>
	{else}
	    <td>-</td>
	{/if}
	</tr>

	{if isset($UsedEquip)}
		<tr class="divetitle">
		    <td colspan="4">{$logbook_usedequip}</td>
		</tr>
        <tr class="divecontent">
		  <td colspan="4">
       {foreach from=$equip_link key=id item=i name=equipment}
           {if isset($multiuser_id)}
                <a href="{$app_path}/equipment.php{$sep1}{$multiuser_id}{$sep2}{$i.equipmentnr}" title="{$i.divegear} {$logbook_place_linktitle}">{$i.divegear}</a>&nbsp;
           {else}
                <a href="{$app_path}/equipment.php{$sep2}{$i.equipmentnr}" title="{$i.divegear} {$logbook_place_linktitle}">{$i.divegear}</a>&nbsp;
           {/if}
        {/foreach}
		
		</td>
		</tr>
    {/if}

	<tr>
        <td colspan="4" class="spacing">&nbsp;</td>
    </tr>
    {if isset($Comments)}
		<tr class="divesection">
		    <td colspan="4">{$dive_sect_comments}</td>
		</tr>
        <tr>
        <td colspan="4">{$Comments}</td>
        </tr>    
        <tr class="divesection">
	    <td colspan="4">&nbsp;</td>
	</tr>
   {/if}
   </table>

{if isset($pics2)}
<div id="controlbar" class="highslide-overlay controlbar">
    <a href="#" class="previous" onclick="return hs.previous(this)" title="Previous (left arrow key)"></a>
    <a href="#" class="next" onclick="return hs.next(this)" title="Next (right arrow key)"></a>
    <a href="#" class="highslide-move" onclick="return false" title="Click and drag to move"></a>
    <a href="#" class="close" onclick="return hs.close(this)" title="Close"></a>
</div>
{/if}
{* Show the links again *}
	<!-- Include links_details -->
{include file='links_details.tpl'}
<!-- End include links_details -->
