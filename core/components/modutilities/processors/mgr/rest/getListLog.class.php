<?php

	/**
	 * Date: 10.06.2020
	 * Time: 21:10
	 */
	class modutilitiesGetListLogProcessor extends modObjectGetListProcessor
	{
		public $classKey = 'Utilreststats';
		public $defaultSortField = 'id';
		public $defaultSortDirection = 'DESC';

		public function process()
		{
			$beforeQuery = $this->beforeQuery();
			if ($beforeQuery !== TRUE) {
				return $this->failure($beforeQuery);
			}
			$data = $this->getData();
			$list = $this->iterate($data);

			foreach ($list as $k=>$v){
				$v['time'] = @implode(' ',$this->modx->util->convert($v['time'],'time','s'));
				$list[$k] = $v;
				if(!$this->modx->getCount('Utilrest',$v['rest_id'])){
					unset($list[$k]);
				}
			}

			return $this->outputArray($list,$data['total']);
		}

	}

	return "modutilitiesGetListLogProcessor";