<?php

	/**
	 * Date: 10.06.2020
	 * Time: 21:10
	 */
	class modUtilitiesUtilrestCategoryCreateProcessor extends modObjectCreateProcessor
	{
		public $classKey = 'Utilrestcategory';

		public function beforeSet()
		{
			$this->dbTest();
			$required = ['name'];
			$unique = ['name'];
			foreach ($required as $tmp) {
				if (!$this->getProperty($tmp)) {
					$this->addFieldError($tmp, 'field_required');
				}
			}

			foreach ($unique as $tmp) {
				if ($this->modx->getCount($this->classKey, [$tmp => $this->getProperty($tmp)])) {
					$this->addFieldError($tmp, 'field_unique');
				}
			}
			if ($this->hasErrors()) {
				return FALSE;
			}
			foreach ($this->properties as $key => $prop) {
				if (!$prop) {
					switch ($key) {
						case 'param':
							$this->setProperty($key, '{"scriptProperties":[],"headers":[],"httpResponseCode":200}');
						case 'permission':
							$this->setProperty($key, '{"allow": {"usergroup":"all"}}');
							break;
						case 'BASIC_auth':
							$this->setProperty($key, 0);
							break;
						case 'category':
							$this->setProperty($key, 1);
							break;
						default	:
							$this->setProperty($key, null);
							break;
					}
				}
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

	return "modUtilitiesUtilrestCategoryCreateProcessor";