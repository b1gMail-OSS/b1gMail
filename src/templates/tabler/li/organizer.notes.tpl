<div class="bm-organizer-page bm-organizer-notes">
	<div id="contentHeader" class="contentHeader bm-organizer-header">
		<div class="left">
			<i class="ti ti-notes icon icon-sm" aria-hidden="true"></i>
			{lng p="notes"}
		</div>
	</div>

	<div class="scrollContainer bm-organizer-split">
		<div id="hSep1" class="bm-organizer-notes-panel">
			<form name="f1" method="post" action="organizer.notes.php?action=action&sid={$sid}">
				<div class="scrollContainer withBottomBar bm-organizer-notes-list">
					<div class="table-responsive">
						<table class="table table-vcenter table-hover card-table bm-organizer-table" id="notesTable">
							<thead>
							<tr>
								<th class="bm-organizer-task-gutter"><label class="form-check mb-0"><input type="checkbox" class="form-check-input m-0" id="allChecker" onclick="checkAll(this.checked, document.forms.f1);" aria-label="{lng p="selaction"}" /></label></th>
								<th style="width:5rem;">
									<a class="bm-organizer-sort-link" href="organizer.notes.php?sid={$sid}&sort=priority&order={$sortOrderInv}">{lng p="priority"}</a>
									{if $sortColumn=='priority'}<i class="ti ti-arrow-{if $sortOrder=='fa-arrow-down'}down{else}up{/if} icon icon-sm ms-1 text-primary" aria-hidden="true"></i>{/if}
								</th>
								<th style="width:9.375rem;">
									<a class="bm-organizer-sort-link" href="organizer.notes.php?sid={$sid}&sort=date&order={$sortOrderInv}">{lng p="date"}</a>
									{if $sortColumn=='date'}<i class="ti ti-arrow-{if $sortOrder=='fa-arrow-down'}down{else}up{/if} icon icon-sm ms-1 text-primary" aria-hidden="true"></i>{/if}
								</th>
								<th>
									<a class="bm-organizer-sort-link" href="organizer.notes.php?sid={$sid}&sort=text&order={$sortOrderInv}">{lng p="text"}</a>
									{if $sortColumn=='text'}<i class="ti ti-arrow-{if $sortOrder=='fa-arrow-down'}down{else}up{/if} icon icon-sm ms-1 text-primary" aria-hidden="true"></i>{/if}
								</th>
								<th class="bm-organizer-task-col-actions">&nbsp;</th>
							</tr>
							</thead>

							{if $noteList}
							<tbody>
							{foreach from=$noteList key=noteID item=note}
							{assign value=$note.priority var=prio}
							<tr>
								<td class="bm-organizer-task-gutter" nowrap="nowrap"><label class="form-check mb-0"><input type="checkbox" class="form-check-input m-0" name="note_{$noteID}" /></label></td>
								<td nowrap="nowrap"{if $sortColumn=='priority'} class="text-primary fw-semibold"{/if}>
									<img src="{$tpldir}images/li/prio_{if $note.priority==-1}low{elseif $note.priority==0}normal{else}high{/if}.gif" border="0" alt="" align="absmiddle" />
									{lng p="prio_$prio"}
								</td>
								<td nowrap="nowrap"{if $sortColumn=='date'} class="text-primary fw-semibold"{/if}>&nbsp;{date timestamp=$note.date nice=true}&nbsp;</td>
								<td nowrap="nowrap"{if $sortColumn=='text'} class="text-primary fw-semibold"{/if}>&nbsp;<a href="javascript:previewNote('{$sid}', '{$noteID}');">{text value=$note.text}</a>&nbsp;</td>
								<td nowrap="nowrap" class="text-end bm-organizer-task-col-actions">
									<div class="btn-group btn-group-sm bm-organizer-task-actions" role="group" aria-label="{lng p="actions"}">
										<a href="organizer.notes.php?action=editNote&id={$noteID}&sid={$sid}" class="btn btn-outline-secondary btn-icon" title="{lng p="edit"}" aria-label="{lng p="edit"}"><i class="ti ti-pencil icon" aria-hidden="true"></i></a>
										<a onclick="return confirm('{lng p="realdel"}');" href="organizer.notes.php?action=deleteNote&id={$noteID}&sid={$sid}" class="btn btn-outline-secondary btn-icon text-danger" title="{lng p="delete"}" aria-label="{lng p="delete"}"><i class="ti ti-trash icon" aria-hidden="true"></i></a>
									</div>
								</td>
							</tr>
							{/foreach}
							</tbody>
							{/if}
						</table>
					</div>
				</div>

				<div id="contentFooter" class="contentFooter bm-organizer-footer">
					<div class="left d-flex flex-wrap align-items-center gap-2">
						<select class="form-select form-select-sm" name="do" style="width:auto;min-width:10rem;">
							<option value="-">------ {lng p="selaction"} ------</option>
							<option value="delete">{lng p="delete"}</option>
						</select>
						<button class="btn btn-sm btn-primary" type="submit">{lng p="ok"}</button>
					</div>
					<div class="right">
						<button type="button" class="btn btn-sm btn-primary" onclick="document.location.href='organizer.notes.php?action=addNote&sid={$sid}';">
							<i class="ti ti-plus icon icon-sm me-1" aria-hidden="true"></i>
							{lng p="addnote"}
						</button>
					</div>
				</div>
			</form>
		</div>

		<div id="hSepSep"></div>

		<div id="hSep2" class="notePreview bm-organizer-note-preview">
			<div id="notePreview" class="bm-organizer-note-preview-inner bm-organizer-note-preview-empty">{lng p="clicknote"}</div>
		</div>
	</div>
</div>

<script>
<!--
	registerLoadAction('initHSep(\'notes\')');
{if isset($showID)}
	registerLoadAction('previewNote(\'{$sid}\', \'{$showID}\')');
{/if}
//-->
</script>
