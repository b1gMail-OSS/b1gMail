			{if $use_fa_icons==1}
				{if $item['ext']==".FOLDER"}
				<i class="fa fa-folder-o {$fa_additionalparamclass}" aria-hidden="true" {$wdicons_additionalparam}></i>
				{else if $item.ext=="jpg" OR $item.ext=="jpeg" OR $item.ext=="png" OR $item.ext=="gif" OR $item.ext=="bmp"}
				<i class="fa fa-file-image-o {$fa_additionalparamclass}" aria-hidden="true" {$wdicons_additionalparam}></i>
				{else if $item.ext=="zip" OR $item.ext=="rar" OR $item.ext=="ace" OR $item.ext=="gz" OR $item.ext=="bz2" OR $item.ext=="pak" OR $item.ext=="pk3" OR $item.ext=="gcf" OR $item.ext=="tar"}
				<i class="fa fa-file-archive-o {$fa_additionalparamclass}" aria-hidden="true" {$wdicons_additionalparam}></i>
				{else if $item.ext=="mpg" OR $item.ext=="mpeg" OR $item.ext=="divx" OR $item.ext=="avi" OR $item.ext=="mkv" OR $item.ext=="mp4" OR $item.ext=="m2ts" OR $item.ext=="mov" OR $item.ext=="qt" OR $item.ext=="webm"}
				<i class="fa fa-file-movie-o {$fa_additionalparamclass}" aria-hidden="true" {$wdicons_additionalparam}></i>
				{else if $item.ext=="odt" OR $item.ext=="doc" OR $item.ext=="docx" OR $item.ext=="rtf" OR $item.ext=="wri" OR $item.ext=="sdw"}
				<i class="fa fa-file-word-o {$fa_additionalparamclass}" aria-hidden="true" {$wdicons_additionalparam}></i>
				{else if $item.ext=="odp" OR $item.ext=="ppt" OR $item.ext=="pptx"}
				<i class="fa fa-file-powerpoint-o {$fa_additionalparamclass}" aria-hidden="true" {$wdicons_additionalparam}></i>
				{else if $item.ext=="ods" OR $item.ext=="xls" OR $item.ext=="xlsx"}
				<i class="fa fa-file-excel-o {$fa_additionalparamclass}" aria-hidden="true" {$wdicons_additionalparam}></i>
				{else if $item.ext=="mp3" OR $item.ext=="flac" OR $item.ext=="aac" OR $item.ext=="ac3" OR $item.ext=="wav" OR $item.ext=="riff"}
				<i class="fa fa-file-sound-o {$fa_additionalparamclass}" aria-hidden="true" {$wdicons_additionalparam}></i>
				{else if $item.ext=="txt" OR $item.ext=="ini" OR $item.ext=="inf" OR $item.ext=="conf" OR $item.ext=="log"}
				<i class="fa fa-file-text-o {$fa_additionalparamclass}" aria-hidden="true" {$wdicons_additionalparam}></i>
				{else if $item.ext=="c" OR $item.ext=="cpp" OR $item.ext=="md" OR $item.ext=="php" OR $item.ext=="go"}
				<i class="fa fa-file-code-o {$fa_additionalparamclass}" aria-hidden="true" {$wdicons_additionalparam}></i>
				{else if $item.ext=="pdf"}
				<i class="fa fa-file-pdf-o {$fa_additionalparamclass}" aria-hidden="true" {$wdicons_additionalparam}></i>
				{else}
				<i class="fa fa-file-o {$fa_additionalparamclass}" aria-hidden="true" {$wdicons_additionalparam}></i>
				{/if}
			{else}
				<img src="webdisk.php?action=displayExtension&ext={$item.ext}&sid={$sid}" border="0" alt="" {$wdicons_imgattr} {$wdicons_additionalparam}>
			{/if}