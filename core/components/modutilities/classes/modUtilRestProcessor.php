<?php
	/**
	 * Date: 12.06.2020
	 * Time: 17:29
	 */

	abstract class modUtilRestProcessor extends modProcessor
	{
		/* @var modX modx */
		public $modx;
		/* @var modUtilitiesRest REST */
		public $REST;

		public function __construct(modX &$modx, array $properties = [],modUtilitiesRest &$REST)
		{
			$this->REST = $REST;
			parent::__construct( $modx, $properties);
		}
	}
	return 'modUtilRestProcessor';