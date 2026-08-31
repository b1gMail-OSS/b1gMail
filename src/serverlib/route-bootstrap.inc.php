<?php
/*
 * Minimal bootstrap for route front controllers (no target script yet).
 * B1GMAIL_DIR is defined by init.inc.php — do not define it here.
 */

require_once dirname(__DIR__) . '/serverlib/route.inc.php';

// require() must run in global scope so config ($mysql) and init stay global.
require RouteResolveAdminScript();
