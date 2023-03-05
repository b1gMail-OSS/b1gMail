<form action="prefs.email.php?action=antispam&save=true&sid={$sid}" method="post" onsubmit="spin(this)">
	<div class="row">
		<div class="col-md-6">
			<fieldset>
				<legend>{lng p="dnsbl"}</legend>

				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="enable"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="spamcheck"{if $bm_prefs.spamcheck=='yes'} checked="checked"{/if}>
						</label>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="dnsblservers"}</label>
					<div class="col-sm-8">
						<textarea class="form-control" name="dnsbl" placeholder="{lng p="dnsblservers"}">{text value=$bm_prefs.dnsbl allowEmpty=true}</textarea>
						<small>{lng p="sepby"}</small>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="dnsblreq"}</label>
					<div class="col-sm-8">
						<input type="number" class="form-control" name="dnsbl_requiredservers" value="{$bm_prefs.dnsbl_requiredservers}" placeholder="{lng p="dnsblreq"}">
					</div>
				</div>
			</fieldset>
		</div>
		<div class="col-md-6">
			<fieldset>
				<legend>{lng p="bayes"}</legend>

				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="enable"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="use_bayes"{if $bm_prefs.use_bayes=='yes'} checked="checked"{/if}>
						</label>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="bayesmode"}</label>
					<div class="col-sm-8">
						<select name="bayes_mode" class="form-select">
							<option value="local"{if $bm_prefs.bayes_mode=='local'} selected="selected"{/if}>{lng p="bayeslocal"}</option>
							<option value="global"{if $bm_prefs.bayes_mode=='global'} selected="selected"{/if}>{lng p="bayesglobal"}</option>
						</select>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="bayesdb"}</label>
					<div class="col-sm-8">
						<div class="input-group">
							<span class="input-group-text">{$bayesWordCount} {lng p="entries"}</span>
							<input{if $bayesWordCount==0} disabled="disabled"{/if} class="btn" type="button" value=" {lng p="reset"} " onclick="if(confirm('{lng p="bayesresetq"}')) document.location.href='prefs.email.php?action=antispam&resetBayesDB=true&sid={$sid}';" />
						</div>
					</div>
				</div>
			</fieldset>
		</div>
	</div>

	<div class="text-end">
		<input class="btn btn-primary" type="submit" value="{lng p="save"}" />
	</div>
</form>
