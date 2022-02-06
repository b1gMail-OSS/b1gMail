{if !$smarty.get.tableOnly}<form name="f1" action="email.php?do=action&{$folderString}&sid={$sid}" onsubmit="transferSelectedMailIDs()" method="post">
<input type="hidden" name="selectedMailIDs" id="selectedMailIDs" value="" />

<div id="contentHeader">
	<div class="left"{if $templatePrefs.showCheckboxes} style="padding-left:2px;"{/if}>
		{if $templatePrefs.showCheckboxes}<input type="checkbox" style="vertical-align:middle;" id="checkAllMails" onclick="if(this.checked) _mailSel.selectAll(); else _mailSel.unselectAll()||showMultiSelPreview(0);" />{/if}
		<i class="fa {if $folderInfo.type == 'inbox'}fa-inbox{elseif $folderInfo.type == 'outbox'}fa-inbox{elseif $folderInfo.type == 'drafts'}fa-envelope{elseif $folderInfo.type == 'spam'}fa-ban{elseif $folderInfo.type == 'trash'}fa-trash-o{elseif $folderInfo.type == 'intellifolder'}fa-folder{else}fa-folder-o{/if}" aria-hidden="true"></i> {$folderInfo.title}
	</div>

	<div class="right">
		{if $folderInfo.type!='intellifolder'&&!$folderInfo.readonly}
		<button onclick="showFolderMenu(event);" type="button">
			<i class="fa fa-gears fa-lg"></i>
		</button>
		{/if}

		<button onclick="switchPage({$pageNo})" type="button">
			<i class="fa fa-refresh fa-lg"></i>
		</button>

		{if !$folderInfo.readonly}<button onclick="folderViewOptions({$folderID});" type="button">
			<i class="fa fa-desktop fa-lg"></i>
		</button>{/if}
	</div>
</div>

<div class="scrollContainer withBottomBar">
{/if}

<table class="bigTable" id="mailTable">
	<colgroup>
		{if $templatePrefs.showCheckboxes}
		<col style="width:24px;" />
		{/if}
		<col style="width:24px;" />
		<col />
	</colgroup>

	{if $mailList}
	{assign var=first value=true}
	{foreach from=$mailList key=mailID item=mail}
	{assign var=mailGroupID value=$mail.groupID}
	{cycle values="listTableTR,listTableTR2" assign="class"}

	{if $mailID<0}
	{cycle values="listTableTR,listTableTR2" assign="class"}
	{if !$first}
	</tbody>
	{/if}
	<tr>
		<td colspan="{if $templatePrefs.showCheckboxes}3{else}2{/if}" class="folderGroup">
			<a style="display:block;cursor:pointer;" onclick="toggleGroup({$mailID},'{$mail.groupID}');">&nbsp;<img id="groupImage_{$mailID}" src="{$tpldir}images/{if $smarty.cookies.toggleGroup.$mailGroupID=='closed'}expand{else}contract{/if}.png" width="11" height="11" border="0" align="absmiddle" alt="" />
			&nbsp;{$mail.text} {if $mail.date && $mail.date!=-1}({date timestamp=$mail.date dayonly=true}){/if}</a>
		</td>
	</tr>
	<tbody id="group_{$mailID}" style="display:{if $smarty.cookies.toggleGroup.$mailGroupID=='closed'}none{/if};">
	{assign var=first value=false}
	{else}
	<tr id="mail_{$mailID}_ntr" class="{$class}">
		{if $templatePrefs.showCheckboxes}
		<td class="narrowRow" style="text-align:center;width:24px;">
			<input type="checkbox" id="selecTable_{$mailID}" />
		</td>
		{/if}
		<td id="mail_{$mailID}_ncol1" style="width:24px;" align="center" class="narrowRow{if $mail.color>0} mailColor_{$mail.color}{/if}">
			<i id="mail_{$mailID}_nicon" class="fa fa-envelope{if !($mail.flags&1)}-o{/if}"></i>
		</td>
		<td draggable="false" id="mail_{$mailID}_ncol2" class="narrowRow" nowrap="nowrap">
			<a draggable="false" href="email.read.php?id={$mailID}&sid={$sid}" onclick="return(false)"{if $mail.flags&8} style="text-decoration:line-through;"{/if}>
				<div id="mail_{$mailID}_nspan1" class="date{if $mail.flags&1} unread{/if}"{if $mail.flags&8} style="text-decoration:line-through;"{/if}>{date timestamp=$mail.timestamp nice=true}</div>
				<div id="mail_{$mailID}_nspan2" class="sender{if $mail.flags&1} unread{/if}">{if $folderID!=-2}{if $mail.from_name}{text value=$mail.from_name}{else}{if $mail.from_mail}{email value=$mail.from_mail}{else}-{/if}{/if}{else}{if $mail.to_name}{text value=$mail.to_name}{else}{if $mail.to_mail}{email value=$mail.to_mail}{else}-{/if}{/if}{/if}</div>
				<div class="subject">
					<i id="maildone_{$mailID}" class="{if $mail.flags&4096}fa fa-check{/if}"></i>
					<i id="mail_{$mailID}_flagimg" class="{if $mail.flags&16}fa fa-flag-o{elseif $mail.priority==1}fa fa-exclamation{elseif $mail.priority==-1}fa fa-long-arrow-down{else}{/if}"></i>
					{if $mail.flags&4||$mail.flags&2}<i class="fa {if $mail.flags&4}fa-mail-forward{elseif $mail.flags&2}fa-mail-reply{/if}"></i>{/if}
					{if $mail.flags&64}<i class="fa fa-paperclip"></i>{/if}
					{if $mail.flags&128}<i class="fa fa-bug" aria-hidden="true"></i>{/if}
					{if $mail.flags&256}<i class="fa fa-ban" aria-hidden="true"></i>{/if}

					{text value=$mail.subject}
				</div>
			</a>
		</td>
	</tr>
	{/if}
	{/foreach}
	{if !$first}
	</tbody>
	{/if}
	{/if}

