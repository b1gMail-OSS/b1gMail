<?php
/*
 * Pretty-URL route registry for plugins (RegisterRoutes hook).
 *
 * Example in a plugin:
 *   public function RegisterRoutes()
 *   {
 *       BMRoute()->nli('my-page', array('action' => 'myPluginAction'));
 *       BMRoute()->liStart('myfeature');
 *       $this->registerRouteMatcher('_matchMyPaths', '_legacyMyPaths');
 *   }
 */

/** @var BMRouteRegistry|null */
$bmRouteRegistry = null;

/** @var array<int, array{priority: int, fn: callable}>|null */
$routePublicMatchers = null;

/** @var array<int, array{priority: int, fn: callable}>|null */
$routePublicLegacyConverters = null;

/**
 * @return BMRouteRegistry
 */
function BMRoute()
{
	global $bmRouteRegistry;

	if($bmRouteRegistry === null)
		$bmRouteRegistry = new BMRouteRegistry();

	return $bmRouteRegistry;
}

/**
 * @param callable|array{0: object, 1: string} $fn
 * @param callable|array{0: object, 1: string}|null $legacyFn
 * @param int $priority Higher runs first
 */
function RouteRegisterMatcher($fn, $legacyFn = null, $priority = 10)
{
	global $routePublicMatchers, $routePublicLegacyConverters;

	if($routePublicMatchers === null)
		$routePublicMatchers = array();
	$routePublicMatchers[] = array('priority' => (int)$priority, 'fn' => $fn);

	if($legacyFn !== null)
	{
		if($routePublicLegacyConverters === null)
			$routePublicLegacyConverters = array();
		$routePublicLegacyConverters[] = array('priority' => (int)$priority, 'fn' => $legacyFn);
	}
}

/**
 * @param array<int, string> $segments
 * @return array{script: string, params: array<string, string>}|null
 */
function RouteRunMatchers(array $segments)
{
	global $routePublicMatchers;

	if(!is_array($routePublicMatchers) || empty($routePublicMatchers))
		return null;

	$sorted = $routePublicMatchers;
	usort($sorted, function($a, $b) {
		return $b['priority'] - $a['priority'];
	});

	foreach($sorted as $entry)
	{
		$result = call_user_func($entry['fn'], $segments);
		if(is_array($result) && isset($result['script']))
			return $result;
	}

	return null;
}

/**
 * @param string $script
 * @param array<string, mixed> $params
 * @return array{path: string, extra: array<string, string>}|null
 */
function RouteRunLegacyConverters($script, array $params)
{
	global $routePublicLegacyConverters;

	if(!is_array($routePublicLegacyConverters) || empty($routePublicLegacyConverters))
		return null;

	$sorted = $routePublicLegacyConverters;
	usort($sorted, function($a, $b) {
		return $b['priority'] - $a['priority'];
	});

	foreach($sorted as $entry)
	{
		$result = call_user_func($entry['fn'], $script, $params);
		if(is_array($result) && isset($result['path']))
			return $result;
	}

	return null;
}

class BMRouteRegistry
{
	/**
	 * NLI route: index.php + query params.
	 *
	 * @param string $path Path without leading slash (lowercase segments)
	 * @param array<string, string> $params
	 */
	public function nli($path, array $params = array())
	{
		RouteRegisterPublicRoute($path, 'index.php', $params);
	}

	/**
	 * Shorthand: index.php?action={action} at /{path}.
	 *
	 * @param string $action Value for action= (case-sensitive for FileHandler)
	 * @param string|null $path Pretty path; defaults to lowercase action
	 */
	public function nliAction($action, $path = null)
	{
		if($path === null)
			$path = strtolower((string)$action);

		$this->nli($path, array('action' => (string)$action));
	}

	/**
	 * LI route to a root PHP script.
	 *
	 * @param string $path
	 * @param string $script e.g. start.php, prefs.php
	 * @param array<string, string> $params
	 */
	public function li($path, $script, array $params = array())
	{
		RouteRegisterPublicRoute($path, $script, $params);
	}

	/**
	 * LI: start.php?action=… at /start/{action} (or custom path).
	 *
	 * @param string $action
	 * @param string|null $path
	 */
	public function liStart($action, $path = null)
	{
		if($path === null)
			$path = 'start/' . strtolower((string)$action);

		$this->li($path, 'start.php', array('action' => (string)$action));
	}

	/**
	 * Dynamic path matcher (incoming request).
	 *
	 * @param callable|array{0: object, 1: string} $matchFn
	 * @param callable|array{0: object, 1: string}|null $legacyFn
	 * @param int $priority
	 */
	public function matcher($matchFn, $legacyFn = null, $priority = 10)
	{
		RouteRegisterMatcher($matchFn, $legacyFn, $priority);
	}
}
