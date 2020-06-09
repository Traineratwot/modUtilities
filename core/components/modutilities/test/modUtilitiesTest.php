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

		public function test_ExistUtil()
		{
			global $modx;
			
			if (isset($modx->util)) {
				$this->assertEquals('modUtilities', get_class($modx->util));
			} else {
				$this->fail();
			}
		}

		public function test_Csv(){
			global $modx;
			/** @var modUtilitiesCsv $csv */
			$csv = $modx->util->csv();
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
			$this->assertEquals($modx->util->rawText($result), $modx->util->rawText($csv->toCsv()),'addRow don`t work');
			$csv->reset();
		}

		public function test_getUserPhoto(){
			global $modx;
			$this->assertTrue(!empty($modx->util->getUserPhoto()));
			$this->assertTrue(!empty($modx->util->getGravatar('traineratwot@yandex.ru')));
			$this->assertEquals('https://placehold.it/128x128?text=avatar',($modx->util->getUserPhoto(99999)));
			$user = $modx->getObject('modUser',1);
			$t = $modx->util->likeString($user->getPhoto(),$modx->util->getUserPhoto($user));
			$this->assertTrue($t['score'] > 80 );
		}
	}
