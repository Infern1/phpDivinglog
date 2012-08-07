{if !isset($embed)}
{include file='header.tpl'}
{/if}
<div id="content">
{*load part *}
{if isset($dive_detail)}

{* Load divetrip details file *}
<!-- Include divetrip_details -->
{include file='divetrip_details.tpl'}
<!-- End include divetrip_details -->
{elseif isset($diver_overview)}
{include file='diver_overview.tpl'}
{else}
<!-- Include divetrip_overview -->
{include file='divetrip_overview.tpl'}
<!-- End include divetrip_overview -->

{/if}
</div>
{include file='footer.tpl'}

