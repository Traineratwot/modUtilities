<?php
	/**
	 * Date: 12.06.2020
	 * Time: 17:29
	 */

	abstract class modUtilRestProcessor extends modProcessor
	{
		/* @var modX modx */
		public $modx;
		/* @var modutilitiesRest REST */
		public $REST;
		/**
		 * @var modUtilities
		 */
		public $util;
		/**
		 * @var array
		 */
		public $headers = [];
		/**
		 * @var array
		 */
		public $LanguageTopics = [];
		/**
		 * @var array
		 */
		public $GET;
		/**
		 * @var array
		 */
		public $POST;
		/**
		 * @var array
		 */
		public $PUT;
		/**
		 * @var array
		 */
		public $REQUEST;
		/**
		 * query
		 * @var string
		 */
		public $url;
		/**
		 * query
		 * @var integer
		 */
		public $httpResponseCode = 0;

		final public function __construct(modX &$modx, array $properties = [], modutilitiesRest &$REST)
		{
			$this->REST             = $REST;
			$this->util             = $REST->util;
			$this->user             = $properties['user'];
			$this->GET              = $properties['input']['GET'];
			$this->httpResponseCode = $properties['httpResponseCode'];
			$this->POST             = $properties['input']['POST'];
			$this->PUT              = $properties['input']['PUT'];
			$this->HEADERS          = $properties['input']['HEADERS'];
			$this->REQUEST          = array_merge($properties['input']['GET'], $properties['input']['POST']);
			$this->REQUEST['PUT']   = $this->PUT;
			$this->FILES = [];
			if (!empty($_FILES)) {
				try {
					$this->FILES = $this->util->files();
					if (get_class($this->FILES) == 'modutilitiesPostFiles') {
						$this->FILES = $this->FILES->FILES;
					}
				} catch (Exception $e) {
					$modx->log(modX::LOG_LEVEL_ERROR, $e->getMessage(), '', __METHOD__ ?: __FUNCTION__, __FILE__, __LINE__);
				}
			}
			$_alias = $modx->context->getOption('request_param_alias', 'q');
			$this->url = $this->GET[$_alias];
			unset($this->GET[$_alias]);
			parent::__construct($modx, $properties);
		}

		final public function run()
		{
			$initialized = $this->initialize();

			foreach ($this->headers as $key => $value) {
				header("$key: $value");
			}
			foreach ($this->LanguageTopics as $topic) {
				$this->modx->lexicon->load($topic);
			}

			if ($initialized !== TRUE) {
				$o = $this->failure($initialized);
			} else {
				$o = $this->process();
				if ($this->httpResponseCode) {
					http_response_code($this->httpResponseCode);
				}
			}
			if (is_array($o) or is_object($o)) {
				$this->util->headerJson();
				$o = json_encode($o, 256);
			}
			return (string)$o;
		}

		public function process()
		{
			switch ($_SERVER['REQUEST_METHOD']) {
				case 'GET':
					return $this->GET();
				case 'POST':
					return $this->POST();
				case 'PUT':
					return $this->PUT();
				case 'DELETE':
					return $this->DELETE();
				case 'PATH':
					return $this->PATH();
				case 'CONNECT':
					return $this->CONNECT();
				case 'HEAD':
					return $this->HEAD();
				case 'OPTIONS':
					return $this->OPTIONS();
				case 'TRACE':
					return $this->TRACE();
			}
		}

		public function GET()
		{
		}

		public function POST()
		{
		}

		public function PUT()
		{
		}

		public function DELETE()
		{
		}

		public function PATH()
		{
		}

		public function CONNECT()
		{
		}

		public function HEAD()
		{
		}

		public function OPTIONS()
		{
		}

		public function TRACE()
		{
		}

		public function success($msg = '', $object = NULL)
		{
			return [
				'success' => TRUE,
				'message' => $msg,
				'object' => $object,
				'code' => $this->httpResponseCode,
			];
		}

		public function failure($msg = '', $object = NULL, $error = [])
		{
			return [
				'success' => FALSE,
				'message' => $msg,
				'object' => $object,
				'errors' => $error,
				'code' => $this->httpResponseCode,
			];
		}
	}

	return 'modUtilRestProcessor';