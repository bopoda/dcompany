<?php

class Controller_Orders
{
	public function main(Http_Request $request)
	{
		if (!Auth::instance()->isLogged()) {
			return new Http_Response_Redirect(Helper_Url::routeUrl('user_auth'));
		}

		$user = Auth::instance()->getUser();
		if ($user['role_id'] == Table_Users::ROLE_MANAGER) {
			return $this->viewMainPageByManager($user);
		}
		elseif ($user['role_id'] == Table_Users::ROLE_SERVICE) {

		}
	}

	private function viewMainPageByManager(array $user)
	{
		$orders = Table_Orders::me()->fetchByUserId($user['id']);

		return new Http_Response_View(
			'manager/order/main.html',
			array(
				'user' => $user,
				'orders' => $orders,
			)
		);
	}

	public function orderAdd()
	{
		if (!Auth::instance()->isLogged()) {
			return new Http_Response_Redirect(Helper_Url::routeUrl('user_auth'));
		}

		$user = Auth::instance()->getUser();

		return new Http_Response_View(
			'manager/order/add.html',
			array(
				'user' => $user,
			)
		);
	}

}
