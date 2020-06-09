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
		/**
		 * @var int
		 */
		public $kb = 1024;
		/**
		 * @var int
		 */
		public $min = 60;

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

		/**
		 * @return string
		 */
		public function __toString()
		{
			return (string)json_encode($this, 256);
		}
	}