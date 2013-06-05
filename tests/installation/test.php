<?php

exec('php ../../installDeployment.php config.ini');

$sqlDir = __DIR__ . '/sql';
if(!is_dir($sqlDir)) {
	throw new Exception('Dir ' . $sqlDir . ' not exists');
} else {

	require __DIR__ . '/../../src/Config.php';
	require __DIR__ . '/../../libs/dibi.min.php';

	$config = parse_ini_file(realpath('config.ini'), TRUE);
	$config = new Config($config);

	dibi::connect($config->getDbConfig());

	$config = $config->getConfig();

	rmdir($sqlDir);
	dibi::query("DROP TABLE [{$config['table']}]");
}


