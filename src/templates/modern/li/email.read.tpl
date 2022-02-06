<div id="contentHeader">
	<div class="left">
		<i class="fa fa-envelope"></i>
		{lng p="mail_read"}: {text value=$subject cut=45}
	</div>
	<div class="right">
		<button type="button" onclick="document.location.href='email.php?folder={$folderID}&sid={$sid}';">
			<i class="fa {if $folderInfo.type == 'inbox'}fa-inbox{elseif $folderInfo.type == 'outbox'}fa-inbox{elseif $folderInfo.type == 'drafts'}fa-envelope{elseif $folderInfo.type == 'spam'}fa-ban{elseif $folderInfo.type == 'trash'}fa-trash-o{elseif $folderInfo.type == 'intellifolder'}fa-folder{else}fa-folder-o{/if}" aria-hidden="true"></i>
			{$folderInfo.title}
		</button>
		{if !$folderInfo.readonly}<button type="button" onclick="moveMail('{$mailID}');">
			<i class="fa fa-arrows" aria-hidden="true"></i>
			{lng p="move"}
		</button>{/if}
	</div>
</div>

<div class="scrollContainer withBottomBar{if $smarty.get.openConversationView||$attachments||$notes}AndLayer{/if}" id="mailReadScrollContainer">
	{hook id="email.read.tpl:head"}

	<div class="previewMailHeader" id="mailHeader">
		<table class="lightTable" width="100%">
			<tr>
				<th width="120">{lng p="from"}:</th>
				<td>{addressList list=$fromAddresses}</td>
			</tr>
			<tr>
				<th>{lng p="subject"}:</th>
				<td><strong>{text value=$subject}</strong></td>
			</tr>
			<tr>
				<th>{lng p="date"}:</th>
				<td>{date timestamp=$date elapsed=true}</td>
			</tr>
			<tr>
				<th>{lng p="to"}:</th>
				<td>{addressList list=$toAddresses}</td>
			</tr>
			{if $ccAddresses}<tr>
				<th>{lng p="cc"}:</th>
				<td>{addressList list=$ccAddresses}</td>
			</tr>{/if}
			{if $replyToAddresses}<tr>
				<th>{lng p="replyto"}:</th>
				<td>{addressList list=$replyToAddresses}</td>
			</tr>{/if}
			{if $priority!=0}<tr>
				<th>{lng p="priority"}:</th>
				<td>
					<img src="{$tpldir}images/li/mailico_{if $priority==-1}low{elseif $priority==1}high{/if}.gif" border="0" alt="" align="absmiddle" />
					{lng p="prio_$priority"}
				</td>
			</tr>{/if}

			{if $smimeStatus!=0&&!($smimeStatus&1)}
			<tr>
				<th>{lng p="security"}:</th>
				<td>
					{if $smimeStatus&2}
					<font color="#FF0000">
						<img src="{$tpldir}images/li/mailico_signed_bad.png" width="16" height="16" border="0" alt="" align="absmiddle" />
						{lng p="badsigned"}
					</font>
					&nbsp;&nbsp;
					{/if}
					{if $smimeStatus&4}
					<img src="{$tpldir}images/li/mailico_signed_ok.png" width="16" height="16" border="0" alt="" align="absmiddle" />
					<a href="javascript:void(0);" onclick="showCertificate('{$smimeCertificateHash}');">{lng p="signed"}</a>
					&nbsp;&nbsp;
					{/if}
					{if $smimeStatus&8}
					<img src="{$tpldir}images/li/mailico_signed_noverify.png" width="16" height="16" border="0" alt="" align="absmiddle" />
					<a href="javascript:void(0);" onclick="showCertificate('{$smimeCertificateHash}');" style="color:#FF8C00;">{lng p="noverifysigned"}</a>
					&nbsp;&nbsp;
					{/if}
					{if $smimeStatus&64}
					<img src="{$tpldir}images/li/mailico_encrypted_error.png" width="16" height="16" border="0" alt="" align="absmiddle" />

					<font color="#FF0000">
						{lng p="decryptionfailed"}
					</font>
					&nbsp;&nbsp;
					{/if}
					{if $smimeStatus&128}
					<img src="{$tpldir}images/li/mailico_encrypted.png" width="16" height="16" border="0" alt="" align="absmiddle" /> {lng p="encrypted"}
					&nbsp;&nbsp;
					{/if}
				</td>
			</tr>
			{/if}

			{if $deliveryStatus}
			<tr>
				<th>{lng p="deliverystatus"}:</th>
				<td>
					{if $deliveryStatus.exception}<i class="fa fa-exclamation-triangle" style="color:orange;"></i>
					{elseif $deliveryStatus.allDelivered}<i class="fa fa-check" style="color:green;"></i>
					{else}<i class="fa fa-refresh"></i>{/if}
					<a href="javascript:showDeliveryStatus({$mailID});">{$deliveryStatus.statusText}</a>
				</td>
			{/if}

			{hook id="email.read.tpl:metaTable"}
		</table>
	</div>

	<div id="bigFormToolbar">

		{if $prevID}<button type="button" onclick="document.location.href='email.read.php?id={$prevID}&sid={$sid}';">
			&laquo;
		</button>{/if}

		<button type="button" onclick="mailReply({$mailID},false);">
			<i class="fa fa-mail-reply"></i>
			{lng p="reply"}
		</button>

		<button type="button" onclick="mailReply({$mailID},true);">
			<i class="fa fa-mail-reply-all"></i>
			{lng p="replyall"}
		</button>

		<button type="button" onclick="document.location.href='email.compose.php?sid={$sid}&forward={$mailID}';">
			<i class="fa fa-mail-forward"></i>
			{lng p="forward"}
		</button>

		<button type="button" onclick="document.location.href='email.compose.php?sid={$sid}&redirect={$mailID}';">
			<i class="fa fa-envelope-o"></i>
			{lng p="redirect"}
		</button>

		<button type="button" onclick="document.location.href='email.read.php?sid={$sid}&action=download&id={$mailID}';">
			<i class="fa fa-download"></i>
			{lng p="download"}
		</button>

		<button type="button" onclick="printMail({$mailID},'{$sid}');">
			<i class="fa fa-print"></i>
			{lng p="print"}
		</button>

		{if !$folderInfo.readonly}<button type="button" onclick="{if $folderID==-5}if(confirm('{lng p="realdel"}')) {/if} document.location.href='email.php?sid={$sid}&do=deleteMail&id={$mailID}&folder={$folderID}';">
			<i class="fa fa-remove"></i>
			{lng p="delete"}
		</button>{/if}

		{if $nextID}<button type="button" onclick="document.location.href='email.read.php?id={$nextID}&sid={$sid}';">
			&raquo;
		</button>{/if}

	</div>


