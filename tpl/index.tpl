{if !isset($embed)}
{include file='header.tpl'}
{/if}

<div id="content">
{if isset($dive_detail)}

	{* Load dive details file *}
	<!-- Include dive_details -->
	{include file='dive_details.tpl'}
	<!-- End include dive_details -->
{elseif isset($diver_overview)}
	{include file='diver_overview.tpl'}
{else}
	<!-- Include dives_overview -->
	{include file='dives_overview.tpl'}
	
	
	<!-- End include dives_overview -->

{/if}
</div>

{if !isset($embed)}
	{include file='footer.tpl'}
{/if}
