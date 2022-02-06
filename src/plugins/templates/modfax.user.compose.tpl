{if $_tplname=='modern'}
<div id="contentHeader">
	<div class="left">
		<i class="fa fa-paper-plane-o" aria-hidden="true"></i>
		{lng p="modfax_send"}
	</div>
</div>

<div class="scrollContainer"><div class="pad">
{else}
<h1><i class="fa fa-paper-plane-o" aria-hidden="true"></i> {lng p="modfax_send"}</h1>
{/if}

<iframe style="visibility:hidden;width:1px;height:1px;" frameborder="0" border="0" src="about:blank" name="faxFormPrepareSendIFrame"></iframe>

<div class="note" style="margin-bottom:1em;display:none;" id="faxError"></div>

<form name="f0" id="faxFormStep1" method="post" action="start.php?action=faxPlugin&do2=prepareSend&sid={$sid}" target="faxFormPrepareSendIFrame" style="display:;" onsubmit="EBID('step1SubmitButton').disabled=true;">
	<table class="listTable">
		<tbody id="faxFormHeadline">
			<tr>
				<th class="listTableHead" colspan="2"> {lng p="modfax_send"}</th>
			</tr>
		</tbody>
			
		<tbody id="faxFormLoad" style="display:;">
			<tr>
				<td align="center">
					<br />
					<i class="fa fa-spinner fa-pulse fa-fw fa-3x"></i>
					<br /><br />
				</td>
			</tr>
		</tbody>
		
		<tbody id="faxFormHead" style="display:none;">
			<tr>
				<td class="listTableLeft">{if $faxPrefs.allow_ownname}* {/if}<label for="fromname">{lng p="modfax_fromname"}:</label></td>
				<td class="listTableRight">
					{if $faxPrefs.allow_ownname}
					<input type="text" name="fromname" id="fromname" style="width:350px;" value="{if $faxPrefs.default_name}{text value=$faxPrefs.default_name}{else}{text value=$userRow.vorname} {text value=$userRow.nachname}{/if}" />
					{else}
					{text value=$faxPrefs.default_name}
					{/if}
				</td>
			</tr>
			<tr>
				<td class="listTableLeft">{if $faxPrefs.allow_ownno}* {/if}<label for="fromno">{lng p="modfax_fromno"}:</label></td>
				<td class="listTableRight">
					{if $faxPrefs.allow_ownno}
					<input type="text" name="fromno" id="fromno" style="width:350px;" value="{if $faxPrefs.default_no}{text value=$faxPrefs.default_no}{elseif $userRow.fax}{text value=$userRow.fax}{/if}" />
					{else}
					{text value=$faxPrefs.default_no}
					{/if}
				</td>
			</tr>
			<tr>
				<td class="listTableLeft">* <label for="to">{lng p="to"}:</label></td>
				<td class="listTableRight">
					<table cellspacing="0" cellpadding="0">
						<tr>
							<td width="364">
								<input type="text" name="to" id="to" style="width:350px;" value="" />
							</td>
							<td>
								<span id="addrDiv_to">
									<a href="javascript:openFaxAddressbook()">
										<i class="fa fa-address-book-o" aria-hidden="true"></i>
										{lng p="fromaddr"}
									</a>
								</span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</tbody>	
	</table>
	
	<table class="listTable" id="faxFormFootTable">
		<tbody id="faxFormFoot" style="display:none;">
			<tr>
				<td class="listTableLeft">&nbsp;</td>
				<td class="listTableRight">
					<input type="submit" class="primary" id="step1SubmitButton" value=" {lng p="modfax_preview"} &raquo; " onclick="faxFormSetError(false)" />
				</td>
			</tr>
		</tbody>
	</table>
</form>

