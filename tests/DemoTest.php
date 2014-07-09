<?php

class DemoTest extends PHPUnit_Framework_TestCase
{
	public function testAny()
	{
		$this->assertEquals(4, 2 + 2);
	}

	public function testFail()
	{
		$this->assertTrue(false);
	}
}