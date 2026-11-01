<fieldset>
	<legend>{lng p="faq"}</legend>

	<form action="{sessionurl file='prefs.faq.php'}" method="post" name="f1" onsubmit="spin(this)">
		{csrffield}
		<div class="card">
			<div class="table-responsive">
				<table class="table table-vcenter table-striped">
					<thead>
					<tr>
						<th class="text-center" style="width:3rem;"><a href="javascript:invertSelection(document.forms.f1,'faq_');">{acpicon file="dot.png" size="sm"}</a></th>
						<th>{lng p="question"}</th>
						<th style="100px;">{lng p="language"}</th>
						<th style="120px;">{lng p="type"}</th>
						<th style="60px;">&nbsp;</th>
					</tr>
					</thead>
					<tbody>
					{foreach from=$faqs item=faq}
						{cycle name=class values="td1,td2" assign=class}
						<tr class="{$class}">
							<td class="text-center"><label class="form-check d-inline-block mb-0"><input class="form-check-input" type="checkbox" name="faq_{$faq.id}" /></label></td>
							<td><a href="{sessionurl file='prefs.faq.php' params="do=edit&id={$faq.id}"}">{$faq.frage}</a><br /><small>{lng p="requires"}: {if $faq.required}{$requirements[$faq.required]}{else}-{/if}</small></td>
							<td>{text value=$faq.lang}</td>
							<td>{text value=$faq.typ}</td>
							<td class="text-nowrap">
								<div class="btn-group btn-group-sm">
									<a href="{sessionurl file='prefs.faq.php' params="do=edit&id={$faq.id}"}" class="btn btn-sm"><i class="fa-regular fa-pen-to-square"></i></a>
									<a href="{sessionurl file='prefs.faq.php' params="delete={$faq.id}"}" onclick="return confirm('{lng p="realdel"}');" class="btn btn-sm"><i class="fa-regular fa-trash-can"></i></a>
								</div>
							</td>
						</tr>
					{/foreach}
					</tbody>
				</table>
			</div>
			<div class="card-footer">
				<div class="input-group input-group-sm bm-mass-action">
					<span class="input-group-text">{lng p="action"}</span>
					<select name="massAction" class="form-select form-select-sm">
							<option value="-">------------</option>
							<optgroup label="{lng p="actions"}">
								<option value="delete">{lng p="delete"}</option>
							</optgroup>
						</select>
					<button type="submit" name="executeMassAction" class="btn btn-dark-lt" value="1">{lng p="execute"}</button>
				</div>
			</div>
		</div>
	</form>
</fieldset>

<fieldset>
	<legend>{lng p="addfaq"}</legend>

	<form action="{sessionurl file='prefs.faq.php' params="add=true"}" method="post" onsubmit="editor.submit();spin(this);">
		{csrffield}
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="question"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="frage" value="" placeholder="{lng p="question"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="type"}</label>
			<div class="col-sm-10">
				<select name="typ" class="form-select">
					<option value="nli">{lng p="nli"}</option>
					<option value="li">{lng p="li"}</option>
					<option value="both">{lng p="both"}</option>
				</select>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="language"}</label>
			<div class="col-sm-10">
				<select name="lang" class="form-select">
					<option value=":all:">{lng p="all"}</option>
					<optgroup label="{lng p="languages"}">
						{foreach from=$languages item=lang key=langID}
							<option value="{$langID}">{text value=$lang.title}</option>
						{/foreach}
					</optgroup>
				</select>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="requires"}</label>
			<div class="col-sm-10">
				<select name="required" class="form-select">
					<option value="">------------</option>
					<optgroup label="{lng p="services"}">
						{foreach from=$requirements item=req key=reqID}
							<option value="{$reqID}">{$req}</option>
						{/foreach}
					</optgroup>
				</select>
			</div>
		</div>
		<div class="mb-3 row">
			<div class="col-sm-12">
				<textarea name="antwort" id="antwort" class="plainTextArea" style="width:100%;height:220px;"></textarea>
				<script src="../clientlib/wysiwyg.js?{fileDateSig file="../../clientlib/wysiwyg.js"}"></script>
				<script type="text/javascript" src="../clientlib/ckeditor/ckeditor.js?{fileDateSig file="../../clientlib/ckeditor/ckeditor.js"}"></script>
				<script>
					<!--
					var editor = new htmlEditor('antwort');
					editor.init();
					registerLoadAction('editor.start()');
					//-->
				</script>
			</div>
		</div>
		<div class="mb-3 row">
			<div class="col-sm-12">
				<select onchange="editor.insertText(this.value);" class="form-select">
					<option value="">-- {lng p="vars"} --</option>
					<option value="%%user%%">%%user%% ({lng p="email"})</option>
					<option value="%%wddomain%%">%%wddomain%% ({lng p="wddomain"})</option>
					<option value="%%selfurl%%">%%selfurl%% ({lng p="selfurl"})</option>
					<option value="%%hostname%%">%%hostname%% ({lng p="hostname"})</option>
				</select>
			</div>
		</div>

		<div class="text-end">
			<input class="btn btn-primary" type="submit" value="{lng p="add"}" />
		</div>
	</form>
</fieldset>