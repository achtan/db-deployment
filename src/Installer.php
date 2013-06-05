<?php

class Installer {

	/**
	 * @var array
	 */
	protected $config;

	/**
	 * @var Logger
	 */
	private $logger;


	/**
	 * @param Config $config
	 * @param Logger $logger
	 */
	public function __construct(Config $config, Logger $logger)
	{
		$this->config = $config->getConfig();
		$this->logger = $logger;
	}


	public function install()
	{
		$this->createDirectory();
		$this->createTable();
	}


	private function createDirectory()
	{
		$sqlDir = $this->config['sqlDir'];
		if(!is_dir($sqlDir)) {
			@mkdir($sqlDir, 0777, TRUE);
			$this->logger->log("Directory {$sqlDir} created!");
		} else {
			$this->logger->log("Directory {$sqlDir} already exists!");
		}
	}


	private function createTable()
	{
		$table = $this->config['table'];
		$charset = $this->config['db']['charset'];
		$tableExists = dibi::query("SHOW TABLES LIKE '{$table}'");
		if(!count($tableExists)) {
			dibi::query("
CREATE TABLE `{$table}` (
 `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `fileName` varchar(255) DEFAULT NULL,
 PRIMARY KEY (`id`),
 KEY `fileName` (`fileName`)
) ENGINE=InnoDB DEFAULT CHARSET={$charset};");
			$this->logger->log("Table {$table} created!");
		} else {
			$this->logger->log("Table {$table} already exists!");
		}
	}


}
