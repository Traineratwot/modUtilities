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

	class modutilitiesTest extends TestCase
	{

		/**
		 * @var modX $modx
		 */

		public function test_ExistUtil()
		{
			global $modx;
			
			if (isset($modx->util)) {
				$this->assertEquals('modutilities', get_class($modx->util));
			} else {
				$this->fail();
			}
		}

		public function test_Csv(){
			global $modx;
			/** @var modutilitiesCsv $csv */
			$csv = $modx->util->csv();
			$this->assertEquals('modutilitiesCsv', get_class($csv),'class not exist');
			$csv->setHead('Первый','второй','третий');
			$csv->addRow(1,2,3);
			$csv->addRow('a','b');
			$csv->addRow([
				'Первый'=>55,
				'третий'=>33
			]);
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

		public function  test__mb_ucfirst(){
			global $modx;
			$this->assertEquals('Привет',$modx->util->mb_ucfirst('привет'));
			$this->assertEquals('Я Есть, Грут',$modx->util->mb_ucfirst('я есть, грут',modutilities::EveryWord));
			$this->assertEquals('Привет. Я есть, грут? Да! Да!',$modx->util->mb_ucfirst('привет. я есть, грут? да! да!',modutilities::AfterDot));
		}

		public function test__member(){
			global $modx;
			$answer = "[{\"groupId\":\"1\",\"groupName\":\"Administrator\",\"roleId\":\"2\",\"roleName\":\"Super User\",\"roleAuthority\":\"0\"}]";
			$r = json_encode($modx->util->member(1));
			$this->assertEquals($answer,json_encode($modx->util->member(1)));
		}
	}
