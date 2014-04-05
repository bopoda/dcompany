<?php

class Command_GenerateTestDataSuite implements Core_Cli_CommandInterface
{
	public function run($config = null, $outputPath = null)
	{
		$collector = new DbPacker_Configuration();

		$collector->addModelPrefix("Table_");

		if ($config) {
			$collector->importConfig($config, PROJECT_DIR . "/etc/db/$config");
		}
		else {
			foreach (glob(PROJECT_DIR . '/etc/db/*') as $config) {
				$collector->importConfig(basename($config), $config);
			}
		}

		$path = $outputPath
			? $outputPath
			: PROJECT_DIR . "/var/db";

		$collector->dump($path);

		echo `bzip2 -f --best $path/*/data/*.sql`;
	}
}