<?php
	/**
	 * Created by Andrey Stepanenko.
	 * User: webnitros
	 * Date: 11.06.2020
	 * Time: 22:49
	 */

	class modUtilitiesDelRestCategoryProcessor extends modObjectRemoveProcessor
	{
		public $classKey = 'Utilrestcategory';
		public $primaryKeyField = 'id';

		public function beforeSet()
		{
			$this->dbTest();
			if ($this->modx->getCount('Utilrest', ['category' => $this->getProperty('id')])) {
				$this->addFieldError('id', 'this category not empty');
			}
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
						$this->modx->query($create);
					}
				}
				rename($path . 'db.php', $path . 'db_.php');
			}
		}
	}

	return 'modUtilitiesDelRestCategoryProcessor';