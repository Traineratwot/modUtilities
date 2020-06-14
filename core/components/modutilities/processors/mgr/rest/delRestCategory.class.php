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


	}

	return 'modUtilitiesDelRestCategoryProcessor';