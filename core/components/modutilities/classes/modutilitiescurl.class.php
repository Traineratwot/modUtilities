<?php

	/**
	 * Created by Kirill Nefediev.
	 * Date: 27.05.2020
	 * Time: 11:51
	 */

	use Curl\Curl;
	if (!class_exists('Curl\Curl')) {
		require dirname(__DIR__) . '/vendor/autoload.php';
	}
	if (class_exists('Curl\Curl')) {
		class modUtilitiesCurl extends Curl
		{
			/* @var modX $modX */
			public $modx;
			/* @var modUtilities $util */
			public $util;

			public function __construct(modX &$modx, modUtilities &$util,$param)
			{
				$this->modx = $modx;
				$this->util = $util;
				$this->DEFAULT_TIMEOUT = $param['DEFAULT_TIMEOUT'];
				try {
					parent::__construct();
				} catch (ErrorException $e) {
					$this->modx->log(MODX_LOG_LEVEL_ERROR, $e->getMessage(), $e->getCode(), __FUNCTION__, $e->getFile(), $e->getLine());
				}
			}
		}
	}