<script src="../clientlib/wysiwyg.js?{fileDateSig file="../../clientlib/wysiwyg.js"}"></script>
<script type="text/javascript" src="../clientlib/ckeditor/ckeditor.js?{fileDateSig file="../../clientlib/ckeditor/ckeditor.js"}"></script>

<fieldset>
	<legend>{lng p="edit"}</legend>
	
	<form action="{$pageURL}&action=packages&do=edit&save=true&id={$package.id}&sid={$sid}" method="post" onsubmit="submitEditors();spin(this)">
		<table width="100%">
			<tr>
				<td width="40" valign="top" rowspan="10"><img src="../plugins/templates/images/pacc_packages32.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="180">{lng p="title"}:</td>
				<td class="td2" id="titles">
					<div id="title0">
						<select name="titles[0][lang]" disabled="disabled">
							<option value="">({lng p="pacc_alllanguages"})</option>
							{foreach from=$languages key=langKey item=langInfo}
							<option value="{$langKey}">{$langInfo.title}</option>
							{/foreach}
						</select>
						<input type="text" size="64" name="titles[0][title]" value="{text value=$package.titel allowEmpty=true}" />
						<button type="button" onclick="addPaccTitle();return false;"><img src="{$tpldir}images/add32.png" width="14" height="14" border="0" alt="" /></button>
					</div>
					{assign var=altTitleIndex value=1}
					{foreach from=$altTitles item=altTitle key=altTitleLang}
					<div id="title{$altTitleIndex}">
						<select name="titles[{$altTitleIndex}][lang]">
							<option value="">({lng p="pacc_alllanguages"})</option>
							{foreach from=$languages key=langKey item=langInfo}
							<option value="{$langKey}"{if $altTitleLang==$langKey} selected="selected"{/if}>{$langInfo.title}</option>
							{/foreach}
						</select>
						<input type="text" size="64" name="titles[{$altTitleIndex}][title]" value="{text value=$altTitle allowEmpty=true}" />
						<button type="button" onclick="removePaccTitle({$altTitleIndex});return false;"><img src="{$tpldir}images/delete.png" width="14" height="14" border="0" alt="" /></button>
					</div>
					{math assign="altTitleIndex" equation="x+1" x=$altTitleIndex}
					{/foreach}
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="group"}:</td>
				<td class="td2"><select name="gruppe">
						{foreach from=$groups item=groupItem}
							<option value="{$groupItem.id}"{if $groupItem.id==$package.gruppe} selected="selected"{/if}>{text value=$groupItem.title}</option>
						{/foreach}
						</select></td>
			</tr>
			<tr>
				<td class="td1">{lng p="pacc_fallbackgroup"}:</td>
				<td class="td2"><select name="fallback_grp">
							<option value="-1">({lng p="pacc_lockaccount"})</option>
						{foreach from=$groups item=groupItem}
							<option value="{$groupItem.id}"{if $groupItem.id==$package.fallback_grp} selected="selected"{/if}>{text value=$groupItem.title}</option>
						{/foreach}
						</select></td>
			</tr>
			<tr>
				<td class="td1">{lng p="template"}:</td>
				<td class="td2"><select name="template">
							<option value="">({lng p="pacc_defaulttpl"})</option>
						{foreach from=$templates item=templateInfo key=template}
							<option value="{$template}"{if $package.template==$template} selected="selected"{/if}>{text value=$templateInfo.title}</option>
						{/foreach}
						</select></td>
			</tr>
			<tr>
				<td class="td1">{lng p="pacc_paymentperiod"}:</td>
				<td class="td2">({lng p="pacc_every"} <input type="text" size="4" name="abrechnung_t" value="{$package.abrechnung_t}" />)
						<select name="abrechnung">
							<option value="einmalig"{if $package.abrechnung=='einmalig'} selected="selected"{/if}>{lng p="pacc_once"}</option>
							<option value="wochen"{if $package.abrechnung=='wochen'} selected="selected"{/if}>{lng p="pacc_period_wochen"}</option>
							<option value="monate"{if $package.abrechnung=='monate'} selected="selected"{/if}>{lng p="pacc_period_monate"}</option>
							<option value="jahre"{if $package.abrechnung=='jahre'} selected="selected"{/if}>{lng p="pacc_period_jahre"}</option>
						</select></td>
			</tr>
			<tr>
				<td class="td1">{lng p="pacc_periodprice"}:</td>
				<td class="td2"><input type="text" size="6" name="preis" value="{$package.preis}" />
								{text value=$pacc_prefs.currency}</td>
			</tr>
			<tr>
				<td class="td1">{lng p="pacc_periods"}:</td>
				<td class="td2">
					<input type="radio" name="laufzeiten_all" value="true" {if $package.laufzeiten=='*'} checked="checked"{/if} id="laufzeiten_all" />
					<label for="laufzeiten_all">{lng p="pacc_periods_all"}</label><br />

					<input type="radio" name="laufzeiten_all" value="false" {if $package.laufzeiten!='*'} checked="checked"{/if} id="laufzeiten_custom" />	
					<input type="text" name="laufzeiten" value="{if $package.laufzeiten=='*'}6,12,18,24{else}{text value=$package.laufzeiten allowEmpty=true}{/if}" size="16" />
					<label for="laufzeiten_custom">
						<small>({lng p="pacc_sepbycomma"})</small>
					</label><br />

					<input type="checkbox" name="max_laufzeit_enable" id="max_laufzeit_enable"{if $package.max_laufzeit!=0} checked="checked"{/if} />
					<label for="max_laufzeit_enable">
						{lng p="pacc_period_limit"}
						<input type="text" name="max_laufzeit" value="{if $package.max_laufzeit==0}48{else}{$package.max_laufzeit}{/if}" size="5" />
					</label>
				</td>
			</tr>
			<tr>
				<td class="td1">{lng p="pacc_accentuation"}:</td>
				<td class="td2"><select name="accentuation">
									<option value="0"{if $package.accentuation==0} selected="selected"{/if}>({lng p="none"})</option>
									<option value="1"{if $package.accentuation==1} selected="selected"{/if}>{lng p="pacc_accent_1"}</option>
									<option value="2"{if $package.accentuation==2} selected="selected"{/if}>{lng p="pacc_accent_2"}</option>
									<option value="3"{if $package.accentuation==3} selected="selected"{/if}>{lng p="pacc_accent_3"}</option>
								</select></td>
			</tr>
			<tr>
				<td class="td1">{lng p="pos"}:</td>
				<td class="td2"><input type="text" size="6" name="order" value="{$package.order}" /></td>
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
						<textarea name="descriptions[0][description]" id="descriptiontext0" class="plainTextArea" style="width:100%;height:180px;">{text value=$package.beschreibung allowEmpty=true}</textarea>
					</div>
				</td>
			</tr>
			{assign var=altDescriptionIndex value=1}
			{foreach from=$altDescriptions item=altDescription key=altDescriptionLang}
			<tr id="description{$altDescriptionIndex}">
				<td></td>
				<td class="td1" style="vertical-align:top;">
					<div>{lng p="description"}:</div>
					<div>
						<select name="descriptions[{$altDescriptionIndex}][lang]">
							<option value="">({lng p="pacc_alllanguages"})</option>
							{foreach from=$languages key=langKey item=langInfo}
							<option value="{$langKey}"{if $altDescriptionLang==$langKey} selected="selected"{/if}>{$langInfo.title}</option>
							{/foreach}
						</select>
						<button type="button" onclick="removePaccDescription({$altDescriptionIndex});return false;"><img src="{$tpldir}images/delete.png" width="14" height="14" border="0" alt="" /></button>
					</div>
				</td>
				<td>
					<div style="border: 1px solid #DDD;background-color:#FFF;margin:0 0.5em;">
						<textarea name="descriptions[{$altDescriptionIndex}][description]" id="descriptiontext{$altDescriptionIndex}" class="plainTextArea" style="width:100%;height:180px;">{text value=$altDescription allowEmpty=true}</textarea>
					</div>
				</td>
			</tr>
			{math assign="altDescriptionIndex" equation="x+1" x=$altDescriptionIndex}
			{/foreach}
			</tbody>
		</table>
	
		<p align="right">
			<input class="button" type="submit" value=" {lng p="save"} " />
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

var titleCounter = {/literal}{$altTitleIndex}{literal};
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

function removePaccTitle(index)
{
	EBID('titles').removeChild(EBID('title'+index));
}

var descriptionCounter = {/literal}{$altDescriptionIndex}{literal};
for(var i = 1; i < descriptionCounter; ++i)
{
	var editor = new htmlEditor('descriptiontext'+i, '{/literal}{$usertpldir}{literal}/images/editor/');
	editor.init();
	editor.start();
	editors[i] = editor;
}
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

function removePaccDescription(index)
{
	editors[index] = null;
	EBID('descriptions').removeChild(EBID('description'+index));
}
//-->
</script>{/literal}
