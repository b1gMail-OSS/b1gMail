<script src="../clientlib/wysiwyg.js?{fileDateSig file="../../clientlib/wysiwyg.js"}"></script>
<script type="text/javascript" src="../clientlib/ckeditor/ckeditor.js?{fileDateSig file="../../clientlib/ckeditor/ckeditor.js"}"></script>

<fieldset>
	<legend>{lng p="pacc_packages"}</legend>
	
	<table class="list">
		<tr>
			<th width="20">&nbsp;</th>
			<th>{lng p="title"}</th>
			<th>{lng p="group"}</th>
			<th>{lng p="price"}</th>
			<th width="55">{lng p="pos"}</th>
			<th width="55">&nbsp;</th>
		</tr>
		
		{foreach from=$packages item=package}
		{cycle name=class values="td1,td2" assign=class}
		<tr class="{$class}">
			<td align="center"><img src="../plugins/templates/images/pacc_packages.png" border="0" alt="" width="16" height="16" /></td>
			<td>{text value=$package.title cut=35}<br />
				<small><a href="{$pageURL}&action=subscriptions&filter=true&packages[{$package.id}]=true&sid={$sid}">{$package.subscribers} {lng p="pacc_subscribers"}</a></small></td>
			<td>{text value=$package.group cut=25}<br />
				<small>{lng p="pacc_fallbackgroup"}: {text value=$package.fallback_group cut=25}</small></td>
			<td>{$package.periodPrice}<br />
				<small>{$package.paymentPeriod}</small></td>
			<td>{$package.order}</td>
			<td>
				<a href="{$pageURL}&action=packages&do=edit&id={$package.id}&sid={$sid}" title="{lng p="edit"}"><img src="{$tpldir}images/edit.png" border="0" alt="{lng p="edit"}" width="16" height="16" /></a>
				<a href="{$pageURL}&action=packages&delete={$package.id}&sid={$sid}" onclick="return confirm('{lng p="realdel"}');" title="{lng p="delete"}"><img src="{$tpldir}images/delete.png" border="0" alt="{lng p="delete"}" width="16" height="16" /></a>
			</td>
		</tr>
		{/foreach}
	</table>
</fieldset>

<fieldset>
	<legend>{lng p="pacc_addpackage"}</legend>
	
	<form action="{$pageURL}&action=packages&add=true&sid={$sid}" method="post" onsubmit="submitEditors();spin(this)">
		<table width="100%">
			<tr>
				<td width="40" valign="top" rowspan="9"><img src="../plugins/templates/images/pacc_packages32.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="180">{lng p="title"}:</td>
				<td class="td2" id="titles">
					<div id="title0">
						<select name="titles[0][lang]" disabled="disabled">
							<option value="">({lng p="pacc_alllanguages"})</option>
							{foreach from=$languages key=langKey item=langInfo}
							<option value="{$langKey}">{$langInfo.title}</option>
							{/foreach}
						</select>
						<input type="text" size="64" name="titles[0][title]" value="" />
						<button type="button" onclick="addPaccTitle();return false;"><img src="{$tpldir}images/add32.png" width="14" height="14" border="0" alt="" /></button>
					</div>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="group"}:</td>
				<td class="td2"><select name="gruppe">
						{foreach from=$groups item=groupItem}
							<option value="{$groupItem.id}">{text value=$groupItem.title}</option>
						{/foreach}
						</select></td>
			</tr>
			<tr>
				<td class="td1">{lng p="pacc_fallbackgroup"}:</td>
				<td class="td2"><select name="fallback_grp">
							<option value="-1">({lng p="pacc_lockaccount"})</option>
						{foreach from=$groups item=groupItem}
							<option value="{$groupItem.id}"{if $groupItem.id==$defaultGroup} selected="selected"{/if}>{text value=$groupItem.title}</option>
						{/foreach}
						</select></td>
			</tr>
			<tr>
				<td class="td1">{lng p="template"}:</td>
				<td class="td2"><select name="template">
							<option value="">({lng p="pacc_defaulttpl"})</option>
						{foreach from=$templates item=templateInfo key=template}
							<option value="{$template}">{text value=$templateInfo.title}</option>
						{/foreach}
						</select></td>
			</tr>
			<tr>
				<td class="td1">{lng p="pacc_paymentperiod"}:</td>
				<td class="td2">({lng p="pacc_every"} <input type="text" size="4" name="abrechnung_t" value="1" />)
						<select name="abrechnung">
							<option value="einmalig">{lng p="pacc_once"}</option>
							<option value="wochen">{lng p="pacc_period_wochen"}</option>
							<option value="monate" selected="selected">{lng p="pacc_period_monate"}</option>
							<option value="jahre">{lng p="pacc_period_jahre"}</option>
						</select></td>
			</tr>
			<tr>
				<td class="td1">{lng p="pacc_periodprice"}:</td>
				<td class="td2"><input type="text" size="6" name="preis" value="0,99" />
								{text value=$pacc_prefs.currency}</td>
			</tr>
			<tr>
				<td class="td1">{lng p="pacc_periods"}:</td>
				<td class="td2">
					<input type="radio" name="laufzeiten_all" value="true" checked="checked" id="laufzeiten_all" />
					<label for="laufzeiten_all">{lng p="pacc_periods_all"}</label><br />

					<input type="radio" name="laufzeiten_all" value="false" id="laufzeiten_custom" />
					<input type="text" name="laufzeiten" value="6,12,18,24" size="16" />
					<label for="laufzeiten_custom">
						<small>({lng p="pacc_sepbycomma"})</small>
					</label><br />

					<input type="checkbox" name="max_laufzeit_enable" id="max_laufzeit_enable" />
					<label for="max_laufzeit_enable">
						{lng p="pacc_period_limit"}
						<input type="text" name="max_laufzeit" value="48" size="5" />
					</label>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="pacc_accentuation"}:</td>
				<td class="td2"><select name="accentuation">
									<option value="0" selected="selected">({lng p="none"})</option>
									<option value="1">{lng p="pacc_accent_1"}</option>
									<option value="2">{lng p="pacc_accent_2"}</option>
									<option value="3">{lng p="pacc_accent_3"}</option>
								</select></td>
			</tr>
			<tr>
				<td class="td1">{lng p="pos"}:</td>
				<td class="td2"><input type="text" size="6" name="order" value="{$nextOrder}" /></td>
			</tr>
			<tbody id="descriptions">
			<tr id="description0">
				<td></td>
				<td class="td1" style="vertical-align:top;">
					<div>{lng p="description"}:</div>
					<div>
						<select name="descriptions[0][lang]" disabled="disabled">
							<option value="">({lng p="pacc_alllanguages"})</option>
							{foreach from=$languages key=langKey item=langInfo}
							<option value="{$langKey}">{$langInfo.title}</option>
							{/foreach}
						</select>
						<button type="button" onclick="addPaccDescription();return false;"><img src="{$tpldir}images/add32.png" width="14" height="14" border="0" alt="" /></button>
					</div>
				</td>
				<td>
					<div style="border: 1px solid #DDD;background-color:#FFF;margin:0 0.5em;">
						<textarea name="descriptions[0][description]" id="descriptiontext0" class="plainTextArea" style="width:100%;height:180px;"></textarea>
					</div>
				</td>
			</tr>
			</tbody>
		</table>
	
		<p align="right">
			<input class="button" type="submit" value=" {lng p="add"} " />
		</p>
	</form>
