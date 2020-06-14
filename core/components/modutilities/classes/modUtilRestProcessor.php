<?php
	/**
	 * Date: 12.06.2020
	 * Time: 17:29
	 */

	abstract class modUtilRestProcessor extends modProcessor
	{
		/* @var modX modx */
		public $modx;
		/* @var modUtilitiesRest REST */
		public $REST;
		/**
		 * @var array
		 */
		public $headers = [];
		/**
		 * @var array
		 */
		public $LanguageTopics = [];

		final public function __construct(modX &$modx, array $properties = [], modUtilitiesRest &$REST)
		{
			$this->REST = $REST;
			parent::__construct($modx, $properties);
		}

		final public function _getProps()
		{
			$this->input['GET'] = $_GET;
			$this->input['POST'] = $_POST;
			$put = file_put_contents('php://input');
			try {
				$put = json_decode($put, TRUE, 512, JSON_THROW_ON_ERROR);
			} catch (JsonException $e) {
			} finally {
				$this->input['PUT'] = $put;
			}
		}


		final public function run()
		{
			$this->_getProps();
			$initialized = $this->initialize();
			foreach ($this->headers as $key => $value) {
				header("$key: $value");
			}
			$topics = $this->LanguageTopics;
			foreach ($this->LanguageTopics as $topic) {
				$this->modx->lexicon->load($topic);
			}

			if ((bool)$initialized !== TRUE) {
				$o = $this->failure($initialized);
			} else {
				$o = $this->process();
			}
			return $o;
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
	}

	return 'modUtilRestProcessor';