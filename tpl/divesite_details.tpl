<h1>{$pagetitle}</h1>
<!-- Include links_details -->
{include file='links_details.tpl'}
<!-- End include links_details -->
<table class="details" cellspacing="0" cellpadding="0" width="100%">
  <tr class="divesection">
    <td colspan="4">{$pagetitle}</td>
  </tr>
  {* Show main site details *}
  <tr class="divetitle">
    <td colspan="2">{$place_place}</td>
    <td colspan="2">{$place_city}</td>
  </tr>
  <tr class="divedetails">
    <td colspan="2">{$Place}</td>
    {if isset($dive_city_nr)}
      <td><a href="{$app_path}/divecity.php{$sep2}{$dive_city_nr}"
          title="{$dive_city} {$logbook_city_linktitle}">{$dive_city}</a></td>
    {else}
      <td>-</td>
    {/if}
  </tr>

  <tr class="divetitle">
    <td>{$place_country}</td>
    <td>{$place_rating}</td>
    <td>{$place_maxdepth}</td>
    <td>{$place_difficulty}</td>
  </tr>
  <tr class="divedetails">
    {if isset($dive_country_nr)}
      <td><a href="{$app_path}/divecountry.php{$sep2}{$dive_country_nr}"
          title="{$dive_country} {$logbook_country_linktitle}">{$dive_country}</a></td>
    {else}
      <td>-</td>
    {/if}
    <td>{$Rating}</td>
    <td>{$MaxDepth}</td>
    <td>{$Difficulty}</td>
  </tr>

  <tr class="divetitle">
    <td>{$place_watername}</td>
    <td>{$place_water}</td>
    <td>{$place_altitude}</td>
    <td>&nbsp;</td>
  </tr>
  <tr class="divedetails">
    <td>{$WaterName}</td>
    <td>{$Water}</td>
    <td>{$Altitude}</td>
    <td>&nbsp;</td>
  </tr>

  {* Show extra site details *}
  <tr class="divetitle">
    <td>{$place_lat}</td>
    <td>{$place_lon}</td>
    <td>&nbsp;</td>
    <td>{$place_datum}</td>
  </tr>

  <tr class="divedetails">
    {if $Lat != ''}
      <td>{$Lat}</td>
    {else}
      <td>-</td>
    {/if}

    {if $Lon != ''}
      <td>{$Lon}</td>
    {else}
      <td>-</td>
    {/if}

    {if isset($site_google_link)}
      <td><a
          href="http://maps.google.com/maps?f=q&amp;hl=en&amp;q={$LatDec},{$LonDec}+({strip}{$Place}{/strip})&amp;ie=UTF8&amp;t=k&amp;om=1"
          target="_blank" title="{$site_google_link}">{$google_map}</a></td>
    {else}
      <td>&nbsp;</td>
    {/if}

    <td>{$datum}</td>
  </tr>

  {if $has_images == '1'}
    {* Show maps *}
    <tr class="divetitle">
      <td colspan="4">{$place_map}</td>
    </tr>

    <tr class="divedetails">
      <td colspan="4">
        <div id="center_images">

          {foreach from=$image_link key=id item=i name=images}
            <a href="{$web_root}/{$i.img_url}" class="thum" data-sub-html="<h4>Location: <a href='{$app_path}/divesite.php{$sep2}{$divesite_id}'
          title='{$Place} '>{$place_city}</a> </h4>">
              <img
                src="{$web_root}/includes/imgp.php?src={$i.img_url}&width={$thumb_width}&height={$thumb_width}&crop-to-fit" />
            </a>
          {/foreach}
        </div>
      </td>
    </tr>
  {/if}

  {* Show site dives if we have them *}
  {if isset($dive_count) && $dive_count != 0}
    <tr class="divetitle">
      <td colspan="4">{$dive_count} {$site_dive_trans}</td>
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

  <tr>
    <td colspan="4" class="spacing">&nbsp;</td>
  </tr>

  {* Comments *}
  {* Show them if we have them *}
  {if isset($Comments)}
    <tr class="divesection">
      <td colspan="4">{$site_sect_comments}</td>
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