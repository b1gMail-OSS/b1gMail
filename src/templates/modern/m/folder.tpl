{if !$listOnly}<div data-role="header" data-position="fixed">
	<a href="email.php?action=folders&sid={$sid}" data-icon="arrow-l" data-direction="reverse" data-transition="slide">{lng p="folders"}</a>
	<h1>{$pageTitle}</h1>
	<a href="email.php?action=compose&sid={$sid}" data-icon="forward" data-iconpos="right" data-dom-cache="false">{lng p="sendmail"}</a>
</div>

<div data-role="content">
	<ul data-role="listview" data-filter="true" data-filter-placeholder="{lng p="search"}..." id="mailList">{/if}
	{foreach from=$mails item=mail key=mailID}
		<li>
			<a href="email.php?action=read&id={$mailID}&sid={$sid}" data-transition="slide">
				{if $mail.flags&1}
				<img src="{$selfurl}{$_tpldir}images/m/dot.png" class="ui-li-icon" style="margin-top:0.75em;" />
				{elseif $mail.flags&2}
				<i class="fa fa-mail-reply" aria-hidden="true"></i>
				{elseif $mail.flags&4}
				<i class="fa fa-share" aria-hidden="true"></i>
				{else}
				<img src="{$selfurl}{$_tpldir}images/li/mailico_empty.gif" class="ui-li-icon" />
				{/if}
				<h3>{if $mail.from_name}{text value=$mail.from_name}{else}{text value=$mail.from_mail}{/if}</h3>
				<p><strong>{text value=$mail.subject}</strong></p>
				<p class="ui-li-aside">{date timestamp=$mail.timestamp short=true}</p>
			</a>
		</li>
	{/foreach}
	{if !$listOnly}</ul>
	
	{if $haveMoreMails}
	<div class="bottomLink" id="moreMailsLink">
		<a href="javascript:void(0);" onclick="loadMoreMails({$folderID},{$nextPageNo});">{lng p="showmore"}...</a>
		<br /><br />
	</div>
	{/if}

</div>{/if}

{if $listOnly&&!$haveMoreMails}<!-- hideMoreMailsLink -->{/if}
