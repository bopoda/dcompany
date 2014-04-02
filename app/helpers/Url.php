<?php

class Helper_Url
{
	static private $router;

	static public function setRouter(Dklab_Route_Uri $router)
	{
		self::$router = $router;
	}

	static protected function getRouter()
	{
		if (!self::$router) {
			throw new Core_Exception(
				'No router specified. Please, inject router instance via setRouter method.'
			);
		}

		return self::$router;
	}

	/**
	 * Get url by relative path to server root
	 *
	 * All links must be wrapped by this method to easily switch base url if necessary
	 *
	 * @static
	 * @param string $relativePath like /domain.com
	 * @return string
	 */
	static public function url($relativePath)
	{
		// Now acts just like wrapper. Base url can be added if necessary
		return $relativePath;
	}

	/**
	 * Generate url by route
	 *
	 * @static
	 * @throws Core_Exception
	 * @param $routeName
	 * @param $data
	 * @return string
	 */
	static public function routeUrl($routeName, $data = array())
	{
		$router = self::getRouter();

		$data['name'] = $routeName;

		$routeUrl = $router->assemble($data);
		$routeUrl = preg_replace('%/$%', '', $routeUrl);

		return self::url($routeUrl);
	}
}
