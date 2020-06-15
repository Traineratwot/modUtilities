<?php

	/**
	 * @property int kb
	 * @property int mb
	 * @property int gb
	 * @property int tb
	 * @property int min
	 * @property int hour
	 * @property int day
	 * @property int week
	 */
	class modUtilitiesConstant
	{

		private $kb = 1024;
		private $min = 60;
		private $mb;
		private $gb;
		private $tb;
		private $hour;
		private $day;
		private $week;

		/**
		 * constant constructor.
		 */
		public function __construct()
		{
			$this->mb = $this->kb * 1024;
			$this->gb = $this->mb * 1024;
			$this->tb = $this->gb * 1024;
			$this->hour = $this->min * 60;
			$this->day = $this->hour * 24;
			$this->week = $this->day * 7;
		}

		public function __isset($name): bool
		{
			return isset($this->$name);
		}

		public function __set($name, $value)
		{
			return FALSE;
		}

		public function __get($name)
		{
			return $this->$name;
		}

		/**
		 * @return string
		 */
		public function __toString()
		{
			$res = [];
			foreach ($this as $k => $v) {
				$res[$k] = $v;
			}
			return (string)json_encode($res, 256);
		}
	}