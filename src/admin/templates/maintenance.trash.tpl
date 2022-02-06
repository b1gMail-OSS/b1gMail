<fieldset>
	<legend>{lng p="trash"}</legend>
	
	{* <form action="maintenance.php?action=trash&do=exec&sid={$sid}" method="post" onsubmit="spin(this)"> *}
	<div id="form">
		<table>
			<tr>
				<td width="40" valign="top"><img src="{$tpldir}images/trash32.png" border="0" alt="" width="32" height="32" /></td>
				<td valign="top">
					<p>
						{lng p="trash_desc"}
					</p>
					
					<blockquote>
						{foreach from=$groups item=group key=groupID}
							<input type="checkbox" name="groups[{$groupID}]" id="group_{$groupID}" checked="checked" />
								<label for="group_{$groupID}"><b>{text value=$group.title}</b></label><br />
						{/foreach}
					</blockquote>
					
					<p>
						{lng p="trash_only"}
					</p>
					
					<blockquote>
						<table>
							<tr>
								<td width="20" valign="top"><input type="checkbox" id="daysOnly" name="daysOnly" checked="checked" /></td>
								<td><label for="daysOnly"><b>{lng p="trash_daysonly"}</b></label><br />
									<input type="text" size="6" name="days" id="days" value="30" /> {lng p="days"}<br /><br /></td>
							</tr>
							<tr>
								<td width="20" valign="top"><input type="checkbox" id="sizesOnly" name="sizesOnly" /></td>
								<td><label for="sizesOnly"><b>{lng p="trash_sizesonly"}</b></label><br />
									<input type="text" size="6" name="size" id="size" value="512" /> KB</td>
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
				{lng p="opsperpage"}:
				<input type="text" name="perpage" id="perpage" value="50" size="5" />
				<input class="button" type="button" onclick="trashExec()" value=" {lng p="execute"} " />
			</div>
		</p>
	</div>
	{* </form> *}
</fieldset>
