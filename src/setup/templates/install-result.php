<h1><?php echo SetupH($lang_setup['installing']); ?></h1>
<p><?php echo $lang_setup['installing_text']; ?></p>
<?php SetupCloseCardBody(); ?>
<div class="table-responsive"><table class="table table-vcenter card-table">
<?php foreach ([
    'inst_dbstruct' => 'Database structure',
    'inst_defaultcfg' => 'Default configuration',
    'inst_admin' => 'Administrator',
    'inst_defaultgroup' => 'Default group',
    'inst_postmaster' => 'Postmaster',
    'inst_exdata' => 'Example data',
    'inst_config' => 'Config file',
] as $key => $fallback): ?>
<tr><th><?php echo SetupH($lang_setup[$key] ?? $fallback); ?></th><td><?php echo SetupResultIcon('ok'); ?></td></tr>
<?php endforeach; ?>
</table></div>
<?php SetupOpenCardBody(); ?>
<?php echo SetupAlert('warning', $lang_setup['remove_setup'], '', ['class' => 'mt-3']); ?>
<?php if (!$lockOk) { echo SetupAlert('danger', $lang_setup['lock_failed']); } ?>
<p><?php echo $lang_setup['finished_text']; ?></p>
<dl class="setup-creds">
<dt><?php echo SetupH($lang_setup['userlogin']); ?></dt><dd><a target="_blank" href="<?php echo SetupH($result['url']); ?>"><?php echo SetupH($result['url']); ?></a></dd>
<dt><?php echo SetupH($lang_setup['adminlogin']); ?></dt><dd><a target="_blank" href="<?php echo SetupH($result['url']); ?>admin/"><?php echo SetupH($result['url']); ?>admin/</a></dd>
<dt><?php echo SetupH($lang_setup['adminuser']); ?></dt><dd><?php echo SetupH($result['adminuser']); ?></dd>
<dt><?php echo SetupH($lang_setup['adminpw']); ?></dt><dd><?php echo SetupH($result['adminpw']); ?></dd>
</dl>
