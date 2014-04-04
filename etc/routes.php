<?php

return array(
    'test' => array(
        'url' => '/',
        'controller' => 'Controller_Test',
        'action' => 'test'
    ),
	'auth' => array(
		'url' => '/auth',
		'controller' => 'Controller_User',
		'action' => 'auth'
	),
	'logout' => array(
		'url' => '/auth',
		'controller' => 'Controller_User',
		'action' => 'logout'
	),
);