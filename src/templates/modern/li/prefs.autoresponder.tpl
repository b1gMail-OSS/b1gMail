<div id="contentHeader">
	<div class="left">
		<i class="fa fa-reply" aria-hidden="true"></i>
		{lng p="autoresponder"}
	</div>
</div>

<div class="scrollContainer"><div class="pad">

<form name="f1" method="post" action="prefs.php?action=autoresponder&do=save&sid={$sid}">
	<table class="listTable">
		<tr>
			<th class="listTableHead" colspan="2"> {lng p="autoresponder"}</th>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="active">{lng p="autoresponder"}:</label></td>
			<td class="listTableRight">
				<input type="checkbox" name="active" id="active"{if $active} checked="checked"{/if} />
				<label for="active"><b>{lng p="enable"}</b></label>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="betreff">{lng p="subject"}:</label></td>
			<td class="listTableRight">
				<input type="text" name="betreff" id="betreff" value="{text allowEmpty=true value=$betreff}" style="width:350px;">
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="mitteilung">{lng p="text"}:</label></td>
			<td class="listTableRight">
				<textarea name="mitteilung" id="mitteilung" style="width:400px;height:200px;">{text allowEmpty=true value=$mitteilung}</textarea>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft">&nbsp;</td>
			<td class="listTableRight">
				<input type="submit" class="primary" value="{lng p="ok"}" />
				<input type="reset" value="{lng p="reset"}" />
			</td>
		</tr>
	</table>
</form>

</div></div>
