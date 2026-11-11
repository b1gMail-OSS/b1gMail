<h1><?php echo SetupH($lang_setup['misc']); ?></h1>
<p><?php echo $lang_setup['misc_text']; ?></p>
<div class="mb-3"><label class="form-label" for="adminuser"><?php echo SetupH($lang_setup['adminuser']); ?></label><input class="form-control" id="adminuser" name="adminuser" type="text" value="<?php echo SetupH(SetupInput('adminuser', 'admin')); ?>" required="required" autocomplete="username" /></div>
<div class="mb-3"><label class="form-label" for="adminpw"><?php echo SetupH($lang_setup['adminpw']); ?></label><?php echo SetupPasswordField('adminpw', 'adminpw', SetupInput('adminpw'), true); ?></div>
<div class="mb-3"><label class="form-label" for="adminpw2"><?php echo SetupH($lang_setup['adminpw_confirm']); ?></label><?php echo SetupPasswordField('adminpw2', 'adminpw2', SetupInput('adminpw2'), true); ?></div>
<div class="mb-3"><label class="form-label" for="domains"><?php echo SetupH($lang_setup['domains']); ?></label><textarea class="form-control setup-domains" id="domains" name="domains" rows="4"><?php echo SetupH(SetupInput('domains', "example.com\nexample.net\nexample.org")); ?></textarea></div>
<div class="mb-3"><label class="form-label" for="url"><?php echo SetupH($lang_setup['url']); ?></label><input class="form-control" id="url" name="url" type="url" value="<?php echo SetupH(SetupInput('url', SetupDetectSelfUrl())); ?>" required="required" /></div>
