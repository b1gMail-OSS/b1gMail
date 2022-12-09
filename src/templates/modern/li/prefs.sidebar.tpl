<div class="sidebarHeading">{lng p="prefs"}</div>
<div class="contentMenuIcons">
	<a href="prefs.php?sid={$sid}"><i class="fa fa-tachometer" aria-hidden="true"></i> {lng p="overview"}</a><br />
	{foreach from=$prefsItems item=null key=item}
	<a href="prefs.php?action={$item}&sid={$sid}">
				  {if isset($prefsIcons[$item])}
					<img src="{$prefsIcons[$item]}" width="16" height="16" style="margin-right: 3px;" border="0" alt="" align="absmiddle" />
				  {else}
					<i class="fa {if $item=='autoresponder'}fa-reply{elseif $item=='aliases'}fa-user{elseif $item=='common'}fa-cogs{elseif $item=='antispam'}fa-ban{elseif $item=='antivirus'}fa-bug{elseif $item=='antivirus'}fa-bug{elseif $item=='orders'}fa-shopping-cart{elseif $item=='faq'}fa-question-circle-o{elseif $item=='filters'}fa-filter{elseif $item=='coupons'}fa-id-badge{elseif $item=='membership'}fa-id-card-o{elseif $item=='extpop3'}fa-compress{elseif $item=='keyring'}fa-key{elseif $item=='signatures'}fa-quote-right{elseif $item=='software'}fa-download{elseif $item=='contact'}fa-user-o{else}{$item}{/if}" style="width: 20px;" aria-hidden="true"></i>
			      {/if} {lng p="$item"}
	</a><br />
	{/foreach}
</div>
