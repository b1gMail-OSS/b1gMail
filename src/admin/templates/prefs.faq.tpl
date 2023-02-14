<fieldset>
	<legend>{lng p="faq"}</legend>

	<form action="prefs.faq.php?sid={$sid}" method="post" name="f1" onsubmit="spin(this)">
		<div class="card">
			<div class="table-responsive">
				<table class="table table-vcenter table-striped">
					<thead>
					<tr>
						<th style="width 25px; text-align:center;"><a href="javascript:invertSelection(document.forms.f1,'faq_');"><img src="{$tpldir}images/dot.png" border="0" alt="" width="10" height="8" /></a></th>
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
							<td class="text-center"><input type="checkbox" name="faq_{$faq.id}" /></td>
							<td><a href="prefs.faq.php?do=edit&id={$faq.id}&sid={$sid}">{$faq.frage}</a><br /><small>{lng p="requires"}: {if $faq.required}{$requirements[$faq.required]}{else}-{/if}</small></td>
							<td>{text value=$faq.lang}</td>
							<td>{text value=$faq.typ}</td>
							<td class="text-nowrap">
								<div class="btn-group btn-group-sm">
									<a href="prefs.faq.php?do=edit&id={$faq.id}&sid={$sid}" class="btn btn-sm"><i class="fa-regular fa-pen-to-square"></i></a>
									<a href="prefs.faq.php?delete={$faq.id}&sid={$sid}" onclick="return confirm('{lng p="realdel"}');" class="btn btn-sm"><i class="fa-regular fa-trash-can"></i></a>
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
	<legend>{lng p="addfaq"}</legend>

	<form action="prefs.faq.php?add=true&sid={$sid}" method="post" onsubmit="editor.submit();spin(this);">
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