</table>
{if !$smarty.get.tableOnly}

</div>

<div id="contentFooter">
	<div class="left">
		<select class="smallInput" name="massAction" id="massAction">
			<option value="-">------ {lng p="selaction"} ------</option>

			<optgroup label="{lng p="actions"}">
			{if !$folderInfo.readonly}<option value="delete">{lng p="delete"}</option>{/if}
				<option value="forward">{lng p="forward"}</option>
				<option value="download">{lng p="download"}</option>
				{hook id="email.folder.tpl:mailSelect.actions"}
			</optgroup>

			{if !$folderInfo.readonly}<optgroup label="{lng p="flags"}">
				<option value="markread">{lng p="markread"}</option>
				<option value="markunread">{lng p="markunread"}</option>
				<option value="mark">{lng p="mark"}</option>
				<option value="unmark">{lng p="unmark"}</option>
				<option value="done">{lng p="markdone"}</option>
				<option value="undone">{lng p="unmarkdone"}</option>
				<option value="markspam">{lng p="markspam"}</option>
				<option value="marknonspam">{lng p="marknonspam"}</option>
				{hook id="email.folder.tpl:mailSelect.flags"}
			</optgroup>

			<optgroup label="{lng p="setmailcolor"}">
				<option value="color_0" class="mailColor_0">{lng p="color_0"}</option>
				<option value="color_1" class="mailColor_1">{lng p="color_1"}</option>
				<option value="color_2" class="mailColor_2">{lng p="color_2"}</option>
				<option value="color_3" class="mailColor_3">{lng p="color_3"}</option>
				<option value="color_4" class="mailColor_4">{lng p="color_4"}</option>
				<option value="color_5" class="mailColor_5">{lng p="color_5"}</option>
				<option value="color_6" class="mailColor_6">{lng p="color_6"}</option>
			</optgroup>

			<optgroup label="{lng p="move"} {lng p="moveto"}">
			{foreach from=$dropdownFolderList key=dFolderID item=dFolderTitle}
			<option value="moveto_{$dFolderID}" style="font-family:courier;">{$dFolderTitle}</option>
			{/foreach}
			</optgroup>{/if}

			{hook id="email.folder.tpl:mailSelect"}
		</select>
		<input class="smallInput" type="submit" value="{lng p="ok"}" />
	</div>

	<div class="right">
		{lng p="pages"}:
		&nbsp;
		<select class="smallInput" onchange="switchPage(this.value)">
			{section name=page start=0 loop=$pageCount step=1}
				<option value="{$smarty.section.page.index+1}"{if $pageNo==$smarty.section.page.index+1} selected="selected"{/if}>{$smarty.section.page.index+1}</option>
			{/section}
		</select>
	</div>
</div>

</form>

<script>
<!--
	currentSortColumn = '{$sortColumn}';
	currentSortOrder = '{$sortOrder}';
	currentPageNo = {$pageNo};
	currentPageCount = {$pageCount};
	narrowMode = true;
	initMailSel();
//-->
</script>
{/if}
