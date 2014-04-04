<?php

class Controller_User
{
	public function auth(Http_Request $request)
	{
		$message = '';
		if ($request->getPost()) {
			$result = Auth::instance()->authorization(
				$request->getPostVar('email'),
				$request->getPostVar('password')
			);

			if (!$result) {
				$message = 'E-mail или пароль введён неверно.';
			}
		}

		if (Auth::instance()->isLogged()) {
			return new Http_Response_Redirect('/');
		}

		return new Http_Response_View(
			'auth.html',
			array(
				'message' => $message,
			)
		);
	}

	public function logout()
	{
		Auth::instance()->logout();

		return new Http_Response_Redirect(Helper_Url::routeUrl('auth'));
	}
}
