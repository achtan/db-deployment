<?php

exec('php ../../installDeployment.php config.ini');

exec('php ../../deployment.php config.ini');

require __DIR__ . '/../../src/Config.php';
require __DIR__ . '/../../libs/dibi.min.php';

$config = parse_ini_file(realpath('config.ini'), TRUE);
$config = new Config($config);

dibi::connect($config->getDbConfig());

$config = $config->getConfig();

$rows = dibi::fetchPairs("SELECT [id], [fileName] FROM [{$config['table']}]");

$except = array(
	'1' => '1-create-table.sql',
	'3' => '3-insert-row.sql'
);
if(count($rows) == 2 && $rows == $except) {
	dibi::query("TRUNCATE TABLE [{$config['table']}]");
	dibi::query("DROP TABLE [test]");
} else {
	throw new Exception('Zly pocet riadkov v tabulke');
}
