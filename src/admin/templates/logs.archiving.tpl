<fieldset>
	<legend>{lng p="archiving"}</legend>
		
	<form action="{sessionurl file='logs.php' params="action=archiving&do=archive"}" method="post" onsubmit="if(EBID('saveCopy').checked || confirm('{lng p="reallynotarc"}')) spin(this); else return(false);">
		{csrffield}
		<p>{lng p="logarc_desc"}</p>

		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label" for="log-archive-date">{lng p="date"}</label>
			<div class="col-sm-4">
				<div class="input-group">
					<input type="text" class="form-control" id="log-archive-date" name="date" data-bs-toggle="datepicker" data-bs-position-element="#log-archive-date" data-bm-datetime="1" data-bs-date-format="DD.MM.YYYY" data-bs-locale="de" autocomplete="off" value="{date timestamp=$smarty.now format="d.m.Y H:i"}" placeholder="TT.MM.JJJJ HH:MM" aria-label="{lng p="date"}">
					<label class="input-group-text mb-0" for="log-archive-date"><i class="icon ti ti-calendar-time"></i></label>
				</div>
			</div>
		</div>

		<div style="float: right;"><input class="btn btn-sm" type="submit" onclick="rebuildCaches()" value="{lng p="execute"}" /></div>
		<div style="float: right;"><input type="text" class="form-control form-control-sm" name="perpage" id="perpage" value="50" size="5" />&nbsp; </div>
		<div style="float: right;">
			<label class="form-check">
				<input class="form-check-input" type="checkbox" name="saveCopy" id="saveCopy" checked="checked">
				<span class="form-check-label">{lng p="savearc"}&nbsp; </span>
			</label>&nbsp;
		</div>
	</form>
</fieldset>
