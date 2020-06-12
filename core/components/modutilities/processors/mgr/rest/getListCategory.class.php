<?php
	/**
	 * Date: 10.06.2020
	 * Time: 21:10
	 */
	class modUtilitiesGetListCategoryProcessor extends modObjectGetListProcessor
	{
		public $classKey = 'Utilrestcategory';
		public $defaultSortField = 'name';

		public function process()
		{
			$this->dbTest();
			return parent::process();
		}

		public function dbTest(): void
		{
			$path = dirname(__DIR__, 3) .DIRECTORY_SEPARATOR.'model'.DIRECTORY_SEPARATOR;
			if (file_exists($path.'db.php')) {
				$db = include $path.'db.php';
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
				rename($path.'db.php',$path.'db_.php');
			}
		}
	}
	return "modUtilitiesGetListCategoryProcessor";