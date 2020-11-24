<?php

	/**
	 * Date: 10.06.2020
	 * Time: 21:10
	 */
	class modutilitiesUtilrestCategoryCreateProcessor extends modObjectCreateProcessor
	{
		public $classKey = 'Utilrestcategory';

		public function initialize()
		{
			if (isset($this->properties['data'])) {
				$this->properties = array_merge($this->properties, json_decode($this->properties['data'], 1));
			}
			return parent::initialize();
		}

		public function beforeSet()
		{

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
							break;
						case 'permission':
							$this->setProperty($key, '{"allow": {"usergroup":"all"}}');
							break;
						case 'BASIC_auth':
							$this->setProperty($key, 0);
							break;
						case 'category':
							$this->setProperty($key, 1);
							break;
						default    :
							$this->setProperty($key, NULL);
							break;
					}
				}
			}

			return !$this->hasErrors();

		}


	}

	return "modutilitiesUtilrestCategoryCreateProcessor";