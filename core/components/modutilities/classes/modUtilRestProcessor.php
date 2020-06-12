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
		public $headers;

		public function __construct(modX &$modx, array $properties = [], modUtilitiesRest &$REST)
		{
			$this->REST = $REST;
			parent::__construct($modx, $properties);
		}

		public function process()
		{
			$this->initialize();
			foreach ($this->headers as $key => $value) {
				header("$key: $value");
			}
			switch ($_SERVER['REQUEST_METHOD']) {
				case 'GET':
					return $this->GET();
					break;
				case 'POST':
					return $this->POST();
					break;
				case 'PUT':
					return $this->PUT();
					break;
				case 'DELETE':
					return $this->DELETE();
					break;
				case 'PATH':
					return $this->PATH();
					break;
				case 'CONNECT':
					return $this->CONNECT();
					break;
				case 'HEAD':
					return $this->HEAD();
					break;
				case 'OPTIONS':
					return $this->OPTIONS();
					break;
				case 'TRACE':
					return $this->TRACE();
					break;
			}
		}

		public function GET(){}
		public function POST(){}
		public function PUT(){}
		public function DELETE(){}
		public function PATH(){}
		public function CONNECT(){}
		public function HEAD(){}
		public function OPTIONS(){}
		public function TRACE(){}
	}

	return 'modUtilRestProcessor';