<div class="pad">

{hook id="email.read.tpl:beforeText"}
<div>
		{if $flags&128}
		<div class="mailWarning">
			&nbsp;
			<img align="absmiddle" border="0" alt="" src="{$tpldir}images/li/infected.png" width="16" height="16" />
			{lng p="infectedtext"}: {$infection}
		</div>
		{/if}
		{if $flags&256}
		<div class="mailNote" id="spamQuestionDiv" style="display:;">
			&nbsp;
			<img align="absmiddle" border="0" alt="" src="{$tpldir}images/li/spam.png" width="16" height="16" />
			{lng p="spamtext"}
			{if !$trained}<a href="javascript:setMailSpamStatus({$mailID}, false)">{lng p="isnotspam"}</a>{/if}
		</div>
		{elseif !$trained}
		<div class="mailNote" id="spamQuestionDiv" style="display:;">
			&nbsp;
			<img align="absmiddle" border="0" alt="" src="{$tpldir}images/li/spam_question.png" width="16" height="16" />
			{lng p="spamquestion"}
			&nbsp;&nbsp;
			<a href="javascript:setMailSpamStatus({$mailID}, true)">
				<img src="{$tpldir}images/li/yes.png" width="16" height="16" border="0" alt="" align="absmiddle" /> {lng p="yes"}
			</a>
			&nbsp;&nbsp;
			<a href="javascript:setMailSpamStatus({$mailID}, false)">
				<img src="{$tpldir}images/li/no.png" width="16" height="16" border="0" alt="" align="absmiddle" /> {lng p="no"}
			</a>
		</div>
		{/if}
		{if $flags&512}
		<div class="mailNote">
			&nbsp;
			<i class="fa fa-comment-o" aria-hidden="true"></i>
			{lng p="certmailinfo"}
		</div>
		{/if}
		{if $htmlAvailable}
		<div class="mailNote">
			&nbsp;
			<i class="fa fa-comment-o" aria-hidden="true"></i>
			{lng p="htmlavailable"}
			<a href="email.read.php?sid={$sid}&id={$mailID}&htmlView=true">{lng p="view"} &raquo;</a>
		</div>
		{/if}
		{if $noExternal}
		<div class="mailNote" id="noExternalDiv" style="display:;">
			&nbsp;
			<i class="fa fa-comment-o" aria-hidden="true"></i>
			{lng p="noexternal"}
			<a href="email.read.php?action=inlineHTML&mode={$textMode}&id={$mailID}&sid={$sid}&enableExternal=true" target="mailFrame" onclick="document.getElementById('noExternalDiv').style.display='none';">{lng p="showexternal"} &raquo;</a>
		</div>
		{/if}
		{if $confirmationTo}
		<div class="mailNote" id="confirmationDiv" style="display:;">
			&nbsp;
			<i class="fa fa-sign-language" aria-hidden="true"></i>
			{lng p="senderconfirmto"}
			<b>{text value=$confirmationTo}</b>.
			<a href="javascript:sendMailConfirmation({$mailID});">{lng p="sendconfirmation"} &raquo;</a>
		</div>
		{elseif $flags&16384}
		<div class="mailNote preview" id="confirmationDiv" style="display:;">
			&nbsp;
			<i class="fa fa-sign-language" aria-hidden="true"></i>
			{lng p="confirmationsent"}
		</div>
		{/if}
		{hook id="email.read.tpl:mailNotes"}
	</div>

	<iframe name="mailFrame" width="100%" style="height:200px;" id="textArea" src="about:blank" class="mailHTMLText" frameborder="no"></iframe>
	<textarea id="textArea_raw" style="display:none;">{text allowEmpty=true value=$text allowDoubleEnc=true}</textarea>

	<script>
	<!--
		initEMailTextArea(EBID('textArea_raw').value);
	//-->
	</script>
