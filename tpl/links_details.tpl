<table cellspacing="0" cellpadding="0" width="100%"><tr>
        <td width="25%" align="left">
{*	First, Previous *}
{if isset($divenr)}
    <div class="divelogcrumbs">
   {if isset($multiuser_id)}
    <a href="{$app_path}/index.php{$sep1}{$multiuser_id}{$sep2}{$first_dive}" class="divelogcrumbs" title="{$first_dive_linktitle}">{$first}</a>
    &nbsp;|&nbsp;
    <a href="{$app_path}/index.php{$sep1}{$multiuser_id}{$sep2}{$next_dive}" class="divelogcrumbs" title="{$previous_dive_linktitle}">{$previous}</a>
    {else}
    <a href="{$app_path}/index.php{$sep2}{$first_dive}" class="divelogcrumbs" title="{$first_dive_linktitle}">{$first}</a>
    &nbsp;|&nbsp;
    <a href="{$app_path}/index.php{$sep2}{$next_dive}" class="divelogcrumbs" title="{$previous_dive_linktitle}">{$previous}</a>
    {/if}
    </div>
{elseif isset($position)}
    <div class="divelogcrumbs">
     {if isset($multiuser_id)}
  <a href="{$app_path}/divesite.php{$sep1}{$multiuser_id}{$sep2}{$first_site_id}" class="divelogcrumbs" title="{$first_site_linktitle}">{$first}</a>
    &nbsp;|&nbsp;
    <a href="{$app_path}/divesite.php{$sep1}{$multiuser_id}{$sep2}{$previous_site_id}" class="divelogcrumbs" title="{$previous_site_linktitle}">{$previous}</a>
    {else}
  <a href="{$app_path}/divesite.php{$sep2}{$first_site_id}" class="divelogcrumbs" title="{$first_site_linktitle}">{$first}</a>
    &nbsp;|&nbsp;
    <a href="{$app_path}/divesite.php{$sep2}{$previous_site_id}" class="divelogcrumbs" title="{$previous_site_linktitle}">{$previous}</a>
    {/if}
    </div>
{elseif isset($equipment_first)}
    <div class="divelogcrumbs">
      {if isset($multiuser_id)}
 <a href="{$app_path}/equipment.php{$sep1}{$multiuser_id}{$sep2}{$first_eq_id}" class="divelogcrumbs" title="{$first_equip_linktitle}">{$first}</a>
    &nbsp;|&nbsp;
    <a href="{$app_path}/equipment.php{$sep1}{$multiuser_id}{$sep2}{$previous_eq_id}" class="divelogcrumbs" title="{$previous_equip_linktitle}">{$previous}</a>
    {else}
 <a href="{$app_path}/equipment.php{$sep2}{$first_eq_id}" class="divelogcrumbs" title="{$first_equip_linktitle}">{$first}</a>
    &nbsp;|&nbsp;
    <a href="{$app_path}/equipment.php{$sep2}{$previous_eq_id}" class="divelogcrumbs" title="{$previous_equip_linktitle}">{$previous}</a>
    {/if}
    </div>

{else}
&nbsp;
{/if}
</td>

{*	Dive Log, Dive Sites, Dive Statistics *}
<td width="50%" align="center">
   {include file='link_base.tpl'}
</td>

{*	Next, Last *}
<td width="25%" align="right">
{if isset($divenr_not_null)}
    <div class="divelogcrumbs">
    {if isset($multiuser_id)}
    <a href="{$app_path}/index.php{$sep1}{$multiuser_id}{$sep2}{$next_dive_nr}" class="divelogcrumbs" title="{$next_dive_linktitle}">{$next}</a>
    &nbsp;|&nbsp;
    <a href="{$app_path}/index.php{$sep1}{$multiuser_id}{$sep2}{$last_dive_nr}" class="divelogcrumbs" title="{$last_dive_linktitle}">{$last}</a>
    {else}
    <a href="{$app_path}/index.php{$sep2}{$next_dive_nr}" class="divelogcrumbs" title="{$next_dive_linktitle}">{$next}</a>
    &nbsp;|&nbsp;
    <a href="{$app_path}/index.php{$sep2}{$last_dive_nr}" class="divelogcrumbs" title="{$last_dive_linktitle}">{$last}</a>
    {/if}
    </div>
{elseif isset($divesite_not_null)}
    <div class="divelogcrumbs">
     {if isset($multiuser_id)}
   <a href="{$app_path}/divesite.php{$sep1}{$multiuser_id}{$sep2}{$next_divesite_nr}" class="divelogcrumbs" title="{$next_site_linktitle}">{$next}</a>
    &nbsp;|&nbsp;
    <a href="{$app_path}/divesite.php{$sep1}{$multiuser_id}{$sep2}{$last_divesite_nr}" class="divelogcrumbs" title="{$last_site_linktitle}">{$last}</a>
    {else}
   <a href="{$app_path}/divesite.php{$sep2}{$next_divesite_nr}" class="divelogcrumbs" title="{$next_site_linktitle}">{$next}</a>
    &nbsp;|&nbsp;
    <a href="{$app_path}/divesite.php{$sep2}{$last_divesite_nr}" class="divelogcrumbs" title="{$last_site_linktitle}">{$last}</a>
    {/if}
    </div>
{elseif isset($equipment_not_null)}
    <div class="divelogcrumbs">
      {if isset($multiuser_id)}
  <a href="{$app_path}/equipment.php{$sep1}{$multiuser_id}{$sep2}{$next_eq_id}" class="divelogcrumbs" title="{$next_equip_linktitle}">{$next}</a>
    &nbsp;|&nbsp;
    <a href="{$app_path}/equipment.php{$sep1}{$multiuser_id}{$sep2}{$last_eq_id}" class="divelogcrumbs" title="{$last_equip_linktitle}">{$last}</a>
    {else}
  <a href="{$app_path}/equipment.php{$sep2}{$next_eq_id}" class="divelogcrumbs" title="{$next_equip_linktitle}">{$next}</a>
    &nbsp;|&nbsp;
    <a href="{$app_path}/equipment.php{$sep2}{$last_eq_id}" class="divelogcrumbs" title="{$last_equip_linktitle}">{$last}</a>

    {/if}
    </div>
{else}
    &nbsp;
{/if}
</td>
</tr>
</table>
