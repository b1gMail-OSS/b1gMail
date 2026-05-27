<div class="bm-organizer-page bm-organizer-form-page">
	<div id="contentHeader" class="contentHeader bm-organizer-header">
		<div class="left">
			<i class="ti ti-notes icon icon-sm" aria-hidden="true"></i>
			{if isset($note)}{lng p="editnote"}{else}{lng p="addnote"}{/if}
		</div>
	</div>

	<form name="f1" method="post" action="organizer.notes.php?action={if isset($note)}saveNote&id={$note.id}{else}createNote{/if}&sid={$sid}" class="card bm-organizer-form-card" onsubmit="return(checkNoteForm(this));">
		<div class="card-body">
			<h3 class="card-title mb-4">{if isset($note)}{lng p="editnote"}{else}{lng p="addnote"}{/if}</h3>

			<div class="row g-3">
				<div class="col-md-4">
					<label class="form-label" for="priority">{lng p="priority"}</label>
					<select class="form-select" name="priority" id="priority">
						<option value="1"{if isset($note.priority) && $note.priority==1} selected="selected"{/if}>{lng p="prio_1"}</option>
						<option value="0"{if empty($note.id) || empty($note.priority)} selected="selected"{/if}>{lng p="prio_0"}</option>
						<option value="-1"{if isset($note.priority) && $note.priority==-1} selected="selected"{/if}>{lng p="prio_-1"}</option>
					</select>
				</div>

				<div class="col-12">
					<label class="form-label required" for="text">{lng p="text"}</label>
					<textarea class="form-control" name="text" id="text" rows="12">{if isset($note.text)}{text value=$note.text allowEmpty=true}{/if}</textarea>
				</div>
			</div>

			<div class="btn-list mt-4">
				<button type="submit" class="btn btn-primary">{lng p="ok"}</button>
				<button type="reset" class="btn">{lng p="reset"}</button>
			</div>
		</div>
	</form>
</div>
