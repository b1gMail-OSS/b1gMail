{if !isset($wdicons_size_class)}
	{if isset($fa_additionalparamclass) && ($fa_additionalparamclass == 'fa-3x' || $fa_additionalparamclass == 'fa-4x')}
		{assign var=wdicons_size_class value='bm-webdisk-icon-lg' scope='global'}
	{else}
		{assign var=wdicons_size_class value='bm-webdisk-icon-sm' scope='global'}
	{/if}
{/if}
<span class="bm-webdisk-item-icon {$wdicons_size_class}" {$wdicons_additionalparam}>
{if $use_fa_icons==1}
	{if $item['ext']==".FOLDER"}
	<i class="ti ti-folder icon" aria-hidden="true"></i>
	{else if $item.ext=="jpg" OR $item.ext=="jpeg" OR $item.ext=="png" OR $item.ext=="gif" OR $item.ext=="bmp"}
	<i class="ti ti-photo icon" aria-hidden="true"></i>
	{else if $item.ext=="zip" OR $item.ext=="rar" OR $item.ext=="ace" OR $item.ext=="gz" OR $item.ext=="bz2" OR $item.ext=="pak" OR $item.ext=="pk3" OR $item.ext=="gcf" OR $item.ext=="tar"}
	<i class="ti ti-file-zip icon" aria-hidden="true"></i>
	{else if $item.ext=="mpg" OR $item.ext=="mpeg" OR $item.ext=="divx" OR $item.ext=="avi" OR $item.ext=="mkv" OR $item.ext=="mp4" OR $item.ext=="m2ts" OR $item.ext=="mov" OR $item.ext=="qt" OR $item.ext=="webm"}
	<i class="ti ti-movie icon" aria-hidden="true"></i>
	{else if $item.ext=="odt" OR $item.ext=="doc" OR $item.ext=="docx" OR $item.ext=="rtf" OR $item.ext=="wri" OR $item.ext=="sdw"}
	<i class="ti ti-file-type-doc icon" aria-hidden="true"></i>
	{else if $item.ext=="odp" OR $item.ext=="ppt" OR $item.ext=="pptx"}
	<i class="ti ti-file-type-ppt icon" aria-hidden="true"></i>
	{else if $item.ext=="ods" OR $item.ext=="xls" OR $item.ext=="xlsx"}
	<i class="ti ti-file-spreadsheet icon" aria-hidden="true"></i>
	{else if $item.ext=="mp3" OR $item.ext=="flac" OR $item.ext=="aac" OR $item.ext=="ac3" OR $item.ext=="wav" OR $item.ext=="riff"}
	<i class="ti ti-file-music icon" aria-hidden="true"></i>
	{else if $item.ext=="txt" OR $item.ext=="ini" OR $item.ext=="inf" OR $item.ext=="conf" OR $item.ext=="log"}
	<i class="ti ti-file-text icon" aria-hidden="true"></i>
	{else if $item.ext=="c" OR $item.ext=="cpp" OR $item.ext=="md" OR $item.ext=="php" OR $item.ext=="go"}
	<i class="ti ti-file-code icon" aria-hidden="true"></i>
	{else if $item.ext=="pdf"}
	<i class="ti ti-file-type-pdf icon" aria-hidden="true"></i>
	{else}
	<i class="ti ti-file icon" aria-hidden="true"></i>
	{/if}
{else}
	<img src="webdisk.php?action=displayExtension&ext={$item.ext}&sid={$sid}" alt="" {$wdicons_imgattr} />
{/if}
</span>
