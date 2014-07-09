<?php

require dirname(__FILE__) . '/../initialize.inc.php';
require 'DatabaseTestCase.php';
require 'ArrayDataSet.php';

set_include_path(implode(PATH_SEPARATOR, array(
	dirname(__FILE__),

	get_include_path(),
)));

// Bootstrap application
require_once APPLICATION_PATH . '/Bootstrap.php';
$bootstrapper = new Bootstrap();
$bootstrapper
	->appendEnvConfig('env/base.php')
	->appendEnvConfig('env/local.php')
	->appendEnvConfig('env/test.php');
$bootstrapper->init();

DbSimple_Container::setModelMapping(array());
DbSimple_Container::setTableMapping(array());