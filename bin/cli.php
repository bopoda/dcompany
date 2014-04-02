#!/usr/bin/php
<?php

// If APPLICATION_ENV is not defined, define less-vulnerable environment
define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'local'));

require dirname(__FILE__) . '/../initialize.inc.php';

// Bootstrap application
require_once APPLICATION_PATH . '/Bootstrap.php';
$bootstrapper = new Bootstrap();
$bootstrapper
	->appendEnvConfig('env/base.php')
	->appendEnvConfig('env/cli.php')
	->appendEnvConfig('env/' . APPLICATION_ENV . '.php')
	->init();

// Run front controller
$commandsPath = APPLICATION_PATH . '/commands/';
$commandLoader = new Core_Cli_CommandLoader($commandsPath);
$frontController = new Core_Controller_Cli($commandLoader, $argv);

try {
	$frontController->run();
}
catch (Exception $e) {
	error_log($e->getMessage() . PHP_EOL . $e->getTraceAsString());
}