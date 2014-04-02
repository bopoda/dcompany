<?php

error_reporting(E_ALL);

// set some misc defaults
date_default_timezone_set('Europe/Minsk');
mb_internal_encoding("UTF-8");
mb_regex_encoding("UTF-8");
setlocale(LC_ALL, 'en_US.UTF-8');

define('PROJECT_DIR', dirname(__FILE__));
define('PROJECT_PID_DIR', PROJECT_DIR . DIRECTORY_SEPARATOR . 'var/run');
define('PROJECT_BIN_DIR', PROJECT_DIR . DIRECTORY_SEPARATOR . 'bin');

// Define path to application directory
define('APPLICATION_PATH', PROJECT_DIR . DIRECTORY_SEPARATOR . 'app');

// Application environment. Sets in .htaccess. "Production" by default.
defined('APPLICATION_ENV')
	|| define(
				'APPLICATION_ENV',
				(isset($_SERVER['APPLICATION_ENV'])
					? $_SERVER['APPLICATION_ENV']
					: (getenv('APPLICATION_ENV')
						? getenv('APPLICATION_ENV')
						: 'production'
					)
				)
			);

require dirname(__FILE__) . "/framework/initialize.inc.php";

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
	PROJECT_DIR . DIRECTORY_SEPARATOR . 'etc',

	PROJECT_DIR . DIRECTORY_SEPARATOR . 'lib',
	PROJECT_DIR . DIRECTORY_SEPARATOR . '3rdparty',

	APPLICATION_PATH . DIRECTORY_SEPARATOR . 'views',

	get_include_path(),
)));

// Register autoloaders
// -- app
require_once 'Core/Loader/Application.php';
$appLoader = new Core_Loader_Application(APPLICATION_PATH);
$appLoader->register();

// -- lib (include_path)
require_once 'Core/Loader/Library.php';
$libraryLoader = new Core_Loader_Library();
$libraryLoader->register();