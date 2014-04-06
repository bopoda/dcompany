<?php

// base config is ready for production

return array(
	'displayErrors' => true,

	'enableTransparentCache' => false,
	'profilerEnabled' => false,

	'servicesPaths' => array(
		'hits' => 'http://hits.informer.com/log.php',
	),

	'defaultDb' => 'db.master',

	'db' => array(
		'db.master' => array(
			'adapter' => 'mysql',
			'host' => 'localhost',
			'name' => 'jekaby_dcompany',
			'username' => 'jekaby',
			'password' => 'vU8ApAha7enUpumA',
		),
		'db.slave' => array(
			'adapter' => 'mysql',
			'host' => 'localhost',
			'name' => 'jekaby_dcompany',
			'username' => 'jekaby',
			'password' => 'vU8ApAha7enUpumA',
		),
		'db' => array(
			'adapter' => 'proxy',
			'read' => 'db.slave',
			'write' => 'db.master',
		),
	),

	'modelMapping' => array(

	),

	'tableMapping' => array(

	),

	'defaultStorage' => 'cache',
	'storage' => array(
		'cache' => array(
			'adapter'  => 'memcache',
			'host'     => 'localhost',
			'port'     => '11211',
		),
		'datahash' => array(
			'adapter'  => 'memcache',
			'host'     => 'localhost',
			'port'     => '11211',
		),
	),

	'slotMapping' => array(

	),

    'hits' => array(
		'enabled' 	=> false,
		'mapping'	=> array(

		)
	),

	'modelRowCache' => array(
	),

	'sphinxRowCache' => array(
	),

	'defaultQueueServer' => array(
		'adapter' => 'PeclAmqp',
		'host' => '127.0.0.1',
		'port' => '5672',
		'username' => 'guest',
		'password' => 'guest',
		'vhost' => '/',
	),

	'internals' => array(
		'http-timeout' => 5,
	),

);
