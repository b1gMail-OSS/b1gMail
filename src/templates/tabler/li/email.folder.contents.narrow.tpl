{if empty($smarty.get.tableOnly)}<form name="f1" action="email.php?do=action&{$folderString}&sid={$sid}" onsubmit="transferSelectedMailIDs()" method="post">
<input type="hidden" name="selectedMailIDs" id="selectedMailIDs" value="" />

<div id="contentHeader">
	<div class="left"{if $templatePrefs.showCheckboxes} style="padding-left:2px;"{/if}>
		{if $templatePrefs.showCheckboxes}<label class="form-check mb-0"><input type="checkbox" class="form-check-input m-0" id="checkAllMails" onclick="if(this.checked) _mailSel.selectAll(); else _mailSel.unselectAll()||showMultiSelPreview(0);" /></label>{/if}
		<i class="ti {if $folderInfo.type == 'inbox'}ti-inbox{elseif $folderInfo.type == 'outbox'}ti-send{elseif $folderInfo.type == 'drafts'}ti-file-pencil{elseif $folderInfo.type == 'spam'}ti-ban{elseif $folderInfo.type == 'trash'}ti-trash{elseif $folderInfo.type == 'intellifolder'}ti-folder{else}ti-folder{/if} icon" aria-hidden="true"></i> {$folderInfo.title}
	</div>

	<div class="right bm-mail-header-actions">
		{if isset($folderInfo.type)&&$folderInfo.type!='intellifolder'&&empty($folderInfo.readonly)}
		<button type="button" class="btn btn-icon btn-ghost-secondary" onclick="showFolderMenu(event);" title="{lng p="folderactions"}" aria-label="{lng p="folderactions"}">
			<i class="ti ti-settings icon" aria-hidden="true"></i>
		</button>
		{/if}

		<button type="button" class="btn btn-icon btn-ghost-secondary" onclick="switchPage({$pageNo})" title="{lng p="refresh"}" aria-label="{lng p="refresh"}">
			<i class="ti ti-refresh icon" aria-hidden="true"></i>
		</button>

		{if empty($folderInfo.readonly)}<button type="button" class="btn btn-icon btn-ghost-secondary" onclick="folderViewOptions({$folderID});" title="{lng p="viewoptions"}" aria-label="{lng p="viewoptions"}">
			<i class="ti ti-layout-sidebar-right icon" aria-hidden="true"></i>
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
	{if isset($mail.groupID)}{assign var=mailGroupID value=$mail.groupID}
	{else}{assign var=mailGroupID value=0}{/if}
	{cycle values="listTableTR,listTableTR2" assign="class"}

	{if $mailID<0}
	{cycle values="listTableTR,listTableTR2" assign="class"}
	{if !$first}
	</tbody>
	{/if}
	<tr>
		<td colspan="{if $templatePrefs.showCheckboxes}3{else}2{/if}" class="folderGroup">
			<a style="display:block;cursor:pointer;" onclick="toggleGroup({$mailID},'{if isset($mail.groupID)}{$mail.groupID}{/if}');">&nbsp;<img id="groupImage_{$mailID}" src="{$tpldir}images/{if !empty($smarty.cookies.toggleGroup.$mailGroupID) && $smarty.cookies.toggleGroup.$mailGroupID=='closed'}expand{else}contract{/if}.png" width="11" height="11" border="0" align="absmiddle" alt="" />
			&nbsp;{$mail.text} {if isset($mail.date) && $mail.date!=-1}({date timestamp=$mail.date dayonly=true}){/if}</a>
		</td>
	</tr>
	<tbody id="group_{$mailID}" style="display:{if !empty($smarty.cookies.toggleGroup.$mailGroupID) && $smarty.cookies.toggleGroup.$mailGroupID=='closed'}none{/if};">
	{assign var=first value=false}
	{else}
	<tr id="mail_{$mailID}_ntr" class="{$class}{if $mail.color>0} mailColor_{$mail.color}{/if}">
		{if $templatePrefs.showCheckboxes}
		<td class="narrowRow" style="text-align:center;width:24px;">
			<label class="form-check mb-0"><input type="checkbox" class="form-check-input m-0" id="selecTable_{$mailID}" /></label>
		</td>
		{/if}
		<td id="mail_{$mailID}_ncol1" class="narrowRow bm-mail-status-icon">
			<i id="mail_{$mailID}_nicon" class="ti {if $mail.flags&1}ti-mail{else}ti-mail-opened{/if}" aria-hidden="true"></i>
		</td>
		<td draggable="false" id="mail_{$mailID}_ncol2" class="narrowRow bm-mail-card-cell">
			<a draggable="false" class="bm-mail-card-link" href="email.read.php?id={$mailID}&sid={$sid}" onclick="return(false)"{if $mail.flags&8} style="text-decoration:line-through;"{/if}>
				<div class="bm-mail-card-top">
					<div id="mail_{$mailID}_nspan2" class="sender{if $mail.flags&1} unread{/if}">{if $folderID!=-2}{if $mail.from_name}{text value=$mail.from_name}{else}{if $mail.from_mail}{email value=$mail.from_mail}{else}-{/if}{/if}{else}{if $mail.to_name}{text value=$mail.to_name}{else}{if $mail.to_mail}{email value=$mail.to_mail}{else}-{/if}{/if}{/if}</div>
					<div id="mail_{$mailID}_nspan1" class="date{if $mail.flags&1} unread{/if}"{if $mail.flags&8} style="text-decoration:line-through;"{/if}>{date timestamp=$mail.timestamp nice=true}</div>
				</div>
				<div class="subject">
					{if $mail.flags&4096}<i id="maildone_{$mailID}" class="fa fa-check" aria-hidden="true"></i>{/if}
					{if $mail.flags&16}<i id="mail_{$mailID}_flagimg" class="ti ti-flag-filled bm-mail-flag-icon" aria-hidden="true"></i>{elseif $mail.priority==1}<i id="mail_{$mailID}_flagimg" class="ti ti-alert-triangle bm-mail-flag-icon" aria-hidden="true"></i>{elseif $mail.priority==-1}<i id="mail_{$mailID}_flagimg" class="ti ti-arrow-down bm-mail-flag-icon" aria-hidden="true"></i>{else}<i id="mail_{$mailID}_flagimg" class="bm-mail-flag-placeholder" aria-hidden="true"></i>{/if}
					{if $mail.flags&4||$mail.flags&2}<i class="fa {if $mail.flags&4}fa-mail-forward{elseif $mail.flags&2}fa-mail-reply{/if}" aria-hidden="true"></i>{/if}
					{if $mail.flags&64}<i class="fa fa-paperclip" aria-hidden="true"></i>{/if}
					{if $mail.flags&128}<i class="fa fa-bug" aria-hidden="true"></i>{/if}
					{if $mail.flags&256}<i class="fa fa-ban" aria-hidden="true"></i>{/if}
					{text value=$mail.subject}
				</div>
				{if ($templatePrefs.mailListPreviewLines|default:2) > 0 && $mail.preview}<div class="bm-mail-preview">{text value=$mail.preview}</div>{/if}
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
{if empty($smarty.get.tableOnly)}

