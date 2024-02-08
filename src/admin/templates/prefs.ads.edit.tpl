<fieldset>
	<legend>{lng p="edit"}</legend>
	
	<form action="prefs.ads.php?do=edit&save=true&id={$ad.id}&sid={$sid}" method="post" onsubmit="spin(this);">
		<div class="mb-3 row">
			<div class="col-sm-12">
				<textarea name="code" id="code" class="form-control" style="font-family:courier;">{$ad.code}</textarea>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="category"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="category" value="{if isset($ad.category)}{text value=$ad.category allowEmpty=true}{/if}" placeholder="{lng p="category"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="weight"}</label>
			<div class="col-sm-10">
				<div class="input-group mb-2">
					<input type="text" class="form-control" name="weight" value="{$ad.weight}" placeholder="{lng p="weight"}">
					<span class="input-group-text">%</span>
				</div>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-check-label">{lng p="paused"}</label>
			<div class="col-sm-10">
				<label class="form-check">
					<input class="form-check-input" type="checkbox" name="paused"{if $ad.paused} checked="checked"{/if}>
				</label>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="comment"}</label>
			<div class="col-sm-10">
				<textarea class="form-control" name="comments" placeholder="{lng p="comment"}">{text allowEmpty=true value=$ad.comments}</textarea>
			</div>
		</div>

		<div class="text-end">
			<input class="btn btn-primary" type="submit" value="{lng p="save"}" />
		</div>
	</form>
</fieldset>
