<?php
	/**
	 * Date: 10.06.2020
	 * Time: 21:10
	 */

	class modUtilitiesGetRestProcessor extends modProcessor
	{
		public function process()
		{
			$this->dbTest();
			return 'привет)';
		}

		public function dbTest(): void
		{
			if (file_exists('../../../model/db.php')) {
				$db = include '../../../model/db.php';
				foreach ($db as $table => $create) {
					try {
						$result = $this->modx->query("SELECT 1 FROM $table LIMIT 1"); // формальный запрос
					} catch (Exception $e) {
						$result = FALSE;
					}
					if($result === FALSE) {
						$this->modx->query($create);
					}
				}
			}
		}
	}
	return "modUtilitiesGetRestProcessor";