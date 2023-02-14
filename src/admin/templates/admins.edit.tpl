<form action="admins.php?action=admins&do=edit&id={$admin.adminid}&save=true&sid={$sid}" method="post" onsubmit="spin(this)">

	<div class="row">
		<div class="col-md-6">
			<fieldset>
				<legend>{lng p="editadmin"}: {text value=$admin.username}</legend>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="username"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="username" name="username" value="{if isset($admin.username)}{text value=$admin.username}{/if}" placeholder="{lng p="username"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="firstname"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="firstname" name="firstname" value="{if isset($admin.firstname)}{text value=$admin.firstname allowEmpty=true}{/if}" placeholder="{lng p="firstname"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="lastname"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="lastname" name="lastname" value="{if isset($admin.lastname)}{text value=$admin.lastname allowEmpty=true}{/if}" placeholder="{lng p="lastname"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="type"}</label>
					<div class="col-sm-8">
						<select class="form-select" name="type"{if $admin.adminid==1} disabled="disabled"{/if} onclick="EBID('perms').style.display=this.value==0?'none':'';">
							<option value="1"{if $admin.type==1} selected="selected"{/if}>{lng p="admin"}</option>
							<option value="0"{if $admin.type==0} selected="selected"{/if}>{lng p="superadmin"}</option>
						</select>
					</div>
				</div>
			</fieldset>
		</div>
		<div class="col-md-6">
			<fieldset>
				<legend>{lng p="password"}</legend>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="newpassword"}</label>
					<div class="col-sm-8">
						<input type="password" class="form-control" id="newpw1" name="newpw1" placeholder="{lng p="newpassword"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="newpassword"} ({lng p="repeat"})</label>
					<div class="col-sm-8">
						<input type="password" class="form-control" id="newpw2" name="newpw2" placeholder="{lng p="newpassword"} ({lng p="repeat"})">
					</div>
				</div>
			</fieldset>
		</div>
	</div>

	<fieldset id="perms" style="display:{if $admin.type==0}none{/if};">
		<legend>{lng p="permissions"}</legend>

		<div class="row">
			<div class="col-md-6">
				<div class="mb-3">
					<label class="form-label">{lng p="areas"}</div>

				<div>
					{foreach from=$permsTable item=permTitle key=permName}
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="perms[{$permName}]" value="1" id="perm_{$permName}"{if $admin.perms.$permName} checked="checked"{/if}>
							<span class="form-check-label">{$permTitle}</span>
						</label>
					{/foreach}
				</div>
			</div>
			<div class="col-md-6">
				<div class="mb-3 ">
					<label class="form-label">{lng p="plugins"}</div>
				<div>
					{foreach from=$pluginList item=pluginTitle key=pluginName}
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="perms[plugins][{$pluginName}]" value="1" id="plugin_{$pluginName}"{if $admin.perms.plugins.$pluginName} checked="checked"{/if}>
							<span class="form-check-label">{text value=$pluginTitle}</span>
						</label>
					{/foreach}
				</div>
			</div>
		</div>
	</fieldset>

	<div class="text-end">
		<input class="btn btn-primary" type="submit" value="{lng p="save"}" />
	</div>
</form>
