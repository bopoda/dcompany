<?php

class Command_Test extends Command_Abstract
{
	public function run()
	{
		echo 'test' . PHP_EOL;
		exit;
	}

}