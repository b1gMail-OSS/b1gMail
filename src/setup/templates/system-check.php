<h1><?php echo SetupH($lang_setup['syscheck']); ?></h1>
<p><?php echo $lang_setup['syscheck_text']; ?></p>
<?php SetupRenderCheckTable($rows); ?>
<?php echo SetupRenderChmod($chmodCommands); ?>
<?php echo SetupAlert($nextStep == STEP_MYSQL ? 'success' : 'danger', $nextStep == STEP_MYSQL ? $lang_setup['checkok_text'] : $lang_setup['checkfail_text'], '', ['class' => 'mt-3']); ?>
