<?php

class Deployment {

	/**
	 * @var array
	 */
	protected $config;

	/**
	 * @var Logger
	 */
	private $logger;

	public function __construct(Config $config, Logger $logger)
	{
		$this->config = $config->getConfig();
		$this->logger = $logger;
	}


	public function deploy()
	{
		$files = $this->findFilesToProcess();

		$filesCount = count($files);
		$i = 1;
		foreach($files as $fileId => $file) {
			$pathName = $file['pathName'];
			$baseName = $file['baseName'];
			$content = file_get_contents($pathName);

			$queries = explode(';', $content);
			$queries = array_map('trim', $queries);
			$queries = array_filter($queries);

			dibi::begin();
			try {
				foreach($queries as $query) {
					dibi::query($query);
				}
				$table = $this->config['table'];
				dibi::query("INSERT INTO [{$table}]", array('id' => $fileId, 'fileName' => $baseName));
				dibi::commit();
			} catch (Exception $e) {
				dibi::rollback();
				throw $e;
			}

			$queriesCount = count($queries);
			$this->writeProgress($i, $filesCount, $baseName . "\t-- " . $queriesCount . ' ' . ($queriesCount != 1 ? 'queries' : 'query') . ' executed');
			$i++;
		}
	}

	/**
	 * @return array
	 */
	private function findFilesToProcess()
	{
		$files = $this->findAllFiles();
		$files = $this->filterOldFiles($files);

		return $files;
	}

	/**
	 * @return array
	 */
	private function findAllFiles()
	{
		$sqlDir = $this->config['sqlDir'];
		$files = array();
		/** @var $fileInfo DirectoryIterator */
		foreach(new DirectoryIterator($sqlDir) as $fileInfo) {
			if($fileInfo->isDot()) continue;
			$fileName = $fileInfo->getFilename();
			preg_match('#^([1-9]+)#', $fileName, $matches);
			$fileId = $matches[1];
			if(!$fileId || array_key_exists($fileId, $files)) {
				$this->logger->log("Ambiguous key for {$fileName}");
			}
			$files[$fileId] = array(
				'pathName' => $fileInfo->getPathname(),
				'baseName' => $fileInfo->getBasename(),
			);
		}

		return $files;
	}

	/**
	 * @param array $files
	 *
	 * @return array
	 */
	private function filterOldFiles(array $files)
	{
		$table = $this->config['table'];
		$skipFiles = dibi::fetchPairs("SELECT id FROM [{$table}] WHERE [id] IN %in", array_keys($files));
		$skipFilesCount = count($skipFiles);
		$i = 1;
		foreach($skipFiles as $fileId) {
			$this->writeProgress($i, $skipFilesCount, $files[$fileId]['baseName'] . "\t-- skipped");
			unset($files[$fileId]);
			$i++;
		}

		return $files;
	}


	/**
	 * @param $count
	 * @param $total
	 * @param $file
	 * @param null $percent
	 */
	private function writeProgress($count, $total, $file, $percent = NULL)
	{
		$len = strlen((string) $total);
		$s = sprintf("(% {$len}d of %-{$len}d) %s", $count, $total, $file);
		if ($percent === NULL) {
			$this->logger->log($s);
		} else {
			echo $s . " [$percent%]\x0D";
		}
	}


}
