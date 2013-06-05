<?php

/**
 * File logger.
 *
 * @author     David Grudl
 */
class Logger
{
	/** @var resource */
	private $file;


	public function __construct($fileName)
	{
		$this->file = fopen($fileName, 'w');
	}


	public function log($s)
	{
		echo "$s        \n";
		fwrite($this->file, "$s\n");
	}

}
