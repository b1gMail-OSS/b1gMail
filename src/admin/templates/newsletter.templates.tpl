<fieldset>
	<legend>{lng p="templates"}</legend>

	<form name="f1" action="newsletter.php?action=templates&sid={$sid}" method="post">
		<div class="card">
			<div class="table-responsive">
				<table class="table table-vcenter table-striped">
					<thead>
					<tr>
						<th width="25" style="text-align:center;"><a href="javascript:invertSelection(document.forms.f1,'tpl_');"><img src="{$tpldir}images/dot.png" border="0" alt="" width="10" height="8" /></a></th>
						<th>{lng p="title"}</th>
						<th>{lng p="subject"}</th>
						<th style="width: 70px;">&nbsp;</th>
					</tr>
					</thead>
					<tbody>
					{foreach from=$templates item=tpl}
						{cycle name=class values="td1,td2" assign=class}
						<tr class="{$class}">
							<td align="center"><input type="checkbox" name="tpl_{$tpl.templateid}" /></td>
							<td>{text value=$tpl.title cut=35}</td>
							<td>{text value=$tpl.subject cut=35}</td>
							<td class="text-nowrap">
								<div class="btn-group btn-group-sm">
									<a href="newsletter.php?action=templates&do=edit&templateID={$tpl.templateid}&sid={$sid}" class="btn btn-sm"><i class="fa-regular fa-pen-to-square"></i></a>
									<a href="newsletter.php?action=templates&delete={$tpl.templateid}&sid={$sid}" onclick="return confirm('{lng p="realdel"}');" class="btn btn-sm"><i class="fa-regular fa-trash-can"></i></a>
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
	<legend>{lng p="addtemplate"}</legend>

	<form name="f1" action="newsletter.php?action=templates&add=true&sid={$sid}" method="post" onsubmit="editor.submit();spin(this);">
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="title"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" id="subject" name="title" value="" placeholder="{lng p="title"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="mode"}</label>
			<div class="col-sm-10">
				<label class="form-check">
					<input class="form-check-input" type="radio" name="mode" value="html" id="mode_html" checked="checked" onclick="if(this.checked) return editor.switchMode('html');">
					<span class="form-check-label">{lng p="htmltext"}</span>
				</label>
				<label class="form-check">
					<input class="form-check-input" type="radio" name="mode" value="text" id="mode_text" onclick="if(this.checked) return editor.switchMode('text');">
					<span class="form-check-label">{lng p="plaintext"}</span>
				</label>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="from"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" id="from" name="from" value="{if isset($from)}{text value=$from}{/if}" placeholder="{lng p="from"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="subject"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" id="subject" name="subject" value="" placeholder="{lng p="subject"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="priority"}</label>
			<div class="col-sm-10">
				<select name="priority" id="priority" class="form-select">
					<option value="1">{lng p="prio_1"}</option>
					<option value="0" selected="selected">{lng p="prio_0"}</option>
					<option value="-1">{lng p="prio_-1"}</option>
				</select>
			</div>
		</div>
		<div class="mb-3 row">
			<div class="col-sm-12">
				<textarea name="emailText" id="emailText" class="plainTextArea" style="width:100%;height:400px;"></textarea>
				<script src="../clientlib/wysiwyg.js?{fileDateSig file="../../clientlib/wysiwyg.js"}"></script>
				<script type="text/javascript" src="../clientlib/ckeditor/ckeditor.js?{fileDateSig file="../../clientlib/ckeditor/ckeditor.js"}"></script>
				<script>
					<!--
					var editor = new htmlEditor('emailText');
					editor.height = 400;
					editor.init();
					registerLoadAction('editor.start()');
					//-->
				</script>
			</div>
		</div>
		<div class="mb-3 row">
			<div class="col-sm-12">
				<select class="form-select" onchange="editor.insertText(this.value);">
					<option value="">-- {lng p="vars"} --</option>
					<option value="%%email%%">%%email%% ({lng p="email"})</option>
					<option value="%%greeting%%">%%greeting%% ({lng p="greeting"})</option>
					<option value="%%salutation%%">%%salutation%% ({lng p="salutation"})</option>
					<option value="%%firstname%%">%%firstname%% ({lng p="firstname"})</option>
					<option value="%%lastname%%">%%lastname%% ({lng p="lastname"})</option>
				</select>
			</div>
		</div>

		<div class="text-end">
			<input class="btn btn-primary" type="submit" value="{lng p="add"}" />
		</div>
	</form>
</fieldset>
