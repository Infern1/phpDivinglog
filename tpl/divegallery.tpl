{include file='header.tpl'}
<div id="content">
  <h1>{$pagetitle}</h1>
  <!-- Include links_overview -->
  {include file='links_overview.tpl'}
  <!-- End include links_overview -->

  <table class="divetable" cellspacing="0" cellpadding="0" width="100%">
    <tr class="divesection">
      <td colspan="4">{$pagetitle}&nbsp;</td>
    </tr>
  </table>

  {if isset($page_numbers.total) && $page_numbers.total > 1}
    <table style="clear: both;" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td colspan="5" align="center" class="divelog-navigation">{$pager_links}</td>
      </tr>
    </table>
  {/if}


  {* Show photos per dive *}

  <div id="center_images">
    {foreach from=$image_link key=id item=i name=images}
      <div class="thumbwrapper">
        <a href="{$web_root}/{$i.img_url}" class="thum" data-sub-html="<h4>Location: <a href='{$app_path}/divesite.php{$sep2}{$i.site_nr}'
        title='{$i.site_name} {$divepic_place_linktitle}'>{$i.site_name}</a> </h4><p> Dive Nr: <a href='{$app_path}/index.php{$sep2}{$i.dive_nr}'
          title='{$divepic_dive_number} {$i.dive_nr} {$divepic_dive_linktitle}'>{$i.dive_nr}</a></p>">
          <img
            src="{$web_root}/includes/imgd.php?src={$i.img_url}&width={$thumb_width}&height={$thumb_width}&crop-to-fit" />
        </a>

        {$divepic_dive_number}
        <a href="{$app_path}/index.php{$sep2}{$i.dive_nr}"
          title="{$divepic_dive_number} {$i.dive_nr} {$divepic_dive_linktitle}">{$i.dive_nr}</a>
        <br>
        {$logbook_place}
        <a href="{$app_path}/divesite.php{$sep2}{$i.site_nr}"
          title="{$i.site_name} {$divepic_place_linktitle}">{$i.site_name}</a>
        <br>
        {$i.img_title}


      </div>
    {/foreach}
  </div>

  {if isset($page_numbers.total)}
    {if $page_numbers.total > 1}
      <table style="clear: both;" width="100%" cellspacing="0" cellpadding="0">
        <tr>
          <td colspan="5" align="center" class="divelog-navigation">{$pager_links}</td>
        </tr>
      </table>
    {/if}
  {/if}

  <table width="100%" cellspacing="0" cellpadding="0">
    <tr class="divesection">
      <td colspan="5">&nbsp;</td>
    </tr>
  </table>
</div>
{literal}
  <script type="text/javascript">
    lightGallery(document.getElementById('center_images'), {
      plugins: [lgZoom, lgThumbnail],
      thumbnail: true,
      selector: '.thum',
    });
  </script>
{/literal}


<!-- Include links_overview -->
{include file='links_overview.tpl'}
<!-- End include links_overview -->
</div>
{include file='footer.tpl'}