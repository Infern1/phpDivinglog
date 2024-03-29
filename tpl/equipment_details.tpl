<h1>{$pagetitle}</h1>
<!-- Include links_details -->
{include file='links_details.tpl'}
<!-- End include links_details -->
<table class="divetable" cellspacing="0" cellpadding="0" width="100%">
  <tr class="divesection">
    <td colspan="4">{$pagetitle}</td>
  </tr>
  {* Show main equipment details *}
  <tr class="divetitle">
    <td colspan="2">{$equip_object}</td>
    <td colspan="2">{$equip_manufacturer}</td>
  </tr>

  <tr class="divedetails">
    {if $Object != ''}
      <td colspan="2" width="50%">{$Object}</td>
    {else}
      <td colspan="2" width="50%>-</td>
      {/if}

      {if $Manufacturer != ''}
      <td colspan=" 2">{$Manufacturer}</td>
    {else}
    <td colspan="2">-</td>
    {/if}
  </tr>

  {*	Show equipment purchase details *}
  <tr class="divetitle">
    <td colspan="2">{$equip_shop}</td>
    <td>{$equip_datep}</td>
    <td>{$equip_price}</td>
  </tr>

  <tr class="divedetails">
    {if $Shop != ''}
    <td colspan="2">{$Shop}</td>
    {else}
    <td colspan="2">-</td>
    {/if}
    {if $DateP != ''}
    <td>{$DateP}</td>
    {else}
    <td>-</td>
    {/if}
    {if $Price != ''}
    <td>{$Price}</td>
    {else}
    <td>-</td>
    {/if}
  </tr>

  <tr class="divetitle">
    <td colspan="2">{$equip_serial}</td>
    <td>{$equip_warranty}</td>
    <td>{$equip_inactive}</td>
  </tr>

  <tr class="divedetails">
    {if $Serial != ''}
    <td colspan="2">{$Serial}</td>
    {else}
    <td colspan="2">-</td>
    {/if}
    {if $Warranty != ''}
    <td>{$Warranty}</td>
    {else}
    <td>-</td>
    {/if}
    {if $Inactive != ''}
    <td>{$Inactive}</td>
    {else}
    <td>-</td>
    {/if}
  </tr>

  {*	Show the rest of the details *}
  <tr class="divetitle">
    <td>{$equip_dater}</td>
    <td>{$equip_datern}</td>
    <td>{$equip_o2servicedate}</td>
    <td>{$equip_weight}</td>
  </tr>

  <tr class="divedetails">
    {if $DateR != ''}
    <td>{$DateR}</td>
    {else}
    <td>-</td>
    {/if}
    {if $DateRN != ''}
    <td>{if $equip_datern_warning}<img src="{$web_root}/images/icon_warning_16.png" border="0" width="16" height="16"
        alt="{$equip_service_warning}" title="{$equip_service_warning}"> {/if}{$DateRN}</td>
    {else}
    <td>-</td>
    {/if}
    {if $O2ServiceDate != ''}
    <td>{if $equip_o2servicedate_warning}<img src="{$web_root}/images/icon_warning_16.png" border="0" width="16"
        height="16" alt="{$equip_service_warning}" title="{$equip_service_warning}"> {/if}{$O2ServiceDate}</td>
    {else}
    <td>-</td>
    {/if}
    {if $Weight != ''}
    <td>{$Weight}</td>
    {else}
    <td>-</td>
    {/if}
  </tr>

  {if isset($PhotoPath)}
  <tr class="divetitle">
    <td colspan="4">{$equip_photo}</td>
  </tr>

  <tr class="divedetails">
    <td colspan="4">
      {if $has_images == '1'}
      <div id="center_images">
        {foreach from=$image_link key=id item=i name=images}
        <a href="{$web_root}/{$i.img_url}" class="thum" title="{$i.img_title}">
          <img
            src="{$web_root}/includes/imgp.php?src={$i.img_url}&width={$thumb_width}&height={$thumb_width}&crop-to-fit" />
        </a>
        {/foreach}
      </div>
      {/if}
    </td>
  </tr>
  {/if}

  <tr>
    <td colspan="4" class="spacing">&nbsp;</td>
  </tr>

  {*	Comments *}
  {*	Show them if we have them *}
  {if isset($Comments) }
  <tr class="divesection">
    <td colspan="4">{$equip_sect_comments}</td>
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