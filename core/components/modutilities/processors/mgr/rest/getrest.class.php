<?php
	/**
	 * Date: 10.06.2020
	 * Time: 21:10
	 */

	class modutilitiesGetRestProcessor extends modObjectGetListProcessor
	{
		public $classKey = 'Utilrest';
		public $defaultSortField = 'id';
		public $defaultSortDirection = 'desc';
		public $primaryKeyField = 'id';
		
	}
	return "modutilitiesGetRestProcessor";