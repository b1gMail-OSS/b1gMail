<?php
/* Smarty version 5.8.0, created on 2026-05-26 14:21:09
  from 'file:li/webdisk.icons.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a159035a83750_49442639',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '3dda91728c7ea48a4d6e6dea6e2fd752d98ae6d8' => 
    array (
      0 => 'li/webdisk.icons.tpl',
      1 => 1779712350,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a159035a83750_49442639 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/li';
if (!(true && ($_smarty_tpl->hasVariable('wdicons_size_class') && null !== ($_smarty_tpl->getValue('wdicons_size_class') ?? null)))) {?>
	<?php if ((true && ($_smarty_tpl->hasVariable('fa_additionalparamclass') && null !== ($_smarty_tpl->getValue('fa_additionalparamclass') ?? null))) && ($_smarty_tpl->getValue('fa_additionalparamclass') == 'fa-3x' || $_smarty_tpl->getValue('fa_additionalparamclass') == 'fa-4x')) {?>
		<?php $_smarty_tpl->assign('wdicons_size_class', 'bm-webdisk-icon-lg', false, 32);?>
	<?php } else { ?>
		<?php $_smarty_tpl->assign('wdicons_size_class', 'bm-webdisk-icon-sm', false, 32);?>
	<?php }
}?>
<span class="bm-webdisk-item-icon <?php echo $_smarty_tpl->getValue('wdicons_size_class');?>
" <?php echo $_smarty_tpl->getValue('wdicons_additionalparam');?>
>
<?php if ($_smarty_tpl->getValue('use_fa_icons') == 1) {?>
	<?php if ($_smarty_tpl->getValue('item')['ext'] == ".FOLDER") {?>
	<i class="ti ti-folder icon" aria-hidden="true"></i>
	<?php } elseif ($_smarty_tpl->getValue('item')['ext'] == "jpg" || $_smarty_tpl->getValue('item')['ext'] == "jpeg" || $_smarty_tpl->getValue('item')['ext'] == "png" || $_smarty_tpl->getValue('item')['ext'] == "gif" || $_smarty_tpl->getValue('item')['ext'] == "bmp") {?>
	<i class="ti ti-photo icon" aria-hidden="true"></i>
	<?php } elseif ($_smarty_tpl->getValue('item')['ext'] == "zip" || $_smarty_tpl->getValue('item')['ext'] == "rar" || $_smarty_tpl->getValue('item')['ext'] == "ace" || $_smarty_tpl->getValue('item')['ext'] == "gz" || $_smarty_tpl->getValue('item')['ext'] == "bz2" || $_smarty_tpl->getValue('item')['ext'] == "pak" || $_smarty_tpl->getValue('item')['ext'] == "pk3" || $_smarty_tpl->getValue('item')['ext'] == "gcf" || $_smarty_tpl->getValue('item')['ext'] == "tar") {?>
	<i class="ti ti-file-zip icon" aria-hidden="true"></i>
	<?php } elseif ($_smarty_tpl->getValue('item')['ext'] == "mpg" || $_smarty_tpl->getValue('item')['ext'] == "mpeg" || $_smarty_tpl->getValue('item')['ext'] == "divx" || $_smarty_tpl->getValue('item')['ext'] == "avi" || $_smarty_tpl->getValue('item')['ext'] == "mkv" || $_smarty_tpl->getValue('item')['ext'] == "mp4" || $_smarty_tpl->getValue('item')['ext'] == "m2ts" || $_smarty_tpl->getValue('item')['ext'] == "mov" || $_smarty_tpl->getValue('item')['ext'] == "qt" || $_smarty_tpl->getValue('item')['ext'] == "webm") {?>
	<i class="ti ti-movie icon" aria-hidden="true"></i>
	<?php } elseif ($_smarty_tpl->getValue('item')['ext'] == "odt" || $_smarty_tpl->getValue('item')['ext'] == "doc" || $_smarty_tpl->getValue('item')['ext'] == "docx" || $_smarty_tpl->getValue('item')['ext'] == "rtf" || $_smarty_tpl->getValue('item')['ext'] == "wri" || $_smarty_tpl->getValue('item')['ext'] == "sdw") {?>
	<i class="ti ti-file-type-doc icon" aria-hidden="true"></i>
	<?php } elseif ($_smarty_tpl->getValue('item')['ext'] == "odp" || $_smarty_tpl->getValue('item')['ext'] == "ppt" || $_smarty_tpl->getValue('item')['ext'] == "pptx") {?>
	<i class="ti ti-file-type-ppt icon" aria-hidden="true"></i>
	<?php } elseif ($_smarty_tpl->getValue('item')['ext'] == "ods" || $_smarty_tpl->getValue('item')['ext'] == "xls" || $_smarty_tpl->getValue('item')['ext'] == "xlsx") {?>
	<i class="ti ti-file-spreadsheet icon" aria-hidden="true"></i>
	<?php } elseif ($_smarty_tpl->getValue('item')['ext'] == "mp3" || $_smarty_tpl->getValue('item')['ext'] == "flac" || $_smarty_tpl->getValue('item')['ext'] == "aac" || $_smarty_tpl->getValue('item')['ext'] == "ac3" || $_smarty_tpl->getValue('item')['ext'] == "wav" || $_smarty_tpl->getValue('item')['ext'] == "riff") {?>
	<i class="ti ti-file-music icon" aria-hidden="true"></i>
	<?php } elseif ($_smarty_tpl->getValue('item')['ext'] == "txt" || $_smarty_tpl->getValue('item')['ext'] == "ini" || $_smarty_tpl->getValue('item')['ext'] == "inf" || $_smarty_tpl->getValue('item')['ext'] == "conf" || $_smarty_tpl->getValue('item')['ext'] == "log") {?>
	<i class="ti ti-file-text icon" aria-hidden="true"></i>
	<?php } elseif ($_smarty_tpl->getValue('item')['ext'] == "c" || $_smarty_tpl->getValue('item')['ext'] == "cpp" || $_smarty_tpl->getValue('item')['ext'] == "md" || $_smarty_tpl->getValue('item')['ext'] == "php" || $_smarty_tpl->getValue('item')['ext'] == "go") {?>
	<i class="ti ti-file-code icon" aria-hidden="true"></i>
	<?php } elseif ($_smarty_tpl->getValue('item')['ext'] == "pdf") {?>
	<i class="ti ti-file-type-pdf icon" aria-hidden="true"></i>
	<?php } else { ?>
	<i class="ti ti-file icon" aria-hidden="true"></i>
	<?php }
} else { ?>
	<img src="webdisk.php?action=displayExtension&ext=<?php echo $_smarty_tpl->getValue('item')['ext'];?>
&sid=<?php echo $_smarty_tpl->getValue('sid');?>
" alt="" <?php echo $_smarty_tpl->getValue('wdicons_imgattr');?>
 />
<?php }?>
</span>
<?php }
}
