{if $bmNotifications}
<ul>
{foreach from=$bmNotifications item=_item}
	<li><a href="#"{if $_item.link} onmousedown="{if $_item.flags&2}{$_item.link}{else}document.location.href='{$_item.link}sid={$sid}';{/if}"{/if}{if !$_item.read} class="unread"{/if}{if $_item.old} style="opacity:0.5;"{/if}>
		{if $_item.icon}<table><tr><td style="width:40px;"><i class="fa {$_item.faIcon} fa-3x" aria-hidden="true"></i></td><td>{/if}
		{text noentities=true value=$_item.text cut=150}
		<div class="date">{date nice=true timestamp=$_item.date}</div>
		{if $_item.icon}</td></tr></table>{/if}
	</a></li>
{/foreach}
</ul>
{else}
	<center style="margin-top:1em;color:#999;"><em>({lng p="nonotifications"})</em></center>
{/if}
