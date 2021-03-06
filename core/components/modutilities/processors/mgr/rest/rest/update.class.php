<?php

	class modutilitiesUpdateRestProcessor extends modObjectUpdateProcessor
	{
		public $classKey = 'Utilrest';
		public $primaryKeyField = 'id';

		public function initialize()
		{
			if (isset($this->properties['data'])) {
				$this->properties = array_merge($this->properties, json_decode($this->properties['data'], 1));
			}
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
				if ($key == 'allowMethod') {
					$prop = implode(',', $prop);
					$this->setProperty($key,$prop );
				}
				if ($key == 'allowMethod') {
					$prop = array_unique(array_values($prop));
					$this->properties[$key] = implode(',', $prop);
					$prop = $this->properties[$key];
				}
				if (is_array($prop) or is_object($prop)) {
					$prop = array_unique($prop);
					if (count($prop) == 0 or empty($prop)) {
						$this->setProperty($key, NULL);
					} elseif (count($prop) == 1 and empty(array_values($prop)[0])) {
						$this->setProperty($key, NULL);
					} else {
						$prop = json_encode($prop, 256);
						$this->setProperty($key, $prop);
					}
				}

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
//			$this->addFieldError($k, 'changes not found )');

			return TRUE;
		}

	}

	return 'modutilitiesUpdateRestProcessor';