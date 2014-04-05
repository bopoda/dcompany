<?php

return array(
	'main' => array(
		'url' => '/',
		'controller' => 'Controller_Orders',
		'action' => 'main'
	),
	'user_auth' => array(
		'url' => '/auth',
		'controller' => 'Controller_User',
		'action' => 'auth'
	),
	'user_logout' => array(
		'url' => '/logout',
		'controller' => 'Controller_User',
		'action' => 'logout'
	),
	'orderAdd' => array(
		'url' => '/order/add',
		'controller' => 'Controller_Orders',
		'action' => 'orderAdd'
	),
	'test' => array(
		'url' => '/test',
		'controller' => 'Controller_Test',
		'action' => 'test'
	),
);