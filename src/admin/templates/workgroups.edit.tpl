<form method="post" action="workgroups.php?{if !$create}do=edit&id={$group.id}&save=true{else}action=create&create=true{/if}&sid={$sid}" onsubmit="spin(this)">
	<div class="row">
		<div class="col-md-{if $create}12{else}6{/if}">
			<fieldset>
				<legend>{lng p="common"}</legend>

				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="title"}</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="title" value="{if !$create}{text value=$group.title}{/if}" placeholder="{lng p="title"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="email"}</label>
					<div class="col-sm-8">
						<input type="email" class="form-control" name="email" value="{email value=$group.email}" placeholder="{lng p="email"}">
					</div>
				</div>
			</fieldset>
		</div>
		{if !$create}
			<div class="col-md-6">
				<fieldset>
					<legend>{lng p="members"}</legend>

					<div class="card">
						<div class="table-responsive">
							<table class="table table-vcenter table-striped">
								<thead>
								<tr>
									<th>{lng p="email"}</th>
									<th style="width: 28px;">&nbsp;</th>
								</tr>

								{foreach from=$members item=member}
									{cycle values="td1,td2" name="class" assign="class"}
									<tr class="{$class}">
										<td><a href="users.php?do=edit&id={$member.id}&sid={$sid}">{email value=$member.email}</a></td>
										<td><a href="workgroups.php?do=edit&id={$group.id}&deleteMember={$member.id}&sid={$sid}" onclick="return(confirm('{lng p="realdel"}'));" class="btn btn-sm"><i class="fa-regular fa-trash-can"></i></a></td>
									</tr>
								{/foreach}
							</table>
						</div>
					</div>
				</fieldset>

				<fieldset>
					<legend>{lng p="addmember"}</legend>

					<div class="mb-3 row">
						<label class="col-sm-4 col-form-label">{lng p="email"}</label>
						<div class="col-sm-8">
							<div class="input-group mb-2">
							<input type="text" class="form-control" name="userMail" value="" placeholder="{lng p="email"}">
							<input type="submit" class="btn" value="{lng p="add"}" />
							</div>
						</div>
					</div>
				</fieldset>
			</div>
		{/if}
	</div>

	<div class="row">
		<div class="col-md-6">
			<div style="float: left;">{lng p="action"}:&nbsp;</div>
			<div style="float: left;">
				<div class="btn-group btn-group-sm">
					<select name="groupAction" class="form-select form-select-sm">
						<optgroup label="{lng p="actions"}">
							<option value="mailto:{email value=$group.email}">{lng p="sendmail"}</option>
							<option value="workgroups.php?singleAction=delete&singleID={$group.id}&sid={$sid}">{lng p="delete"}</option>
						</optgroup>
					</select>
					<input type="button" name="executeMassAction" value="{lng p="ok"}" onclick="executeAction('groupAction');" class="btn btn-sm btn-dark-lt" />
				</div>
			</div>
		</div>
		<div class="col-md-6 text-end">
			<input class="btn btn-primary" type="submit" value=" {lng p="save"} " />
		</div>
	</div>
</form>