</p>
{hook id="email.read.tpl:afterText"}

<div id="afterText">
{if $vcards}
<p>
{foreach from=$vcards item=card key=key}
	<div class="mailBox"><table width="100%">
			<tr>
				<td rowspan="4" width="66" align="center"><i class="fa fa-address-card-o" aria-hidden="true"></i></td>
				<td width="100"><b>{lng p="firstname"}:</td>
				<td>{text value=$card.vorname}</td>
				<td rowspan="4" width="140" background="{$tpldir}images/li/box_bg.gif">
					<a href="email.read.php?id={$mailID}&action=importVCF&attachment={$key}&sid={$sid}"><i class="fa fa-upload" aria-hidden="true"></i> {lng p="importvcf"}</a><br />
					<a href="email.read.php?id={$mailID}&action=downloadAttachment&attachment={$key}&sid={$sid}"><i class="fa fa-download" aria-hidden="true"></i> {lng p="download"}</a>
				</td>
			</tr>
			<tr>
				<td><b>{lng p="surname"}:</td>
				<td>{text value=$card.nachname}</td>
			</tr>
			<tr>
				<td><b>{lng p="company"}:</td>
				<td>{text value=$card.firma}</td>
			</tr>
			<tr>
				<td><b>{lng p="email"}:</td>
				<td>{email value=$card.email}</td>
			</tr>
		</table></div>
{/foreach}
</p>
{/if}

{hook id="email.read.tpl:foot"}

<form id="quoteForm" action="email.compose.php?sid={$sid}&reply={$mailID}" method="post">
	<input type="hidden" name="text" id="quoteText" value="" />
</form>

</div>

</div></div>

