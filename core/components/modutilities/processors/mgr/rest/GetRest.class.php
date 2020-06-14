<?php
	/**
	 * Date: 10.06.2020
	 * Time: 21:10
	 */

	class modUtilitiesGetRestProcessor extends modProcessor
	{
		public function process()
		{

			$limit = (int)$this->getProperty('limit');
			$start = (int)$this->getProperty('start');
			$sort = (string)$this->getProperty('sort');
			$dir = (string)$this->getProperty('dir');
			$array = array();
			/** @var Utilrest $rest */
			$q = $this->modx->newQuery('Utilrest');
			$q->select('Utilrest.*,Utilrestcategory.name as catName');
			$q->innerJoin('Utilrestcategory','Utilrestcategory','Utilrestcategory.id=Utilrest.category');
			$q->limit($limit,$start);
			if($sort){
				$q->sortby($sort,$dir);
			}
			if ($q->prepare() && $q->stmt->execute()){
			    while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
				    $array[] = $row;
			    }
			}
			return json_encode(array(
				'success' => true,
				'total' => count($array),
				'results' => $array,
//				'debug' => $q->toSQL(),
			));
		}


	}
	return "modUtilitiesGetRestProcessor";