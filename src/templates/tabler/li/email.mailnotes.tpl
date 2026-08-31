{if $folderID==-3}
<div class="alert alert-info bm-mail-alert{if isset($preview)} preview{/if}" role="alert">
	<div class="d-flex">
		<div><i class="ti ti-file-pencil alert-icon icon" aria-hidden="true"></i></div>
		<div class="bm-mail-alert-body">
			{lng p="thisisadraft"}
			<a class="alert-link" href="email.compose.php?redirect={$mailID}{$sessionUrlSuffix}">{lng p="editsend"}</a>
		</div>
	</div>
</div>
{/if}
{if $flags&128}
<div class="alert alert-danger bm-mail-alert{if isset($preview)} preview{/if}" role="alert">
	<div class="d-flex">
		<div><i class="ti ti-virus alert-icon icon" aria-hidden="true"></i></div>
		<div class="bm-mail-alert-body">{lng p="infectedtext"}: {$infection}</div>
	</div>
</div>
{/if}
{if $flags&256}
<div class="alert alert-warning bm-mail-alert bm-mail-alert-spam{if isset($preview)} preview{/if}" id="spamQuestionDiv" style="display:;">
	<div class="d-flex">
		<div><i class="ti ti-ban alert-icon icon" aria-hidden="true"></i></div>
		<div class="bm-mail-alert-body">
			{lng p="spamtext"}
			{if !$trained}<a class="alert-link" href="javascript:setMailSpamStatus({$mailID}, false{if isset($preview)}, true{/if});">{lng p="isnotspam"}</a>{/if}
		</div>
	</div>
</div>
{elseif !$trained}
<div class="alert alert-warning bm-mail-alert bm-mail-alert-spam{if isset($preview)} preview{/if}" id="spamQuestionDiv" style="display:;">
	<div class="d-flex">
		<div><i class="ti ti-help-circle alert-icon icon" aria-hidden="true"></i></div>
		<div class="bm-mail-alert-body">
			<p class="bm-mail-alert-text mb-2 mb-md-0">{lng p="spamquestion"}</p>
			<div class="bm-mail-alert-actions">
				<button type="button" class="btn btn-sm btn-warning" onclick="setMailSpamStatus({$mailID}, true{if isset($preview)}, true{/if});">{lng p="yes"}</button>
				<button type="button" class="btn btn-sm btn-ghost-secondary" onclick="setMailSpamStatus({$mailID}, false{if isset($preview)}, true{/if});">{lng p="no"}</button>
			</div>
		</div>
	</div>
</div>
{/if}
{if $flags&512}
<div class="alert alert-info bm-mail-alert{if isset($preview)} preview{/if}" role="alert">
	<div class="d-flex">
		<div><i class="ti ti-certificate alert-icon icon" aria-hidden="true"></i></div>
		<div class="bm-mail-alert-body">{lng p="certmailinfo"}</div>
	</div>
</div>
{/if}
{if $htmlAvailable}
<div class="alert alert-info bm-mail-alert{if isset($preview)} preview{/if}" role="alert">
	<div class="d-flex">
		<div><i class="ti ti-code alert-icon icon" aria-hidden="true"></i></div>
		<div class="bm-mail-alert-body">
			{lng p="htmlavailable"}
			<a class="alert-link" href="email.read.php?id={$mailID}&htmlView=true{$sessionUrlSuffix}">{lng p="view"} &raquo;</a>
		</div>
	</div>
</div>
{/if}
{if $noExternal}
<div class="alert alert-info bm-mail-alert{if isset($preview)} preview{/if}" id="noExternalDiv" style="display:;" role="alert">
	<div class="d-flex">
		<div><i class="ti ti-photo-off alert-icon icon" aria-hidden="true"></i></div>
		<div class="bm-mail-alert-body">
			{lng p="noexternal"}
			<a class="alert-link" href="email.read.php?action=inlineHTML&mode={$textMode}&id={$mailID}&enableExternal=true{$sessionUrlSuffix}" target="{if isset($preview)}textArea{else}mailFrame{/if}" onclick="document.getElementById('noExternalDiv').style.display='none';">{lng p="showexternal"} &raquo;</a>
		</div>
	</div>
</div>
{/if}
{if $confirmationTo}
<div class="alert alert-yellow bm-mail-alert bm-mail-alert-confirm{if isset($preview)} preview{/if}" id="confirmationDiv" style="display:;" role="alert">
	<div class="d-flex">
		<div><i class="ti ti-mail-check alert-icon icon" aria-hidden="true"></i></div>
		<div class="bm-mail-alert-body">
			{lng p="senderconfirmto"}
			<strong>{text value=$confirmationTo}</strong>.
			<a class="alert-link" href="javascript:sendMailConfirmation({$mailID});">{lng p="sendconfirmation"} &raquo;</a>
		</div>
	</div>
</div>
{elseif $flags&16384}
<div class="alert alert-success bm-mail-alert bm-mail-alert-confirm{if isset($preview)} preview{/if}" id="confirmationDiv" style="display:;" role="alert">
	<div class="d-flex">
		<div><i class="ti ti-check alert-icon icon" aria-hidden="true"></i></div>
		<div class="bm-mail-alert-body">{lng p="confirmationsent"}</div>
	</div>
</div>
{/if}
