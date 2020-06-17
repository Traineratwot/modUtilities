<?php

	class modUtilitiesOuterAPI
	{
		/* @var modX $modX */
		public $modx;
		/* @var modUtilities $util */
		public $util;

		public function __construct(modX &$modx, modUtilities &$util, $param)
		{
			$this->modx = $modx;
			$this->util = $util;


		}

		public function newQuery()
		{
			return new curlQuery($this);
		}

	}

	class curlQuery
	{
		/* @var modUtilitiesOuterAPI $API */
		public $API;

		public function __construct(modUtilitiesOuterAPI $API)
		{
			$this->API = $API;
			$this->curl = ;
		}


	}