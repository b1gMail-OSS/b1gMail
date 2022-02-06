<div class="addressContainer withBottomBar"><div class="padContainer">
	<table>
		<tr>
			<th class="picture">
				<div style="background-image: url({if !$contact || $contact.picture==''}{$tpldir}images/li/no_picture.png{else}organizer.addressbook.php?action=addressbookPicture&id={$contact.id}&sid={$sid}{/if});"></div>
			</th>
			<td>
				<h1>
					{if !$contact.vorname&&!$contact.nachname&&$contact.firma}
					{text value=$contact.firma}
					{else}
					{text value=$contact.vorname} {text value=$contact.nachname}
					{/if}
				</h1>
				{if ($contact.vorname||$contact.nachname)&&$contact.firma}
				<div class="company">
					{if $contact.position}{text value=$contact.position}, {/if}{text value=$contact.firma}
				</div>
				{/if}
			</td>
		</tr>
		
		<!-- priv -->
		{if $contact.email}<tr>
			<th>{lng p="email"}</th>
			<td><a href="email.compose.php?to={$privEmailTo}&sid={$sid}">{text value=$contact.email}</a></td>
		</tr>
		<tr><td class="spacer" colspan="2"></td></tr>{/if}
		{if $contact.tel}<tr>
			<th>{lng p="phone"}</th>
			<td>{text value=$contact.tel}</td>
		</tr>{/if}
		{if $contact.fax}<tr>
			<th>{lng p="fax"}</th>
			<td>{text value=$contact.fax}</td>
		</tr>{/if}
		{if $contact.handy}<tr>
			<th>{lng p="mobile"}</th>
			<td>{text value=$contact.handy}</td>
		</tr>{/if}
		{if $contact.tel||$contact.fax||$contact.handy}<tr><td class="spacer" colspan="2"></td></tr>{/if}
		
		{if $contact.strassenr||$contact.ort||$contact.plz||$contact.land}<tr>
			<th>{lng p="priv"}</th>
			<td>
				{if $contact.strassenr}{text value=$contact.strassenr}<br />{/if}
				{if $contact.ort||$contact.plz}{text value=$contact.plz} {text value=$contact.ort}<br />{/if}
				{if $contact.land}{text value=$contact.land}<br />{/if}
			</td>
		</tr>{/if}
		
		{if ($contact.email||$contact.tel||$contact.fax||$contact.handy||$contact.strassenr||$contact.ort||$contact.plz||$contact.land)&&($contact.work_email||$contact.work_tel||$contact.work_fax||$contact.work_handy||$contact.work_strassenr||$contact.work_ort||$contact.work_plz||$contact.work_land)}<tr><td class="spacer" colspan="2"><hr /></td></tr>{/if}
		
		<!-- work -->
		{if $contact.work_email}<tr>
			<th>{lng p="email"}</th>
			<td><a href="email.compose.php?to={$workEmailTo}&sid={$sid}">{text value=$contact.work_email}</a></td>
		</tr>
		<tr><td class="spacer" colspan="2"></td></tr>{/if}
		{if $contact.work_tel}<tr>
			<th>{lng p="phone"}</th>
			<td>{text value=$contact.work_tel}</td>
		</tr>{/if}
		{if $contact.work_fax}<tr>
			<th>{lng p="fax"}</th>
			<td>{text value=$contact.work_fax}</td>
		</tr>{/if}
		{if $contact.work_handy}<tr>
			<th>{lng p="mobile"}</th>
			<td>{text value=$contact.work_handy}</td>
		</tr>{/if}
		{if $contact.work_tel||$contact.work_fax||$contact.work_handy}<tr><td class="spacer" colspan="2"></td></tr>{/if}
		
		{if $contact.work_strassenr||$contact.work_ort||$contact.work_plz||$contact.work_land}<tr>
			<th>{lng p="work"}</th>
			<td>
				{if $contact.work_strassenr}{text value=$contact.work_strassenr}<br />{/if}
				{if $contact.work_ort||$contact.work_plz}{text value=$contact.work_plz} {text value=$contact.work_ort}<br />{/if}
				{if $contact.work_land}{text value=$contact.work_land}<br />{/if}
			</td>
		</tr>{/if}
		
		{if $contact.kommentar||$contact.web||$contact.geburtsdatum}<tr><td class="spacer" colspan="2"><hr /></td></tr>{/if}
		
		{if $contact.geburtsdatum}<tr>
			<th>{lng p="birthday"}</th>
			<td>{date timestamp=$contact.geburtsdatum format="%d. %B %Y"}</td>
		</tr>{/if}
		{if $contact.web}<tr>
			<th>{lng p="web"}</th>
			<td><a target="_blank" href="deref.php?{text value=$contact.web}">{text value=$contact.web}</a></td>
		</tr>{/if}
		{if $contact.kommentar}<tr>
			<th>{lng p="notes"}</th>
			<td>{$contact.kommentar}</td>
		</tr>{/if}
	</table>
</div></div>

<div class="contentFooter">
	<div class="right">
		<button type="button" class="primary" onclick="document.location.href='organizer.addressbook.php?action=editContact&id={$contact.id}&sid={$sid}';">
			<i class="fa fa-edit"></i>
			{lng p="edit"}
		</button>
	</div>
</div>
