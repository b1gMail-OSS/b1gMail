<fieldset>
	<legend>{lng p="edit"}</legend>
	
	<form action="{$pageURL}&action=signatures&do=edit&id={$sig.signatureid}&save=true&sid={$sid}" method="post" onsubmit="spin(this);">
		<div class="row">
			<div class="col-sm-8">
				<div class="mb-3 row">
					<div class="col-sm-12">
						<textarea name="text" id="text" class="form-control plainTextArea" style="min-height: 200px;">{text value=$sig.text allowEmpty=true}</textarea>
					</div>
				</div>
			</div>
			<div class="col-sm-2">
				<div class="mb-3">
					<label class="form-label">{lng p="modfax_fontname"}</label>
					<select class="form-select form-select-sm" name="fontname" onchange="faxFormFontNameChanged(this)">
						<option value="arial"{if $sig.fontname=='arial'} selected="selected"{/if}>Arial</option>
						<option value="times"{if $sig.fontname=='times'} selected="selected"{/if}>Times</option>
						<option value="courier"{if $sig.fontname=='courier'} selected="selected"{/if}>Courier</option>
					</select>
				</div>
				<div class="mb-3">
					<label class="form-label">{lng p="modfax_fontsize"}</label>
					<select class="form-select form-select-sm" name="fontsize" onchange="faxFormFontSizeChanged(this)">
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
					</select>
				</div>
				<div class="mb-3">
					<label class="form-label">{lng p="modfax_align"}</label>
					<select class="form-select form-select-sm" name="align" onchange="faxFormAlignChanged(this)">
						<option value="L"{if $sig.align=='L'} selected="selected"{/if}>{lng p="modfax_alignleft"}</option>
						<option value="C"{if $sig.align=='C'} selected="selected"{/if}>{lng p="modfax_aligncenter"}</option>
						<option value="R"{if $sig.align=='R'} selected="selected"{/if}>{lng p="modfax_alignright"}</option>
						<option value="J"{if $sig.align=='J'} selected="selected"{/if}>{lng p="modfax_alignjustify"}</option>
					</select>
				</div>
			</div>
			<div class="col-sm-2">
				<div class="mb-3 row">
					<div class="col-sm-12">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="style[]" value="4" id="style_bold" onclick="faxFormStyleChanged()"{if $sig.style&4} checked="checked"{/if}>
							<span class="form-check-label">{lng p="modfax_bold"}</span>
						</label>
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="style[]" value="8" id="style_italic" onclick="faxFormStyleChanged()"{if $sig.style&8} checked="checked"{/if}>
							<span class="form-check-label">{lng p="modfax_italic"}</span>
						</label>
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="style[]" value="16" id="style_underlined" onclick="faxFormStyleChanged()"{if $sig.style&16} checked="checked"{/if}>
							<span class="form-check-label">{lng p="modfax_underlined"}</span>
						</label>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-4">
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="modfax_line"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="style[]" value="1" id="style_1"{if $sig.style&1} checked="checked"{/if}>
							<span class="form-check-label">{lng p="modfax_top"}</span>
						</label>
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="style[]" value="2" id="style_2"{if $sig.style&2} checked="checked"{/if}>
							<span class="form-check-label">{lng p="modfax_bottom"}</span>
						</label>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="modfax_showon"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="showon[]" value="1" id="showon_1"{if $sig.showon&1} checked="checked"{/if}>
							<span class="form-check-label">{lng p="modfax_firstpage"}</span>
						</label>
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="showon[]" value="2" id="showon_2"{if $sig.showon&2} checked="checked"{/if}>
							<span class="form-check-label">{lng p="modfax_otherpages"}</span>
						</label>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="modfax_placement"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="radio" name="showon[99]" value="4" id="showon_4"{if $sig.showon&4} checked="checked"{/if}>
							<span class="form-check-label">{lng p="modfax_top"}</span>
						</label>
						<label class="form-check">
							<input class="form-check-input" type="radio" name="showon[99]" value="8" id="showon_8"{if $sig.showon&8} checked="checked"{/if}>
							<span class="form-check-label">{lng p="modfax_bottom"}</span>
						</label>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="modfax_margin"}</label>
					<div class="col-sm-8">
						<div class="input-group mb-2">
							<input type="text" class="form-control" name="margin" value="{$sig.margin}" placeholder="{lng p="modfax_margin"}">
							<span class="input-group-text">mm</span>
						</div>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="weight"}</label>
					<div class="col-sm-8">
						<div class="input-group mb-2">
							<input type="text" class="form-control" name="weight" value="{$sig.weight}" placeholder="{lng p="modfax_margin"}">
							<span class="input-group-text">%</span>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="groups"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="groups[]" value="*" id="group_all"{if $sig.groups=='*'} checked="checked"{/if}>
							<span class="form-check-label">{lng p="all"}</span>
						</label>
						{foreach from=$groups item=group key=groupID}
							<label class="form-check">
								<input class="form-check-input" type="checkbox" name="groups[]" value="{$groupID}" id="group_{$groupID}"{if $group.checked} checked="checked"{/if}>
								<span class="form-check-label">{text value=$group.title}</span>
							</label>
						{/foreach}
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="paused"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="paused"{if $sig.paused} checked="checked"{/if}>
						</label>
					</div>
				</div>
			</div>
		</div>

		<div class="text-end">
			<input class="btn btn-primary" type="submit" value="{lng p="save"}" />
		</div>
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
