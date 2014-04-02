<?php

class Controller_Test
{
	public function test()
	{
		return new Http_Response_Json(array(
			'test' => true,
		));
	}
}
