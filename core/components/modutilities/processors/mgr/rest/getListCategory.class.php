<?php

	/**
	 * Date: 10.06.2020
	 * Time: 21:10
	 */
	class modUtilitiesGetListCategoryProcessor extends modObjectGetListProcessor
	{
		public $classKey = 'Utilrestcategory';
		public $defaultSortField = 'name';

		public function process()
		{
			return parent::process();
		}

	}

	return "modUtilitiesGetListCategoryProcessor";