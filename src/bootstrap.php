<?php

echo '
DB Deployment
--------------
';

if (version_compare(PHP_VERSION, '5.3.0', '<')) {
	throw new Exception('Deployment requires PHP 5.3.0 or newer.');
}

require __DIR__ . '/Logger.php';
require __DIR__ . '/Config.php';
require __DIR__ . '/Installer.php';
require __DIR__ . '/Deployment.php';
require __DIR__ . '/../libs/dibi.min.php';

// load config file
if (!isset($_SERVER['argv'][1])) {
	die("Usage: {$_SERVER['argv'][0]} <config_file> [-t | --test]\n\n");
}

$configFile = realpath($_SERVER['argv'][1]);
if (!$configFile) {
	die("Missing config file {$_SERVER['argv'][1]}");
}

//$options = getopt('t', array('test'));
$config = parse_ini_file($configFile, TRUE);


$logger = new Logger(preg_replace('#\.\w+$#', '.log', $configFile));

$logger->log("Started at " . date('[Y/m/d H:i]'));
$logger->log("Config file is $configFile");

$config = new Config($config);

dibi::connect($config->getDbConfig());


if(INSTALL === TRUE) {
	$logger->log("Installing\n");
	$installer = new Installer($config, $logger);
	$installer->install();
}

if(DEPLOY === TRUE) {
	$logger->log("Deploying\n");
	$deployment = new Deployment($config, $logger);
	$deployment->deploy();
}

$logger->log("\nFinished at " . date('[Y/m/d H:i]'));

