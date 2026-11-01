<fieldset>
	<legend>{lng p="banners"}</legend>

	<form action="{sessionurl file='prefs.ads.php'}" method="post" name="f1" onsubmit="spin(this)">
		{csrffield}
		<div class="card">
			<div class="table-responsive">
				<table class="table table-vcenter table-striped">
					<thead>
					<tr>
						<th class="text-center" style="width:3rem;"><a href="javascript:invertSelection(document.forms.f1,'ad_');">{acpicon file="dot.png" size="sm"}</a></th>
						<th>{lng p="banner"}</th>
						<th width="75">{lng p="category"}</th>
						<th width="72">{lng p="weight"}</th>
						<th width="45">{lng p="views"}</th>
						<th width="100">&nbsp;</th>
					</tr>
					</thead>
					<tbody>
					{foreach from=$ads item=ad}
						{cycle name=class values="td1,td2" assign=class}
						<tr class="{$class}">
							<td class="text-center"><label class="form-check d-inline-block mb-0"><input class="form-check-input" type="checkbox" name="ad_{$ad.id}" /></label></td>
							<td>{$ad.code}</td>
							<td>{text value=$ad.category cut=10}</td>
							<td>{$ad.weight}%</td>
							<td>{$ad.views}</td>
							<td class="text-nowrap">
								<div class="btn-group btn-group-sm">
									<a href="{sessionurl file='prefs.ads.php' params="{if !$ad.paused}de{/if}activate={$ad.id}"}" class="btn btn-sm">{if !$ad.paused}<i class="fa-regular fa-square-check" title="{if $ad.paused}{lng p="continue"}{else}{lng p="pause"}{/if}"></i>{else}<i class="fa-regular fa-square" title="{if $ad.paused}{lng p="continue"}{else}{lng p="pause"}{/if}"></i>{/if}</a>
									<a href="{sessionurl file='prefs.ads.php' params="reset={$ad.id}"}" onclick="return confirm('{lng p="reallyresetstats"}');" title="{lng p="resetstats"}" class="btn btn-sm"><i class="fa-solid fa-arrow-trend-down"></i></a>
									<a href="{sessionurl file='prefs.ads.php' params="do=edit&id={$ad.id}"}" class="btn btn-sm"><i class="fa-regular fa-pen-to-square"></i></a>
									<a href="{sessionurl file='prefs.ads.php' params="delete={$ad.id}"}" onclick="return confirm('{lng p="realdel"}');" class="btn btn-sm"><i class="fa-regular fa-trash-can"></i></a>
								</div>
							</td>
						</tr>
					{/foreach}
					</tbody>
				</table>
			</div>
			<div class="card-footer">
				<div class="input-group input-group-sm bm-mass-action">
					<span class="input-group-text">{lng p="action"}</span>
					<select name="massAction" class="form-select form-select-sm">
							<option value="-">------------</option>
							<optgroup label="{lng p="actions"}">
								<option value="pause">{lng p="pause"}</option>
								<option value="continue">{lng p="continue"}</option>
								<option value="delete">{lng p="delete"}</option>
							</optgroup>
						</select>
					<button type="submit" name="executeMassAction" class="btn btn-dark-lt" value="1">{lng p="execute"}</button>
				</div>
			</div>
		</div>
	</form>
</fieldset>

<fieldset>
	<legend>{lng p="addbanner"}</legend>

	<form action="{sessionurl file='prefs.ads.php' params="add=true"}" method="post" onsubmit="spin(this);">
		{csrffield}
		<div class="mb-3 row">
			<div class="col-sm-12">
				<textarea name="code" id="code" class="form-control" style="font-family:courier;"></textarea>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="category"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="category" value="" placeholder="{lng p="category"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="weight"}</label>
			<div class="col-sm-10">
				<div class="input-group mb-2">
					<input type="text" class="form-control" name="weight" value="" placeholder="{lng p="weight"}">
					<span class="input-group-text">%</span>
				</div>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-check-label">{lng p="paused"}</label>
			<div class="col-sm-10">
				<label class="form-check">
					<input class="form-check-input" type="checkbox" name="paused">
				</label>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="comment"}</label>
			<div class="col-sm-10">
				<textarea class="form-control" name="comments" placeholder="{lng p="comment"}"></textarea>
			</div>
		</div>

		<div class="text-end">
			<input class="btn btn-primary" type="submit" value="{lng p="add"}" />
		</div>
	</form>
</fieldset>