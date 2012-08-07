{if !isset($embed)}
{include file='header.tpl'}
{/if}
<div id="content">
{*load part *}
{if isset($dive_detail)}

{* Load diveshop details file *}
<!-- Include diveshop_details -->
{include file='diveshop_details.tpl'}
<!-- End include diveshop_details -->
{elseif isset($diver_overview)}
{include file='diver_overview.tpl'}
{else}
<!-- Include diveshop_overview -->
{include file='diveshop_overview.tpl'}
<!-- End include diveshop_overview -->

{/if}
</div>
{include file='footer.tpl'}

