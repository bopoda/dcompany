<?php

// {{{
// This code is needed to track ALL abnormal shutdowns,
// that is why we register at the beginning.
// This portion of code should be as simple as possible,
// and should not have and dependencies.
	ob_start();

	define('HI_LOG_ERROR', 1790);

	// also define after bootstrap:
	// NORMAL_SHUTDOWN_SUCCEEDED, DISPLAY_ERRORS_ENABLED, HITS_DISABLED

	function check_shutdown_fatal_error()
	{
		// this constant will be defined at the bottom of this script
		// if smth goes wrong, it will never be defined.
		if (defined('NORMAL_SHUTDOWN_SUCCEEDED'))
			return;

		@header('HTTP/1.0 500 Internal Server Error', true, 500);

		// If we guaranteely know that displayErrors=true — do this
		if (defined("DISPLAY_ERRORS_ENABLED") && DISPLAY_ERRORS_ENABLED) {
			$error = error_get_last();

			@header('Content-type: text/html; charset=utf8', true, 500);

			echo <<<EOT
<h1>Internal Server Error</h1>
{$error['message']}<br />
at {$error['file']}:{$error['line']}
EOT;
		}

		// If we guarantelly know that hits are disabled, do not send them
		if (!defined('HITS_DISABLED') || !HITS_DISABLED) {
			@file_get_contents('http://hits.informer.com/log.php?id=' . HI_LOG_ERROR);
		}
	}

	register_shutdown_function('check_shutdown_fatal_error');

// }}}

require dirname(__FILE__) . '/../initialize.inc.php';

// Bootstrap application
require_once APPLICATION_PATH . '/Bootstrap.php';
$bootstrapper = new Bootstrap();
$bootstrapper
	->appendEnvConfig('env/base.php')
	->appendEnvConfig('env/' . APPLICATION_ENV . '.php');
$bootstrapper->init();

// This is for abnormal shutdowns.
define('HITS_DISABLED', !Core_Config::getValue('hits/enabled'));
define('DISPLAY_ERRORS_ENABLED', Core_Config::getValue('displayErrors'));

// Run front controller
$frontController = $bootstrapper->initFrontController();
$request = new Http_Request($_GET, $_POST, $_COOKIE, $_SERVER, $_ENV);


try {
	$frontController->dispatch($request);
}
catch (Exception $e) {

	while (@ob_get_clean());

	@header('HTTP/1.0 500 Internal Server Error', true, 500);

	Hits::hit('global_error');

	// get the name of exception
	$clname = get_class($e);

	// shorten paths
	$file = str_replace(
		array(
			 PROJECT_DIR . DIRECTORY_SEPARATOR,
			 FRAMEWORK_DIR . DIRECTORY_SEPARATOR
		),
		'',
		$e->getFile()
	);

	error_log(
		'Request '.$request->getUri() . ' caused  ' . $clname
		. ' at ' . $file . ':' . $e->getLine()
		. ': ' . $e->getMessage()
		. PHP_EOL . $e->getTraceAsString() . PHP_EOL
	);

	NewRelic::sendException($e, get_class($e) . '(' . $e->getMessage() . ')');

	if (Core_Config::getValue('displayErrors')) { // dump an exception
		echo <<<EOT
<h1>Internal Server Error</h1>
{$clname} : {$e->getMessage()} <br />
at {$file}:{$e->getLine()}
<hr />
<h2>Call Stack</h2>
<pre>{$e->getTraceAsString()}</pre>
EOT;
	}
}

define('NORMAL_SHUTDOWN_SUCCEEDED', true);

