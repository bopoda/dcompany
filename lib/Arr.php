<?php

class Arr
{
	/**
	 * @param $array
	 * @param $key
	 * @param null $default
	 * @return mixed
	 */
	public static function get($array, $key, $default = NULL)
	{
		return isset($array[$key]) ? $array[$key] : $default;
	}
}