<div class="contentBottomLayer" id="bottomLayer_attachments" style="display:{if $smarty.get.openConversationView||$notes||!$attachments}none{/if};">
	<div class="contentHeader">
		<div class="left">
			<input type="checkbox" style="margin-left:-0.5em;" id="allChecker" onclick="checkAll(this.checked, document.forms.attachmentsForm, 'att');" />
			<i class="fa fa-paperclip"></i>
			{lng p="attachments"} ({$attachments|@count})
		</div>
		<div class="right">
			<button onclick="readMailHideBottomLayers()">
				<i class="fa fa-close"></i>
			</button>
		</div>
	</div>

	<form name="attachmentsForm" method="get" action="email.read.php">
	<input type="hidden" name="id" value="{$mailID}" />
	<input type="hidden" name="sid" value="{$sid}" />

	<div class="scrollContainer withBottomBar">
		<table class="listTable" style="border:none;border-radius:0;">
			{foreach from=$attachments item=attachment key=attID}
			{cycle values="listTableTD,listTableTD2" assign="class"}
			<tr>
				<td class="{$class}" width="26"><input type="checkbox" name="att[]" id="att_{$attID}" value="{$attID}" /></td>
				<td class="{$class}"><i class="fa fa-paperclip"></i>
										<a href="javascript:advancedOptions('{$attID}', 'right', 'bottom', '{$tpldir}');"><img id="advanced_{$attID}_arrow" src="{$tpldir}images/li/mini_arrow_right.png" width="13" height="13" border="0" alt="" align="absmiddle" />
										{text value=$attachment.filename cut=45}</a></td>
				<td class="{$class}" width="20%">{text value=$attachment.mimetype cut=45}</td>
				<td class="{$class}" width="20%" style="text-align:right;">{lng p="approx"} {size bytes=$attachment.size}&nbsp;&nbsp;
											<a href="email.read.php?id={$mailID}&action=downloadAttachment&attachment={$attID}&sid={$sid}"><i class="fa fa-download"></i></a>
											&nbsp;</td>
			</tr>
			<tbody id="advanced_{$attID}_body" style="display:none;">
			<tr>
				<td class="attDiv">&nbsp;</td>
				<td colspan="3" class="attDiv">
					<input type="button" value=" {lng p="download"} " onclick="document.location.href='email.read.php?id={$mailID}&action=downloadAttachment&attachment={$attID}&sid={$sid}';" />
					<input type="button" value=" {lng p="savetowebdisk"} " onclick="saveAttachmentToWebdisk({$mailID}, '{$attID}', '{$attachment.filename}', '{$sid}')" />
					{if $attachment.viewable}<input type="button" value=" {lng p="view"} " onclick="javascript:window.open('email.read.php?id={$mailID}&action=downloadAttachment&attachment={$attID}&view=true&sid={$sid}');" />{/if}
					{if $attachment.mimetype=='message/rfc822'||$attachment.filetype=='.eml'}<input type="button" value=" {lng p="view"} " onclick="javascript:showAttachedMail({$mailID}, '{$attID}', '{text value=$attachment.filename cut=45 escape=true}');" />{/if}
					{if $attachment.mimetype=='application/zip'||$attachment.filetype=='.zip'}<input type="button" value=" {lng p="view"} " onclick="javascript:showAttachedZIP({$mailID}, '{$attID}', '{text value=$attachment.filename cut=45 escape=true}');" />{/if}
				</td>
			</tr>
			</tbody>
			{/foreach}
		</table>
	</div>

	<div class="contentFooter">
	 	<div class="left">
			<select class="smallInput" name="do">
				<option value="-">------ {lng p="selaction"} ------</option>
				<option value="downloadAttachments">{lng p="download"}</option>
				{hook id="email.read.tpl:attachSelect"}
			</select>
			<input class="smallInput" type="submit" value="{lng p="ok"}" />
		</div>
	</div>
	</form>
</div>

{if $conversationView}
<div class="contentBottomLayer" id="bottomLayer_conversation" style="display:{if !$smarty.get.openConversationView}none{/if};">
	<div class="contentHeader">
		<div class="left">
			<i class="fa fa-comment"></i>
			{lng p="conversation"}
		</div>
		<div class="right">
			<button onclick="readMailHideBottomLayers()">
				<i class="fa fa-close"></i>
			</button>
		</div>
	</div>

	<div class="bigForm">
		<iframe id="conversationIFrame" style="width:100%;height:100%;" src="email.read.php?action=showThread&id={$mailID}&sid={$sid}" border="0" frameborder="0"></iframe>
	</div>
</div>
{/if}

