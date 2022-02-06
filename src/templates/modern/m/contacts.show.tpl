<div data-role="header" data-position="fixed">
	<a href="contacts.php?sid={$sid}" data-icon="arrow-l" data-direction="reverse" data-transition="slide">{lng p="contacts"}</a>
	<h1>{$pageTitle}</h1>
</div>

<div data-role="content">
	<table id="contactHeading">
		<tr>
			<th class="picture">
				<div style="background-image: url({if !$contact || $contact.picture==''}{$selfurl}{$_tpldir}images/li/no_picture.png{else}contacts.php?action=addressbookPicture&id={$contact.id}&sid={$sid}{/if});"></div>
			</th>
			<td>
				<h2>
					{if !$contact.vorname&&!$contact.nachname&&$contact.firma}
					{text value=$contact.firma}
					{else}
					{text value=$contact.vorname} {text value=$contact.nachname}
					{/if}
				</h2>
			</td>
		</tr>
	</table>

	<ul data-role="listview" data-inset="true">
		{if $contact.email}<li><a href="email.php?action=compose&to={$privEmailTo}&sid={$sid}">{text value=$contact.email}</a></li>{/if}
		{if $contact.tel}<li><a href="tel:{text value=$contact.tel}">{lng p="phone"}: {text value=$contact.tel}</a></li>{/if}
		{if $contact.handy}<li><a href="tel:{text value=$contact.handy}">{lng p="mobile"}: {text value=$contact.handy}</a></li>{/if}
		{if $contact.fax}<li>{lng p="fax"}: {text value=$contact.fax}</li>{/if}
		{if $contact.tel||$contact.fax||$contact.handy}</ul><ul data-role="listview" data-inset="true">{/if}
		
		{if $contact.strassenr||$contact.ort||$contact.plz||$contact.land}<li><strong>{lng p="priv"}:</strong><br />
				{if $contact.strassenr}{text value=$contact.strassenr}<br />{/if}
				{if $contact.ort||$contact.plz}{text value=$contact.plz} {text value=$contact.ort}<br />{/if}
				{if $contact.land}{text value=$contact.land}<br />{/if}
		</li>{/if}

		{if ($contact.email||$contact.tel||$contact.fax||$contact.handy||$contact.strassenr||$contact.ort||$contact.plz||$contact.land)&&($contact.work_email||$contact.work_tel||$contact.work_fax||$contact.work_handy||$contact.work_strassenr||$contact.work_ort||$contact.work_plz||$contact.work_land)}</ul><ul data-role="listview" data-inset="true">{/if}

		{if $contact.work_email}<li><a href="email.php?action=compose&to={$workEmailTo}&sid={$sid}">{text value=$contact.work_email}</a></li>{/if}
		{if $contact.work_tel}<li><a href="tel:{text value=$contact.work_tel}">{lng p="phone"}: {text value=$contact.work_tel}</a></li>{/if}
		{if $contact.work_handy}<li><a href="tel:{text value=$contact.work_handy}">{lng p="mobile"}: {text value=$contact.work_handy}</a></li>{/if}
		{if $contact.work_fax}<li>{lng p="fax"}: {text value=$contact.work_fax}</li>{/if}
		{if $contact.work_tel||$contact.work_fax||$contact.work_handy}</ul><ul data-role="listview" data-inset="true">{/if}
		
		{if $contact.work_strassenr||$contact.work_ort||$contact.work_plz||$contact.work_land}<li><strong>{lng p="work"}:</strong><br />
				{if $contact.work_strassenr}{text value=$contact.work_strassenr}<br />{/if}
				{if $contact.work_ort||$contact.work_plz}{text value=$contact.work_plz} {text value=$contact.work_ort}<br />{/if}
				{if $contact.work_land}{text value=$contact.work_land}<br />{/if}
		</li>{/if}

		{if $contact.kommentar||$contact.web||$contact.geburtsdatum}</ul><ul data-role="listview" data-inset="true">{/if}
		
		{if $contact.web}<li><a target="_blank" href="deref.php?{text value=$contact.web}">{lng p="web"}: {text value=$contact.web}</a></li>{/if}
		{if $contact.geburtsdatum}<li>{lng p="birthday"}: {date timestamp=$contact.geburtsdatum format="%d. %B %Y"}</li>{/if}
		{if $contact.kommentar}<li><strong>{lng p="notes"}:</strong><br />{$contact.kommentar}</li>{/if}
	</ul>
</div>
