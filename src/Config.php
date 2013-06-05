<?php

class Config {

	/**
	 * @var array
	 */
	protected $defaultConfig = array(
		'rootDir' => '.',
		'sqlDir' => '/sql',
		'db' => array(
			'driver'   => 'mysql',
			'host'     => 'localhost',
			'username' => 'root',
			'password' => 'root',
			'database' => 'test',
			'charset'  => 'utf8',
		),
		'table' => '__db-deployment',
	);

	/**
	 * @var array
	 */
	protected $config;


	/**
	 * @param array $config
	 */
	public function __construct(array $config)
	{
		$dbConfig = array_merge($this->defaultConfig['db'], $config['db']);
		$config['db'] = $dbConfig;
		$config = array_merge($this->defaultConfig, $config);

		$config['rootDir'] = realpath($config['rootDir']);
		$config['sqlDir'] = $config['rootDir'].$config['sqlDir'];

		$this->config = $config;
	}


	/**
	 * @return array
	 */
	public function getConfig()
	{
		return $this->config;
	}


	/**
	 * @return array
	 */
	public function getDbConfig()
	{
		return $this->config['db'];
	}
}
