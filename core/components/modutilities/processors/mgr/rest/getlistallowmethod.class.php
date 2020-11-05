<?php

	/**
	 * Date: 10.06.2020
	 * Time: 21:10
	 */
	class modutilitiesGetListAllowMethodProcessor extends modProcessor
	{
		public function process()
		{

			$array = [];
			$array_ = $this->modx->util->getSetOption('Utilrest', 'allowMethod');
			foreach ($array_ as $m){
				$array[] = ['name'=>$m];
			}
			return json_encode([
				'success' => TRUE,
				'total' => count($array),
				'results' => $array,
//				'debug' => $q->toSQL(),
			]);
		}


	}

	return "modutilitiesGetListAllowMethodProcessor";