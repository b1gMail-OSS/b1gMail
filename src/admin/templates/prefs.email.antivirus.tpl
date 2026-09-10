<form action="{sessionurl file='prefs.email.php' params="action=antivirus&save=true"}" method="post" onsubmit="spin(this)">
	{csrffield}
	<div class="row">
		<div class="col-md-6">
			<fieldset>
				<legend>{lng p="clamintegration"}</legend>

				<div class="alert alert-warning">{lng p="clamwarning"}</div>

				<div class="mb-3">
					<div class="form-selectgroup form-selectgroup-boxes d-flex flex-column">
						<label class="form-selectgroup-item flex-fill">
							<input class="form-selectgroup-input" type="radio" id="clamd_mode_off" name="clamd_mode" value="off"{if $clamdMode=='off'} checked="checked"{/if} onchange="clamPrefs()">
							<div class="form-selectgroup-label d-flex align-items-center p-3">
								<div class="me-3"><span class="form-selectgroup-check"></span></div>
								<div>{lng p="clamd_off"}<br /><small>{lng p="clamd_off_desc"}</small></div>
							</div>
						</label>
						<label class="form-selectgroup-item flex-fill">
							<input class="form-selectgroup-input" type="radio" id="clamd_mode_socket" name="clamd_mode" value="socket"{if $clamdMode=='socket'} checked="checked"{/if} onchange="clamPrefs()">
							<div class="form-selectgroup-label d-flex align-items-center p-3">
								<div class="me-3"><span class="form-selectgroup-check"></span></div>
								<div>{lng p="clamd_socket"}<br /><small>{lng p="clamd_socket_desc"}</small></div>
							</div>
						</label>
						<label class="form-selectgroup-item flex-fill">
							<input class="form-selectgroup-input" type="radio" id="clamd_mode_tcp" name="clamd_mode" value="tcp"{if $clamdMode=='tcp'} checked="checked"{/if} onchange="clamPrefs()">
							<div class="form-selectgroup-label d-flex align-items-center p-3">
								<div class="me-3"><span class="form-selectgroup-check"></span></div>
								<div>{lng p="clamd_tcp"}<br /><small>{lng p="clamd_tcp_desc"}</small></div>
							</div>
						</label>
					</div>
				</div>
			</fieldset>
		</div>
		<div class="col-md-6">
			<fieldset>
				<legend>{lng p="prefs"}</legend>

				<div id="clamd_prefs_off" style="display:{if $clamdMode!='off'}none{/if};">
					<i>({lng p="none"})</i>
				</div>

				<div id="clamd_prefs_socket" style="display:{if $clamdMode!='socket'}none{/if};">
					<div class="mb-3 row">
						<label class="col-sm-4 col-form-label">{lng p="clamd_socket_path"}</label>
						<div class="col-sm-8">
							<input type="text" class="form-control" name="clamd_socket" value="{text allowEmpty=true value=$clamdSocketPath}" placeholder="/var/run/clamav/clamd.ctl">
							{if $clamdSocketAvailable}
								<small class="text-success">{lng p="clamd_socket_ok"}</small>
							{else}
								<small class="text-danger">{lng p="clamd_socket_missing"}{if $clamdSocketErr} ({text value=$clamdSocketErr}){/if}</small>
								<div class="form-text">{lng p="clamd_socket_permhint"}</div>
							{/if}
						</div>
					</div>
				</div>

				<div id="clamd_prefs_tcp" style="display:{if $clamdMode!='tcp'}none{/if};">
					<div class="mb-3 row">
						<label class="col-sm-4 col-form-label">{lng p="host"}</label>
						<div class="col-sm-8">
							<input type="text" class="form-control" name="clamd_host" value="{text allowEmpty=true value=$clamdTcpHost}" placeholder="127.0.0.1">
						</div>
					</div>
					<div class="mb-3 row">
						<label class="col-sm-4 col-form-label">{lng p="port"}</label>
						<div class="col-sm-8">
							<input type="number" class="form-control" name="clamd_port" value="{$clamdTcpPort}" placeholder="3310" min="1" max="65535">
						</div>
					</div>
				</div>
			</fieldset>
		</div>
	</div>

	<div class="text-end">
		<input class="btn btn-primary" type="submit" value="{lng p="save"}" />
	</div>
</form>

<script type="text/javascript">
function clamPrefs()
{
	var off = EBID('clamd_mode_off'),
		sock = EBID('clamd_mode_socket'),
		tcp = EBID('clamd_mode_tcp'),
		prefsOff = EBID('clamd_prefs_off'),
		prefsSock = EBID('clamd_prefs_socket'),
		prefsTcp = EBID('clamd_prefs_tcp');

	prefsOff.style.display = prefsSock.style.display = prefsTcp.style.display = 'none';
	if(off && off.checked)
		prefsOff.style.display = '';
	else if(sock && sock.checked)
		prefsSock.style.display = '';
	else if(tcp && tcp.checked)
		prefsTcp.style.display = '';
}
</script>
