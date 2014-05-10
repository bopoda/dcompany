<?php

class Helper_User
{
	public static function getAvaSrc(array $user)
	{
		if ($user['avatar']) {
			return "/static/img/avatar/{$user['avatar']}";
		}

		return '/static/img/user.jpg';
	}
}