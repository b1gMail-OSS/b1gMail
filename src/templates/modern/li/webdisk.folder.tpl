<div class="withRightSidebar">

	<div id="contentHeader">
		<div class="left">
			<i class="fa fa-share-square-o" aria-hidden="true"></i>
			<a href="#" onclick="switchWebdiskFolder(0)">{lng p="webdisk"}</a> {foreach from=$currentPath item=folder} &raquo; <a href="#" onclick="switchWebdiskFolder({$folder.id});">{text value=$folder.title}</a> {/foreach}
		</div>
	</div>
	
	<div id="wdDnDNote"><i class="fa fa-upload" aria-hidden="true"></i>
						{lng p="dragfileshere"}</div>
	
	{hook id="webdisk.folder.tpl:head"}
	
	{if $isShared}
	<form action="email.compose.php?sid={$sid}" method="post" name="mailForm">
		<input type="hidden" name="subject" value="{text value=$shareMailSubject allowEmpty=true}" />
		<textarea name="text" style="display:none">{text value=$shareMail allowEmpty=true}</textarea>
	</form>
	{/if}
	
	<form enctype="multipart/form-data" action="webdisk.php?folder={$folderID}&sid={$sid}" method="post" name="f1" onsubmit="transferSelectedWebdiskItems();">
	<input type="hidden" name="" value="" id="wdAction" />
	<input type="hidden" name="massAction" value="" id="wdMassAction" />
	<input type="hidden" name="selectedWebdiskItems" id="selectedWebdiskItems" value="" />
	
	<div class="scrollContainer withBottomBar noSelect" id="wdDnDArea">
	{if $upload}
		<fieldset style="margin-top:1em;">
			<legend>{lng p="uploadfiles"}</legend>
			<table width="100%">
				{assign var="i" value=0}
				{section name=file loop=$upload}
				<tr>
					<td width="16"><i class="fa fa-file-o" aria-hidden="true"></i></td>
					<td><input type="file" name="file{$i}" style="width: 100%;" size="30" /></td>
				</tr>
				{assign var="i" value=$i+1}
				{/section}
				<tr>
					<td>&nbsp;</td>
					<td><i class="fa fa-refresh fa-spin fa-fw" style="display:none;" id="progressBar"></i>&nbsp;<input id="sbButton" class="primary" type="button" value="{lng p="ok"}" onclick="EBID('wdAction').name='action';EBID('wdAction').value='uploadFiles';EBID('progressBar').style.display='';this.disabled=true;document.forms.f1.submit();" /></td>
				</tr>
			</table>
		</fieldset>
		<br />
	{elseif $isShared}
		<div class="note" style="margin-bottom:1em;margin-top:1em;">
			<small>{lng p="sharednote"}</small><br />
			<i class="fa fa-share-square-o" aria-hidden="true"></i> <a target="_blank" href="{$shareURL}" style="color:blue;">{$shareURL}</a>
			<button onclick="document.forms.mailForm.submit();return(false);">
				<i class="fa fa-envelope-o" aria-hidden="true"></i>
				{lng p="sendmail2"}
			</button>
		</div>
	{/if}
	
	{if $viewMode=='icons'}
	
	<div id="wdContentDiv">
	{foreach from=$folderContent item=item}
	<div style="padding:0.5em;width:120px;height:80px;float:left;text-align:center;" draggable="false">
		<a id="wli_{$item.type}_{$item.id}"
			class="webdiskItem"
			title="{text value=$item.title}">
			<img src="webdisk.php?action=displayExtension&ext={$item.ext}&sid={$sid}" border="0" alt="" draggable="true"><br />
			<span id="wd_{$item.type}_{$item.id}" draggable="false">{text value=$item.title cut=15}</span><br />
			<small style="color:#666;line-height:1.5em;" draggable="false">{if $item.type==1}{lng p="folder"}{else}{size bytes=$item.size}{/if}</small>
		</a>
	</div>
	{/foreach}
	</div>
	
	{else}
	
	<table class="bigTable" id="wdContentTable">
		<tr>
			<th width="24">&nbsp;</th>
			<th>{lng p="filename"}</th>
			<th width="150">{lng p="created"}</th>
			<th width="80">{lng p="size"}</th>
			<th width="120">{lng p="type"}</th>
		</tr>
		{foreach from=$folderContent item=item}	
		{cycle values="listTableTR,listTableTR2" assign="class"}
		<tr class="{$class}" id="wli_{$item.type}_{$item.id}">
			<td style="text-align:center;">
				<img src="webdisk.php?action=displayExtension&ext={$item.ext}&sid={$sid}" width="16" height="16" border="0" alt="" />
			</td>
			<td nowrap="nowrap" 
				style="cursor:default;"
				id="wd_{$item.type}_{$item.id}">
				{text value=$item.title}
			</td>
			<td>&nbsp;{date timestamp=$item.created nice=true}</td>
			<td>&nbsp;{if $item.type==1}-{else}{size bytes=$item.size}{/if}</td>
			<td>&nbsp;{if $item.type==1}{lng p="folder"}{elseif $item.ext=='?'}{lng p="file"}{else}.{$item.ext}-{lng p="file"}{/if}</td>
		</tr>
		{/foreach}
	</table>
		
	{/if}
	</div>
	
	</form>
							
	<div id="contentFooter">
		<div class="left">
			<select class="smallInput" id="massAction">
				<option value="-">------ {lng p="selaction"} ------</option>
				<option value="download">{lng p="download"}</option>
				<option value="delete">{lng p="delete"}</option>
				{hook id="webdisk.folder.tpl:select"}
			</select>
			<input type="button" value=" {lng p="ok"} " class="smallInput"
			 	onclick="EBID('wdMassAction').value=EBID('massAction').value;transferSelectedWebdiskItems();document.forms.f1.submit();"/>
		</div>
	</div>
	
	{hook id="webdisk.folder.tpl:foot"}
	
	{if !$smarty.post.inline}
	<script src="./clientlib/dndupload.js?{fileDateSig file="../../clientlib/dndupload.js"}" type="text/javascript"></script>
	
	<script>
	<!--
	{if $hotkeys}
		registerLoadAction('registerWebdiskFolderHotkeyHandler()');
	{/if}
		initDnDUpload(EBID('mainContent'), 'webdisk.php?sid='+currentSID+'&folder={$folderID}&action=dndUpload', function() {literal}{{/literal} document.location.href='webdisk.php?sid='+currentSID+'&folder={$folderID}'; {literal}}{/literal});
		currentWebdiskFolderID = {$folderID};
		var treeID = webdiskGetTreeIDbyFolderID({$folderID});
		if(treeID > 0)
			webdisk_d.openTo(treeID);
		initWDSel();
	//-->
	</script>
	{/if}

</div>

<div id="folderLoading" style="display:none"><i class="fa fa-spinner fa-pulse fa-fw fa-3x"></i></div>

<div id="rightSidebar">
	{include file="li/webdisk.sidebar.tpl"}
</div>
