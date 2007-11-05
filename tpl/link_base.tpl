    <div class="divelogcrumbs">
        {if isset($multiuser_id)}
        <a href="{$app_path}/" class="divelogcrumbs" title="{$diver_choice_linktitle}">{$diver_choice}</a> |
         <a href="{$app_path}/index.php/{$multiuser_id}/list" class="divelogcrumbs" title="{$dive_log_linktitle}">{$dive_log}</a> | 
        <a href="{$app_path}/divesite.php/{$multiuser_id}/list" class="divelogcrumbs" title="{$dive_sites_linktitle}">{$dive_sites}</a> | 
        <a href="{$app_path}/equipment.php/{$multiuser_id}/list" class="divelogcrumbs" title="{$dive_equip_linktitle}">{$dive_equip}</a> | 
        <a href="{$app_path}/divestats.php/{$multiuser_id}/list" class="divelogcrumbs" title="{$dive_stats_linktitle}">{$dive_stats}</a>
       {else}
        <a href="{$app_path}/index.php/list" class="divelogcrumbs" title="{$dive_log_linktitle}">{$dive_log}</a> | 
        <a href="{$app_path}/divesite.php/list" class="divelogcrumbs" title="{$dive_sites_linktitle}">{$dive_sites}</a> | 
        <a href="{$app_path}/equipment.php/list" class="divelogcrumbs" title="{$dive_equip_linktitle}">{$dive_equip}</a> | 
        <a href="{$app_path}/divestats.php/list" class="divelogcrumbs" title="{$dive_stats_linktitle}">{$dive_stats}</a>
        {/if}
           </div>