</fieldset>

{literal}<script>
<!--
var editor0 = new htmlEditor('descriptiontext0', '{$usertpldir}/images/editor/');
editor0.init();
registerLoadAction('editor0.start()');

var editors = {};
function submitEditors()
{
	editor0.submit();

	for(var index in editors)
	{
		if(editors[index] != null)
			editors[index].submit();
	}
}

var titleCounter = 0;
function addPaccTitle()
{
	var titleIndex = ++titleCounter;

	var titleDiv = EBID('title0').cloneNode(true);
	titleDiv.id = 'title'+titleIndex;

	var titleSelect = titleDiv.getElementsByTagName('select')[0];
	titleSelect.name = 'titles['+titleIndex+'][lang]';
	titleSelect.disabled = false;

	var titleTitle = titleDiv.getElementsByTagName('input')[0];
	titleTitle.name = 'titles['+titleIndex+'][title]';

	var titleButton = titleDiv.getElementsByTagName('button')[0];
	var titleButtonImg = titleButton.getElementsByTagName('img')[0];
	titleButtonImg.src = titleButtonImg.src.replace('add32', 'delete');
	titleButton.onclick = function() {
		EBID('titles').removeChild(titleDiv);
		return false;
	};

	EBID('titles').appendChild(titleDiv);
}

var descriptionCounter = 0;
function addPaccDescription()
{
	var descriptionIndex = ++descriptionCounter;

	var descriptionDiv = EBID('description0').cloneNode(true);

	var descriptionTextArea = descriptionDiv.getElementsByTagName('textarea')[0];
	var pn = descriptionTextArea.parentNode;
	while (pn.firstChild) {
		pn.removeChild(pn.firstChild);
	}
	descriptionTextArea.id = 'descriptiontext'+descriptionIndex;
	descriptionTextArea.name = 'descriptions['+descriptionIndex+'][description]';
	descriptionTextArea.value = '';
	pn.appendChild(descriptionTextArea);

	var descriptionButton = descriptionDiv.getElementsByTagName('button')[0];
	var descriptionButtonImg = descriptionButton.getElementsByTagName('img')[0];
	descriptionButtonImg.src = descriptionButtonImg.src.replace('add32', 'delete');
	descriptionButton.onclick = function() {
		editors[descriptionIndex] = null;
		EBID('descriptions').removeChild(descriptionDiv);
		return false;
	};

	var descriptionSelect = descriptionDiv.getElementsByTagName('select')[0];
	descriptionSelect.name = 'descriptions['+descriptionIndex+'][lang]';
	descriptionSelect.disabled = false;

	EBID('descriptions').appendChild(descriptionDiv);

	var editor = new htmlEditor('descriptiontext'+descriptionIndex, '{/literal}{$usertpldir}{literal}/images/editor/');
	editor.init();
	editor.start();
	editors[descriptionIndex] = editor;
}
//-->
</script>{/literal}
