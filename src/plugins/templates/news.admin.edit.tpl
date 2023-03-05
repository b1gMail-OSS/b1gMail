<fieldset>
	<legend>{lng p="edit"}</legend>
	
	<form action="{$pageURL}&action=news&do=edit&id={$news.newsid}&sid={$sid}" method="post" onsubmit="EBID('title').focus();if(EBID('title').value.length<2) return(false);editor.submit();spin(this)">
		<input type="hidden" name="save" value="true" />

		<div class="row">
			<div class="col-md-8">
				<div class="mb-3 row">
					<label class="col-sm-2 col-form-label">{lng p="title"}</label>
					<div class="col-sm-10">
						<input type="text" class="form-control" name="title" id="title" value="{if isset($news.title)}{text value=$news.title}{/if}" placeholder="{lng p="title"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-2 col-form-label">{lng p="type"}</label>
					<div class="col-sm-10">
						<select name="loggedin" class="form-select">
							<option value="nli"{if $news.loggedin=='nli'} selected="selected"{/if}>{lng p="nli"}</option>
							<option value="li"{if $news.loggedin=='li'} selected="selected"{/if}>{lng p="li"}</option>
							<option value="both"{if $news.loggedin=='both'} selected="selected"{/if}>{lng p="both"}</option>
						</select>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="groups"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="all_groups" id="all_groups"{if $news.groups=='*'} checked="checked"{/if}>
							<span class="form-check-label">{lng p="all"}</span>
						</label>
						{foreach from=$groups item=group}
							<label class="form-check">
								<input class="form-check-input" type="checkbox" name="groups[]" value="{$group.id}" id="group_{$group.id}" onclick="if(this.checked) EBID('all_groups').checked=false;"{if $group.checked} checked="checked"{/if}>
								<span class="form-check-label">{text value=$group.title}</span>
							</label>
						{/foreach}
					</div>
				</div>
			</div>
		</div>
		<div class="mb-3 row">
			<div class="col-sm-12">
				<textarea name="text" id="text" class="plainTextArea" style="width:100%;height:180px;">{text value=$news.text}</textarea>
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
			<input class="btn btn-primary" type="submit" value=" {lng p="save"} " />
		</div>
	</form>
</fieldset>
