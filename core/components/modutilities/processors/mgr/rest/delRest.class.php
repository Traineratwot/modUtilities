<?php
	/**
	 * Created by Andrey Stepanenko.
	 * User: webnitros
	 * Date: 11.06.2020
	 * Time: 22:49
	 */

	class modUtilitiesDelRestProcessor extends modObjectRemoveProcessor
	{
		public $classKey = 'Utilrest';
		public $primaryKeyField = 'id';
		public function beforeSet()
		{
			$this->dbTest();

			return !$this->hasErrors();

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
	return 'modUtilitiesDelRestProcessor';