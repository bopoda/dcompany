<?php

class Auth
{
	protected static $instance;

	/**
	 * Singleton pattern
	 *
	 * @return Auth
	 */
	public static function instance()
	{
		if ( ! isset(Auth::$instance)) {
			Auth::$instance = new self();
		}

		return Auth::$instance;
	}

	public function isLogged()
	{
		return isset($_SESSION['user']);
	}

	public function getUser()
	{
		if ($this->isLogged()) {
			return $_SESSION['user'];
		}
	}

	public function authorization($email, $password)
	{
		$user = Table_Users::me()->fetchRowByEmail($email);

		if ($user && $user['password_md5'] == $this->getPasswordHash($password)) {

			$_SESSION['user'] = $user;

			return true;
		}

		return false;
	}

	public function logout()
	{
		unset($_SESSION['user']);
	}

	private function getPasswordHash($password)
	{
		return md5($password);
	}

}