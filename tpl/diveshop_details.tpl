<h1>{$pagetitle}</h1>
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
        <div id="center_images">
          {foreach from=$image_link key=id item=i name=images}
            <a href="{$web_root}/{$i.img_url}" class="thum">
              <img
                src="{$web_root}/includes/imgp.php?src={$i.img_url}&width={$thumb_width}&height={$thumb_width}&crop-to-fit" />
            </a>
          {/foreach}
        </div>
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
          <a href="{$app_path}/divetrip.php{$sep2}{$trip.ID}"
            title="{$dtrip_number_title}{$trip.ID} - {$trip.TripName}">{if $trip.TripName == ''}{$trip.ID}{else}{$trip.TripName}{/if}</a>{if !$trip@last}{if $comma_separated}{$comma_separator}{else}&nbsp;{/if}{/if}

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
          <a href="{$app_path}/index.php{$sep2}{$dive}"
            title="{$dlog_number_title}{$dive}">{$dive}</a>{if !$dive@last}{if $comma_separated}{$comma_separator}{else}&nbsp;{/if}{/if}

        {/foreach}
      </td>
    </tr>
  {/if}

  <tr class="divesection">
    <td colspan="4">&nbsp;</td>
  </tr>
</table>

{literal}
  <script type="text/javascript">
    lightGallery(document.getElementById('center_images'), {
      plugins: [lgZoom, lgThumbnail],
      thumbnail: true,
      selector: '.thum',
    });
  </script>
{/literal}

{/literal}
<!-- Include links_details -->
{include file='links_details.tpl'}
<!-- End include links_details -->