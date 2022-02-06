<fieldset>
	<legend>{lng p="db"}</legend>
	
	{if $execute}
		<table class="list">
		<tr>
			<th>&nbsp;</th>
			<th>{lng p="table"}</th>
			<th>{lng p="query"}</th>
			<th width="100">{lng p="status"}</th>
		</tr>
		{foreach from=$result item=table}
		{cycle values="td1,td2" name=class assign=class}
		<tr class="{$class}">
			<td width="20"><img src="{$tpldir}images/db_table.png" border="0" alt="" width="16" height="16" /></td>
			<td>{$table.table}</td>
			<td><code>{$table.query}</code></td>
			<td align="right">{if $table.type!='status' && $table.type!='info' && $table.type!='note'}
				<img align="absmiddle" src="{$tpldir}images/error.png" width="16" height="16" alt="" border="0" />
				{lng p="error"}
			{else}
				<img align="absmiddle" src="{$tpldir}images/ok.png" width="16" height="16" alt="" border="0" />
				{lng p="success"}
			{/if}</td>
		</tr>
		{/foreach}
		</table>
		
		<p align="right">
			<input class="button" type="button" value=" {lng p="back"} " onclick="document.location.href='optimize.php?sid={$sid}';" />
		</p>
	{elseif $executeStruct}
	<form action="optimize.php?do=repairStruct&sid={$sid}" method="post" onsubmit="spin(this)">
		<table class="list">
		<tr>
			<th>&nbsp;</th>
			<th>{lng p="table"}</th>
			<th width="120">{lng p="exists"}</th>
			<th width="120">{lng p="structstate"}</th>
			<th width="120">{lng p="status"}</th>
		</tr>
		{foreach from=$result item=table}
		{cycle values="td1,td2" name=class assign=class}
		<tr class="{$class}">
			<td width="20"><img src="{$tpldir}images/db_table.png" border="0" alt="" width="16" height="16" /></td>
			<td>{$table.table}</td>
			<td>{if $table.exists}{lng p="yes"}{else}{lng p="no"}{/if}</td>
			<td>{$table.missing} / {$table.invalid}</td>
			<td align="right">{if !$table.exists || $table.missing || $table.invalid}
				<img align="absmiddle" src="{$tpldir}images/error.png" width="16" height="16" alt="" border="0" />
				{lng p="error"}
			{else}
				<img align="absmiddle" src="{$tpldir}images/ok.png" width="16" height="16" alt="" border="0" />
				{lng p="ok"}
			{/if}</td>
		</tr>
		{/foreach}
		</table>
		
		<p>
			<div style="float:left;">
				{if $repair}<img src="{$tpldir}images/warning.png" border="0" alt="" width="16" height="16" align="absmiddle" />
				{lng p="dbwarn"}{/if}
			</div>
			<div style="float:right;">
				<input class="button" type="button" value=" {lng p="back"} " onclick="document.location.href='optimize.php?sid={$sid}';" />
				{if $repair}<input class="button" type="submit" value=" {lng p="repairstruct"} " />{/if}
			</div>
		</p>
	</form>
	{else}
	<form action="optimize.php?sid={$sid}&do=execute" method="post" onsubmit="spin(this)">
		<table>
			<tr>
				<td>{lng p="tables"}:</td>
				<td>{lng p="action"}:</td>
			</tr>
			<tr>
				<td valign="top"><select size="10" name="tables[]" multiple="multiple">
				{foreach from=$tables item=table}
					<option value="{$table}" selected="selected">{$table}</option>
				{/foreach}
				</select></td>
				<td valign="top">
					<table>
						<tr>
							<td valign="top" width="20" align="center"><input type="radio" id="op_optimize" name="operation" value="optimize" checked="checked" /></td>
							<td valign="top"><img src="{$tpldir}images/db_optimize.png" border="0" alt="" width="32" height="32" /></td>
							<td><label for="op_optimize"><b>{lng p="op_optimize"}</b></label><br />
								{lng p="op_optimize_desc"}</td>
						</tr>
						<tr>
							<td colspan="3">
								&nbsp;
							</td>
						</tr>
						<tr>
							<td valign="top" width="20" align="center"><input type="radio" id="op_repair" name="operation" value="repair" /></td>
							<td valign="top"><img src="{$tpldir}images/db_repair.png" border="0" alt="" width="32" height="32" /></td>
							<td><label for="op_repair"><b>{lng p="op_repair"}</b></label><br />
								{lng p="op_repair_desc"}</td>
						</tr>
						<tr>
							<td colspan="3">
								&nbsp;
							</td>
						</tr>
						<tr>
							<td valign="top" width="20" align="center"><input type="radio" id="op_struct" name="operation" value="struct" /></td>
							<td valign="top"><img src="{$tpldir}images/db_struct.png" border="0" alt="" width="32" height="32" /></td>
							<td><label for="op_struct"><b>{lng p="op_struct"}</b></label><br />
								{lng p="op_struct_desc"}</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		
		<p>
			<div style="float:left;">
				<img src="{$tpldir}images/warning.png" border="0" alt="" width="16" height="16" align="absmiddle" />
				{lng p="dbwarn"}
			</div>
			<div style="float:right;">
				<input class="button" type="submit" value=" {lng p="execute"} " />
			</div>
		</p>
	</form>
	{/if}
</fieldset>