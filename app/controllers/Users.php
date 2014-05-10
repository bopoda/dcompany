<?php

class Controller_Users
{
	public function usersInfo(Http_Request $request)
	{
		if (!Auth::instance()->isLogged()) {
			return new Http_Response_Redirect(Helper_Url::routeUrl('user_auth'));
		}
		$user = Auth::instance()->getUser();



		return new Http_Response_View(
			'users/info.html',
			array(
				'user' => $user,
			)
		);
	}
}
