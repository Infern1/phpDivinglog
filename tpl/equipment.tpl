{if !isset($embed)}
{include file='header.tpl'}
{/if}
<div id="content">
{*load part *}
{if isset($equipment_id)}

{* Load equipment details file *}
<!-- Include equipment_details -->
{include file='equipment_details.tpl'}
<!-- End include equipment_details -->
{elseif isset($diver_overview)}
{include file='diver_overview.tpl'}
{else}
<!-- Include equipment_overview -->
{include file='equipment_overview.tpl'}
<!-- End include equipment_overview -->

{/if}
</div>
{include file='footer.tpl'}


