<fieldset>
	<legend>{lng p="modfax_signatures"}</legend>

	<form action="{$pageURL}&action=signatures&sid={$sid}" method="post" name="f1" onsubmit="spin(this)">
		<div class="card">
			<div class="table-responsive">
				<table class="table table-vcenter table-striped">
					<thead>
					<tr>
						<th style="width: 25px; text-align: center;"><a href="javascript:invertSelection(document.forms.f1,'sigs[]');"><img src="{$tpldir}images/dot.png" border="0" alt="" width="10" height="8" /></a></th>
						<th>{lng p="modfax_signature"}</th>
						<th style="width: 75px;">{lng p="weight"}</th>
						<th style="width: 70px;">{lng p="modfax_used"}</th>
						<th style="width: 70px;">&nbsp;</th>
					</tr>
					</thead>
					<tbody>
					{foreach from=$signatures item=sig}
						{cycle name=class values="td1,td2" assign=class}
						<tr class="{$class}">
							<td class="text-center"><input type="checkbox" name="sigs[]" value="{$sig.signatureid}" /></td>
							<td>{$sig.displayText}</td>
							<td>{$sig.weight}%</td>
							<td>{$sig.counter}</td>
							<td class="text-nowrap">
								<div class="btn-group btn-group-sm">
									<a href="{$pageURL}&action=signatures&{if !$sig.paused}de{/if}activate={$sig.signatureid}&sid={$sid}" class="btn btn-sm" title="{if $sig.paused}{lng p="continue"}{else}{lng p="pause"}{/if}">{if !$sig.paused}<i class="fa-regular fa-circle-check text-green"></i>{else}<i class="fa-regular fa-circle-pause text-cyan"></i>{/if}</a>
									<a href="{$pageURL}&action=signatures&do=edit&id={$sig.signatureid}&sid={$sid}" class="btn btn-sm"><i class="fa-regular fa-pen-to-square"></i></a>
									<a href="{$pageURL}&action=signatures&delete={$sig.signatureid}&sid={$sid}" onclick="return confirm('{lng p="realdel"}');" class="btn btn-sm"><i class="fa-regular fa-trash-can"></i></a>
								</div>
							</td>
						</tr>
					{/foreach}
					</tbody>
				</table>
			</div>
			<div class="card-footer">
				<div style="float: left;">{lng p="action"}:&nbsp;</div>
				<div style="float: left;">
					<div class="btn-group btn-group-sm">
						<select name="massAction" class="form-select form-select-sm">
							<option value="-">------------</option>
							<optgroup label="{lng p="actions"}">
								<option value="pause">{lng p="pause"}</option>
								<option value="continue">{lng p="continue"}</option>
								<option value="delete">{lng p="delete"}</option>
							</optgroup>
						</select>
						<input type="submit" name="executeMassAction" value="{lng p="execute"}" class="btn btn-sm btn-dark-lt" />
					</div>
				</div>
			</div>
		</div>
	</form>
</fieldset>

<fieldset>
	<legend>{lng p="modfax_addsignature"}</legend>

	<form action="{$pageURL}&action=signatures&add=true&sid={$sid}" method="post" onsubmit="spin(this);">
		<div class="row">
			<div class="col-sm-8">
				<div class="mb-3 row">
					<div class="col-sm-12">
						<textarea name="text" id="text" class="form-control plainTextArea" style="min-height: 200px;"></textarea>
					</div>
				</div>
			</div>
			<div class="col-sm-2">
				<div class="mb-3">
					<label class="form-label">{lng p="modfax_fontname"}</label>
						<select class="form-select form-select-sm" name="fontname" onchange="faxFormFontNameChanged(this)">
							<option value="arial">Arial</option>
							<option value="times">Times</option>
							<option value="courier">Courier</option>
						</select>
				</div>
				<div class="mb-3">
					<label class="form-label">{lng p="modfax_fontsize"}</label>
						<select class="form-select form-select-sm" name="fontsize" onchange="faxFormFontSizeChanged(this)">
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
						</select>
				</div>
				<div class="mb-3">
					<label class="form-label">{lng p="modfax_align"}</label>
						<select class="form-select form-select-sm" name="align" onchange="faxFormAlignChanged(this)">
							<option value="L">{lng p="modfax_alignleft"}</option>
							<option value="C">{lng p="modfax_aligncenter"}</option>
							<option value="R">{lng p="modfax_alignright"}</option>
							<option value="J">{lng p="modfax_alignjustify"}</option>
						</select>
				</div>
			</div>
			<div class="col-sm-2">
				<div class="mb-3 row">
					<div class="col-sm-12">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="style[]" value="4" id="style_bold" onclick="faxFormStyleChanged()">
							<span class="form-check-label">{lng p="modfax_bold"}</span>
						</label>
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="style[]" value="8" id="style_italic" onclick="faxFormStyleChanged()">
							<span class="form-check-label">{lng p="modfax_italic"}</span>
						</label>
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="style[]" value="16" id="style_underlined" onclick="faxFormStyleChanged()">
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
							<input class="form-check-input" type="checkbox" name="style[]" value="1" id="style_1" checked="checked">
							<span class="form-check-label">{lng p="modfax_top"}</span>
						</label>
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="style[]" value="2" id="style_2">
							<span class="form-check-label">{lng p="modfax_bottom"}</span>
						</label>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="modfax_showon"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="showon[]" value="1" id="showon_1" checked="checked">
							<span class="form-check-label">{lng p="modfax_firstpage"}</span>
						</label>
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="showon[]" value="2" id="showon_2" checked="checked">
							<span class="form-check-label">{lng p="modfax_otherpages"}</span>
						</label>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="modfax_placement"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="radio" name="showon[99]" value="4" id="showon_4">
							<span class="form-check-label">{lng p="modfax_top"}</span>
						</label>
						<label class="form-check">
							<input class="form-check-input" type="radio" name="showon[99]" value="8" id="showon_8" checked="checked">
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
							<input type="text" class="form-control" name="margin" value="30" placeholder="{lng p="modfax_margin"}">
							<span class="input-group-text">mm</span>
						</div>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="weight"}</label>
					<div class="col-sm-8">
						<div class="input-group mb-2">
							<input type="text" class="form-control" name="weight" value="100" placeholder="{lng p="modfax_margin"}">
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
							<input class="form-check-input" type="checkbox" name="groups[]" value="*" id="group_all" checked="checked">
							<span class="form-check-label">{lng p="all"}</span>
						</label>
						{foreach from=$groups item=group key=groupID}
							<label class="form-check">
								<input class="form-check-input" type="checkbox" name="groups[]" value="{$groupID}" id="group_{$groupID}">
								<span class="form-check-label">{text value=$group.title}</span>
							</label>
						{/foreach}
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="paused"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="paused">
						</label>
					</div>
				</div>
			</div>
		</div>

		<div class="text-end">
			<input class="btn btn-primary" type="submit" value="{lng p="add"}" />
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
