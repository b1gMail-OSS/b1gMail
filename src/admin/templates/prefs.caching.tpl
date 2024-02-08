<form action="prefs.common.php?action=caching&save=true&sid={$sid}" method="post" onsubmit="spin(this)">
	<div class="row">
		<div class="col-md-6">
			<fieldset>
				<legend>{lng p="cachemanager"}</legend>

				<div class="mb-3">
					<div class="form-selectgroup form-selectgroup-boxes d-flex flex-column">
						<label class="form-selectgroup-item flex-fill">
							<input class="form-selectgroup-input" type="radio" id="cache_disable" name="cache_type" value="0"{if $bm_prefs.cache_type==0} checked="checked"{/if} onchange="cachePrefs()">
							<div class="form-selectgroup-label d-flex align-items-center p-3">
								<div class="me-3"><span class="form-selectgroup-check"></span></div>
								<div>{lng p="ce_disable"}<br /><small>{lng p="ce_disable_desc"}</small></div>
							</div>
						</label>
						<label class="form-selectgroup-item flex-fill">
							<input class="form-selectgroup-input" type="radio" id="cache_b1gmail" name="cache_type" value="1"{if $bm_prefs.cache_type==1} checked="checked"{/if} onchange="cachePrefs()">
							<div class="form-selectgroup-label d-flex align-items-center p-3">
								<div class="me-3"><span class="form-selectgroup-check"></span></div>
								<div>{lng p="ce_b1gmail"}<br /><small>{lng p="ce_b1gmail_desc"}</small></div>
							</div>
						</label>
						<label class="form-selectgroup-item flex-fill">
							<input class="form-selectgroup-input" type="radio" id="cache_memcache" name="cache_type" value="2"{if $bm_prefs.cache_type==2} checked="checked"{/if}{if !$memcache} disabled="disabled"{/if} onchange="cachePrefs()">
							<div class="form-selectgroup-label d-flex align-items-center p-3">
								<div class="me-3"><span class="form-selectgroup-check"></span></div>
								<div>{lng p="ce_memcache"}<br /><small>{lng p="ce_memcache_desc"}</small></div>
							</div>
						</label>
					</div>
				</div>
			</fieldset>
		</div>
		<div class="col-md-6">
			<fieldset>
				<legend>{lng p="prefs"}</legend>
				<div id="prefs_0" style="display:{if $bm_prefs.cache_type!=0}none{/if};">
					<i>({lng p="none"})</i>
				</div>

				<div id="prefs_3" style="display:{if $bm_prefs.cache_type==0}none{/if};">
					<div class="mb-3 row">
						<label class="col-sm-4 col-form-check-label">{lng p="parseonly"}</label>
						<div class="col-sm-8">
							<label class="form-check">
								<input class="form-check-input" type="checkbox" name="cache_parseonly"{if $bm_prefs.cache_parseonly=='yes'} checked="checked"{/if}>
							</label>
						</div>
					</div>
				</div>

				<div id="prefs_1" style="display:{if $bm_prefs.cache_type!=1}none{/if};">
					<div class="mb-3 row">
						<label class="col-sm-4 col-form-label">{lng p="cachesize"}</label>
						<div class="col-sm-8">
							<div class="input-group mb-2">
								<input type="text" class="form-control" name="filecache_size" value="{$bm_prefs.filecache_size/1024/1024}" placeholder="{lng p="cachesize"}">
								<span class="input-group-text">MB <!--<small>({lng p="inactiveonly"})</small>--></span>
							</div>
						</div>
					</div>
				</div>

				<div id="prefs_2" style="display:{if $bm_prefs.cache_type!=2}none{/if};">
					<div class="mb-3 row">
						<label class="col-sm-4 col-form-check-label">{lng p="persistent"}</label>
						<div class="col-sm-8">
							<label class="form-check">
								<input class="form-check-input" type="checkbox" name="memcache_persistent"{if $bm_prefs.memcache_persistent=='yes'} checked="checked"{/if}>
							</label>
						</div>
					</div>
					<div class="mb-3 row">
						<label class="col-sm-4 col-form-label">{lng p="servers"}</label>
						<div class="col-sm-8">
							<textarea class="form-control" name="memcache_servers">{text value=$bm_prefs.memcache_servers allowEmpty=true}</textarea>
							<small>{lng p="memcachesepby"}</small>
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
