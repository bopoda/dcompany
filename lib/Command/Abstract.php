<?php

abstract class Command_Abstract implements Core_Cli_CommandInterface
{
	protected function disallowConcurrentExecution()
	{
		$pidFile = PROJECT_PID_DIR . DIRECTORY_SEPARATOR . get_class($this);
		$pid = @file_get_contents($pidFile);

		if ($pid && is_dir("/proc/". $pid)) {
			echo 'Only one concurrent copy of this command is allowed.', PHP_EOL;
			exit;
		}
		
		file_put_contents($pidFile, getmypid());
	}
}