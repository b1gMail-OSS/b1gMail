<form action="index.php?action=login" method="post">
<input type="hidden" name="do" value="login" />
<input type="hidden" name="email_full" value="{text value=$email}" />
<input type="hidden" name="passwordMD5" value="{text value=$password}" />
{if $savelogin}<input type="hidden" name="savelogin" value="true" />{/if}

	<div class="container">
		<div class="page-header"><h1>{lng p="smsvalidation"}</h1></div>

		<p>
			{lng p="smsvalidation_text"}
		</p>

		<p>
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label class="control-label" for="sms_validation_code">{lng p="validationcode"}</label>
						<div class="input-group">
							<span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
							<input type="text" name="sms_validation_code" id="sms_validation_code" class="form-control" required="true" value="" />
						</div>
					</div>
				</div>
			</div>

			<button type="submit" class="btn btn-success">
				<span class="glyphicon glyphicon-ok"></span> {lng p="ok"}
			</button>
		</p>

		{if $enableResend}
		<p>
			<br />

			<div class="alert alert-warning">
				<h4>{lng p="didnotgetcode"}</h4>
				
				<p>
					{$resendText}
				</p>
				
				{if $allowResend}
				<p>
					<input type="submit" class="btn btn-warning" value="{lng p="resendcode"}" name="resendCode" onclick="$('sms_validation_code').val('');" />
				</p>
				{/if}
			</div>
		</p>
		{/if}
	</div>

	{if $smarty.post.sms_validation_code}
	<script>
	<!--
		markFieldAsInvalid('sms_validation_code');
	//-->
	</script>
	{/if}
</form>
