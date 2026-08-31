<div class="bm-organizer-address-detail">
	<div class="bm-organizer-address-detail-body">
		<div class="d-flex align-items-start gap-3 mb-4">
			<span class="avatar avatar-lg bm-organizer-address-avatar" style="background-image: url({if !$contact || $contact.picture==''}{$tpldir}images/li/no_picture.png{else}organizer.addressbook.php?action=addressbookPicture&id={$contact.id}{$sessionUrlSuffix}{/if});"></span>
			<div class="min-w-0">
				<h3 class="mb-1">
					{if !$contact.vorname&&!$contact.nachname&&$contact.firma}
					{text value=$contact.firma}
					{else}
					{text value=$contact.vorname} {text value=$contact.nachname}
					{/if}
				</h3>
				{if ($contact.vorname||$contact.nachname)&&$contact.firma}
				<div class="text-secondary">
					{if $contact.position}{text value=$contact.position}, {/if}{text value=$contact.firma}
				</div>
				{/if}
			</div>
		</div>

		<table class="table table-sm table-borderless bm-organizer-address-detail-table">
		<tbody>
		{if $contact.email}
		<tr>
			<th>{lng p="email"}</th>
			<td><a href="email.compose.php?to={$privEmailTo}{$sessionUrlSuffix}">{text value=$contact.email}</a></td>
		</tr>
		{/if}
		{if $contact.tel}
		<tr>
			<th>{lng p="phone"}</th>
			<td>{text value=$contact.tel}</td>
		</tr>
		{/if}
		{if $contact.fax}
		<tr>
			<th>{lng p="fax"}</th>
			<td>{text value=$contact.fax}</td>
		</tr>
		{/if}
		{if $contact.handy}
		<tr>
			<th>{lng p="mobile"}</th>
			<td>{text value=$contact.handy}</td>
		</tr>
		{/if}
		{if $contact.strassenr||$contact.ort||$contact.plz||$contact.land}
		<tr class="bm-organizer-address-detail-section">
			<th>{lng p="priv"}</th>
			<td>
				{if $contact.strassenr}{text value=$contact.strassenr}<br />{/if}
				{if $contact.ort||$contact.plz}{text value=$contact.plz} {text value=$contact.ort}<br />{/if}
				{if $contact.land}{text value=$contact.land}{/if}
			</td>
		</tr>
		{/if}

		{if $contact.work_email}
		<tr class="bm-organizer-address-detail-section">
			<th>{lng p="email"}</th>
			<td><a href="email.compose.php?to={$workEmailTo}{$sessionUrlSuffix}">{text value=$contact.work_email}</a></td>
		</tr>
		{/if}
		{if $contact.work_tel}
		<tr>
			<th>{lng p="phone"}</th>
			<td>{text value=$contact.work_tel}</td>
		</tr>
		{/if}
		{if $contact.work_fax}
		<tr>
			<th>{lng p="fax"}</th>
			<td>{text value=$contact.work_fax}</td>
		</tr>
		{/if}
		{if $contact.work_handy}
		<tr>
			<th>{lng p="mobile"}</th>
			<td>{text value=$contact.work_handy}</td>
		</tr>
		{/if}
		{if $contact.work_strassenr||$contact.work_ort||$contact.work_plz||$contact.work_land}
		<tr>
			<th>{lng p="work"}</th>
			<td>
				{if $contact.work_strassenr}{text value=$contact.work_strassenr}<br />{/if}
				{if $contact.work_ort||$contact.work_plz}{text value=$contact.work_plz} {text value=$contact.work_ort}<br />{/if}
				{if $contact.work_land}{text value=$contact.work_land}{/if}
			</td>
		</tr>
		{/if}

		{if $contact.geburtsdatum}
		<tr class="bm-organizer-address-detail-section">
			<th>{lng p="birthday"}</th>
			<td>{date timestamp=$contact.geburtsdatum format="%d. %B %Y"}</td>
		</tr>
		{/if}
		{if $contact.web}
		<tr>
			<th>{lng p="web"}</th>
			<td><a target="_blank" rel="noopener noreferrer" href="{derefurl url=$contact.web}">{text value=$contact.web}</a></td>
		</tr>
		{/if}
		{if $contact.kommentar}
		<tr>
			<th>{lng p="notes"}</th>
			<td class="bm-organizer-address-notes">{$contact.kommentar}</td>
		</tr>
		{/if}
		</tbody>
		</table>
	</div>

	<div class="contentFooter bm-organizer-footer">
		<div class="right bm-organizer-footer-tools">
			<button type="button" class="btn btn-sm btn-primary" onclick="document.location.href='{sessionurl file='organizer.addressbook.php' params="action=editContact&id={$contact.id}"|escape:'javascript'}';">
				<i class="ti ti-pencil icon icon-sm me-1" aria-hidden="true"></i>
				{lng p="edit"}
			</button>
		</div>
	</div>
</div>
