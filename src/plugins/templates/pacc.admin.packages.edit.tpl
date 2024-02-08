<script src="../clientlib/wysiwyg.js?{fileDateSig file="../../clientlib/wysiwyg.js"}"></script>
<script type="text/javascript" src="../clientlib/ckeditor/ckeditor.js?{fileDateSig file="../../clientlib/ckeditor/ckeditor.js"}"></script>

<fieldset>
	<legend>{lng p="edit"}</legend>

	<form action="{$pageURL}&action=packages&do=edit&save=true&id={$package.id}&sid={$sid}" method="post" onsubmit="submitEditors();spin(this)">

		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="title"}</label>
			<div class="col-sm-10" id="titles">
				<div class="input-group mb-2" id="title0">
					<select name="titles[0][lang]" disabled="disabled" class="form-control">
						<option value="">({lng p="pacc_alllanguages"})</option>
						{foreach from=$languages key=langKey item=langInfo}
							<option value="{$langKey}">{$langInfo.title}</option>
						{/foreach}
					</select>
					<input type="text" class="form-control" name="titles[0][title]" value="{text value=$package.titel allowEmpty=true}" placeholder="{lng p="title"}">
					<button type="button" class="btn" onclick="addPaccTitle();return false;"><i class="fa-solid fa-plus"></i></button>
				</div>

				{assign var=altTitleIndex value=1}
				{foreach from=$altTitles item=altTitle key=altTitleLang}
					<div class="input-group mb-2" id="title{$altTitleIndex}">
						<select name="titles[0][lang]" disabled="disabled" class="form-control">
							<option value="">({lng p="pacc_alllanguages"})</option>
							{foreach from=$languages key=langKey item=langInfo}
								<option value="{$langKey}"{if $altTitleLang==$langKey} selected="selected"{/if}>{$langInfo.title}</option>
							{/foreach}
						</select>
						<input type="text" class="form-control" name="titles[{$altTitleIndex}][title]" value="{text value=$altTitle allowEmpty=true}" placeholder="{lng p="title"}">
						<button type="button" class="btn" onclick="addPaccTitle({$altTitleIndex});return false;"><i class="fa-solid fa-plus"></i></button>
					</div>
					{math assign="altTitleIndex" equation="x+1" x=$altTitleIndex}
				{/foreach}
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="group"}</label>
			<div class="col-sm-10">
				<select name="gruppe" class="form-select">
					{foreach from=$groups item=groupItem}
						<option value="{$groupItem.id}"{if $groupItem.id==$package.gruppe} selected="selected"{/if}>{text value=$groupItem.title}</option>
					{/foreach}
				</select>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="pacc_fallbackgroup"}</label>
			<div class="col-sm-10">
				<select name="fallback_grp" class="form-select">
					<option value="-1">({lng p="pacc_lockaccount"})</option>
					{foreach from=$groups item=groupItem}
						<option value="{$groupItem.id}"{if $groupItem.id==$package.fallback_grp} selected="selected"{/if}>{text value=$groupItem.title}</option>
					{/foreach}
				</select>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="template"}</label>
			<div class="col-sm-10">
				<select name="template" class="form-select">
					<option value="">({lng p="pacc_defaulttpl"})</option>
					{foreach from=$templates item=templateInfo key=template}
						<option value="{$template}"{if $package.template==$template} selected="selected"{/if}>{text value=$templateInfo.title}</option>
					{/foreach}
				</select>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="pacc_paymentperiod"}</label>
			<div class="col-sm-10">
				<div class="input-group mb-2">
					<span class="input-group-text">{lng p="pacc_every"}</span>
					<input type="text" class="form-control" name="abrechnung_t" value="1" placeholder="{lng p="pacc_every"}">
					<select name="abrechnung" class="form-select">
						<option value="einmalig"{if $package.abrechnung=='einmalig'} selected="selected"{/if}>{lng p="pacc_once"}</option>
						<option value="wochen"{if $package.abrechnung=='wochen'} selected="selected"{/if}>{lng p="pacc_period_wochen"}</option>
						<option value="monate"{if $package.abrechnung=='monate'} selected="selected"{/if}>{lng p="pacc_period_monate"}</option>
						<option value="jahre"{if $package.abrechnung=='jahre'} selected="selected"{/if}>{lng p="pacc_period_jahre"}</option>
					</select>
				</div>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="pacc_periodprice"}</label>
			<div class="col-sm-10">
				<div class="input-group mb-2">
					<input type="text" class="form-control" name="preis" value="{$package.preis}" placeholder="{lng p="pacc_periodprice"}">
					<span class="input-group-text">{text value=$pacc_prefs.currency}</span>
				</div>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-check-label">{lng p="contactform_name"}</label>
			<div class="col-sm-10">
				<div class="input-group mb-2">
					<span class="input-group-text">
                                <input class="form-check-input m-0" type="radio" name="laufzeiten_all" value="true"{if $package.laufzeiten=='*'} checked="checked"{/if} id="laufzeiten_all">
                              </span>
					<input type="text" class="form-control" value="{lng p="pacc_periods_all"}" disabled>
				</div>

				<div class="input-group mb-2">
					<span class="input-group-text">
                    	<input class="form-check-input m-0" type="radio" name="laufzeiten_all" value="false"{if $package.laufzeiten!='*'} checked="checked"{/if} id="laufzeiten_custom">
                    </span>
					<input type="text" class="form-control" name="laufzeiten" value="{if $package.laufzeiten=='*'}6,12,18,24{else}{text value=$package.laufzeiten allowEmpty=true}{/if}" placeholder="{lng p="pacc_sepbycomma"}">
				</div>
				<div class="input-group mb-2">
					<span class="input-group-text">
                    	<input class="form-check-input m-0" type="checkbox" name="max_laufzeit_enable" id="max_laufzeit_enable"{if $package.max_laufzeit!=0} checked="checked"{/if}>
					</span>
					<span class="input-group-text">{lng p="pacc_period_limit"}</span>
					<input type="text" class="form-control" name="max_laufzeit" value="{if $package.max_laufzeit==0}48{else}{$package.max_laufzeit}{/if}" placeholder="{lng p="pacc_period_limit"}">
				</div>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="pacc_accentuation"}</label>
			<div class="col-sm-10">
				<select name="accentuation" class="form-select">
					<option value="0"{if $package.accentuation==0} selected="selected"{/if}>({lng p="none"})</option>
					<option value="1"{if $package.accentuation==1} selected="selected"{/if}>{lng p="pacc_accent_1"}</option>
					<option value="2"{if $package.accentuation==2} selected="selected"{/if}>{lng p="pacc_accent_2"}</option>
					<option value="3"{if $package.accentuation==3} selected="selected"{/if}>{lng p="pacc_accent_3"}</option>
				</select>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="pos"}</label>
			<div class="col-sm-10">
				<input type="number" class="form-control" name="order" value="{$package.order}"  placeholder="{lng p="pos"}">
			</div>
		</div>
		<div id="descriptions">
			<div class="mb-3 row" id="description0">
				<label class="col-sm-2 col-form-label">
					{lng p="description"}
					<div class="input-group input-group-sm mb-2" id="title0">
						<select name="descriptions[0][lang]" disabled="disabled" class="form-select">
							<option value="">({lng p="pacc_alllanguages"})</option>
							{foreach from=$languages key=langKey item=langInfo}
								<option value="{$langKey}">{$langInfo.title}</option>
							{/foreach}
						</select>
						<button type="button" class="btn" onclick="addPaccDescription();return false;"><i class="fa-solid fa-plus"></i></button>
					</div>
				</label>
				<div class="col-sm-10">
					<textarea name="descriptions[0][description]" id="descriptiontext0" class="plainTextArea" style="width:100%;height:180px;">{text value=$package.beschreibung allowEmpty=true}</textarea>
				</div>
			</div>
		</div>
		{assign var=altDescriptionIndex value=1}
		{foreach from=$altDescriptions item=altDescription key=altDescriptionLang}
			<div id="descriptions">
				<div class="mb-3 row" id="description{$altDescriptionIndex}">
					<label class="col-sm-2 col-form-label">
						{lng p="description"}
						<div class="input-group input-group-sm mb-2" id="title0">
							<select name="descriptions[{$altDescriptionIndex}][lang]" class="form-select">
								<option value="">({lng p="pacc_alllanguages"})</option>
								{foreach from=$languages key=langKey item=langInfo}
									<option value="{$langKey}"{if $altDescriptionLang==$langKey} selected="selected"{/if}>{$langInfo.title}</option>
								{/foreach}
							</select>
							<button type="button" class="btn" onclick="addPaccDescription({$altDescriptionIndex});return false;"><i class="fa-solid fa-plus"></i></button>
						</div>
					</label>
					<div class="col-sm-10">
						<textarea name="descriptions[0][description]" id="descriptiontext{$altDescriptionIndex}" class="plainTextArea" style="width:100%;height:180px;">{text value=$package.beschreibung allowEmpty=true}</textarea>
					</div>
				</div>
			</div>
		{math assign="altDescriptionIndex" equation="x+1" x=$altDescriptionIndex}
		{/foreach}

		<div class="text-end">
			<input class="btn btn-primary" type="submit" value="{lng p="save"}" />
		</div>
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
		var titleButtonImg = titleButton.getElementsByTagName('i')[0];
		titleButtonImg.className = titleButtonImg.className.replace('fa-plus', 'fa-trash');
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
		var descriptionButtonImg = descriptionButton.getElementsByTagName('i')[0];
		descriptionButtonImg.className = descriptionButtonImg.className.replace('fa-plus', 'fa-trash');
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
