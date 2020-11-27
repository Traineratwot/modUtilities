<?php

	/**
	 * Date: 10.06.2020
	 * Time: 21:10
	 */
	class modutilitiesUtilrestCreateProcessor extends modObjectCreateProcessor
	{
		public $classKey = 'Utilrest';

		public function initialize()
		{
			if (isset($this->properties['data'])) {
				$this->properties = array_merge($this->properties, json_decode($this->properties['data'], 1));
			}
			return parent::initialize();
		}

		public function beforeSet()
		{
			$required = ['url', 'snippet'];
			$unique = ['url'];
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
				if (empty($prop)) {
					switch ($key) {
						case 'BASIC_auth':
							$this->setProperty($key, 0);
							break;
						case 'category':
							$this->setProperty($key, 'default');
							break;
						default    :
							$this->setProperty($key, NULL);
							break;
					}
				}
				if ($key == 'allowMethod') {
					$this->setProperty($key, implode(',', $prop));
				}
			}

			return !$this->hasErrors();

		}


	}

	return "modutilitiesUtilrestCreateProcessor";