<?php

class Controller_Users
{
	public function usersList(Http_Request $request)
	{
		if (!Auth::instance()->isLogged()) {
			return new Http_Response_Redirect(Helper_Url::routeUrl('user_auth'));
		}
		$user = Auth::instance()->getUser();

		$users = Table_Users::me()->fetchAll();

		return new Http_Response_View(
			'users/list.html',
			array(
				'user' => $user,
				'users' => $users,
			)
		);
	}
}
