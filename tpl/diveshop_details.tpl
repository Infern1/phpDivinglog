<!-- Include links_details -->
{include file='links_details.tpl'}
<!-- End include links_details -->
  <table class="details" cellspacing="0" cellpadding="0" width="100%">
    <tr class="divesection">
      <td colspan="4">{$pagetitle}</td>
    </tr>

    {* Show main shop details *}
    <tr class="divetitle">
      <td colspan="2">{$shop_name}</td>
      <td colspan="2">{$shop_type}</td>
    </tr>
    <tr class="divedetails">
      <td colspan="2">{$ShopName}</td>
      <td colspan="2">{$ShopType}</td>
    </tr>

    <tr class="divetitle">
      <td colspan="2">{$shop_location}</td>
      <td colspan="2">{$shop_rating}</td>
    </tr>
    <tr class="divedetails">
      <td colspan="2">
        {if $Street != ""}
          {$Street}<br>
        {/if}
        {if $Address2 != ""}
          {$Address2}<br>
        {/if}
        {if $City != "" || $State != "" || $Zip != ""} 
          {$City} {$State} {$Zip}<br>
        {/if}
        {if $Country != ""}
          {$Country}
        {/if}
      &nbsp;</td>
      <td colspan="2">{$Rating}</td>
    </tr>

    <tr class="divetitle">
      <td colspan="2">{$shop_email}</td>
      <td colspan="2">{$shop_url}</td>
    </tr>
    <tr class="divedetails">
      <td colspan="2">{$Email}</td>
      <td colspan="2">{$URL}</td>
    </tr>

    <tr class="divetitle">
      <td>{$shop_phone}</td>
      <td>{$shop_mobile}</td>
      <td>{$shop_fax}</td>
      <td>&nbsp;</td>
    </tr>
    <tr class="divedetails">
      <td>{$Phone}</td>
      <td>{$Mobile}</td>
      <td>{$Fax}</td>
      <td>&nbsp;</td>
    </tr>

    {if $has_images == '1'}
    <tr class="divetitle">
	<td colspan="4">{$shop_photo}</td>
    </tr>

    <tr class="divedetails">
	<td colspan="4">
    {foreach from=$image_link key=id item=i name=images}
            <a id="thumb" href="{$web_root}/{$i.img_url}" class="highslide" onclick="return hs.expand(this)" title="{$i.img_title}">
            <img src="{$web_root}/{$i.img_url}" alt="Highslide JS" title="{$i.img_title}" height="{$thumb_height}" width="{$thumb_width}" ></a>
           <div class='highslide-caption'>
           {$i.img_title}
           </div>
    {/foreach}
    </td>
    </tr>
    {/if}

    <tr>
      <td colspan="4" class="spacing">&nbsp;</td>
    </tr>

    {* Comments *}
    {* Show them if we have them *}
    {if isset($Comments)}
    <tr class="divesection">
      <td colspan="4">{$shop_sect_comments}</td>
    </tr>
    <tr class="divedetails">
      <td colspan="4">{$Comments}</td>
    </tr>
    {/if}

    <tr class="divesection">
      <td colspan="4">{$shop_sect_activity}</td>
    </tr>

    {* Show shop trips if we have them *}
    {if $trip_count != 0}
    <tr class="divetitle">
      <td colspan="4">{$trip_count} {$shop_trip_trans} {$ShopType}:</td>
    </tr>

    <tr class="divedetails">
      <td colspan="4">
      {foreach from=$trips item=trip}
        {if isset($multiuser_id)}
        <a href="{$app_path}/divetrip.php{$sep1}{$multiuser_id}{$sep2}{$trip}"
title="{$dtrip_number_title}{$trip}">{$trip}</a>
        {else}
        <a href="{$app_path}/divetrip.php{$sep2}{$trip}"
title="{$dtrip_number_title}{$trip}">{$trip}</a>
        {/if}
      {/foreach}
      </td>
    </tr>
    {/if}

    {* Show shop dives if we have them *}
    {if $dive_count != 0}
    <tr class="divetitle">
      <td colspan="4">{$dive_count} {$shop_dive_trans} {$ShopType}:</td>
    </tr>

    <tr class="divedetails">
      <td colspan="4">
      {foreach from=$dives item=dive}
        {if isset($multiuser_id)}
        <a href="{$app_path}/index.php{$sep1}{$multiuser_id}{$sep2}{$dive}"
title="{$dlog_number_title}{$dive}">{$dive}</a>
        {else}
        <a href="{$app_path}/index.php{$sep2}{$dive}"
title="{$dlog_number_title}{$dive}">{$dive}</a>
        {/if}
      {/foreach}
      </td>
    </tr>
    {/if}

    <tr class="divesection">
      <td colspan="4">&nbsp;</td>
    </tr>
  </table>

<!-- Include links_details -->
{include file='links_details.tpl'}
<!-- End include links_details -->
