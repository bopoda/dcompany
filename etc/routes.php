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
	'ordersByStatus' => array(
		'url' => '/orders/list/status=(private|pending|closed)',
		'controller' => 'Controller_Orders',
		'action' => 'ordersByStatus'
	),
//	'ajaxOrderEditField' => array(
//		'url' => '/ajax/order/editField',
//		'controller' => 'Controller_Orders',
//		'action' => 'ajaxOrderEditField'
//	),
	'ajaxAddOrderHtml' => array(
		'url' => '/ajax/order/addOrderHtml',
		'controller' => 'Controller_Orders',
		'action' => 'ajaxAddOrderHtml'
	),
	'test' => array(
		'url' => '/test',
		'controller' => 'Controller_Test',
		'action' => 'test'
	),
);