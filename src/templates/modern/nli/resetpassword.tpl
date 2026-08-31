<div class="container">
	<div class="page-header"><h1>{text value=$pageTitle}</h1></div>

	<p>{lng p="resetpwhint"}</p>

	{if isset($errorInfo)}
		<div class="alert alert-danger" role="alert"><strong>{lng p="error"}:</strong> {$errorInfo}</div>
	{/if}

	<form method="post" action="{$resetPasswordUrl}" autocomplete="off" class="form-horizontal" style="max-width:420px;">
		{csrffield}
		<input type="hidden" name="do" value="setPassword" />
		<input type="hidden" name="key" value="{$resetPasswordKey}" />
		<div class="form-group">
			<label class="control-label" for="pass1">{lng p="password"}</label>
			<input type="password" class="form-control" name="pass1" id="pass1" required="required" autocomplete="new-password" autofocus="autofocus" />
		</div>
		<div class="form-group">
			<label class="control-label" for="pass2">{lng p="repeat"}</label>
			<input type="password" class="form-control" name="pass2" id="pass2" required="required" autocomplete="new-password" />
		</div>
		<div class="form-group">
			<button type="submit" class="btn btn-success">{lng p="resetpwsubmit"}</button>
		</div>
	</form>

	<p><a href="{$nliUrlHome}{$sessionUrlSuffix}">&larr; {lng p="back"}</a></p>
</div>
