{if !$haveRelease}
	<fieldset>
		<legend>{lng p="welcome"}</legend>

		<p>{lng p="tbx_welcome1"}</p>
		<p>{lng p="tbx_welcome2"}</p>
	</fieldset>
{/if}

<fieldset>
	<legend>{lng p="versions"}</legend>

	<div class="card">
		<div class="table-responsive">
			<table class="table table-vcenter table-striped">
				<thead>
				<tr>
					<th>{lng p="version"}</th>
					<th style="width: 180px;">{lng p="status"}</th>
					<th style="width: 60px;">&nbsp;</th>
				</tr>
				</thead>
				<tbody>
				{foreach from=$versions item=version key=versionID}
					{cycle name=class values="td1,td2" assign=class}
					<tr class="{$class}">
						<td>{$version.base_version}.{$versionID}</td>
						<td>{if $version.status=='released'}{lng p="released"}{else}{lng p="created"}{/if}</td>
						<td class="text-nowrap">
							{if $version.status=='created'}
								<div class="btn-group btn-group-sm">
									<a href="toolbox.php?do=editVersionConfig&versionid={$versionID}&sid={$sid}" title="{lng p="prefs"}" class="btn btn-sm"><i class="fa-regular fa-pen-to-square"></i></a>
									<a href="toolbox.php?do=release&versionid={$versionID}&sid={$sid}" title="{lng p="release"}" class="btn btn-sm"><i class="fa-solid fa-code-compare"></i></a>
								</div>
							{/if}
						</td>
					</tr>
				{/foreach}
				</tbody>
			</table>
		</div>
	</div>
</fieldset>

<fieldset>
	<legend>{lng p="addversion"}</legend>

	<form action="toolbox.php?do=addVersion&sid={$sid}" method="post" onsubmit="spin(this)">
		<div class="mb-3 row">
			<label class="col-sm-4 col-form-label">{lng p="baseversion"}</label>
			<div class="col-sm-8">
				<div class="form-control-plaintext" style="font-weight: bold;">{$latestVersion}</div>
			</div>
		</div>

		<div class="text-end">
			<input class="btn btn-primary" type="submit" value="{lng p="add"}" />
		</div>
	</form>
</fieldset>
