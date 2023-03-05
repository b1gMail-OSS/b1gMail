<fieldset>
	<legend>{lng p="news_news"}</legend>

	<div class="card">
		<div class="table-responsive">
			<table class="table table-vcenter table-striped">
				<thead>
				<tr>
					<th>{lng p="title"}</th>
					<th style="width: 150px;">{lng p="type"}</th>
					<th style="width: 55px;">&nbsp;</th>
				</tr>
				</thead>
				<tbody>
				{foreach from=$news item=item}
					{cycle name=class values="td1,td2" assign=class}
					<tr class="{$class}">
						<td>
							{text value=$item.title cut=55}<br />
							<small>{date timestamp=$item.date dayonly=true}</small>
						</td>
						<td>{lng p=$item.loggedin}</td>
						<td class="text-nowrap">
							<div class="btn-group btn-group-sm">
								<a href="{$pageURL}&action=news&do=edit&id={$item.newsid}&sid={$sid}" title="{lng p="edit"}" class="btn btn-sm"><i class="fa-regular fa-pen-to-square"></i></a>
								<a href="{$pageURL}&action=news&delete={$item.newsid}&sid={$sid}" onclick="return confirm('{lng p="realdel"}');" title="{lng p="delete"}" class="btn btn-sm"><i class="fa-regular fa-trash-can"></i></a>
							</div>
						</td>
					</tr>
				{/foreach}
				</tbody>
			</table>
		</div>
	</div>
</fieldset>

<fieldset>
	<legend>{lng p="news_addnews"}</legend>

	<form action="{$pageURL}&action=news&add=true&sid={$sid}" method="post" onsubmit="EBID('title').focus();if(EBID('title').value.length<2) return(false);editor.submit();spin(this)">
		<div class="row">
			<div class="col-md-8">
				<div class="mb-3 row">
					<label class="col-sm-2 col-form-label">{lng p="title"}</label>
					<div class="col-sm-10">
						<input type="text" class="form-control" name="title" id="title" value="" placeholder="{lng p="title"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-2 col-form-label">{lng p="type"}</label>
					<div class="col-sm-10">
						<select name="loggedin" class="form-select">
							<option value="nli">{lng p="nli"}</option>
							<option value="li">{lng p="li"}</option>
							<option value="both">{lng p="both"}</option>
						</select>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="groups"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="all_groups" id="all_groups" checked="checked">
							<span class="form-check-label">{lng p="all"}</span>
						</label>
						{foreach from=$groups item=group}
							<label class="form-check">
								<input class="form-check-input" type="checkbox" name="groups[]" value="{$group.id}" id="group_{$group.id}" onclick="if(this.checked) EBID('all_groups').checked=false;">
								<span class="form-check-label">{text value=$group.title}</span>
							</label>
						{/foreach}
					</div>
				</div>
			</div>
		</div>
		<div class="mb-3 row">
			<div class="col-sm-12">
				<textarea name="text" id="text" class="plainTextArea" style="width:100%;height:180px;"></textarea>
				<script src="../clientlib/wysiwyg.js?{fileDateSig file="../../clientlib/wysiwyg.js"}"></script>
				<script type="text/javascript" src="../clientlib/ckeditor/ckeditor.js?{fileDateSig file="../../clientlib/ckeditor/ckeditor.js"}"></script>
				<script>
					<!--
					var editor = new htmlEditor('text');
					editor.init();
					registerLoadAction('editor.start()');
					//-->
				</script>
			</div>
		</div>

		<div class="text-end">
			<input class="btn btn-primary" type="submit" value=" {lng p="news_addnews"} " />
		</div>
	</form>
</fieldset>
