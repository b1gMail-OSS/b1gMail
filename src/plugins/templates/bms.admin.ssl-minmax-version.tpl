<div class="input-group">
	<span class="input-group-text">{lng p="bms_ssl_min"}</span>
	<select class="form-select" name="ssl_min_version">
		<option value="0"{if $bms_prefs.ssl_min_version==0} selected="selected"{/if}>-</option>
		<option value="768"{if $bms_prefs.ssl_min_version==768} selected="selected"{/if}>SSL3</option>
		<option value="769"{if $bms_prefs.ssl_min_version==769} selected="selected"{/if}>TLSv1</option>
		<option value="770"{if $bms_prefs.ssl_min_version==770} selected="selected"{/if}>TLSv1.1</option>
		<option value="771"{if $bms_prefs.ssl_min_version==771} selected="selected"{/if}>TLSv1.2</option>
		<option value="772"{if $bms_prefs.ssl_min_version==772} selected="selected"{/if}>TLSv1.3</option>
	</select>
	<span class="input-group-text">/</span>
	<span class="input-group-text">{lng p="bms_ssl_max"}</span>
	<select class="form-select" name="ssl_max_version">
		<option value="0"{if $bms_prefs.ssl_max_version==0} selected="selected"{/if}>-</option>
		<option value="768"{if $bms_prefs.ssl_max_version==768} selected="selected"{/if}>SSL3</option>
		<option value="769"{if $bms_prefs.ssl_max_version==769} selected="selected"{/if}>TLSv1</option>
		<option value="770"{if $bms_prefs.ssl_max_version==770} selected="selected"{/if}>TLSv1.1</option>
		<option value="771"{if $bms_prefs.ssl_max_version==771} selected="selected"{/if}>TLSv1.2</option>
		<option value="772"{if $bms_prefs.ssl_max_version==772} selected="selected"{/if}>TLSv1.3</option>
	</select>
</div>
