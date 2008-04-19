<div class="divelogcrumbs">
{if isset($multiuser_id)}
    <a href="{$app_path}/" class="divelogcrumbs" title="{$diver_choice_linktitle}">{$diver_choice}</a> |
    <a href="{$app_path}/index.php{$sep1}{$multiuser_id}{$sep2}list" class="divelogcrumbs" title="{$dive_log_linktitle}">{$dive_log}</a> | 
    <a href="{$app_path}/divesite.php{$sep1}{$multiuser_id}{$sep2}list" class="divelogcrumbs" title="{$dive_sites_linktitle}">{$dive_sites}</a> | 
    <a href="{$app_path}/equipment.php{$sep1}{$multiuser_id}{$sep2}list" class="divelogcrumbs" title="{$dive_equip_linktitle}">{$dive_equip}</a> | 
    <a href="{$app_path}/divestats.php{$sep1}{$multiuser_id}{$sep2}list" class="divelogcrumbs" title="{$dive_stats_linktitle}">{$dive_stats}</a> |
    <a href="{$app_path}/divegallery.php{$sep1}{$multiuser_id}{$sep2}list" class="divelogcrumbs" title="{$dive_gallery_linktitle}">{$dive_gallery}</a>


{else}
    <a href="{$app_path}/index.php{$list}" class="divelogcrumbs" title="{$dive_log_linktitle}">{$dive_log}</a> | 
    <a href="{$app_path}/divesite.php{$list}" class="divelogcrumbs" title="{$dive_sites_linktitle}">{$dive_sites}</a> | 
    <a href="{$app_path}/equipment.php{$list}" class="divelogcrumbs" title="{$dive_equip_linktitle}">{$dive_equip}</a> | 
    <a href="{$app_path}/divestats.php{$list}" class="divelogcrumbs" title="{$dive_stats_linktitle}">{$dive_stats}</a>
|    <a href="{$app_path}/divegallery.php{$list}" class="divelogcrumbs" title="{$dive_gallery_linktitle}">{$dive_gallery}</a>
{/if}
</div>

