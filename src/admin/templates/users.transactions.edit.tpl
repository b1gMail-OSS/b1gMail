<form action="users.php?do=editTransaction&transactionid={$tx.transactionid}&save=true&sid={$sid}" method="post" onsubmit="spin(this);">
	<fieldset>
		<legend>{lng p="edittransaction"} ({email value=$user.email}, #{$user.id})</legend>

		<fieldset>
			<legend>{lng p="addtransaction"}</legend>

			<div class="mb-3 row">
				<label class="col-sm-2 col-form-label">{lng p="description"}</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" name="description" value="{text value=$tx.description allowEmpty=true}" required="required" placeholder="{lng p="description"}">
				</div>
			</div>
			<div class="mb-3 row">
				<label class="col-sm-2 col-form-label">{lng p="credits"}</label>
				<div class="col-sm-10">
					<input type="number" class="form-control" min="-999999" max="999999" step="1" name="amount" value="{$tx.amount}" placeholder="{lng p="credits"}">
				</div>
			</div>
			<div class="mb-3 row">
				<label class="col-sm-2 col-form-label">{lng p="status"}</label>
				<div class="col-sm-10">
					<select name="status" class="form-select">
						<option value="1"{if $tx.status==1} selected="selected"{/if}>{lng p="booked"}</option>
						<option value="2"{if $tx.status==2} selected="selected"{/if}>{lng p="cancelled"}</option>
					</select>
				</div>
			</div>
		</fieldset>

		<div class="row">
			<div class="col-md-6"><input class="btn btn-primary" type="button" value="&laquo; {lng p="back"}" onclick="document.location.href='users.php?do=transactions&id={$user.id}&sid={$sid}';" /></div>
			<div class="col-md-6 text-end"><input class="btn btn-primary" type="submit" value="{lng p="save"}" /></div>
		</div>
	</fieldset>
</form>