<?php

	/**
	 * Date: 10.06.2020
	 * Time: 21:10
	 */
	class modUtilitiesGetListAllowmethodProcessor extends modProcessor
	{
		public function process()
		{
			$this->dbTest();
			$array = [];
			$array_ = $this->modx->util->getSetOption('modutil_utilrest', 'allowMethod');
			foreach ($array_ as $m){
				$array[] = ['name'=>$m];
			}
			return json_encode([
				'success' => TRUE,
				'total' => count($array),
				'results' => $array,
//				'debug' => $q->toSQL(),
			]);
		}

		public function dbTest(): void
		{
			$path = dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'model' . DIRECTORY_SEPARATOR;
			if (file_exists($path . 'db.php')) {
				$db = include $path . 'db.php';
				foreach ($db as $table => $create) {
					try {
						$result = $this->modx->query("SELECT 1 FROM $table LIMIT 1"); // формальный запрос
					} catch (Exception $e) {
						$result = FALSE;
					}
					if ($result === FALSE) {
						$create = explode(';',$create);
						foreach ($create as $q){
							$this->modx->query($q);
						}
					}
				}
				rename($path . 'db.php', $path . 'db_.php');
			}
		}
	}

	return "modUtilitiesGetListAllowmethodProcessor";