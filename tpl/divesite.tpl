{include file='header.tpl'}
<div id="content">
{*load part *}
{if isset($dive_detail)}

{* Load divesite details file *}
<!-- Include divesite_details -->
{include file='divesite_details.tpl'}
<!-- End include divesite_details -->
{elseif isset($diver_overview)}
{include file='diver_overview.tpl'}
{else}
<!-- Include dives_overview -->
{include file='divesite_overview.tpl'}
<!-- End include divesite_overview -->

{/if}
</div>
{include file='footer.tpl'}

