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
		public $pyComand;
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
			$this->pyComand = $this->modx->config['modUtilities_py_command'];
			if (!$this->getVersion()) {
				$this->modx->log(MODX_LOG_LEVEL_ERROR, 'Python not found for: "' . $this->pyComand . '"');
				throw new Exception('Python not found for: "' . $this->pyComand . '"');
			}
		}

		private function getVersion()
		{
			$out = exec($this->pyComand . ' --version', $out2, $code);
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
			if (empty($this->modules)) {
				exec('pip freeze', $lines, $code);
				if ($code == 0) {
					foreach ($lines as $k => $line) {
						$lines[$k] = explode('==', $line);
					}
					$this->modules = $lines;
					return TRUE;
				}
				$this->modules === 'error';
				return FALSE;
			}
			return TRUE;
		}

		public function python($source, $json)
		{
			return $this->py($source, $json);
		}

		public function run($source, $json = TRUE)
		{
			return $this->py($source, $json);
		}

		public function py($source, $json, $param = [])
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
				$out = exec($this->pyComand . ' ' . $source, $lines, $code);
				if ($code !== 0) {
					throw new Exception('py error: ' . $lines . " " . $out, $code);
				}
				return [
					'success' => TRUE,
					'out' => $out,
					'json' => $json ? $this->util->jsonValidate($out) : FALSE,
					'lines' => $lines,
					'code' => $code,
					'time' => microtime(TRUE) - $timer,
				];

			} catch (Exception $e) {
				return [
					'success' => FALSE,
					'msg' => $e->getMessage(),
					'code' => $e->getCode(),
				];
			}
		}
	}