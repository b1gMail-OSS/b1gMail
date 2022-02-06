<table class="listTable">
	<tr>
		<td colspan="2" class="folderGroup">
			<table cellpadding="0" width="100%">
				<tr>
					<td align="left">
						<select name="block[#][type]" onclick="faxFormBlockTypeChanged(#, this)" id="block_#_type" class="smallInput">
							<option value="0">{lng p="modfax_textblock"}</option>
							<option value="1">{lng p="modfax_pagebreak"}</option>
							<option value="2">{lng p="modfax_cover"}</option>
							{if $faxPrefs.allow_pdf}<option value="3">{lng p="modfax_pdffile"}</option>{/if}
						</select>
					</td>
					<td align="right">
						<a href="javascript:void(0);" onclick="if(confirm('{lng p="realdel"}')) faxFormDeleteBlock(#)" id="block_#_del" style="display:;"><i class="fa fa-trash" aria-hidden="true"></i></a>
						<a href="javascript:void(0);" onclick="faxFormAddBlock(#,true)"><i class="fa fa-plus" aria-hidden="true"></i></a>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<div id="block_#_0" style="display:none;">
				<table width="100%" cellpadding="0" cellspacing="0">
					<tr>
						<td><textarea name="block[#][text][text]" id="block_#_text" class="composeTextarea" style="width:100%;height:200px;font-family:arial;font-size:12px;text-decoration:none;font-weight:normal;"></textarea></td>
						<td class="listTableRightest" style="width:120px;text-align:left;font-size:11px;">
							<label for="block_#_fontname">{lng p="modfax_fontname"}:</label><br />
							<select class="smallInput" name="block[#][text][fontname]" id="block_#_fontname" onchange="faxFormFontNameChanged(#, this)">
								<option value="arial">Arial</option>
								<option value="times">Times</option>
								<option value="courier">Courier</option>
							</select><br /><br />
							
							<label for="block_#_fontsize">{lng p="modfax_fontsize"}:</label><br />
							<select class="smallInput" name="block[#][text][fontsize]" id="block_#_fontsize" onchange="faxFormFontSizeChanged(#, this)">
								<option value="24">24</option>
								<option value="22">22</option>
								<option value="20">20</option>
								<option value="18">18</option>
								<option value="16">16</option>
								<option value="14">14</option>
								<option value="12" selected="selected">12</option>
								<option value="11">11</option>
								<option value="10">10</option>
								<option value="8">8</option>
								<option value="6">6</option>
							</select><br /><br />
							
							<label for="block_#_align">{lng p="modfax_align"}:</label><br />
							<select class="smallInput" name="block[#][text][align]" id="block_#_align" onchange="faxFormAlignChanged(#, this)">
								<option value="left">{lng p="modfax_alignleft"}</option>
								<option value="center">{lng p="modfax_aligncenter"}</option>
								<option value="right">{lng p="modfax_alignright"}</option>
								<option value="justify">{lng p="modfax_alignjustify"}</option>
							</select><br /><br />
							
							<input type="checkbox" name="block[#][text][bold]" id="block_#_bold" onclick="faxFormStyleChanged(#)" /> <label for="block_#_bold">{lng p="modfax_bold"}</label>
							<input type="checkbox" name="block[#][text][italic]" id="block_#_italic" onclick="faxFormStyleChanged(#)" /> <label for="block_#_italic">{lng p="modfax_italic"}</label><br />
							<input type="checkbox" name="block[#][text][underlined]" id="block_#_underlined" onclick="faxFormStyleChanged(#)" /> <label for="block_#_underlined">{lng p="modfax_underlined"}</label>
						</td>
					</tr>
				</table>
			</div>
			<div id="block_#_1" style="display:none;">
				<i>({lng p="modfax_pagebreak"})</i>
			</div>
			<div id="block_#_2" style="display:none;">
				<table style="padding:1em;">
					<tr>
						<td><strong><label for="block_#_toname">{lng p="to"} ({lng p="name"})</label></strong>: &nbsp;</td>
						<td><input type="text" style="width:350px;" name="block[#][cover][toname]" id="block_#_toname" value="" /></td>
					</tr>
					<tr>
						<td><strong><label for="block_#_subject">{lng p="subject"}:</label></strong> &nbsp;</td>
						<td><input type="text" style="width:350px;" name="block[#][cover][subject]" id="block_#_subject" value="" /></td>
					</tr>
					<tr>
						<td><strong><label for="block_#_phone">{lng p="phone"}:</label></strong> &nbsp;</td>
						<td><input type="text" style="width:350px;" name="block[#][cover][phone]" id="block_#_phone" value="{text value=$userRow.tel}" /></td>
					</tr>
					<tr>
						<td><strong><label>{lng p="modfax_remark"}:</label></strong> &nbsp;</td>
						<td>
							<input type="checkbox" name="block[#][cover][remark][]" id="block_#_remark_0" value="0" />
							<label for="block_#_remark_0">{lng p="modfax_remark0"}</label>
							
							<input type="checkbox" name="block[#][cover][remark][]" id="block_#_remark_1" value="1" />
							<label for="block_#_remark_1">{lng p="modfax_remark1"}</label>
							
							<input type="checkbox" name="block[#][cover][remark][]" id="block_#_remark_2" value="2" />
							<label for="block_#_remark_2">{lng p="modfax_remark2"}</label>
							
							<input type="checkbox" name="block[#][cover][remark][]" id="block_#_remark_3" value="3" />
							<label for="block_#_remark_3">{lng p="modfax_remark3"}</label>
							
							<input type="checkbox" name="block[#][cover][remark][]" id="block_#_remark_4" value="4" />
							<label for="block_#_remark_4">{lng p="modfax_remark4"}</label>
						</td>
					</tr>
				</table>
			</div>{if $faxPrefs.allow_pdf}
			<div id="block_#_3" style="display:none;">
				<table style="padding:1em;">
					<tr>
						<td><strong>{lng p="modfax_file"}:</strong> &nbsp;</td>
						<td><i class="fa fa-file-pdf-o" aria-hidden="true"></i>
							<span id="block_#_filename"><small><i>({lng p="modfax_nofilesel"})</i></small></span></td>
						<td><input type="button" value=" {lng p="modfax_browse"}... " onclick="faxFormChoseFile(#)" /></td>
					</tr>
					<tr>
						<td><strong>{lng p="modfax_pages"}:</strong> &nbsp;</td>
						<td colspan="2"><span id="block_#_pages"><small><i>({lng p="modfax_nofilesel"})</i></small></span></td>
					</tr>
				</table>
				<input type="hidden" id="block_#_fileid" name="block[#][pdf][fileid]" value="0" />
			</div>{/if}
		</td>
	</tr>
</table>
