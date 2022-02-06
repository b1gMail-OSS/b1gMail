<div class="container">
	<div class="page-header"><h1>{lng p="contact"}</h1></div>

	{if $contactform}
	<div class="row">
		<div class="col-md-6">
			<p>
				{$imprint}
			</p>
		</div>
		<div class="col-md-6">
			<form action="index.php?action=imprint" method="post">
				<input type="hidden" name="do" value="submitContactForm" />

				<div class="panel panel-default">
					<div class="panel-heading panel-title">
						<span class="glyphicon glyphicon-comment"></span>
						{lng p="contactform"}
					</div>
					<div class="panel-body">
						{if $success}
						<div class="alert alert-success" role="alert"><strong>{lng p="thankyou"}.</strong> {lng p="cform_sent"}</div>
						{else}
					
						{if $errorMsg}<div class="alert alert-danger" role="alert"><strong>{lng p="error"}:</strong> {$errorMsg}</div>{/if}

						{if $contactform_name}<div class="form-group">
							<label class="control-label" for="name">
								{lng p="name"}
								<span class="required">{lng p="required"}</span>
							</label>
							<div class="input-group">
								<span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
								<input type="text" class="form-control" required="true" name="name" id="name" value="{text value=$smarty.post.name allowEmpty=true}" />
							</div>
						</div>{/if}
						<div class="form-group">
							<label class="control-label" for="email">
								{lng p="email"}
								<span class="required">{lng p="required"}</span>
							</label>
							<div class="input-group">
								<span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
								<input type="text" class="form-control" required="true" name="email" id="email" value="{text value=$smarty.post.email allowEmpty=true}" />
							</div>
						</div>
						{if $contactform_subject}<div class="form-group">
							<label class="control-label" for="subject">
								{lng p="subject"}
								<span class="required">{lng p="required"}</span>
							</label>
							<select class="form-control" id="subject" name="subject">
								<option value="">--- {lng p="pleasechose"} ---</option>
								{foreach from=$contactform_subjects item=subject}
								<option value="{text value=$subject}"{if $smarty.post.subject==$subject} selected="selected"{/if}>{text value=$subject}</option>
								{/foreach}
							</select>
						</div>{/if}
						<div class="form-group">
							<label class="control-label" for="text">
								{lng p="message"}
								<span class="required">{lng p="required"}</span>
							</label>
							<textarea class="form-control" name="text" id="text" rows="6" required="true">{text value=$smarty.post.text allowEmpty=true}</textarea>
						</div>
						<div class="row">
							{if $captchaInfo.hasOwnInput}
							<div class="col-md-12">
								<div class="form-group" id="captchaContainer">
									<label class="control-label">
										{lng p="safecode"}
										<span class="required">{lng p="required"}</span>
									</label>
									{$captchaHTML}
								</div>
							</div>
							{else}
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label" for="safecode">
										{lng p="safecode"}
										<span class="required">{lng p="required"}</span>
									</label>
									<input type="text" class="form-control" required="true" name="safecode" id="safecode" />
								</div>
							</div>
							<div class="col-md-6" id="captchaContainer">
								{$captchaHTML}
							</div>
							{/if}
						</div>

						<button type="submit" class="btn btn-success">{lng p="submit"}</button>
						{/if}
					</div>
				</div>

				{if $invalidFields}<script>
				<!--
					$(document).ready(function() {ldelim}
					{foreach from=$invalidFields item=field}
					markFieldAsInvalid('{$field}');
					{/foreach}
					{rdelim});
				//-->
				</script>{/if}
			</form>
		</div>
	</div>
	{else}
	<p>
		{$imprint}
	</p>
	{/if}
</div>
