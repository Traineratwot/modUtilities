<?php

	class modUtilitiesRest
	{
		/* @var modX $modX */
		public $modx;
		/* @var modUtilities $util */
		public $util;
		/* @var xPDOObject|Utilrest $rest */
		public $rest;
		/* @var xPDOObject|Utilrestcategory $rest */
		public $restCategory;
		/** @var array */
		public $permission;
		/** @var array */
		public $param;

		public function __construct(modX &$modx, modUtilities &$util, $param)
		{
			$this->modx = $modx;
			$this->util = $util;
			$this->properties = $param;
			if (!$this->modx->addPackage('modUtilities', MODX_CORE_PATH . 'components/modutilities/model/', 'modutil_')) {
				$this->modx->log(MODX_LOG_LEVEL_FATAL, 'can`t addPackage "modUtilities"');
				http_response_code(503);
				die;
			}
			if ($this->init()) {
				$response = $this->run();
				$this->writeLog();
				exit($response);
			}

		}

		final protected function writeLog(){

		}

		public function init()
		{
			/** @var Utilrest $tmp */
			$tmp = $this->modx->getObject('Utilrest', [
				'url' => $this->getProperty('url'),
			]);
			if ($tmp and $tmp instanceof \Utilrest_mysql) {
				$dd = $tmp->get('category', '', '', '');
				$category = $this->modx->getObject('Utilrestcategory', [
					'id' => $tmp->getProperty('category'),
				]);
				$this->rest = $tmp;
				$this->restCategory = $category;
				try {
					$this->permission = json_decode($tmp->getProperty('permission', $category->getProperty('permission', '{"allow": {"usergroup": "all"}}')), 1, 512, JSON_THROW_ON_ERROR);
					$this->param = json_decode($tmp->getProperty('param', $category->getProperty('param', '{"headers": [], "httpResponseCode": 200, "scriptProperties": []}')), 1, 512, JSON_THROW_ON_ERROR);
				} catch (JsonException $e) {
					$this->modx->log(MODX_LOG_LEVEL_FATAL, 'JSON in permission or param ' . $e->getMessage(), $e->getCode(), __FUNCTION__, $e->getFile(), $e->getLine());
					http_response_code(501);
					die;
				}
				$this->setHeader();
				if (!$this->security()) {
					http_response_code(401);
					$this->modx->sendForward($this->modx->config['error_page']);
					die();
				}
				return TRUE;
			}
			$this->rest = FALSE;
			return FALSE;
		}

		final protected function security()
		{
			try {
				$userId = (int)$this->modx->user->get('id');
				if ((bool)$this->restCategory->getProperty('BASIC_auth') or (bool)$this->rest->getProperty('BASIC_auth')) {
					if (!isset($_SERVER['PHP_AUTH_USER'])) {
						header('WWW-Authenticate: Basic realm="' . $this->modx->config['site_name'] . '_REST"');
						header('HTTP/1.0 401 Unauthorized');
						exit;
					} else {
						/** @var modUser $user */
						$user = $this->modx->getObject('modUser', [
							'username' => $_SERVER['PHP_AUTH_USER'],
						]);
						if ($user) {
							if ($user->passwordMatches($_SERVER['PHP_AUTH_PW'])) {
								$userId = $user->get('id');
							} else {
								throw new Exception('wrong password', 0);
							}
						} else {
							throw new Exception('user not found', 0);
						}
					}
				}
				$allow = TRUE;
				foreach ($this->permission['allow'] as $key => $value) {
					switch ($key) {
						case 'usergroup':
							if ($value === 'all') {
								throw new Exception(TRUE, 1);
							}
							foreach ($value as $group => $role) {
								if ($this->modx->util->member($userId, $group, $role)) {
									$allow = TRUE;
								}
							}
							break;
						case 'userIds':
							if (array_search($userId, $value) !== FALSE) {
								throw new Exception(TRUE, 1);
							}
							break;
						default:
							throw new Exception(TRUE, 1);
							break;
					}
				}
				foreach ($this->permission['deny'] as $key => $value) {
					switch ($key) {
						case 'usergroup':
							if ($value === 'all') {
								$allow = FALSE;
							}
							foreach ($value as $group => $role) {
								if ($this->modx->util->member($userId, $group, $role)) {
									$allow = FALSE;
								}
							}
							break;
						case 'userIds':
							if (array_search($userId, $value) !== FALSE) {
								throw new Exception(FALSE, 0);
							}
							break;
						default:
							throw new Exception(FALSE, 0);
							break;
					}
				}
				throw new Exception($allow, $allow);
			} catch (Exception $e) {
				return (bool)$e->getCode();
			}
			return FALSE;
		}

		public function setHeader(): void
		{
			header_remove();
			$allowMethod = $this->rest->get('allowMethod');
			header("Access-Control-Allow-Methods: $allowMethod");
			header("Access-Control-Allow-Origin: *");
			header("Cross-Origin-Resource-Policy: cross-origin");
			foreach ($this->param['headers'] as $key => $value) {
				header("$key: $value");
			}
			http_response_code($this->param['httpResponseCode'] ?: 200);
		}

		final public function getProperty($k, $default = NULL)
		{
			return array_key_exists($k, $this->properties) ? $this->properties[$k] : $default;
		}

		public function run()
		{
			try {
				$scriptProperties = $this->param['scriptProperties'];
				$scriptProperties['REST'] = &$this;
				$snippet = $this->rest->get('snippet');
				if (!$snippet) {
					throw new Exception('undefended Snippet');
				}
				/** @var modSnippet $sp */
				$sp = $this->modx->getObject('modSnippet', [
					'id:LIKE' => $snippet,
					'OR:name:LIKE' => $snippet,
				]);
				if ($sp) {
					return $sp->process($scriptProperties);
				}
				if (!class_exists('modUtilRestProcessor')) {
					include __DIR__ . DIRECTORY_SEPARATOR . 'modutilrestprocessor.php';
				}
				$snippet = strtr($snippet, [
					'{core_path}' => MODX_CORE_PATH,
					'{base_path}' => MODX_BASE_PATH,
					'{assets_path}' => MODX_ASSETS_PATH,
					'{manager_path}' => MODX_MANAGER_PATH,
					'{processors_path}' => MODX_PROCESSORS_PATH,
					'{connectors_path}' => MODX_CONNECTORS_PATH,
				]);
				if (!file_exists($snippet)) {
					throw new Exception('file ' . $snippet . ' not FOUND');
				}
				/** @var modUtilRestProcessor $process */
				$className = include $snippet;
				if (!$className or gettype($className) != 'string') {
					throw new Exception('undefended class. add `return "classname" to end of file `' . $snippet);
				}
				$process = new $className($this->modx, $scriptProperties, $this);
				if (!$process instanceof modUtilRestProcessor and !$process instanceof modProcessor) {
					throw new Exception('invalid class');
				}
				return $process->process();
			} catch (Exception $e) {
				$this->errors = $e->getMessage();
				$this->modx->log(MODX_LOG_LEVEL_ERROR, $e->getMessage(), $e->getCode(), __FUNCTION__, $e->getFile(), $e->getLine());
				return FALSE;
			}
		}
	}