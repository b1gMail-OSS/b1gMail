<fieldset>
	<legend>{lng p="trash"}</legend>

	<div class="alert alert-warning">{lng p="undowarn"}</div>

	<div id="form">
		<p>{lng p="trash_desc"}</p>

		<div class="mb-3 row">
			<div class="col-sm-12">
				<label class="form-check">
					{foreach from=$groups item=group key=groupID}
					<input class="form-check-input" type="checkbox" name="groups[{$groupID}]" id="group_{$groupID}" checked="checked">
					<span class="form-check-label">{text value=$group.title}</span>
					{/foreach}
				</label>
			</div>
		</div>

		<p>{lng p="trash_only"}</p>

		<div class="mb-3 row">
			<div class="col-sm-12">
				<div class="input-group mb-2">
                        <span class="input-group-text">
                        	<input class="form-check-input m-0" type="checkbox" id="daysOnly" name="daysOnly" checked="checked">
                        </span>
					<span class="input-group-text">{lng p="trash_daysonly"}</span>
					<input type="text" class="form-control" name="days" id="days" value="30">
					<span class="input-group-text">{lng p="days"}</span>
				</div>
			</div>
		</div>
		<div class="mb-3 row">
			<div class="col-sm-12">
				<div class="input-group mb-2">
                        <span class="input-group-text">
                        	<input class="form-check-input m-0" type="checkbox" id="sizesOnly" name="sizesOnly">
                        </span>
					<span class="input-group-text">{lng p="trash_sizesonly"}</span>
					<input type="text" class="form-control" name="size" id="size" value="512">
					<span class="input-group-text">KB</span>
				</div>
			</div>
		</div>

		<div style="float: right;"><input class="btn btn-sm btn-warning" type="button" value="{lng p="execute"}" onclick="trashExec()" /></div>
		<div style="float: right;"><input type="text" class="form-control form-control-sm" name="perpage" id="perpage" value="50" size="5" />&nbsp; </div>
		<div style="float: right;">{lng p="opsperpage"}&nbsp; </div>
	</div>
</fieldset>
