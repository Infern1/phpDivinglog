{include file='header.tpl'}
<div id="content">
{*load part *}
{if isset($divecity_id)}

{* Load divesite details file *}
<!-- Include divecity_details -->
{include file='divecity_details.tpl'}
<!-- End include divecity_details -->
{elseif isset($diver_overview)}
{include file='diver_overview.tpl'}
{else}
<!-- Include divecite_overview -->
{include file='divecity_overview.tpl'}
<!-- End include divesite_overview -->

{/if}
</div>
{include file='footer.tpl'}

