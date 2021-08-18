{include file='header.tpl'}
<div id="content">
    <h1>{$pagetitle}</h1>
    <!-- Include links_overview -->
    {include file='links_overview.tpl'}
    <!-- End include links_overview -->
    {if isset($diver_overview)}
        {include file='diver_overview.tpl'}
    {else}
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
                    {if isset($pics_resized)}
                        <a id="thumb{$id}" href="{$web_root}/{$i.img_url}" class="highslide" onclick="return hs.expand(this)"
                            title="{$i.img_title}">
                            <img src="{$web_root}/{$i.img_thumb_url}" alt="Highslide JS" title="{$i.img_title}"></a>
                    {else}
                        <a id="thumb{$id}" href="{$web_root}/{$i.img_url}" class="highslide" onclick="return hs.expand(this)"
                            title="{$i.img_title}">
                            <img src="{$web_root}/imagesize.php?w={$thumb_width}&img={$i.img_url}" alt="Highslide JS"
                                title="{$i.img_title}" height="{$thumb_height}" width="{$thumb_width}"></a>
                    {/if}
                    <div class='highslide-caption'>
                        {$divepic_dive_number}
                        {if isset($multiuser_id)}
                            <a href="{$app_path}/index.php{$sep1}{$multiuser_id}{$sep2}{$i.dive_nr}"
                                title="{$divepic_dive_number} {$i.dive_nr} {$divepic_dive_linktitle}">{$i.dive_nr}</a>
                        {else}
                            <a href="{$app_path}/index.php{$sep2}{$i.dive_nr}"
                                title="{$divepic_dive_number} {$i.dive_nr} {$divepic_dive_linktitle}">{$i.dive_nr}</a>
                        {/if}
                        <br>
                        {$logbook_place}
                        {if isset($multiuser_id)}
                            <a href="{$app_path}/divesite.php{$sep1}{$multiuser_id}{$sep2}{$i.site_nr}"
                                title="{$i.site_name} {$divepic_place_linktitle}">{$i.site_name}</a>
                        {else}
                            <a href="{$app_path}/divesite.php{$sep2}{$i.site_nr}"
                                title="{$i.site_name} {$divepic_place_linktitle}">{$i.site_name}</a>
                        {/if}
                        <br>
                        {$i.img_title}
                        {if isset($i.img_date)}
                            <br>
                            Date: {$i.img_date}
                        {/if}
                    </div>
                    <div class="thumbtext">
                        <p>
                            {$divepic_dive_number}
                            {if isset($multiuser_id)}
                                <a href="{$app_path}/index.php{$sep1}{$multiuser_id}{$sep2}{$i.dive_nr}"
                                    title="{$divepic_dive_number} {$i.dive_nr} {$divepic_dive_linktitle}">{$i.dive_nr}</a>
                            {else}
                                <a href="{$app_path}/index.php{$sep2}{$i.dive_nr}"
                                    title="{$divepic_dive_number} {$i.dive_nr} {$divepic_dive_linktitle}">{$i.dive_nr}</a>
                            {/if}
                            <br>
                            {$logbook_place}<br>
                            {if isset($multiuser_id)}
                                <a href="{$app_path}/divesite.php{$sep1}{$multiuser_id}{$sep2}{$i.site_nr}"
                                    title="{$i.site_name} {$divepic_place_linktitle}">{$i.site_name}</a>
                            {else}
                                <a href="{$app_path}/divesite.php{$sep2}{$i.site_nr}"
                                    title="{$i.site_name} {$divepic_place_linktitle}">{$i.site_name}</a>
                            {/if}
                            {if $i.img_date != ''}
                                <br>
                                Date: {$i.img_date}
                            {/if}
                        </p>
                    </div>
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

        <div id="controlbar" class="highslide-overlay controlbar">
            <a href="#" class="previous" onclick="return hs.previous(this)" title="Previous (left arrow key)"></a>
            <a href="#" class="next" onclick="return hs.next(this)" title="Next (right arrow key)"></a>
            <a href="#" class="highslide-move" onclick="return false" title="Click and drag to move"></a>
            <a href="#" class="close" onclick="return hs.close(this)" title="Close"></a>
        </div>

    {/if}
    <!-- Include links_overview -->
    {include file='links_overview.tpl'}
    <!-- End include links_overview -->
</div>
{include file='footer.tpl'}