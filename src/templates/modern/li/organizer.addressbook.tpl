<div id="contentHeader">
	<div class="left">
		<i class="fa fa-address-book-o" aria-hidden="true"></i>
		{lng p="addressbook"}
	</div>
	<div class="right">
		<small>{lng p="view"}:</small>
		<select class="smallInput" onchange="document.location.href='organizer.addressbook.php?sid='+currentSID+'&group={$currentGroup}&letter='+this.value;">
			<option value="">{lng p="all"}</option>
			{foreach from=$alpha key=key item=letter}
			<option value="{$key}"{if $smarty.request.letter==$key} selected="selected"{/if}>{$letter}</option>
			{/foreach}
		</select>
		
		&nbsp;
		
		<small>{lng p="group"}:</small>
		<select class="smallInput" onchange="updateCurrentGroup(this.value,'{$sid}')">
			<option value="-1"{if $currentGroup==-1} selected="selected"{/if}>------------</option>
			<optgroup label="{lng p="groups"}">
			{foreach from=$groupList key=groupID item=group}
				<option value="{$groupID}"{if $currentGroup==$groupID} selected="selected"{/if}>{text value=$group.title cut=25}</option>
			{/foreach}
			</optgroup>
		</select>
		
		&nbsp;
		
		<button type="button" onclick="abGroups();">
			<i class="fa fa-users" aria-hidden="true"></i>
			{lng p="editgroups"}
		</button>
		
		&nbsp;
		
		<button type="button" onclick="abImport();">
			<i class="fa fa-upload" aria-hidden="true"></i>
			{lng p="import"}
		</button>
		
		<button type="button" onclick="abExport();">
			<i class="fa fa-download" aria-hidden="true"></i>
			{lng p="export"}
		</button>
	</div>
</div>

<form name="f1" method="post" action="organizer.addressbook.php?action=action&sid={$sid}" onsubmit="transferSelectedAddresses();">
<input name="addrIDs" id="addrIDs" value="" />

<div class="scrollContainer" style="overflow:hidden;">

		<div class="addressContents" id="hSep1">
			<div class="addressContainer withBottomBar">
				<table class="bigTable" id="addressTable">
					<tr style="height:auto;">
						{if $templatePrefs.showCheckboxes}
						<th style="width:24px;">&nbsp;</th>
						{/if}
						<th>{lng p="name"}</th>
					</tr>

					{if $addressList}
					{foreach from=$addressList key=letter item=addresses}
					{assign var=groupID value="addr$letter"}
			
					<tr style="height:auto;">
						<td colspan="{if $templatePrefs.showCheckboxes}2{else}1{/if}" class="folderGroup">
							<a style="display:block;cursor:pointer;" onclick="toggleGroup('{$letter}','addr{$letter}');">&nbsp;<img id="groupImage_{$letter}" src="{$tpldir}images/{if $smarty.cookies.toggleGroup.$groupID=='closed'}expand{else}contract{/if}.png" width="11" height="11" border="0" align="absmiddle" alt="" />
							&nbsp;{$letter}</a>
						</td>
					</tr>

					<tbody id="group_{$letter}" style="display:{if $smarty.cookies.toggleGroup.$groupID=='closed'}none{/if};">
			
					{foreach from=$addresses key=addressID item=address}
					{cycle values="listTableTD,listTableTD2" assign="class"}
					<tr id="addr_{$addressID}">
						{if $templatePrefs.showCheckboxes}
						<td style="text-align:center;width:24px;">
							<input type="checkbox" id="selecTable_{$mailID}" />
						</td>
						{/if}
						<td class="{$class}">
							{if !$address.vorname&&!$address.nachname&&$address.firma}
							<strong>{text value=$address.firma}
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
			
			<div class="contentFooter">
				<div class="left">
					<select class="smallInput" name="do">
						<option value="-">------ {lng p="selaction"} ------</option>

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
					<input class="smallInput" type="submit" value="{lng p="ok"}" />
				</div>

				<div class="right">
					<button type="button" class="primary" onclick="document.location.href='organizer.addressbook.php?action=addContact&sid={$sid}';">
						<i class="fa fa-plus-circle"></i>
						{lng p="add"}
					</button>
				</div>
			</div>
		</div>
		
		<div id="hSepSep"></div>
		
		<div class="addressPreview" id="hSep2">
			<div id="previewArea" style="display:none;"></div>
			<div id="multiSelPreview">
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

</form>
