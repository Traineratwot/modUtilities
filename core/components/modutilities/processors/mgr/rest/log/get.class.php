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
		public $where = [];

		public function beforeQuery()
		{


			$search = $this->getProperty('query');
			if (!empty($search)) {
				if (is_numeric($search)) {
					$this->where = [
						'rest_id' => (int)$search,
					];
				} else {
					$var = $this->modx->newObject($this->classKey);
					$arr = $var->_fields;
					unset($arr['rest_id']);
					unset($arr['id']);
					unset($arr['time']);
					unset($arr['datetime']);
					foreach ($arr as $field => $n) {
						$this->where["OR:{$field}:LIKE"] = "%" . $search . "%";
					}
				}
			}
			return parent::beforeQuery();
		}
		public function getData()
		{
			$data = [];
			$limit = (int)$this->getProperty('limit');
			$start = (int)$this->getProperty('start');

			/* query for chunks */
			$c = $this->modx->newQuery($this->classKey);
			if (!empty($this->where)) {
				$c->where($this->where);
			}
			$c = $this->prepareQueryBeforeCount($c);
			$data['total'] = $this->modx->getCount($this->classKey, $c);
			$c = $this->prepareQueryAfterCount($c);

			$sortClassKey = $this->getSortClassKey();
			$sortKey = $this->modx->getSelectColumns($sortClassKey, $this->getProperty('sortAlias', $sortClassKey), '', [$this->getProperty('sort')]);
			if (empty($sortKey)) $sortKey = $this->getProperty('sort');
			$c->sortby($sortKey, $this->getProperty('dir'));
			if ($limit > 0) {
				$c->limit($limit, $start);
			}
			$c->prepare();
			$data['results'] = $this->modx->getCollection($this->classKey, $c);
			return $data;
		}

		public function process()
		{
			$beforeQuery = $this->beforeQuery();
			if ($beforeQuery !== TRUE) {
				return $this->failure($beforeQuery);
			}
			$data = $this->getData();
			$list = $this->iterate($data);

			foreach ($list as $k => $v) {
				$v['time'] = @implode(' ', $this->modx->util->convert($v['time'], 'time', 's'));
				$list[$k] = $v;
			}

			return $this->outputArray($list, $data['total']);
		}

	}

	return "modutilitiesGetListLogProcessor";