<fieldset>
	<legend>{lng p="edit"}</legend>
	
	<form action="{$pageURL}&action=signatures&do=edit&id={$sig.signatureid}&save=true&sid={$sid}" method="post" onsubmit="spin(this);">
		<table width="100%">
			<tr>
				<td width="40" valign="top" rowspan="10"><img src="../plugins/templates/images/modfax_sig32.png" border="0" alt="" width="32" height="32" /></td>
				<td colspan="2">
					<table width="100%">
						<tr>
							<td style="border: 1px solid #DDDDDD;background-color:#FFFFFF;"><textarea name="text" id="text" class="plainTextArea" style="width:100%;height:200px;font-family:{$sig.fontname};font-size:{$sig.fontsize}px;text-align:{$sig.alignText};{if $sig.style&4}font-weight:bold;{/if}{if $sig.style&8}font-style:italic;{/if}{if $sig.style&16}text-decoration:underline;{/if}">{text value=$sig.text allowEmpty=true}</textarea></td>
							<td valign="top" width="145" style="padding-left:1em;">
								{lng p="modfax_fontname"}:<br />
								<select class="smallInput" name="fontname" onchange="faxFormFontNameChanged(this)">
									<option value="arial"{if $sig.fontname=='arial'} selected="selected"{/if}>Arial</option>
									<option value="times"{if $sig.fontname=='times'} selected="selected"{/if}>Times</option>
									<option value="courier"{if $sig.fontname=='courier'} selected="selected"{/if}>Courier</option>
								</select><br /><br />
								
								{lng p="modfax_fontsize"}:<br />
								<select class="smallInput" name="fontsize" onchange="faxFormFontSizeChanged(this)">
									<option value="24"{if $sig.fontsize==24} selected="selected"{/if}>24</option>
									<option value="22"{if $sig.fontsize==22} selected="selected"{/if}>22</option>
									<option value="20"{if $sig.fontsize==20} selected="selected"{/if}>20</option>
									<option value="18"{if $sig.fontsize==18} selected="selected"{/if}>18</option>
									<option value="16"{if $sig.fontsize==16} selected="selected"{/if}>16</option>
									<option value="14"{if $sig.fontsize==14} selected="selected"{/if}>14</option>
									<option value="12"{if $sig.fontsize==12} selected="selected"{/if}>12</option>
									<option value="11"{if $sig.fontsize==11} selected="selected"{/if}>11</option>
									<option value="10"{if $sig.fontsize==10} selected="selected"{/if}>10</option>
									<option value="8"{if $sig.fontsize==8} selected="selected"{/if}>8</option>
									<option value="6"{if $sig.fontsize==6} selected="selected"{/if}>6</option>
								</select><br /><br />
								
								{lng p="modfax_align"}:<br />
								<select class="smallInput" name="align" onchange="faxFormAlignChanged(this)">
									<option value="L"{if $sig.align=='L'} selected="selected"{/if}>{lng p="modfax_alignleft"}</option>
									<option value="C"{if $sig.align=='C'} selected="selected"{/if}>{lng p="modfax_aligncenter"}</option>
									<option value="R"{if $sig.align=='R'} selected="selected"{/if}>{lng p="modfax_alignright"}</option>
									<option value="J"{if $sig.align=='J'} selected="selected"{/if}>{lng p="modfax_alignjustify"}</option>
								</select><br /><br />
								
								<input type="checkbox" name="style[]" value="4" id="style_bold" onclick="faxFormStyleChanged()"{if $sig.style&4} checked="checked"{/if} />
									<label for="style_bold">{lng p="modfax_bold"}</label>
								<input type="checkbox" name="style[]" value="8" id="style_italic" onclick="faxFormStyleChanged()"{if $sig.style&8} checked="checked"{/if} />
									<label for="style_italic">{lng p="modfax_italic"}</label><br />
								<input type="checkbox" name="style[]" value="16" id="style_underlined" onclick="faxFormStyleChanged()"{if $sig.style&16} checked="checked"{/if} />
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
					<input type="checkbox" name="style[]" value="1" id="style_1"{if $sig.style&1} checked="checked"{/if} />
					<label for="style_1">{lng p="modfax_top"}</label> <br />
					<input type="checkbox" name="style[]" value="2" id="style_2"{if $sig.style&2} checked="checked"{/if} />
					<label for="style_2">{lng p="modfax_bottom"}</label>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="modfax_showon"}:</td>
				<td class="td2">
					<input type="checkbox" name="showon[]" value="1" id="showon_1"{if $sig.showon&1} checked="checked"{/if} />
					<label for="showon_1">{lng p="modfax_firstpage"}</label> <br />
					<input type="checkbox" name="showon[]" value="2" id="showon_2"{if $sig.showon&2} checked="checked"{/if} />
					<label for="showon_2">{lng p="modfax_otherpages"}</label>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="modfax_placement"}:</td>
				<td class="td2">
					<input type="radio" name="showon[99]" value="4" id="showon_4"{if $sig.showon&4} checked="checked"{/if} />
					<label for="showon_4">{lng p="modfax_top"}</label> <br />
					<input type="radio" name="showon[99]" value="8" id="showon_8"{if $sig.showon&8} checked="checked"{/if} />
					<label for="showon_8">{lng p="modfax_bottom"}</label>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="modfax_margin"}:</td>
				<td class="td2">
					<input type="text" name="margin" value="{$sig.margin}" size="4" /> mm
				</td>
			</tr>
			
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td class="td1" width="160">{lng p="weight"}:</td>
				<td class="td2"><input type="text" name="weight" size="4" value="{$sig.weight}" />%</td>
			</tr>
			<tr>
				<td class="td1">{lng p="groups"}:</td>
				<td class="td2">
					<input type="checkbox" name="groups[]" value="*" id="group_all"{if $sig.groups=='*'} checked="checked"{/if} />
						<label for="group_all"><b>{lng p="all"}</b></label>
					{foreach from=$groups item=group key=groupID}
						<input type="checkbox" name="groups[]" value="{$groupID}" id="group_{$groupID}"{if $group.checked} checked="checked"{/if} />
							<label for="group_{$groupID}"><b>{text value=$group.title}</b></label><br />
					{/foreach}
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="paused"}?</td>
				<td class="td2"><input type="checkbox" name="paused"{if $sig.paused} checked="checked"{/if} /></td>
			</tr>
		</table>
	
		<p align="right">
			<input class="button" type="submit" value=" {lng p="save"} " />
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
