<div data-role="header" data-position="fixed">
	<a href="email.php?folder={$folderID}&sid={$sid}" data-icon="arrow-l" data-direction="reverse" data-transition="slide">{$folderName}</a>
	<h1>{$pageTitle}</h1>
	<div data-role="controlgroup" data-type="horizontal" class="ui-btn-right">
		<a{if !$nextID} class="ui-disabled"{/if} href="email.php?action=read&id={$nextID}&sid={$sid}" data-role="button" data-icon="arrow-u" data-iconpos="notext">&nbsp;</a>
		<a{if !$prevID} class="ui-disabled"{/if} href="email.php?action=read&id={$prevID}&sid={$sid}" data-role="button" data-icon="arrow-d" data-iconpos="notext">&nbsp;</a>
	</div>
</div>

<div data-role="content">
	<ul data-role="listview" class="metaList">
		<li>
        	<label>{lng p="from"}:</label>
        	<p>{text value=$from}</p>
		</li>
		{if $cc}<li>
        	<label>{lng p="cc"}:</label>
        	<p>{text value=$cc}</p>
		</li>{/if}
		<li>
        	<label>{lng p="to"}:</label>
        	<p>{text value=$to}</p>
		</li>
		<li>
			<label>{lng p="subject"}:</label>
			<p>{text value=$subject}</p>
			<p class="ui-li-aside">
				{date timestamp=$date nice=true}<br />
				<a href="email.php?action=read{if !$isUnread}&unread=true{/if}&id={$mailID}&sid={$sid}" class="unreadLink"><img src="{$selfurl}{$_tpldir}images/m/dot.png" />{if $isUnread}{lng p="unread"}{else}{lng p="read"}{/if}</a>
			</p>
		</li>
	</ul>
	
	<div data-role="controlgroup" data-type="horizontal" style="text-align:center;">
		<a href="email.php?action=deleteMail&id={$mailID}&sid={$sid}" data-role="button" data-icon="delete">{lng p="delete"}</a>
		<a href="email.php?action=compose&reply={$mailID}&sid={$sid}" data-role="button" data-icon="edit">{lng p="reply"}</a>
		<a href="email.php?action=compose&forward={$mailID}&sid={$sid}" data-role="button" data-icon="forward">{lng p="forward"}</a>
	</div>
	
	<p>
		{$text}
	</p>

	{if $attachments}
	<p>
		<br />
		<h3>{lng p="attachments"}</h3>
		<ul data-role="listview" data-inset="true">
			{foreach from=$attachments item=att key=attID}
			<li>
				<a href="email.php?action=attachment{if $att.viewable}&view=true{/if}&id={$mailID}&attachment={$attID}&sid={$sid}" rel="external" target="_blank">
					{text value=$att.filename}
					<p class="ui-li-aside">{size bytes=$att.size}</p>
				</a>
			</li>
			{/foreach}
		</ul>
	</p>
	{/if}
</div>
