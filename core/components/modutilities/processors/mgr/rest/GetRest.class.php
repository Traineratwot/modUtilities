<?php
	/**
	 * Date: 10.06.2020
	 * Time: 21:10
	 */

	class modUtilitiesGetRestProcessor extends modProcessor
	{
		public function process()
		{
			$this->dbTest();
			$limit = (int)$this->getProperty('limit');
			$start = (int)$this->getProperty('start');
			$array = array();
			/** @var Utilrest $rest */
			$q = $this->modx->newQuery('Utilrest',);
			$q->select('Utilrest.*,Utilrestcategory.name as catName');
			$q->innerJoin('Utilrestcategory','Utilrestcategory','Utilrestcategory.id=Utilrest.category');
			$q->limit($limit,$start);
			if ($q->prepare() && $q->stmt->execute()){
			    while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
				    $array[] = $row;
			    }
			}
			return json_encode(array(
				'success' => true,
				'total' => 60,
				'results' => $array,
				'debug' => $q->toSQL(),
			));
		}

		public function dbTest(): void
		{
			$path = dirname(__DIR__, 3) .DIRECTORY_SEPARATOR.'model'.DIRECTORY_SEPARATOR;
			if (file_exists($path.'db.php')) {
				$db = include $path.'db.php';
				foreach ($db as $table => $create) {
					try {
						$result = $this->modx->query("SELECT 1 FROM $table LIMIT 1"); // формальный запрос
					} catch (Exception $e) {
						$result = FALSE;
					}
					if($result === FALSE) {
						$this->modx->query($create);
					}
				}
				rename($path.'db.php',$path.'db_.php');
			}
		}
	}
	return "modUtilitiesGetRestProcessor";