</div>

<div id="contentFooter" class="contentFooter bm-mail-list-footer">
	<div class="bm-mail-list-footer-row">
		<div class="bm-mail-footer-actions">
			<div class="input-group input-group-sm bm-mail-action-group">
			<select class="form-select" name="massAction" id="massAction" aria-label="{lng p="selaction"}">
				<option value="-">{lng p="selaction"}</option>

			<optgroup label="{lng p="actions"}">
			{if empty($folderInfo.readonly)}<option value="delete">{lng p="delete"}</option>{/if}
				<option value="forward">{lng p="forward"}</option>
				<option value="download">{lng p="download"}</option>
				{hook id="email.folder.tpl:mailSelect.actions"}
			</optgroup>

			{if empty($folderInfo.readonly)}<optgroup label="{lng p="flags"}">
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
			<button type="submit" class="btn btn-primary btn-sm bm-mail-footer-ok" aria-label="{lng p="ok"}">
				<i class="ti ti-check bm-mail-footer-ok-icon" aria-hidden="true"></i>
				<span class="bm-mail-footer-ok-text">{lng p="ok"}</span>
			</button>
			</div>
		</div>

		<div class="bm-mail-footer-pagination">
			<div class="input-group input-group-sm bm-mail-page-group">
				<span class="input-group-text bm-mail-footer-page-label">{lng p="pages"}</span>
				<select class="form-select" onchange="switchPage(this.value)" aria-label="{lng p="pages"}">
				{section name=page start=0 loop=$pageCount step=1}
					<option value="{$smarty.section.page.index+1}"{if $pageNo==$smarty.section.page.index+1} selected="selected"{/if}>{$smarty.section.page.index+1}</option>
				{/section}
				</select>
			</div>
		</div>
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