<form name="f1" id="faxFormStep2" method="post" action="start.php?action=faxPlugin&do2=send&sid={$sid}" style="display:none;" onsubmit="EBID('step2BackButton').disabled=true;EBID('step2SubmitButton').disabled=true;">
	<input type="hidden" name="fileID" id="previewFileID" value="0" />
	<input type="hidden" name="fromname" id="previewFromName" value="" />
	<input type="hidden" name="fromno" id="previewFromNo" value="" />
	<input type="hidden" name="to" id="previewTo" value="" />

	<table class="listTable">
		<tr>
			<th class="listTableHead" colspan="2"> {lng p="modfax_send"}</th>
		</tr>
		
		<tr>
			<td class="listTableLeft">&nbsp;</td>
			<td class="listTableRight" style="padding-bottom:1em;">{lng p="modfax_previewtext"}</td>
		</tr>
		
		<tr>
			<td class="listTableLeft">{lng p="preview"}:</td>
			<td class="listTableRight">
				<i class="fa fa-file-pdf-o" aria-hidden="true"></i>
				<a href="#" target="_blank" id="previewDownloadLink">{lng p="download"}</a>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft">{lng p="modfax_pages"}:</td>
			<td class="listTableRight">
				<span id="previewPages">0</span>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft">{lng p="price"}:</td>
			<td class="listTableRight">
				<span id="previewPrice">0</span>
				&nbsp; <small><i>({lng p="accbalance"}: {$accBalance})</i></small>
			</td>
		</tr>
		
		<tr>
			<td class="listTableLeft">&nbsp;</td>
			<td class="listTableRight" style="padding-bottom:1em;">{lng p="modfax_previewtext2"}</td>
		</tr>
		
		{if $codeID}
		<tr>
			<td class="listTableLeft">&nbsp;</td>
			<td class="listTableRight">
				<table cellpadding="0">
					<tr>
						<td><img src="index.php?action=codegen&id={$codeID}" border="0" alt="" style="cursor:pointer;" onclick="this.src='index.php?action=codegen&id={$codeID}&rand='+parseInt(Math.random()*10000);" /></td>
						<td width="120"><small>{lng p="notreadable"}</small></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="safecode">{lng p="safecode"}:</label></td>
			<td class="listTableRight">
				<input type="hidden" name="codeID" value="{$codeID}" />
				<input type="text" maxlength="6" size="20" style="text-align:center;width:212px;" name="safecode" id="safecode" />
			</td>
		</tr>
		{elseif $captchaInfo}
		<tr>
			<td class="listTableLeft">&nbsp;</td>
			<td class="listTableRight">
				<table cellpadding="0">
					<tr>
						<td>
							<label for="safecode">{lng p="safecode"}:</label>
						</td>
						<td style="padding-left:2em;" id="captchaContainer">{$captchaHTML}</td>
						{if !$captchaInfo.hasOwnInput}
						<td style="padding-left:2em;">
							<input type="text" size="20" style="text-align:center;width:212px;" name="safecode" id="safecode" />
						</td>
						{/if}
						{if $captchaInfo.showNotReadable}<td style="padding-left:2em;"><small>{lng p="notreadable"}</small></td>{/if}
					</tr>
				</table>
			</td>
		</tr>
		{/if}
		
		<tr>
			<td class="listTableLeft">&nbsp;</td>
			<td class="listTableRight">
				<input type="button" id="step2BackButton" value=" &laquo; {lng p="back"} " onclick="goFaxFormStep1();" />
				<input type="button" class="primary" id="step2SubmitButton" value=" {lng p="modfax_send"} " onclick="{if $codeID||$captchaInfo}checkSafeCode({$codeID});{else}document.forms.f1.submit();{/if}" />
			</td>
		</tr>
	</table>
</form>

