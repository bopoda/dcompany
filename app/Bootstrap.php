<?php

class Bootstrap extends Core_Bootstrap
{
	public function init()
	{
		parent::init();
		$this->initHelperUrl();
		$this->initProfiler();

		session_start();
	}

	protected function initHelperUrl()
	{
		Helper_Url::setRouter($this->getRouter());
	}

	protected function initProfiler()
	{
		if (Core_Config::getValue('profilerEnabled')) {
			Pinba::enable();
		}
	}
}
