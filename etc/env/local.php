<?php

return array(
	'displayErrors' => true,

	'enableTransparentCache' => false,
	'profilerEnabled' => false,

	'db' => array(
		'db.master' => array(
			'adapter' => 'mysql',
			'host' => '127.0.0.1',
			'name' => 'dcompany',
			'username' => 'root',
			'password' => '',
		),
		'db.slave' => array(
			'adapter' => 'mysql',
			'host' => '127.0.0.1',
			'name' => 'dcompany',
			'username' => 'root',
			'password' => '',
		),
	),

	'storage' => array(
		'cache' => array(
			'adapter'  => 'memcache',
			'host'     => '127.0.0.1',
			'port'     => '11211',
		),
		'datahash' => array(
			'adapter'  => 'memcache',
			'host'     => '127.0.0.1',
			'port'     => '11211',
		),
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
		'http-timeout' => 30,
	),

);
