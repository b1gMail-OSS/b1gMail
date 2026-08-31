{capture assign="dialogTitleText"}{lng p="viewoptions"}{/capture}
{include file="li/dialog.head.tpl" dialogTitle=$dialogTitleText dialogBodyClass="bm-dialog-viewoptions" dialogOnLoad="documentLoader();bmViewOptionsDialogInit();"}

<div class="bm-dialog-page">
	<form action="email.php?folder={$folderID}&do=setViewOptions&overlay=true{$sessionUrlSuffix}" method="post" class="bm-dialog-form">
		{csrffield}
		<div class="row g-3">
			<div class="col-12 col-sm-6">
				<label class="form-label" for="group_mode">{lng p="group_mode"}</label>
				<select name="group_mode" id="group_mode" class="form-select">
					<option value="-"{if $groupMode=='-'} selected="selected"{/if}>------------</option>

					<optgroup label="{lng p="props"}">
						<option value="fetched"{if $groupMode=='fetched'} selected="selected"{/if}>{lng p="date"}</option>
						<option value="von"{if $groupMode=='von'} selected="selected"{/if}>{lng p="from"}</option>
					</optgroup>

					<optgroup label="{lng p="flags"}">
						<option value="gelesen"{if $groupMode=='gelesen'} selected="selected"{/if}>{lng p="read"}</option>
						<option value="beantwortet"{if $groupMode=='beantwortet'} selected="selected"{/if}>{lng p="answered"}</option>
						<option value="weitergeleitet"{if $groupMode=='weitergeleitet'} selected="selected"{/if}>{lng p="forwarded"}</option>
						<option value="flagged"{if $groupMode=='flagged'} selected="selected"{/if}>{lng p="flagged"}</option>
						<option value="done"{if $groupMode=='done'} selected="selected"{/if}>{lng p="done"}</option>
						<option value="attach"{if $groupMode=='attach'} selected="selected"{/if}>{lng p="attachment"}</option>
						<option value="color"{if $groupMode=='color'} selected="selected"{/if}>{lng p="color"}</option>
					</optgroup>
				</select>
			</div>
			<div class="col-12 col-sm-6">
				<label class="form-label" for="perpage">{lng p="mails_per_page"}</label>
				<select name="perpage" id="perpage" class="form-select">
					{section start=5 step=5 loop=55 name=num}
					<option value="{$smarty.section.num.index}"{if $perPage==$smarty.section.num.index} selected="selected"{/if}>{$smarty.section.num.index}</option>
					{/section}
					{section start=75 step=25 loop=175 name=num}
					<option value="{$smarty.section.num.index}"{if $perPage==$smarty.section.num.index} selected="selected"{/if}>{$smarty.section.num.index}</option>
					{/section}
				</select>
			</div>
		</div>

		<div class="bm-dialog-actions">
			<button type="button" class="btn btn-ghost-secondary" onclick="parent.hideOverlay()">{lng p="cancel"}</button>
			<button type="submit" class="btn btn-primary">
				<i class="ti ti-check icon" aria-hidden="true"></i>
				{lng p="ok"}
			</button>
		</div>
	</form>
</div>

<script>
<!--
function bmDialogFitFrame()
{
	try {
		var frame = window.frameElement;
		if(!frame || !window.parent)
			return;

		var parentWin = window.parent,
			parentH = parentWin.innerHeight || parentWin.document.documentElement.clientHeight || 600,
			isMobile = parentWin.matchMedia && parentWin.matchMedia('(max-width: 575.98px)').matches,
			docH = Math.ceil(document.documentElement.scrollHeight),
			cap, nextH;

		if(isMobile)
		{
			cap = Math.floor(parentH * 0.92) - 8;
			nextH = Math.min(Math.max(docH, 120), cap);
		}
		else
		{
			cap = Math.floor(parentH * 0.85) - 56;
			nextH = Math.min(Math.max(docH, 160), cap);
		}

		frame.style.setProperty('height', nextH + 'px', 'important');
		frame.style.setProperty('min-height', Math.min(nextH, isMobile ? 120 : 160) + 'px', 'important');
	} catch(e) {}
}

function bmViewOptionsDialogInit()
{
	bmDialogFitFrame();
	window.addEventListener('resize', bmDialogFitFrame);
}
//-->
</script>

{include file="li/dialog.foot.tpl"}
