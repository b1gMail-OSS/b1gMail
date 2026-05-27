<div class="bm-organizer-page bm-organizer-addressbook">
	<div id="contentHeader" class="contentHeader bm-organizer-header">
		<div class="left">
			<i class="ti ti-address-book icon icon-sm" aria-hidden="true"></i>
			{lng p="addressbook"}
		</div>
		<div class="right bm-organizer-header-actions">
			<div class="d-flex flex-wrap align-items-center gap-2">
				<label class="small text-secondary mb-0" for="abLetterFilter">{lng p="view"}:</label>
				<select class="form-select form-select-sm" id="abLetterFilter" style="width:auto;min-width:4rem;" onchange="document.location.href='organizer.addressbook.php?sid='+currentSID+'&group={$currentGroup}&letter='+this.value;">
					<option value="">{lng p="all"}</option>
					{foreach from=$alpha key=key item=letter}
					<option value="{$key}"{if $smarty.request.letter==$key} selected="selected"{/if}>{$letter}</option>
					{/foreach}
				</select>

				<label class="small text-secondary mb-0" for="abGroupFilter">{lng p="group"}:</label>
				<select class="form-select form-select-sm" id="abGroupFilter" style="width:auto;min-width:8rem;" onchange="updateCurrentGroup(this.value,'{$sid}')">
					<option value="-1"{if $currentGroup==-1} selected="selected"{/if}>------------</option>
					<optgroup label="{lng p="groups"}">
					{foreach from=$groupList key=groupID item=group}
						<option value="{$groupID}"{if $currentGroup==$groupID} selected="selected"{/if}>{text value=$group.title cut=25}</option>
					{/foreach}
					</optgroup>
				</select>

				<button type="button" class="btn btn-sm btn-outline-primary" onclick="abGroups();">
					<i class="ti ti-users icon icon-sm me-1" aria-hidden="true"></i>
					{lng p="editgroups"}
				</button>
				<button type="button" class="btn btn-sm btn-outline-primary" onclick="abImport();">
					<i class="ti ti-upload icon icon-sm me-1" aria-hidden="true"></i>
					{lng p="import"}
				</button>
				<button type="button" class="btn btn-sm btn-outline-primary" onclick="abExport();">
					<i class="ti ti-download icon icon-sm me-1" aria-hidden="true"></i>
					{lng p="export"}
				</button>
			</div>
		</div>
	</div>

	<div class="scrollContainer bm-organizer-split">
		<div class="addressContents bm-organizer-address-list" id="hSep1">
			<div class="addressContainer withBottomBar bm-organizer-address-table-wrap">
				<table class="table table-vcenter table-hover card-table bm-organizer-table" id="addressTable">
					<thead>
					<tr>
						<th class="bm-organizer-task-gutter">&nbsp;</th>
						<th>{lng p="name"}</th>
					</tr>
					</thead>

					{if $addressList}
					{foreach from=$addressList key=letter item=addresses}
					{assign var=groupID value="addr$letter"}

					<tbody>
					<tr class="bm-organizer-section-row">
						<td class="bm-organizer-task-gutter">
							<button type="button" class="bm-organizer-section-toggle bm-organizer-section-toggle-icon" onclick="toggleGroup('{$letter}','addr{$letter}');" aria-label="{$letter}">
								<i class="ti ti-chevron-{if $smarty.cookies.toggleGroup.$groupID=='closed'}right{else}down{/if} icon icon-sm" id="groupImage_{$letter}" aria-hidden="true"></i>
							</button>
						</td>
						<td>
							<button type="button" class="bm-organizer-section-toggle" onclick="toggleGroup('{$letter}','addr{$letter}');">
								{$letter}
							</button>
						</td>
					</tr>
					</tbody>

					<tbody id="group_{$letter}" style="display:{if $smarty.cookies.toggleGroup.$groupID=='closed'}none{/if};">

					{foreach from=$addresses key=addressID item=address}
					<tr id="addr_{$addressID}">
						<td class="bm-organizer-task-gutter">
							{if $templatePrefs.showCheckboxes}
							<label class="form-check mb-0">
								<input type="checkbox" class="form-check-input m-0" id="selecTable_{$addressID}" />
							</label>
							{/if}
						</td>
						<td>
							{if !$address.vorname&&!$address.nachname&&$address.firma}
							<strong>{text value=$address.firma}</strong>
							{else}
							{text value=$address.vorname}
							<strong>{text value=$address.nachname}</strong>
							{/if}
						</td>
					</tr>
					{/foreach}

					</tbody>

					{/foreach}
					{/if}
				</table>
			</div>

			<form name="f1" method="post" action="organizer.addressbook.php?action=action&sid={$sid}" onsubmit="transferSelectedAddresses();">
			<input name="addrIDs" id="addrIDs" value="" type="hidden" />

			<div id="contentFooter" class="contentFooter bm-organizer-footer">
				<div class="left bm-organizer-footer-actions">
					<div class="input-group input-group-sm bm-organizer-action-group">
						<select class="form-select" name="do" aria-label="{lng p="selaction"}">
							<option value="-">{lng p="selaction"}</option>

							<optgroup label="{lng p="actions"}">
								<option value="export">{lng p="export_csv"}</option>
								<option value="sendmail">{lng p="sendmail"}</option>
								<option value="delete">{lng p="delete"}</option>
							</optgroup>

							{if $groupList}<optgroup label="{lng p="associatewith"}">
							{foreach from=$groupList key=groupID item=group}
								<option value="addtogroup_{$groupID}">{text value=$group.title cut=32}</option>
							{/foreach}
							</optgroup>{/if}
						</select>
						<button class="btn btn-primary" type="submit">{lng p="ok"}</button>
					</div>
				</div>

				<div class="right bm-organizer-footer-tools">
					<button type="button" class="btn btn-sm btn-primary" onclick="document.location.href='organizer.addressbook.php?action=addContact&sid={$sid}';">
						<i class="ti ti-plus icon icon-sm me-1" aria-hidden="true"></i>
						{lng p="add"}
					</button>
				</div>
			</div>
			</form>
		</div>

		<div id="hSepSep"></div>

		<div class="addressPreview bm-organizer-address-preview" id="hSep2">
			<div id="previewArea" style="display:none;"></div>
			<div id="multiSelPreview" class="bm-organizer-address-preview-empty">
				<div id="multiSelPreview_vCenter">
					<div id="multiSelPreview_inner">
						<div id="multiSelPreview_count">{lng p="nocontactselected"}</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script>
	<!--
		registerLoadAction('initHSep(\'addr\')');
		initAddrSel();
	//-->
	</script>
</div>
