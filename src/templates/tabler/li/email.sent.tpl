<div class="bm-mail-sent-page">
	<div class="alert alert-success bm-mail-sent-success mb-4" role="alert">
		<div class="d-flex align-items-start gap-3">
			<i class="ti ti-circle-check icon icon-lg text-success flex-shrink-0 mt-1" aria-hidden="true"></i>
			<div class="flex-fill min-w-0">
				<h3 class="alert-title mb-2">{lng p="sendmail"}</h3>
				<div class="text-secondary">{lng p="mailsent"}</div>
				<div class="mt-3">
					<a href="email.php?sid={$sid}" class="btn btn-sm btn-ghost-secondary">
						<i class="ti ti-arrow-left icon icon-sm me-1" aria-hidden="true"></i>{lng p="back"}
					</a>
				</div>
			</div>
		</div>
	</div>

	{if $addrMails}
	<div class="card bm-mail-sent-addressbook">
		<div class="card-header">
			<h3 class="card-title mb-0">
				<i class="ti ti-address-book icon icon-sm me-2 text-primary" aria-hidden="true"></i>{lng p="addressbook"}
			</h3>
		</div>
		<div class="card-body">
			<p class="text-secondary mb-4">{lng p="addraddtext"}</p>

			<form action="organizer.addressbook.php?action=quickAdd&sid={$sid}" method="post" onsubmit="return ajaxFormSubmit(this);" class="bm-mail-sent-address-form">
				{foreach from=$addrMails item=item key=i}
				<div class="card bm-mail-sent-contact mb-3">
					<div class="card-header py-2">
						<label class="form-check mb-0" for="addr_{$i}">
							<input type="checkbox" class="form-check-input" name="addr[{$i}][email]" value="{if isset($item.email)}{text value=$item.email}{/if}" id="addr_{$i}" checked="checked" />
							<span class="form-check-label fw-semibold">{text value=$item.email}</span>
						</label>
					</div>
					<div class="card-body">
						<div class="row g-3">
							<div class="col-md-4">
								<label class="form-label" for="addr_{$i}_firstname">{lng p="firstname"}</label>
								<input type="text" class="form-control form-control-sm" name="addr[{$i}][firstname]" id="addr_{$i}_firstname" value="{if isset($item.firstname)}{text value=$item.firstname allowEmpty=true}{/if}" />
							</div>
							<div class="col-md-4">
								<label class="form-label" for="addr_{$i}_lastname">{lng p="surname"}</label>
								<input type="text" class="form-control form-control-sm" name="addr[{$i}][lastname]" id="addr_{$i}_lastname" value="{if isset($item.lastname)}{text value=$item.lastname allowEmpty=true}{/if}" />
							</div>
							<div class="col-md-4">
								<label class="form-label" for="addr_{$i}_company">{lng p="company"}</label>
								<input type="text" class="form-control form-control-sm" name="addr[{$i}][company]" id="addr_{$i}_company" />
							</div>
							{if $groups}
							<div class="col-12">
								<span class="form-label d-block mb-2">{lng p="groupmember"}</span>
								<div class="d-flex flex-wrap gap-3">
									{foreach from=$groups item=group key=groupID}
									<label class="form-check mb-0" for="group_{$i}_{$groupID}">
										<input type="checkbox" class="form-check-input" id="group_{$i}_{$groupID}" name="addr[{$i}][groups][]" value="{$groupID}" />
										<span class="form-check-label">{text value=$group.title cut=18}</span>
									</label>
									{/foreach}
								</div>
							</div>
							{/if}
						</div>
					</div>
				</div>
				{/foreach}

				<div class="d-flex justify-content-end">
					<button type="submit" class="btn btn-primary">
						<i class="ti ti-device-floppy icon icon-sm me-1" aria-hidden="true"></i>{lng p="save"}
					</button>
				</div>
			</form>
		</div>
	</div>
	{/if}
</div>
