<div class="crumbs" style="text-align:center;">
{* 	Dive Sites, Dive Statistics *}
{if isset($multiuser_id)}
<a href="{$app_path}/index.php{$sep1}{$multiuser_id}{$list}" class="crumbs" title="{$dive_log_linktitle}">{$dive_log}</a><br>
<a href="{$app_path}/divesite.php/{$multiuser_id}{$list}" class="crumbs" title="{$dive_sites_linktitle}">{$dive_sites}</a><br>
<a href="{$app_path}/equipment.php/{$multiuser_id}{$list}" class="crumbs" title="{$dive_equip_linktitle}">{$dive_equip}</a><br> 
<a href="{$app_path}/divestats.php/{$multiuser_id}{$list}" class="crumbs" title="{$dive_stats_linktitle}">{$dive_stats}</a>
{else}
<a href="{$app_path}/index.php{$list}" class="crumbs" title="{$dive_log_linktitle}">{$dive_log}</a><br>
<a href="{$app_path}/divesite.php{$list}" class="crumbs" title="{$dive_sites_linktitle}">{$dive_sites}</a><br> 
<a href="{$app_path}/equipment.php{$list}" class="crumbs" title="{$dive_equip_linktitle}">{$dive_equip}</a><br>
<a href="{$app_path}/divestats.php{$list}" class="crumbs" title="{$dive_stats_linktitle}">{$dive_stats}</a>
{/if}
</div>
