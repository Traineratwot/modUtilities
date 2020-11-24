<?php


	class modUtilitiesPython
	{

		/**
		 * @var modX
		 */
		public $modx;
		/**
		 * @var modUtilities
		 */
		public $util;
		public $properties;
		/**
		 * @var mixed
		 */
		public $pyCommand;
		/**
		 * @var string
		 */
		public $version;
		public $modules = FALSE;
		private $requres;

		public function __construct(modX &$modx, modUtilities &$util, $param)
		{
			$this->modx = $modx;
			$this->util = $util;
			$this->properties = $param;
			$this->pyCommand = $this->modx->getOption('modUtilities_py_command', $this->modx->config, 'py');
			if (!$this->getVersion()) {
				$this->modx->log(MODX_LOG_LEVEL_ERROR, 'Python not found for: "' . $this->pyCommand . '"');
				throw new Exception('Python not found for command: "' . $this->pyCommand . '"');
			}
		}

		private function getVersion()
		{
			$out = exec($this->pyCommand . ' --version', $out2, $code);
			if ($code == 0) {
				$this->version = trim(str_ireplace('python', '', $out));
				return TRUE;
			} else {
				return FALSE;
			}
		}

		public function pyRequire($Requires = [])
		{
			if ($this->modules === FALSE) {
				$this->getPipModules();
			}
			if (is_array($this->modules)) {
				if (is_array($Requires) and !empty($Requires)) {
					$installed = [];
					$needUpdate = [];
					$noInstalled = [];
					if ($this->util->isAssoc($Requires)) {
						foreach ($Requires as $module => $version) {
							if (array_key_exists($module, $this->modules)) {
								if (version_compare($version, $this->modules[$module]) >= 0) {
									$needUpdate[] = $module;
								}
								$installed[] = $module;
							} else {
								$noInstalled[] = $module;
							}
						}
					} else {
						foreach ($Requires as $module) {
							if (array_key_exists($module, $this->modules)) {
								$installed[] = $module;
							} else {
								$noInstalled[] = $module;
							}
						}
					}
					$this->requres = [
						'installed' => $installed,
						'needUpdate' => $needUpdate,
						'noInstalled' => $noInstalled,
					];
					return $this->requres;
				}
				$this->requres = TRUE;
				return $this->modules;
			}
			$this->requres = FALSE;
			return FALSE;
		}

		public function getPipModules()
		{
			if (!$this->util->cacheManager) {
				$this->util->cacheManager = $this->modx->getCacheManager();
			}
			$lines = $this->util->cacheManager->get('pythonPips', [xPDO::OPT_CACHE_KEY => 'modUtilities']);
			if (!empty($lines)) {
				$this->modules = $lines;
				return $lines;
			}
			exec('pip freeze', $lines, $code);
			if ($code == 0) {
				foreach ($lines as $k => $line) {
					$lines[$k] = explode('==', $line);
				}
				$this->modules = $lines;
				$this->util->cacheManager->set('pythonPips', $lines, 3600, [xPDO::OPT_CACHE_KEY => 'modUtilities']);
				return $lines;
			}
			$this->modules === 'error';
			return FALSE;
		}

		public function python($source, $param)
		{
			return $this->py($source, $param);
		}

		public function run($source, $param)
		{
			return $this->py($source, $param);
		}

		public function py($source, $param = [])
		{
			try {
				$timer = microtime(TRUE);
				$source = realpath($source);
				if (!file_exists($source)) {
					throw new Exception('file ' . $source . ' not found', 404);
				}
				if ($this->requres === FALSE or (!empty($this->requres['noInstalled']) or !empty($this->requres['needUpdate']))) {
					throw new Exception('py requres: ' . $this->util->varsInfo($this->requres), 404);
				}
				$command = $this->pyCommand . ' ' . $source . ' ' . implode(' ', $param);
				$out = exec($command, $lines, $code);
				if ($code !== 0) {
					throw new Exception('py error: ' . $out, $code);
				}

				if ($this->util->jsonValidate($out)) {
					return [
						'out' => $this->util->output['jsonValidate']['result'],
						'lines' => $lines,
						'code' => $code,
						'command' => $command,
						'time' => microtime(TRUE) - $timer,
					];
				}
				return [
					'success' => TRUE,
					'out' => $out,
					'lines' => $lines,
					'code' => $code,
					'command' => $command,
					'time' => microtime(TRUE) - $timer,
				];

			} catch (Exception $e) {
				return [
					'success' => FALSE,
					'msg' => $e->getMessage(),
					'code' => $e->getCode(),
					'command' => $command,
				];
			}
		}
	}