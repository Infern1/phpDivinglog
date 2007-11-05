<table cellspacing="0" cellpadding="0" width="100%">
<tr>
{* 	First, Previous *}
    <td  align="left">
    {if $start == '1'}
        &nbsp;
    {else }
        <div class="divelogcrumbs">
            <a href="{$app_path}/{$base_page}?start=1&amp;max={$max}" class="divelogcrumbs" title="{$first_group_linktitle}">{$first}</a>
        &nbsp;|&nbsp;
        <a href="{$app_path}/{$base_page}?start={$previous}&amp;max={$max}" class="divelogcrumbs" title="{$previous_group_linktitle}">{$langprevious}</a>
            
        </div>
    {/if}
    </td>

    {* 	Dive Sites, Dive Statistics *}
    <td  align="center">
    {include file='link_base.tpl'}
    </td>

    {* Next, Last *}
      <td  align="right">
    {if isset($startmax) }
        &nbsp;
    {else }
    <div class="divelogcrumbs">
        <a href="{$app_path}/{$base_page}?start={$next}&amp;max={$max}" class="divelogcrumbs" title="{$next_group_linktitle}">{$next}</a>
        &nbsp;|&nbsp;
        <a href="{$app_path}/{$base_page}?start={$startlast}&amp;max={$max}" class="divelogcrumbs" title="{$last_group_linktitle}">{$last}</a>
    </div>
    {/if}
    </td>
    </tr>
</table>


