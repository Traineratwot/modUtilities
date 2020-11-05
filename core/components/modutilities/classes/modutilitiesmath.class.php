<?php

	class modutilitiesMath
	{
		public function __construct(modX &$modx, modUtilities &$util, $param = [])
		{
			//$this->modx = $modx;
			$this->util = $util;
		}

		/**
		 * @param float|int $value
		 * @param string    $math
		 * @param float|int $percent
		 * @param int       $round
		 * @return float|int|false
		 */
		public function call($value = 0, $math = '', $percent = 0, $round = 2)
		{
			$return = 0;
			if(!is_numeric($value)){
				throw new Exception('not a number passed $percent ='.$percent);
			}
			if(!is_numeric($percent)){
				throw new Exception('not a number passed $percent ='.$percent);
			}

			switch ($math) {
				case '-%':
					$return = $this->subtractPercent($value, $percent);
					break;
				case '%':
					$return = $this->calcPercent($value, $percent);
					break;
				case '+%':
					$return = $this->addPercent($value, $percent);
					break;
				case '=%':
					$return = $this->eqPercent($value, $percent);
					break;
				case '/%':
					$return = $this->divPercent($value, $percent);
					break;
				default:
					return FALSE;
			}
			if ($round) {
				$return = round($return, $round);
			}
			return $return;
		}

		/**
		 * @param int $value
		 * @param int $percent
		 * @return float|int|mixed
		 */
		public function subtractPercent($value = 0, $percent = 0)
		{
			return $value - $this->calcPercent($value, $percent);
		}

		/**
		 * @param int $value
		 * @param int $percent
		 * @return float|int
		 */
		public function calcPercent($value = 0, $percent = 0)
		{
			return $value * ($percent / 100);
		}

		/**
		 * @param int $value
		 * @param int $percent
		 * @return float|int|mixed
		 */
		public function addPercent($value = 0, $percent = 0)
		{
			return $value + $this->calcPercent($value, $percent);
		}

		/**
		 * @param int $value
		 * @param int $percent
		 * @return float|int
		 */
		public function eqPercent($value = 0, $percent = 0)
		{
			return 100 - $this->divPercent($value, $percent);
		}

		public function divPercent($value = 0, $percent = 0)
		{
			if(!$value){
				return 0;
			}
			return (($value - $percent) * 100) / $value;
		}
	}