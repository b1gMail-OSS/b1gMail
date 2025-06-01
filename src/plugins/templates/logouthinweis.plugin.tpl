<div class="jumbotron splash" style="background-image: url({$tpldir}images/nli/{$templatePrefs.splashImage});">
	<div class="container">
		<div class="panel panel-primary login">
			<div class="panel-heading">
				<i class="glyphicon glyphicon-exclamation-sign"></i> {$title}
			</div>
			<div class="panel-body">
				{$msg}
				<br /><br />
				<div class="form-group">
					<button type="button" class="btn btn-success" onclick="document.location.href='{$backLink}';">
						{lng p="go_to_mailbox"}
					</button>	
				</div>
			</div>
		</div>
	</div>
</div>