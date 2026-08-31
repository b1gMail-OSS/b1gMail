<fieldset>
	<legend>{lng p="modfax_signatures"}</legend>
	
	<form action="{$pageURL}{$sessionUrlSuffixHtml}" method="post" name="f1" onsubmit="spin(this)">
		{csrffield}
	<div class="card">
		<div class="table-responsive">
			<table class="table table-vcenter table-striped card-table">
				<thead>
				<tr>
					<th style="width: 25px;"><a href="javascript:invertSelection(document.forms.f1,'sigs[]');" class="text-secondary"><i class="ti ti-selector"></i></a></th>
					<th>{lng p="modfax_signature"}</th>
					<th style="width: 75px;">{lng p="weight"}</th>
					<th style="width: 70px;">{lng p="modfax_used"}</th>
					<th style="width: 110px;">&nbsp;</th>
				</tr>
				</thead>
				<tbody>
				{foreach from=$signatures item=sig}
				<tr>
					<td class="text-center"><input type="checkbox" class="form-check-input m-0" name="sigs[]" value="{$sig.signatureid}" /></td>
					<td>{$sig.displayText}</td>
					<td>{$sig.weight}%</td>
					<td>{$sig.counter}</td>
					<td class="text-nowrap">
						<div class="btn-group btn-group-sm">
							{if $sig.paused}
							<button type="submit" name="activate" value="{$sig.signatureid}" class="btn btn-sm" title="{lng p="continue"}"><i class="ti ti-player-play"></i></button>
							{else}
							<button type="submit" name="deactivate" value="{$sig.signatureid}" class="btn btn-sm" title="{lng p="pause"}"><i class="ti ti-player-pause"></i></button>
							{/if}
							<a href="{$sig.editUrl}{$sessionUrlSuffixHtml}" class="btn btn-sm" title="{lng p="edit"}"><i class="fa-regular fa-pen-to-square"></i></a>
							<button type="submit" name="delete" value="{$sig.signatureid}" class="btn btn-sm" title="{lng p="delete"}" onclick="return confirm('{lng p="realdel"}');"><i class="fa-regular fa-trash-can"></i></button>
						</div>
					</td>
				</tr>
				{/foreach}
				</tbody>
			</table>
		</div>
	</div>
	<div class="d-flex flex-wrap align-items-center gap-2 mt-3">
		<label class="mb-0">{lng p="action"}:</label>
		<select name="massAction" class="form-select form-select-sm" style="width: auto;">
			<option value="-">------------</option>
			<option value="pause">{lng p="pause"}</option>
			<option value="continue">{lng p="continue"}</option>
			<option value="delete">{lng p="delete"}</option>
		</select>
		<button type="submit" name="executeMassAction" value="1" class="btn btn-sm btn-primary">{lng p="execute"}</button>
	</div>
	</form>
</fieldset>

