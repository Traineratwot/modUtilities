<?php
	/**
	 * Created by Andrey Stepanenko.
	 * User: webnitros
	 * Date: 11.06.2020
	 * Time: 22:49
	 */

	class modUtilitiesDelRestProcessor extends modObjectRemoveProcessor
	{
		public $classKey = 'Utilrest';
		public $primaryKeyField = 'id';
		public function beforeSet()
		{
			return !$this->hasErrors();
		}


	}
	return 'modUtilitiesDelRestProcessor';