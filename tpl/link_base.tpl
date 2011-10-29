<div class="divelogcrumbs">
{if isset($multiuser_id)}
    <a href="{$app_path}/" 
class="divelogcrumbs" title="{$diver_choice_linktitle}">{$diver_choice}</a> |
    <a href="{$app_path}/index.php{$sep1}{$multiuser_id}{$sep2}list" 
class="divelogcrumbs" title="{$dive_log_linktitle}">{$dive_log}</a> | 
    <a href="{$app_path}/divegallery.php{$sep1}{$multiuser_id}{$sep2}list" 
class="divelogcrumbs" title="{$dive_gallery_linktitle}">{$dive_gallery}</a> | 
    <a href="{$app_path}/equipment.php{$sep1}{$multiuser_id}{$sep2}list" 
class="divelogcrumbs" title="{$dive_equip_linktitle}">{$dive_equip}</a><br>
    <a href="{$app_path}/divecountry.php{$sep1}{$multiuser_id}{$sep2}list" 
class="divelogcrumbs" title="{$dive_country_linktitle}">{$dive_countries}</a> |
    <a href="{$app_path}/divecity.php{$sep1}{$multiuser_id}{$sep2}list" 
class="divelogcrumbs" title="{$dive_city_linktitle}">{$dive_cities}</a> | 
    <a href="{$app_path}/divesite.php{$sep1}{$multiuser_id}{$sep2}list" 
class="divelogcrumbs" title="{$dive_sites_linktitle}">{$dive_sites}</a><br>
    <a href="{$app_path}/divestats.php{$sep1}{$multiuser_id}{$sep2}list" 
class="divelogcrumbs" title="{$dive_stats_linktitle}">{$dive_stats}</a> |
    <a href="{$app_path}/diveshop.php{$sep1}{$multiuser_id}{$sep2}list" 
class="divelogcrumbs" title="{$dive_shops_linktitle}">{$dive_shops}</a> |
    <a href="{$app_path}/divetrip.php{$sep1}{$multiuser_id}{$sep2}list" 
class="divelogcrumbs" title="{$dive_trips_linktitle}">{$dive_trips}</a>
{else}
    <a href="{$app_path}/index.php{$list}" 
class="divelogcrumbs" title="{$dive_log_linktitle}">{$dive_log}</a> | 
    <a href="{$app_path}/divegallery.php{$list}" 
class="divelogcrumbs" title="{$dive_gallery_linktitle}">{$dive_gallery}</a> |
    <a href="{$app_path}/equipment.php{$list}" 
class="divelogcrumbs" title="{$dive_equip_linktitle}">{$dive_equip}</a><br>
    <a href="{$app_path}/divecountry.php{$list}" 
class="divelogcrumbs" title="{$dive_country_linktitle}">{$dive_countries}</a> |
    <a href="{$app_path}/divecity.php{$list}" 
class="divelogcrumbs" title="{$dive_city_linktitle}">{$dive_cities}</a> | 
    <a href="{$app_path}/divesite.php{$list}" 
class="divelogcrumbs" title="{$dive_sites_linktitle}">{$dive_sites}</a><br>
    <a href="{$app_path}/divestats.php{$list}" 
class="divelogcrumbs" title="{$dive_stats_linktitle}">{$dive_stats}</a> |
    <a href="{$app_path}/diveshop.php{$list}" 
class="divelogcrumbs" title="{$dive_shops_linktitle}">{$dive_shops}</a> | 
    <a href="{$app_path}/divetrip.php{$list}" 
class="divelogcrumbs" title="{$dive_trips_linktitle}">{$dive_trips}</a>
{/if}
</div>
