<?php

	class modUtilitiesUpdateRestProcessor  extends modObjectUpdateProcessor
	{
		public $classKey = 'Utilrest';
		public $primaryKeyField = 'id';
		public function initialize(){
			$this->properties = array_merge($this->properties,json_decode($this->properties['data'],1));
			return parent::initialize();
		}

		public function beforeSet()
		{
			$this->dbTest();
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

			return !$this->hasErrors();

		}


	}
	return 'modUtilitiesUpdateRestProcessor';