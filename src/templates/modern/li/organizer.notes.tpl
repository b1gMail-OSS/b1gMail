<div id="vSep1">
	<div>
		
		<div id="contentHeader">
			<div class="left">
				<i class="fa fa-sticky-note-o" aria-hidden="true"></i>
				{lng p="notes"}
			</div>
		</div>
	
		<form name="f1" method="post" action="organizer.notes.php?action=action&sid={$sid}">
		<div class="scrollContainer withBottomBar">
			<table class="bigTable">
				<tr>
					<th width="20"><input type="checkbox" id="allChecker" onclick="checkAll(this.checked, document.forms.f1);" /></th>
					<th width="80">
						<a href="organizer.notes.php?sid={$sid}&sort=priority&order={$sortOrderInv}">{lng p="priority"}</a>
						{if $sortColumn=='priority'}<img src="{$tpldir}images/li/{$sortOrder}.gif" border="0" alt="" align="absmiddle" />{/if}
					</th>
					<th width="150">
						<a href="organizer.notes.php?sid={$sid}&sort=date&order={$sortOrderInv}">{lng p="date"}</a>
						{if $sortColumn=='date'}<img src="{$tpldir}images/li/{$sortOrder}.gif" border="0" alt="" align="absmiddle" />{/if}
					</th>
					<th>
						<a href="organizer.notes.php?sid={$sid}&sort=text&order={$sortOrderInv}">{lng p="text"}</a>
						{if $sortColumn=='text'}<img src="{$tpldir}images/li/{$sortOrder}.gif" border="0" alt="" align="absmiddle" />{/if}					
					</th>
					<th width="55">&nbsp;</th>
				</tr>
				
				{if $noteList}
				<tbody class="listTBody">
				{foreach from=$noteList key=noteID item=note}
				{cycle values="listTableTD,listTableTD2" assign="class"}
				{assign value=$note.priority var=prio}
				<tr>
					<td class="{$class}" nowrap="nowrap"><input type="checkbox" name="note_{$noteID}" /></td>
					<td class="{if $sortColumn=='priority'}listTableTDActive{else}{$class}{/if}" nowrap="nowrap"><img src="{$tpldir}images/li/prio_{if $note.priority==-1}low{elseif $note.priority==0}normal{else}high{/if}.gif" border="0" alt="" align="absmiddle" /> {lng p="prio_$prio"}</td>
					<td class="{if $sortColumn=='date'}listTableTDActive{else}{$class}{/if}" nowrap="nowrap">&nbsp;{date timestamp=$note.date nice=true}&nbsp;</td>
					<td class="{if $sortColumn=='text'}listTableTDActive{else}{$class}{/if}" nowrap="nowrap">&nbsp;<a href="javascript:previewNote('{$sid}', '{$noteID}');">{text value=$note.text}</a>&nbsp;</td>
					<td class="{$class}" nowrap="nowrap">
						<a href="organizer.notes.php?action=editNote&id={$noteID}&sid={$sid}"><i class="fa fa-pencil" aria-hidden="true"></i></a>
						<a onclick="return confirm('{lng p="realdel"}');" href="organizer.notes.php?action=deleteNote&id={$noteID}&sid={$sid}"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
					</td>
				</tr>
				{/foreach}
				</tbody>
				{/if}
			</table>
		</div>
		
		<div id="contentFooter">
			<div class="left">
				<select class="smallInput" name="do">
					<option value="-">------ {lng p="selaction"} ------</option>
					<option value="delete">{lng p="delete"}</option>
				</select>
				<input class="smallInput" type="submit" value="{lng p="ok"}" />
			</div>
			<div class="right">
				<button type="button" class="primary" onclick="document.location.href='organizer.notes.php?action=addNote&sid={$sid}';">
					<i class="fa fa-plus-circle"></i>
					{lng p="addnote"}
				</button>
			</div>
		</div>
		
		</form>
	</div>
</div>
<div id="vSepSep"></div>
<div id="vSep2">
	<div class="scrollContainer withoutContentHeader notePreview">
		<div id="notePreview">{lng p="clicknote"}</div>
	</div>
</div>

<script>
<!--
	registerLoadAction('initVSep()');
{if $showID}
	registerLoadAction('previewNote(\'{$sid}\', \'{$showID}\')');
{/if}
//-->
</script>
