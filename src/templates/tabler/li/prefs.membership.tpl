<div class="bm-prefs-page bm-prefs-page-membership">
<div id="contentHeader" class="contentHeader bm-organizer-header bm-prefs-header">
	<div class="left">
		<i class="ti ti-id icon icon-sm" aria-hidden="true"></i>
		{lng p="membership"}
	</div>
</div>

<div class="scrollContainer bm-prefs-body"><div class="pad bm-prefs-form-pad">
	<div class="row row-cards">

		<div class="col-12">
			<div class="card">
				<div class="card-header">
					<h3 class="card-title">{lng p="changepw"}</h3>
				</div>
				<form action="{sessionurl file='prefs.php' params='action=membership&do=changePW'}" method="post">
					{csrffield}
					<div class="card-body">
						{if isset($errorStep)}
						<div class="alert alert-danger" role="alert">{$errorInfo}</div>
						{/if}
						<div class="mb-3 row">
							<label class="col-md-3 col-form-label" for="pass1">{lng p="password"}</label>
							<div class="col-md-9 col-lg-5">
								<input type="password" class="form-control" name="pass1" id="pass1" value="" autocomplete="new-password" />
							</div>
						</div>
						<div class="row">
							<label class="col-md-3 col-form-label" for="pass2">{lng p="repeat"}</label>
							<div class="col-md-9 col-lg-5">
								<input type="password" class="form-control" name="pass2" id="pass2" value="" autocomplete="new-password" />
							</div>
						</div>
					</div>
					<div class="card-footer">
						<button type="submit" class="btn btn-primary">{lng p="save"}</button>
						<button type="reset" class="btn">{lng p="reset"}</button>
					</div>
				</form>
			</div>
		</div>

		<div class="col-12">
			<div class="card">
				<div class="card-header">
					<h3 class="card-title">{lng p="accbalance"}</h3>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-3 col-form-label">{lng p="accbalance"}</div>
						<div class="col-md-9 col-lg-5 col-form-label">{$accBalance} {lng p="credits"}</div>
					</div>
				</div>
				<div class="card-footer">
					{if $allowCharge}<a class="btn btn-primary" href="{sessionurl file='prefs.php' params='action=membership&do=chargeAccount'}">{lng p="charge"}</a>{/if}
					<button type="button" class="btn" onclick="showStatement()">{lng p="statement"}</button>
				</div>
			</div>
		</div>

		{if $workgroups}
		<div class="col-12">
			<div class="card">
				<div class="card-header">
					<h3 class="card-title">{lng p="wgmembership"}</h3>
				</div>
				<div class="table-responsive">
					<table class="table table-vcenter card-table">
						<thead>
							<tr>
								<th>{lng p="workgroup"}</th>
								<th>{lng p="email"}</th>
							</tr>
						</thead>
						{foreach from=$workgroups item=workgroup}
						<tbody>
							<tr>
								<td>
									<button type="button" class="btn btn-ghost-secondary btn-icon btn-sm me-1" onclick="toggleGroup({$workgroup.id});" aria-expanded="false" aria-controls="group_{$workgroup.id}">
										<i class="ti ti-chevron-right icon icon-sm" id="groupImage_{$workgroup.id}" aria-hidden="true"></i>
									</button>
									<i class="ti ti-users icon icon-sm text-secondary" aria-hidden="true"></i>
									{text value=$workgroup.title}
									<span class="text-secondary">({$workgroup.memberCount})</span>
								</td>
								<td>
									<a href="{sessionurl file='email.compose.php' params="to={$workgroup.email}"}">{text value=$workgroup.email}</a>
								</td>
							</tr>
						</tbody>
						<tbody id="group_{$workgroup.id}" class="bm-prefs-wg-members" style="display:none;">
							{foreach from=$workgroup.members item=member}
							<tr>
								<td class="bm-prefs-wg-member">
									<i class="ti ti-user icon icon-sm text-secondary" aria-hidden="true"></i>
									{text value=$member.nachname}, {text value=$member.vorname}
								</td>
								<td>
									<a href="{sessionurl file='email.compose.php' params="to={$member.email}"}">{$member.email}</a>
								</td>
							</tr>
							{/foreach}
						</tbody>
						{/foreach}
					</table>
				</div>
			</div>
		</div>
		{/if}

		{if $regDate||$allowCancel}
		<div class="col-12">
			<div class="card">
				<div class="card-header">
					<h3 class="card-title">{lng p="membership"}</h3>
				</div>
				{if $regDate}
				<div class="card-body">
					<div class="row">
						<div class="col-md-3 col-form-label">{lng p="membersince"}</div>
						<div class="col-md-9 col-lg-5 col-form-label">{date timestamp=$regDate dayonly=true}</div>
					</div>
				</div>
				{/if}
				{if $allowCancel}
				<div class="card-footer">
					<a class="btn btn-outline-danger" href="{sessionurl file='prefs.php' params='action=membership&do=cancelAccount'}">{lng p="cancelmembership"}</a>
				</div>
				{/if}
			</div>
		</div>
		{/if}

	</div>
</div></div>
</div>
