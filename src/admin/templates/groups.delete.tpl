<form action="groups.php?do=realDelete&sid={$sid}" method="post">
	<fieldset>
		<legend>{lng p="deletegroup"}</legend>

		{lng p="groupdeletedesc"}

		{foreach from=$groupsToDelete item=dGroupTitle key=dGroupID}
			<div class="mb-3 row">
				<label class="col-sm-4 col-form-label">{text value=$dGroupTitle}</label>
				<div class="col-sm-8">
					<select name="groups[{$dGroupID}]" class="form-select">
						{foreach from=$groups key=groupID item=groupTitle}
							<option value="{$groupID}">{text value=$groupTitle}</option>
						{/foreach}
					</select>
				</div>
			</div>
		{/foreach}
	</fieldset>

	<div class="text-end">
		<input class="btn btn-primary" type="submit" value="{lng p="move"} &amp; {lng p="delete"}" />
	</div>
</form>