<fieldset>
	<legend>{lng p="modfax_addsignature"}</legend>
	
	<form action="{$pageURL}{$sessionUrlSuffixHtml}" method="post" onsubmit="spin(this);">
		{csrffield}
		<input type="hidden" name="add" value="1" />
		<table width="100%">
			<tr>
				<td width="40" valign="top" rowspan="10"><img src="../plugins/templates/images/modfax_sig32.png" border="0" alt="" width="32" height="32" /></td>
				<td colspan="2">
					<table width="100%">
						<tr>
							<td style="border: 1px solid #DDDDDD;background-color:#FFFFFF;"><textarea name="text" id="text" class="plainTextArea" style="width:100%;height:200px;font-family:arial;font-size:12px;text-align:left;"></textarea></td>
							<td valign="top" width="145" style="padding-left:1em;">
								{lng p="modfax_fontname"}:<br />
								<select class="smallInput" name="fontname" onchange="faxFormFontNameChanged(this)">
									<option value="arial">Arial</option>
									<option value="times">Times</option>
									<option value="courier">Courier</option>
								</select><br /><br />
								
								{lng p="modfax_fontsize"}:<br />
								<select class="smallInput" name="fontsize" onchange="faxFormFontSizeChanged(this)">
									<option value="24">24</option>
									<option value="22">22</option>
									<option value="20">20</option>
									<option value="18">18</option>
									<option value="16">16</option>
									<option value="14">14</option>
									<option value="12" selected="selected">12</option>
									<option value="11">11</option>
									<option value="10">10</option>
									<option value="8">8</option>
									<option value="6">6</option>
								</select><br /><br />
								
								{lng p="modfax_align"}:<br />
								<select class="smallInput" name="align" onchange="faxFormAlignChanged(this)">
									<option value="L">{lng p="modfax_alignleft"}</option>
									<option value="C">{lng p="modfax_aligncenter"}</option>
									<option value="R">{lng p="modfax_alignright"}</option>
									<option value="J">{lng p="modfax_alignjustify"}</option>
								</select><br /><br />
								
								<input type="checkbox" name="style[]" value="4" id="style_bold" onclick="faxFormStyleChanged()" />
									<label for="style_bold">{lng p="modfax_bold"}</label>
								<input type="checkbox" name="style[]" value="8" id="style_italic" onclick="faxFormStyleChanged()" />
									<label for="style_italic">{lng p="modfax_italic"}</label><br />
								<input type="checkbox" name="style[]" value="16" id="style_underlined" onclick="faxFormStyleChanged()" />
									<label for="style_underlined">{lng p="modfax_underlined"}</label>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td class="td1">{lng p="modfax_line"}:</td>
				<td class="td2">
					<input type="checkbox" name="style[]" value="1" id="style_1" checked="checked" />
					<label for="style_1">{lng p="modfax_top"}</label> <br />
					<input type="checkbox" name="style[]" value="2" id="style_2" />
					<label for="style_2">{lng p="modfax_bottom"}</label>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="modfax_showon"}:</td>
				<td class="td2">
					<input type="checkbox" name="showon[]" value="1" id="showon_1" checked="checked" />
					<label for="showon_1">{lng p="modfax_firstpage"}</label> <br />
					<input type="checkbox" name="showon[]" value="2" id="showon_2" checked="checked" />
					<label for="showon_2">{lng p="modfax_otherpages"}</label>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="modfax_placement"}:</td>
				<td class="td2">
					<input type="radio" name="showon[99]" value="4" id="showon_4" />
					<label for="showon_4">{lng p="modfax_top"}</label> <br />
					<input type="radio" name="showon[99]" value="8" id="showon_8" checked="checked" />
					<label for="showon_8">{lng p="modfax_bottom"}</label>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="modfax_margin"}:</td>
				<td class="td2">
					<input type="text" name="margin" value="30" size="4" /> mm
				</td>
			</tr>
			
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td class="td1" width="160">{lng p="weight"}:</td>
				<td class="td2"><input type="text" name="weight" size="4" value="100" />%</td>
			</tr>
			<tr>
				<td class="td1">{lng p="groups"}:</td>
				<td class="td2">
					<input type="checkbox" name="groups[]" value="*" id="group_all" checked="checked" />
						<label for="group_all"><b>{lng p="all"}</b></label>
					{foreach from=$groups item=group key=groupID}
						<input type="checkbox" name="groups[]" value="{$groupID}" id="group_{$groupID}" />
							<label for="group_{$groupID}"><b>{text value=$group.title}</b></label><br />
					{/foreach}
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="paused"}?</td>
				<td class="td2"><input type="checkbox" name="paused" /></td>
			</tr>
		</table>
	
		<p align="right">
			<input class="button" type="submit" value=" {lng p="add"} " />
		</p>
	</form>
</fieldset>

<script>
<!--
	{literal}function faxFormFontNameChanged(field)
	{
		EBID('text').style.fontFamily = field.value;
		EBID('text').focus();
	}
	
	function faxFormFontSizeChanged(field)
	{
		EBID('text').style.fontSize = field.value + 'px';
		EBID('text').focus();
	}
	
	function faxFormAlignChanged(field)
	{
		var align = 'left';
		
		if(field.value == 'L')
			align = 'left';
		else if(field.value == 'C')
			align = 'center';
		else if(field.value == 'R')
			align = 'right';
		else if(field.value == 'J')
			align = 'justify';
		
		EBID('text').style.textAlign = align;
		EBID('text').focus();
	}
	
	function faxFormStyleChanged()
	{
		EBID('text').style.fontWeight =
			EBID('style_bold').checked ? 'bold' : 'normal';
		EBID('text').style.textDecoration =
			EBID('style_underlined').checked ? 'underline' : 'none';
		EBID('text').style.fontStyle =
			EBID('style_italic').checked ? 'italic' : 'normal';
		EBID('text').focus();
	}{/literal}
//-->
</script>
