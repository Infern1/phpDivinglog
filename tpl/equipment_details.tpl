<!-- Include links_details -->
{include file='links_details.tpl'}
<!-- End include links_details -->
<table class="divetable" cellspacing="0" cellpadding="0" width="100%">
    <tr class="divesection">
	    <td colspan="4">&nbsp;</td>
	</tr>
    {* Show main equipment details *}
	<tr class="divetitle">
	<td colspan="2">{$equip_object}</td>
	<td colspan="2">{$equip_manufacturer}</td>
	</tr>

	<tr class="divecontent">
        {if isset($Object)} <td colspan="2">{$Object}</td>
        {else} <td>-</td>
        {/if}

        {if isset($Manufacturer)}  <td colspan="2">{$Manufacturer}</td>
        {else } <td>-</td>
        {/if}
	</tr>

    {*	Show equipment purchase details *}
	 <tr class="divetitle">
	  <td colspan="2">{$equip_shop}</td>
	  <td>{$equip_datep}</td>
	  <td>{$equip_price}</td>
	 </tr>

	 <tr class="divecontent">
	{if isset($Shop)} 
    <td colspan="2">{$Shop}</td>
	{else} 
    <td>-</td>
	{/if}
	{if isset($DateP)}
	<td>{$DateP}</td>
    {else}
	 <td>-</td>
	{/if}
	{if isset($Price)}
    <td>{$Price}</td>
    {else} 
    <td>-</td>
	{/if}
	 </tr>
    {*	Show the rest of the details *}
	 <tr class="divetitle">
	  <td>{$equip_serial}</td>
	  <td>{$equip_warranty}</td>
	  <td>{$equip_dater}</td>
	  { if isset($PhotoPath)} <td>{$equip_photo}</td>
        {else} <td>&nbsp;</td>
	    {/if}
	 </tr>

	 <tr class="divecontent">
	{if isset($Serial)}<td>{$Serial}</td>
    {else}<td>-</td>
	{/if}
	{if isset($Warranty)} <td>{$Warranty}</td>
    { else } <td>-</td>
	{/if}
	{if isset($DateR)}<td>{$DateR}</td>
	{else } <td>-</td>
	{/if}
	{if isset($PhotoPath)}
<!--		<div>-->
        <td>
    {foreach from=$image_link key=id item=i name=images}
             <a id="thumb" href="{$web_root}/{$i.img_url}" class="highslide" onclick="return hs.expand(this)">
                    <img src="{$web_root}/{$i.img_url}" alt="Highslide JS" title="{$i.img_title}" height="80" width="120" ></a>
       <div class='highslide-caption'>
        {$i.img_title}
        </div>
    {/foreach}
		</td>
<!--</div>-->
    {else}
	    <td>&nbsp;</td>
	{/if}
	 </tr>

	 <tr><td colspan="4" class="spacing">&nbsp;</td></tr>

    {*	Comments *}
    {*	Show them if we have them *}
	{if isset($Comments) }
		<tr class="divesection">
		    <td colspan="4">{$equip_sect_comments}</td>
		</tr>
        <tr>
            <td colspan="4">{$Comments}</td>
        </tr>
    {/if}
	   <tr class="divesection">
	    <td colspan="4">&nbsp;</td>
	</tr>
     </table>
   <!-- Include links_details -->
	{include file='links_details.tpl'}
    <!-- End include links_details -->

