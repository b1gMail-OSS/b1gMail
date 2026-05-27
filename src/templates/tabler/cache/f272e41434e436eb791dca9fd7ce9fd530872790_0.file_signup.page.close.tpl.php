<?php
/* Smarty version 5.8.0, created on 2026-05-25 15:47:51
  from 'file:nli/signup.page.close.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a146f27b11016_34578796',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'f272e41434e436eb791dca9fd7ce9fd530872790' => 
    array (
      0 => 'nli/signup.page.close.tpl',
      1 => 1779724054,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:nli/msp.footer.tpl' => 1,
    'file:nli/page.close.tpl' => 1,
  ),
))) {
function content_6a146f27b11016_34578796 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/nli';
if ((($tmp = $_smarty_tpl->getValue('nliCompactLayout') ?? null)===null||$tmp==='' ? false ?? null : $tmp)) {?>
			</div>
			<div class="card-footer bm-signup-card-footer">
				<p id="signupFinishHint" class="text-secondary small d-none mb-2"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"completesignup"), $_smarty_tpl);?>
</p>
				<div class="text-secondary small mb-3">
					<i class="ti ti-info-circle me-1" aria-hidden="true"></i>
					<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"iprecord"), $_smarty_tpl);?>

				</div>
				<div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
					<label class="form-check mb-0">
						<input type="checkbox" class="form-check-input" name="tos" value="true"<?php if ((true && (true && null !== ($_smarty_tpl->getValue('_safePost')['tos'] ?? null))) && $_smarty_tpl->getValue('_safePost')['tos'] == 'true') {?> checked="checked"<?php }?> />
						<span class="form-check-label">
							<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"accepttos"), $_smarty_tpl);?>

							<a href="#" data-bs-toggle="modal" data-bs-target="#tosModal"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"tos"), $_smarty_tpl);?>
</a>
						</span>
					</label>
					<button type="submit" id="signupSubmit" class="btn btn-success">
						<i class="ti ti-check me-1" aria-hidden="true"></i><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"submit"), $_smarty_tpl);?>

					</button>
				</div>
			</div>
			</form>
		</div>
		<?php $_smarty_tpl->renderSubTemplate("file:nli/msp.footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
	</div>
</div>
<?php } else {
$_smarty_tpl->renderSubTemplate("file:nli/page.close.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
}
}
}
