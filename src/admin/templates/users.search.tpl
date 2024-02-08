<form action="users.php?action=search&do=search&sid={$sid}" method="post" onsubmit="spin(this)" name="f1">
	<fieldset>
		<legend>{lng p="search"}</legend>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="searchfor"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="q" value="" placeholder="{lng p="searchfor"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="searchin"}</label>
			<div class="col-sm-3">
				<label class="form-check">
					<input class="form-check-input" type="checkbox" name="searchIn[id]" id="searchIn_id" checked="checked">
					<span class="form-check-label">{lng p="id"}</span>
				</label>
				<label class="form-check">
					<input class="form-check-input" type="checkbox" name="searchIn[name]" id="searchIn_name" checked="checked">
					<span class="form-check-label">{lng p="name"}</span>
				</label>
				<label class="form-check">
					<input class="form-check-input" type="checkbox" id="searchIn_all" checked="checked" onchange="invertSelection(document.forms.f1,'searchIn',true,this.checked)">
					<span class="form-check-label">{lng p="all"}</span>
				</label>
			</div>
			<div class="col-sm-3">
				<label class="form-check">
					<input class="form-check-input" type="checkbox" name="searchIn[email]" id="searchIn_email" checked="checked">
					<span class="form-check-label">{lng p="email"}</span>
				</label>
				<label class="form-check">
					<input class="form-check-input" type="checkbox" name="searchIn[altmail]" id="searchIn_altmail" checked="checked">
					<span class="form-check-label">{lng p="altmail"}</span>
				</label>
			</div>
			<div class="col-sm-4">
				<label class="form-check">
					<input class="form-check-input" type="checkbox" name="searchIn[address]" id="searchIn_address" checked="checked">
					<span class="form-check-label">{lng p="address"}</span>
				</label>
				<label class="form-check">
					<input class="form-check-input" type="checkbox" name="searchIn[telfaxmobile]" id="searchIn_telfaxmobile" checked="checked">
					<span class="form-check-label">{lng p="tel"} / {lng p="fax"} / {lng p="cellphone"}</span>
				</label>
        <label class="form-check">
					<input class="form-check-input" type="checkbox" name="searchIn[absendername]" id="searchIn_absendername" checked="checked">
					<span class="form-check-label">{lng p="sendername"}</span>
				</label>
			</div>
		</div>
	</fieldset>

	<div class="text-end">
		<input class="btn btn-primary" type="submit" value="{lng p="search"}" />
	</div>
</form>
