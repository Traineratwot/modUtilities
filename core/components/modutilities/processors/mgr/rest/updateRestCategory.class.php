<?php

	class modUtilitiesUpdateRestCategoryProcessor  extends modObjectUpdateProcessor
	{
		public $classKey = 'Utilrestcategory';
		public $primaryKeyField = 'id';
		public function initialize(){
			$this->properties = array_merge($this->properties,json_decode($this->properties['data'],1));
			return parent::initialize();
		}

		public function beforeSet()
		{
			$this->dbTest();
			$unique = ['name'];

			foreach ($unique as $tmp) {
				$t = $this->modx->getObject($this->classKey, [$tmp => $this->getProperty($tmp)]);
				if ((string)$t->get($this->primaryKeyField) !== (string)$this->getProperty($this->primaryKeyField)) {
					$this->addFieldError($tmp, 'field_unique');
				}
			}
			if ($this->hasErrors()) {
				return FALSE;
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
	return 'modUtilitiesUpdateRestCategoryProcessor';