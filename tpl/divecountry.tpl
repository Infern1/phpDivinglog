{include file='header.tpl'}
<div id="content">
{*load part *}
{if isset($dive_detail)}

{* Load divecountry details file *}
<!-- Include divecountry_details -->
{include file='divecountry_details.tpl'}
<!-- End include divecountry_details -->
{elseif isset($diver_overview)}
{include file='diver_overview.tpl'}
{else}
<!-- Include divecountry_overview -->
{include file='divecountry_overview.tpl'}
<!-- End include divecountry_overview -->

{/if}
</div>
{include file='footer.tpl'}

