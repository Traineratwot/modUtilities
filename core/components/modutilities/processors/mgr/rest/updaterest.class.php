<?php

	class modutilitiesUpdateRestProcessor extends modObjectUpdateProcessor
	{
		public $classKey = 'Utilrest';
		public $primaryKeyField = 'id';

		public function initialize()
		{
			$this->properties = array_merge($this->properties, json_decode($this->properties['data'], 1));
			return parent::initialize();
		}

		public function beforeSet()
		{

			$unique = ['url'];

			foreach ($unique as $tmp) {
				$t = $this->modx->getObject($this->classKey, [$tmp => $this->getProperty($tmp)]);
				if ($t and (string)$t->get($this->primaryKeyField) !== (string)$this->getProperty($this->primaryKeyField)) {
					$this->addFieldError($tmp, 'field_unique');
				}
			}
			if ($this->hasErrors()) {
				return FALSE;
			}
			foreach ($this->properties as $key => $prop) {
				if (empty($prop)) {
					switch ($key) {
						case 'param':
							$this->setProperty($key, NULL);
						case 'permission':
							$this->setProperty($key, NULL);
							break;
						case 'BASIC_auth':
							$this->setProperty($key, NULL);
							break;
						case 'category':
							$this->setProperty($key, NULL);
							break;
						default    :
							$this->setProperty($key, NULL);
							break;
					}
				}
			}
			return !$this->hasErrors();

		}

		public function beforeSave()
		{

			foreach ($this->object->_fields as $k => $v) {
				if ($this->object->isDirty($k)) {
					return TRUE;
				}
			}
			$this->addFieldError($k, 'changes not found )');

			return FALSE;
		}

	}

	return 'modutilitiesUpdateRestProcessor';