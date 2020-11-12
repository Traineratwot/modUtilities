<?php

	class modUtilitiesPython
	{

		/**
		 * @var modX
		 */
		private $modx;
		/**
		 * @var modUtilities
		 */
		private $util;
		private $properties;
		/**
		 * @var mixed
		 */
		private $pyComand;

		public function __construct(modX &$modx, modUtilities &$util, $param)
		{
			$timer = microtime(TRUE);
			$this->modx = $modx;
			$this->util = $util;
			$this->properties = $param;
			$this->pyComand = $this->modx->config['modUtilities_py_command'];
			$this->getVersion();
		}

		private function getVersion(){
			exec($this->pyComand.' -v',$out);
			var_dump($out);
		}

		public function python($source)
		{
			return $this->py($source);
		}

		public function run($source)
		{
			return $this->py($source);
		}

		public function py($source)
		{

		}
	}