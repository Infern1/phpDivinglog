<div class="crumbs" style="text-align:center;">
{* 	Dive Sites, Dive Statistics *}
{if isset($multiuser_id)}
<a href="{$app_path}/index.php{$sep1}{$multiuser_id}{$list}" class="crumbs" title="{$dive_log_linktitle}">{$dive_log}</a><br>
<a href="{$app_path}/divesite.php/{$multiuser_id}{$list}" class="crumbs" title="{$dive_sites_linktitle}">{$dive_sites}</a><br>
<a href="{$app_path}/equipment.php/{$multiuser_id}{$list}" class="crumbs" title="{$dive_equip_linktitle}">{$dive_equip}</a><br> 
<a href="{$app_path}/diveshop.php/{$multiuser_id}{$list}" class="crumbs" title="{$dive_shops_linktitle}">{$dive_shops}</a><br> 
<a href="{$app_path}/divetrip.php/{$multiuser_id}{$list}" class="crumbs" title="{$dive_trips_linktitle}">{$dive_trips}</a><br> 
<a href="{$app_path}/divecountry.php/{$multiuser_id}{$list}" class="crumbs" title="{$dive_country_linktitle}">{$dive_countries}</a><br> 
<a href="{$app_path}/divestats.php/{$multiuser_id}{$list}" class="crumbs" title="{$dive_stats_linktitle}">{$dive_stats}</a>
{else}
<a href="{$app_path}/index.php{$list}" class="crumbs" 
title="{$dive_log_linktitle}">{$dive_log}</a><br>
<a href="{$app_path}/divesite.php{$list}" class="crumbs" title="{$dive_sites_linktitle}">{$dive_sites}</a><br> 
<a href="{$app_path}/equipment.php{$list}" class="crumbs" title="{$dive_equip_linktitle}">{$dive_equip}</a><br>
<a href="{$app_path}/diveshop.php{$list}" class="crumbs" title="{$dive_shops_linktitle}">{$dive_shops}</a><br>
<a href="{$app_path}/divetrip.php{$list}" class="crumbs" title="{$dive_trips_linktitle}">{$dive_trips}</a><br>
<a href="{$app_path}/divecountry.php{$list}" class="crumbs" title="{$dive_country_linktitle}">{$dive_countries}</a><br>
<a href="{$app_path}/divestats.php{$list}" class="crumbs" 
title="{$dive_stats_linktitle}">{$dive_stats}</a>
{/if}
</div>
