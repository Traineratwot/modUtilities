<?php

	class modutilitiesRest
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
		/** @var Utilreststats $log */
		public $log;

		/** @var int $logLimit */
		public $logLimit = 1000;

		public function __construct(modX &$modx, modUtilities &$util, $param)
		{
			$timer = microtime(TRUE);
			$this->modx = $modx;
			$this->util = $util;
			$this->properties = $param;
			$this->logLimit = $this->modx->getOption('modUtilRestlogLimit', NULL, 1000, FALSE);

			if (!$this->modx->addPackage('modutilities', MODX_CORE_PATH . 'components/modutilities/model/')) {
				$this->modx->log(MODX_LOG_LEVEL_FATAL, 'can`t addPackage "modutilities"');
				http_response_code(503);
				die;
			}
			if ($this->init()) {
				$this->log = $this->modx->newObject('Utilreststats');
				$this->log->set('rest_id', $this->rest->get('id'));
				$response = $this->run();
				$this->log->set('output', $response);
				$this->log->set('time', round(microtime(TRUE) - $timer, 6));
				$this->log->set('datetime', date(DATE_ATOM));
				$this->clearLog();
				$this->log->save();
				exit($response);
			}
		}

		public function clearLog()
		{
			$this->logLimit--;
			$count = $this->modx->getCount('Utilreststats');
			if ($count > $this->logLimit) {
				$l = $count - $this->logLimit;
				$q = "DELETE FROM " . $this->log->_table . " order by id ASC LIMIT $l ";
				if (($q = $this->modx->prepare($q))) {
					$q->execute();
				}
			}

		}

		public function init()
		{
			/** @var Utilrest $tmp */
			$tmp = $this->modx->getObject('Utilrest', [
				'url' => $this->getProperty('url'),
			]);
			if ($tmp and $tmp instanceof Utilrest) {
				$cat = $tmp->getProperty('category', '', '', '');
				$category = $this->modx->getObject('Utilrestcategory', [
					'name' => $cat,
				]);
				if (!$category instanceof Utilrestcategory and is_numeric($cat)) {
					$this->modx->log(modX::LOG_LEVEL_ERROR, "DEPRECATED REST category: read this <a href='https://github.com/Traineratwot/modUtilities/wiki/Fix-category'>[ https://github.com/Traineratwot/modUtilities/wiki/Fix-category ]</a>", '', __METHOD__, __FILE__, __LINE__);
					$category = $this->modx->getObject('Utilrestcategory', [
						'id' => $cat,
					]);
				}
				if (!$category instanceof Utilrestcategory) {
					return 'invalid REST category';
				}
				$this->rest = $tmp;
				$this->restCategory = $category;
				$replaceConfig = [];
				$this->permission = $tmp->getProperty('permission', $category->getProperty('permission', '{"allow": {"usergroup": "all"}}'));
				if ($this->util->strTest($this->permission, ['[++'])) {
					if (empty($replaceConfig)) {
						$replaceConfig = $this->replaceConfig();
					}
					$this->permission = strtr($this->permission, $replaceConfig);
				}
				$this->permission = $this->util->jsonValidate($this->permission);
				if (!$this->permission) {
					$this->modx->log(MODX_LOG_LEVEL_FATAL, 'invalid JSON in permission');
					http_response_code(501);
					die;
				}
				$this->param = $tmp->getProperty('param', $category->getProperty('param', '{"headers": [], "httpResponseCode": 200, "scriptProperties": []}'));
				if ($this->util->strTest($this->param, ['[++'])) {
					if (empty($replaceConfig)) {
						$replaceConfig = $this->replaceConfig();
					}
					$this->param = strtr($this->param, $replaceConfig);
				}
				$this->param = $this->util->jsonValidate($this->param);
				if (!$this->param) {
					$this->modx->log(MODX_LOG_LEVEL_FATAL, 'invalid JSON in param ');
					http_response_code(501);
					die;
				}
				if (!$this->setHeader() or !$this->security()) {
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
				if (((bool)$this->restCategory->getProperty('BASIC_auth') or (bool)$this->rest->getProperty('BASIC_auth')) and !$userId) {
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
								$user->addSessionContext('web');
								$userId = $user->get('id');
							} else {
								throw new Exception('wrong password', 0);
							}
						} else {
							throw new Exception('user not found', 0);
						}
					}
				}
				$this->userId = $userId;
				$allow = TRUE;
				foreach ($this->permission['allow'] as $key => $value) {
					if(is_null($value)){
						break;
					}
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
							if (in_array($userId, $value) !== FALSE) {
								throw new Exception(TRUE, 1);
							} else {
								if (!empty($value)) {
									throw new Exception(FALSE, 0);
								}
							}
							break;
						case 'ip':
							$ip = $this->util->getIP();
							if ($ip !== FALSE) {
								if (is_array($value)) {
									if (in_array($ip, $value)) {
										throw new Exception(TRUE, 1);
									} else {
										throw new Exception(FALSE, 0);
									}
								} else {
									if ($value == 'this') {
										if ($_SERVER['SERVER_ADDR'] == $ip) {
											throw new Exception(TRUE, 1);
										} else {
											throw new Exception(FALSE, 0);
										}
									}
								}
							}
						default:
							throw new Exception(TRUE, 1);
							break;
					}
				}
				foreach ($this->permission['deny'] as $key => $value) {
					if(is_null($value)){
						break;
					}
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
						case 'ip':
							$ip = $this->util->getIP();
							if ($ip !== FALSE) {
								if (is_array($value)) {
									if (in_array($ip, $value)) {
										throw new Exception(FALSE, 0);
									} else {
										throw new Exception(TRUE, 1);
									}
								} else {
									if ($value == 'this') {
										if ($_SERVER['SERVER_ADDR'] == $ip) {
											throw new Exception(FALSE, 0);
										} else {
											throw new Exception(TRUE, 1);
										}
									}
								}
							}
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

		public function setHeader()
		{
			header_remove();
			$allowMethod = $this->rest->get('allowMethod', $this->restCategory->get('allowMethod', []));
			header("Access-Control-Allow-Methods: $allowMethod");
			header("Access-Control-Allow-Origin: *");
			header("Cross-Origin-Resource-Policy: cross-origin");
			if (is_array($this->param['headers'])) {
				foreach ($this->param['headers'] as $key => $value) {
					header("$key: $value");
				}
			}
			if (!empty($allowMethod) and is_array($allowMethod)) {
				if (!in_array($_SERVER['REQUEST_METHOD'], $allowMethod, TRUE)) {
					return FALSE;
				}
			}
			http_response_code($this->param['httpResponseCode'] ?: 200);
			return TRUE;
		}

		final public function getProperty($k, $default = NULL)
		{
			return array_key_exists($k, $this->properties) ? $this->properties[$k] : $default;
		}

		public function run()
		{
			try {
				//сбор входных данных
				$scriptProperties = $this->param['scriptProperties'];
				$scriptProperties['httpResponseCode'] = $this->param['httpResponseCode'];
				$scriptProperties['REST'] = &$this;

				$scriptProperties['input']['GET'] = $_GET;
				$scriptProperties['input']['POST'] = $_POST;
				$scriptProperties['input']['HEADERS'] = $this->util->getThisHeaders();
				$put = file_get_contents('php://input');

				$put = $this->util->jsonValidate($put, 512) ?: $put;
				$scriptProperties['input']['PUT'] = $put ?: NULL;
				$tt = $this->util->jsonValidate($scriptProperties['input']);
				$this->log->set('input', $tt ?: NULL);
				//сбор данных о подключении
				$ip = $this->util->getIP();
				$scriptProperties['user'] = [
					'ip' => $ip,
					'modxId' => $this->userId ?: 0,
					'httpMethod' => @$_SERVER['REQUEST_METHOD'],
				];

				$this->log->set('user', json_encode($scriptProperties['user'], 256));

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
					$scriptProperties = array_merge($scriptProperties, $_REQUEST);
					return $sp->process($scriptProperties);
				}
				if (!class_exists('modX')) {
					return 'ERROR MODX not FOUND';
				}
				if (!class_exists('modProcessor')) {
					include MODX_CORE_PATH . DIRECTORY_SEPARATOR . 'model' . DIRECTORY_SEPARATOR . 'modx' . DIRECTORY_SEPARATOR . 'modprocessor.class.php';
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
				return $process->run();
			} catch (Exception $e) {
				$this->errors = $e->getMessage();
				$this->modx->log(MODX_LOG_LEVEL_ERROR, $e->getMessage(), $e->getCode(), __FUNCTION__, $e->getFile(), $e->getLine());
				return FALSE;
			}
		}

		public function replaceConfig()
		{
			$repl = [];
			foreach ($this->modx->config as $key => $value) {
				$repl['[[++' . $key . ']]'] = $value;
			}
			return $repl;
		}

	}
