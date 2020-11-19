<?php
	/**
	 * Created by Kirill Nefediev.

	 * Date: 11.06.2020
	 * Time: 22:49
	 */

	class modutilitiesDelRestCategoryProcessor extends modObjectRemoveProcessor
	{
		public $classKey = 'Utilrestcategory';
		public $primaryKeyField = 'id';

		public function beforeSet()
		{

			if ($this->modx->getCount('Utilrest', ['category' => $this->getProperty('id')])) {
				$this->addFieldError('id', 'this category not empty');
			}
			return !$this->hasErrors();

		}


	}

	return 'modutilitiesDelRestCategoryProcessor';