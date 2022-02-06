<div class="innerWidget">
	<fieldset>
		<legend>{lng p="email"}</legend>
		<a href="email.php?sid={$sid}"><i class="fa fa-inbox" aria-hidden="true"></i>
										{lng p="inbox"}</a><br />
		<a href="email.compose.php?sid={$sid}"><i class="fa fa-envelope-o" aria-hidden="true"></i>
										{lng p="sendmail"}</a><br />
		<a href="email.folders.php?sid={$sid}"><i class="fa fa-folder-open-o" aria-hidden="true"></i>
										{lng p="folderadmin"}</a><br />
	</fieldset>
	<fieldset>
		<legend>{lng p="organizer"}</legend>
		<a href="organizer.php?sid={$sid}"><i class="fa fa-tachometer" aria-hidden="true"></i>
										{lng p="overview"}</a><br />
		<a href="organizer.calendar.php?sid={$sid}"><i class="fa fa-calendar" aria-hidden="true"></i>
										{lng p="calendar"}</a><br />
		<a href="organizer.todo.php?sid={$sid}"><i class="fa fa-tasks" aria-hidden="true"></i>
										{lng p="tasks"}</a><br />
		<a href="organizer.addressbook.php?sid={$sid}"><i class="fa fa-address-book" aria-hidden="true"></i>
										{lng p="addressbook"}</a><br />
		<a href="organizer.notes.php?sid={$sid}"><i class="fa fa-sticky-note-o" aria-hidden="true"></i>
										{lng p="notes"}</a><br />
	</fieldset>
	{if $pageTabs.webdisk}<fieldset>
		<legend>{lng p="webdisk"}</legend>
		<a href="webdisk.php?sid={$sid}"><i class="fa fa-cloud" aria-hidden="true"></i>
										{lng p="webdisk"}</a><br />
		<a href="webdisk.php?sid={$sid}&do=uploadFilesForm"><i class="fa fa-share-square-o" aria-hidden="true"></i>
										{lng p="uploadfiles"}</a><br />
	</fieldset>{/if}
	<fieldset>
		<legend>{lng p="misc"}</legend>
		<a href="prefs.php?sid={$sid}"><i class="fa fa-cog" aria-hidden="true"></i>
										{lng p="prefs"}</a><br />
		<a href="start.php?sid={$sid}&action=logout"><i class="fa fa-sign-out" aria-hidden="true"></i>
										{lng p="logout"}</a><br />
	</fieldset>
</div>