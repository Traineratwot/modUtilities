<?php
	/**
	 * Created by Kirill Nefediev.
	 * Date: 09.06.2020
	 * Time: 18:48
	 */
	if (!isset($modx)) {
		$base_path = __DIR__;
		// Ищем MODX
		while (!file_exists($base_path . '/config.core.php')) {
			$base_path = dirname($base_path);
		}
		if (file_exists($base_path . '/index.php')) {
			ini_set('display_errors', 1);
			ini_set("max_execution_time", 50);
			define('MODX_API_MODE', TRUE);
			require $base_path . '/index.php';
		} else {
			die("modx not found");
		}
	}
	if (!isset($modx)) {
		die("modx not found");
	}

	use PHPUnit\Framework\TestCase;


	class modUtilitiesTest extends TestCase
	{

		/**
		 * @var modX
		 */
		public $modx;

		public function test_ExistUtil()
		{
			global $modx;
			$this->modx = &$modx;
			if (isset($this->modx->util)) {
				$this->assertEquals('modUtilities', get_class($this->modx->util));
			} else {
				$this->fail();
			}
		}
		public function test_Csv(){
			global $modx;
			$this->modx = &$modx;
			/** @var modUtilitiesCsv $csv */
			$csv = $this->modx->util->csv();
			$this->assertEquals('modUtilitiesCsv', get_class($csv),'class not exist');
			$csv->setHead('Первый столбец','Two',3);
			$csv->addRow(1,2,3);
			$csv->addRow('a','b');
			$csv->addRow([
				'Two'=>55,
				'3'=>33
			]);
			$csv->addRow([
				'two'=>55,
				'4'=>33
			]);
			$result = '﻿Первый столбец;Two;3
			1;2;3
			a;b;
			;55;33';
			$this->assertEquals($this->modx->util->rawText($result), $this->modx->util->rawText($csv->toCsv()),'addRow don`t work');
			echo $csv->toCsv();
		}

		function test_getUserPhoto(){
			global $modx;
			$this->modx = &$modx;
			$this->assertTrue(!empty($this->modx->util->getUserPhoto()));
		}
	}
