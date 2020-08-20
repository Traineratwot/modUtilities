<?php
	/**
	 * Created by Kirill Nefediev.

	 * Date: 11.06.2020
	 * Time: 22:49
	 */

	class modutilitiesDelRestProcessor extends modObjectRemoveProcessor
	{
		public $classKey = 'Utilrest';
		public $primaryKeyField = 'id';
		public function beforeSet()
		{
			return !$this->hasErrors();
		}


	}
	return 'modutilitiesDelRestProcessor';