<div class="contentBottomLayer" id="bottomLayer_props" style="display:{if !$notes||$smarty.get.openConversationView}none{/if};">
	<div class="contentHeader">
		<div class="left">
			<i class="fa fa-tags"></i>
			{lng p="props"}
		</div>
		<div class="right">
			<button onclick="readMailHideBottomLayers()">
				<i class="fa fa-close"></i>
			</button>
		</div>
	</div>

	<form method="post" action="email.read.php?id={$mailID}&sid={$sid}">
	<input type="hidden" name="do" value="saveMeta" />

	<div class="bigForm">
		<table class="listTable" style="border:none;border-radius:0;">
			<tr>
				<th class="listTableHead">{lng p="color"}</th>
				<th class="listTableHead">{lng p="flags"}</th>
				<th class="listTableHead">{lng p="notes"}</th>
			</tr>
			<tr>
				<td width="160" style="padding:5px;">
					<table>
						<tr>
							<td><input type="radio"{if $color==1} checked="checked"{/if} name="color" value="1" /></td>
							<td><div class="calendarDate_0" style="padding:0px;margin:0px;margin-left:5px;width:12px;height:12px;"></div></td>

							<td><input type="radio"{if $color==2} checked="checked"{/if} name="color" value="2" /></td>
							<td><div class="calendarDate_1" style="padding:0px;margin:0px;margin-left:5px;width:12px;height:12px;"></div></td>

							<td><input type="radio"{if $color==3} checked="checked"{/if} name="color" value="3" /></td>
							<td><div class="calendarDate_2" style="padding:0px;margin:0px;margin-left:5px;width:12px;height:12px;"></div></td>
						</tr>
						<tr>
							<td><input type="radio"{if $color==4} checked="checked"{/if} name="color" value="4" /></td>
							<td><div class="calendarDate_3" style="padding:0px;margin:0px;margin-left:5px;width:12px;height:12px;"></div></td>

							<td><input type="radio"{if $color==5} checked="checked"{/if} name="color" value="5" /></td>
							<td><div class="calendarDate_4" style="padding:0px;margin:0px;margin-left:5px;width:12px;height:12px;"></div></td>

							<td><input type="radio"{if $color==6} checked="checked"{/if} name="color" value="6" /></td>
							<td><div class="calendarDate_5" style="padding:0px;margin:0px;margin-left:5px;width:12px;height:12px;"></div></td>
						</tr>
						<tr>
							<td><input type="radio"{if $color==0} checked="checked"{/if} name="color" value="0" /></td>
							<td colspan="5">&nbsp;{lng p="none"}</td>
						</tr>
					</table>
				</td>
				<td width="150" class="listTableTDActive" style="padding:5px;padding-left:10px;">
					<i class="fa fa-envelope"></i>
					<input type="checkbox" name="flags[1]" id="flags1"{if $smarty.post.do=='saveMeta'&&($flags&1)} checked="checked"{/if} />
					<label for="flags1">{lng p="unread"}</label><br />

					<i class="fa fa-flag"></i>
					<input type="checkbox" name="flags[16]" id="flags16"{if $flags&16} checked="checked"{/if} />
					<label for="flags16">{lng p="marked"}</label><br />

					<i class="fa fa-check"></i>
					<input type="checkbox" name="flags[4096]" id="flags4096"{if $flags&4096} checked="checked"{/if} />
					<label for="flags4096">{lng p="done"}</label><br />
				</td>
				<td style="padding:5px;">
					<textarea style="width:100%;height:65px;box-sizing:border-box;" name="notes">{text value=$notes allowEmpty=true}</textarea>
				</td>
			</tr>
		</table>
	</div>

	<div class="contentFooter">
	 	<div class="right">
			<button class="primary" type="submit"{if $folderInfo.readonly} disabled="disabled" style="color:grey;"{/if}>
				<i class="fa fa-check"></i>
				{lng p="save"}
			</button>
		</div>
	</div>

	</form>
</div>

<div id="contentFooter">
	<div class="left">
		{if $attachments}
		<button type="button" onclick="readMailShowBottomLayer('attachments')">
			<i class="fa fa-paperclip"></i>
			{lng p="attachments"} <strong>({$attachments|@count})</strong>
		</button>
		{/if}

		{if $conversationView}
		<button type="button" onclick="readMailShowBottomLayer('conversation')">
			<i class="fa fa-comment"></i>
			{lng p="conversation"}
		</button>
		{/if}

		<button type="button" onclick="readMailShowBottomLayer('props')">
			<i class="fa fa-tags"></i>
			{lng p="props"}
		</button>
	</div>
</div>

{include file="li/email.addressmenu.tpl"}
