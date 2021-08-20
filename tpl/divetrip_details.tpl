<h1>{$pagetitle}</h1>
<!-- Include links_details -->
{include file='links_details.tpl'}
<!-- End include links_details -->
<table class="details" cellspacing="0" cellpadding="0" width="100%">
  <tr class="divesection">
    <td colspan="4">{$pagetitle}</td>
  </tr>

  {* Show main trip details *}
  <tr class="divetitle">
    <td colspan="2">{$trip_name}</td>
    <td colspan="2">{$trip_rating}</td>
  </tr>
  <tr class="divedetails">
    <td colspan="2">{$TripName}</td>
    <td colspan="2">{$Rating}</td>
  </tr>

  <tr class="divetitle">
    <td colspan="2">{$trip_shop}</td>
    <td colspan="2">{$trip_country}</td>
  </tr>
  <tr class="divedetails">
    {if $dive_shop_nr != ""}
      <td colspan="2"><a href="{$app_path}/diveshop.php{$sep2}{$dive_shop_nr}"
          title="{$dive_shop_name} {$logbook_shop_linktitle}">{$dive_shop_name}</a></td>

    {else}
      <td>-</td>
    {/if}
    {if $dive_country_nr != ""}
      <td colspan="2"><a href="{$app_path}/divecountry.php{$sep2}{$dive_country_nr}"
          title="{$dive_country_name} {$logbook_country_linktitle}">{$dive_country_name}</a></td>
    {else}
      <td>-</td>
    {/if}
  </tr>

  <tr class="divetitle">
    <td colspan="2">{$trip_startdate}</td>
    <td colspan="2">{$trip_enddate}</td>
  </tr>
  <tr class="divedetails">
    <td colspan="2">{$StartDate}</td>
    <td colspan="2">{$EndDate}</td>
  </tr>

  {if $has_images1 == '1'}
    <tr class="divetitle">
      <td colspan="4">{$trip_photo}</td>
    </tr>

    <tr class="divedetails">
      <td colspan="4">
        <div id="center_images">
          {foreach from=$image_link key=id item=i name=images}
            <a href="{$web_root}/{$i.img_url}" class="thum" title="{$i.img_title}">
              <img
                src="{$web_root}/includes/imgp.php?src={$i.img_url}&width={$thumb_width}&height={$thumb_width}&crop-to-fit" />
            </a>
          {/foreach}
        </div>
      </td>
    </tr>
  {/if}

  {* Show trip dives if we have them *}
  {if $dive_count != 0}
    <tr class="divetitle">
      <td colspan="4">{$dive_count} {$trip_dive_trans}</td>
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

  {* Show buddy details *}
  {if $buddy != ''}
    <tr class="divetitle">
      <td colspan="4">{$trip_buddy}</td>
    </tr>
    <tr class="divedetails">
      <td colspan="4">{$buddy}</td>
    </tr>
  {/if}

  {* Comments *}
  {* Show them if we have them *}
  {if isset($Comments)}
    <tr class="divesection">
      <td colspan="4">{$trip_sect_comments}</td>
    </tr>
    <tr class="divedetails">
      <td colspan="4">{$Comments}</td>
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
<!-- Include links_details -->
{include file='links_details.tpl'}
<!-- End include links_details -->