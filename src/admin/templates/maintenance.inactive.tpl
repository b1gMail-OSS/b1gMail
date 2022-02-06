<fieldset>
	<legend>{lng p="inactiveusers"}</legend>
	
	<form action="maintenance.php?do=exec&sid={$sid}" method="post" onsubmit="spin(this)">
		<table>
			<tr>
				<td width="40" valign="top"><img src="{$tpldir}images/user_inactive32.png" border="0" alt="" width="32" height="32" /></td>
				<td valign="top">
					<p>
						{lng p="activity_desc1"}
					</p>
					
					<blockquote>
						<table>
							<tr>
								<td width="20" valign="top"><input type="checkbox" id="queryTypeLogin" name="queryTypeLogin" checked="checked" /></td>
								<td><label for="queryTypeLogin"><b>{lng p="notloggedinsince"}</b></label><br />
									<input type="text" size="6" name="loginDays" value="90" /> {lng p="days"}<br /><br /></td>
							</tr>
							<tr>
								<td width="20" valign="top"><input type="checkbox" id="queryTypeGroups" name="queryTypeGroups" checked="checked" /></td>
								<td><label for="queryTypeGroups"><b>{lng p="whobelongtogrps"}</b></label><br />
									{foreach from=$groups item=group key=groupID}
										<input type="checkbox" name="groups[{$groupID}]" id="group_{$groupID}" checked="checked" />
											<label for="group_{$groupID}"><b>{text value=$group.title}</b></label><br />
									{/foreach}</td>
							</tr>
						</table>
					</blockquote>
					
					<p>
						{lng p="activity_desc2"}
					</p>
					
					<blockquote>
						<table>
							<tr>
								<td width="20" valign="top"><input type="radio" id="queryActionShow" name="queryAction" value="show" checked="checked" /></td>
								<td><label for="queryActionShow"><b>{lng p="showlist"}</b></label></td>
							</tr>
							<tr>
								<td width="20" valign="top"><input type="radio" id="queryActionLock" name="queryAction" value="lock" /></td>
								<td><label for="queryActionLock"><b>{lng p="lock"}</b></label></td>
							</tr>
							<tr>
								<td width="20" valign="top"><input type="radio" id="queryActionMove" name="queryAction" value="move" /></td>
								<td><label for="queryActionMove"><b>{lng p="movetogroup"}:</b></label><br />
									<select name="moveGroup">
									{foreach from=$groups item=groupItem}
										<option value="{$groupItem.id}">{text value=$groupItem.title}</option>
									{/foreach}
									</select></td>
							</tr>
							<tr>
								<td width="20" valign="top"><input type="radio" id="queryActionDelete" name="queryAction" value="delete" /></td>
								<td><label for="queryActionDelete"><b>{lng p="delete"}</b></label></td>
							</tr>
						</table>
					</blockquote>
				</td>
			</tr>
		</table>
		
		<p>
			<div style="float:left;">
				<img src="{$tpldir}images/warning.png" border="0" alt="" width="16" height="16" align="absmiddle" />
				{lng p="undowarn"}
			</div>
			<div style="float:right;">
				<input class="button" type="submit" value=" {lng p="execute"} " />
			</div>
		</p>
	</form>
</fieldset>
