{if $bmNotifications}
<ul>
{foreach from=$bmNotifications item=_item}
	<li><a href="#"{if $_item.link} onmousedown="{if $_item.flags&2}{$_item.link}{else}document.location.href='{$_item.link}';{/if}"{/if}{if !$_item.read} class="unread"{/if}{if $_item.old} style="opacity:0.5;"{/if}>
		{if $_item.faIcon}<div class="bm-notify-row"><span class="bm-notify-icon">{include file="li/icon.tpl" faIcon=$_item.faIcon iconClass='icon-lg'}</span><span class="bm-notify-text">{else}<span class="bm-notify-text">{/if}
		{text noentities=true value=$_item.text cut=150}
		<div class="date">{date nice=true timestamp=$_item.date}</div>
		</span>{if $_item.faIcon}</div>{/if}
	</a></li>
{/foreach}
</ul>
{else}
	<div class="bm-notify-empty text-center text-secondary py-3"><em>({lng p="nonotifications"})</em></div>
{/if}
