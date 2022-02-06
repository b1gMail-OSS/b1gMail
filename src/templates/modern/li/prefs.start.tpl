<div id="contentHeader">
	<div class="left">
		<i class="fa fa-tachometer" aria-hidden="true"></i>
		{lng p="prefs"}
	</div>
</div>

<div class="scrollContainer"><div class="pad">

{if $templatePrefs.prefsLayout=='onecolumn'}
	<table width="100%">
	{foreach from=$prefsItems item=null key=item}
	<tr>
		<td width="55" height="62" valign="top">
			<a href="prefs.php?action={$item}&sid={$sid}">
				{if $prefsIcons[$item]}
					<img src="{$prefsIcons[$item]}" width="48" height="48" style="margin-right: 3px;" border="0" alt="" align="absmiddle" />
				{else}
					<i class="fa {if $item=='autoresponder'}fa-reply{elseif $item=='aliases'}fa-user{elseif $item=='common'}fa-cogs{elseif $item=='antispam'}fa-ban{elseif $item=='antivirus'}fa-bug{elseif $item=='antivirus'}fa-bug{elseif $item=='orders'}fa-shopping-cart{elseif $item=='faq'}fa-question-circle-o{elseif $item=='filters'}fa-filter{elseif $item=='coupons'}fa-id-badge{elseif $item=='membership'}fa-id-card-o{elseif $item=='extpop3'}fa-compress{elseif $item=='keyring'}fa-key{elseif $item=='signatures'}fa-quote-right{elseif $item=='software'}fa-download{elseif $item=='contact'}fa-user-o{else}{$item}{/if} fa-4x" aria-hidden="true"></i>
				{/if}
			</a>
		</td>
		<td class="prefsBox">
			<h2><a href="prefs.php?action={$item}&sid={$sid}">{lng p="$item"}</a></h2>
			{lng p="prefs_d_$item"}
		</td>
	</tr>
	{/foreach}
	</table>
{else}
	<table width="100%" cellpadding="5">
		<tr>
		{assign var="i" value=0}
		{foreach from=$prefsItems item=null key=item}
		{assign var="i" value=$i+1}
		{if $i==3}
		{assign var="i" value=1}
		</tr>
		<tr>
		{/if}
		
		<td width="50%">
			<table width="100%" class="listTable">
				<tr>
					<th colspan="3" class="listTableHead"><a href="prefs.php?action={$item}&sid={$sid}"><b>{lng p="$item"}</b></a></th>
				</tr>
				<tr>
					<td class="listTableIconSide">
						{if $prefsIcons[$item]}
							<img src="{$prefsIcons[$item]}" width="48" height="48" style="margin-right: 3px;" border="0" alt="" align="absmiddle" />
						{else}
							<i class="fa {if $item=='autoresponder'}fa-reply{elseif $item=='aliases'}fa-user{elseif $item=='common'}fa-cogs{elseif $item=='antispam'}fa-ban{elseif $item=='antivirus'}fa-bug{elseif $item=='antivirus'}fa-bug{elseif $item=='orders'}fa-shopping-cart{elseif $item=='faq'}fa-question-circle-o{elseif $item=='filters'}fa-filter{elseif $item=='coupons'}fa-id-badge{elseif $item=='membership'}fa-id-card-o{elseif $item=='extpop3'}fa-compress{elseif $item=='keyring'}fa-key{elseif $item=='signatures'}fa-quote-right{elseif $item=='software'}fa-download{elseif $item=='contact'}fa-user-o{else}{$item}{/if} fa-4x" aria-hidden="true"></i>
						{/if}
					</td>
					<td valign="top" style="padding:6px;">{lng p="prefs_d_$item"}</td>
					<td class="listTableSide"><a href="prefs.php?action={$item}&sid={$sid}">&raquo;</a></td>
				</tr>
			</table>
		</td>
		
		{/foreach}
		{if $i<3}
		{math assign="i" equation="x - y" x=2 y=$i}
		{section loop=$i name=rest}
			<td>&nbsp;</td> 
		{/section}
		{/if}
		</tr>
	</table>
{/if}

</div></div>
