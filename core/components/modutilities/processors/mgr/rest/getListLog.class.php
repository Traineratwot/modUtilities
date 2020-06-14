<?php

	/**
	 * Date: 10.06.2020
	 * Time: 21:10
	 */
	class modUtilitiesGetListLogProcessor extends modObjectGetListProcessor
	{
		public $classKey = 'Utilreststats';
		public $defaultSortField = 'datetime';

		public function process()
		{
			$beforeQuery = $this->beforeQuery();
			if ($beforeQuery !== TRUE) {
				return $this->failure($beforeQuery);
			}
			$data = $this->getData();
			$list = $this->iterate($data);

			foreach ($list as $k=>$v){
				$v['time'] = implode(' ',$this->modx->util->convert($v['time'],'time','s'));
				$list[$k] = $v;
			}

			return $this->outputArray($list, $data['total']);
		}

	}

	return "modUtilitiesGetListLogProcessor";