<script>
<!--
{literal}
	var faxFormBlockCode = '',
		highestBlockID = 0,
		faxFormStep = 1;
		
	function faxFormAddBlock(before, after)
	{
		var beforeElem 	= (before >= 0) ? EBID('block_'+before) : EBID('faxFormFootTable'),
			blockID 	= ++highestBlockID,
			blockCode 	= faxFormBlockCode.replace(/\#/g, blockID),
			blockLayer 	= document.createElement('div');
		
		blockLayer.setAttribute('id', 'block_' + blockID);
		blockLayer.innerHTML = blockCode;
		
		if(after)
			beforeElem.parentNode.insertBefore(blockLayer, beforeElem.nextSibling);
		else
			beforeElem.parentNode.insertBefore(blockLayer, beforeElem);
		
		EBID('block_' + blockID + '_0').style.display = '';
		
		if(blockID == 1)
			EBID('block_' + blockID + '_del').style.display = 'none';
		
		return(blockID);
	}
	
	function faxFormDeleteBlock(blockID)
	{
		var blockLayer = EBID('block_' + blockID);
		blockLayer.parentNode.removeChild(blockLayer);
	}
	
	function faxFormBlockTypeChanged(blockID, comboBox)
	{
		for(var i=0; i<=3; i++)
			if(EBID('block_' + blockID + '_' + i))
				EBID('block_' + blockID + '_' + i).style.display = comboBox.value == i ? '' : 'none';
	}
	
	function faxFormFontNameChanged(blockID, comboBox)
	{
		EBID('block_' + blockID + '_text').style.fontFamily = comboBox.value;
		EBID('block_' + blockID + '_text').focus();
	}
	
	function faxFormFontSizeChanged(blockID, comboBox)
	{
		EBID('block_' + blockID + '_text').style.fontSize = comboBox.value + 'px';
		EBID('block_' + blockID + '_text').focus();
	}
	
	function faxFormStyleChanged(blockID)
	{
		EBID('block_' + blockID + '_text').style.fontWeight =
			EBID('block_' + blockID + '_bold').checked ? 'bold' : 'normal';
		EBID('block_' + blockID + '_text').style.textDecoration =
			EBID('block_' + blockID + '_underlined').checked ? 'underline' : 'none';
		EBID('block_' + blockID + '_text').style.fontStyle =
			EBID('block_' + blockID + '_italic').checked ? 'italic' : 'normal';
		EBID('block_' + blockID + '_text').focus();
	}
	
	function faxFormAlignChanged(blockID, comboBox)
	{
		EBID('block_' + blockID + '_text').style.textAlign = comboBox.value;
		EBID('block_' + blockID + '_text').focus();
	}
	
	function faxFormChoseFile(blockID)
	{
		openOverlay('start.php?action=faxPlugin&do2=addPDFFile&blockID=' + blockID + '&sid=' + currentSID,
			lang['modfax_browsepdf'],
			520,
			140,
			true);
	}
	
	function openFaxAddressbook()
	{	
		openOverlay('start.php?action=faxPlugin&do=addressBook&sid=' + currentSID,
			lang['addressbook'],
			450,
			380,
			true);
	}
	
	function goFaxFormStep1()
	{
		faxFormStep 						= 1;
		EBID('faxFormStep1').style.display 	= '';
		EBID('faxFormStep2').style.display 	= 'none';
		EBID('step1SubmitButton').disabled 	= false;
	}
	
	function faxFormStep2(fileID, pageCount, price)
	{
		EBID('previewDownloadLink').href 	= 'start.php?action=faxPlugin&do2=downloadPreview&fileID=' + fileID + '&sid=' + currentSID;
		EBID('previewPages').innerHTML 		= pageCount;
		EBID('previewPrice').innerHTML 		= price;
		EBID('previewFileID').value 		= fileID;
		
		EBID('previewTo').value				= EBID('to').value;
		if(EBID('fromname'))
			EBID('previewFromName').value	= EBID('fromname').value;
		if(EBID('fromno'))
			EBID('previewFromNo').value		= EBID('fromno').value;
		
		faxFormStep = 2;
		EBID('faxFormStep1').style.display 	= 'none';
		EBID('faxFormStep2').style.display 	= '';
	}
	
	function faxFormSetError(text)
	{
		if(!text)
		{
			EBID('faxError').style.display 		= 'none';
		}
		else
		{
			EBID('faxError').innerHTML 			= text;
			EBID('faxError').style.display 		= '';
			EBID('step1SubmitButton').disabled 	= false;
		}
	}
	
	function _initFaxForm(e)
	{
		if(e.readyState == 4)
		{
			faxFormBlockCode 					= e.responseText;
			
			var blockID = -1;
			{/literal}
			{foreach from=$defaultTpl item=blockType key=blockID}
			{if $blockType>=0}
			if(blockID == -1)
				blockID = faxFormAddBlock(blockID);
			else
				blockID = faxFormAddBlock(blockID, true);
			EBID('block_' + blockID + '_type').value = {$blockType};
			faxFormBlockTypeChanged(blockID, EBID('block_' + blockID + '_type'));
			{/if}
			{/foreach}
			{literal}
			
			EBID('faxFormHead').style.display 	= '';
			EBID('faxFormFoot').style.display 	= '';
			EBID('faxFormLoad').style.display 	= 'none';
		}
	}
	
	function initFaxForm()
	{
		MakeXMLRequest('start.php?action=faxPlugin&do2=getFormBlockCode&sid=' + currentSID, _initFaxForm);
	}

	registerLoadAction(initFaxForm);
{/literal}
//-->
</script>

{if $_tplname=='modern'}
</div></div>
{/if}
