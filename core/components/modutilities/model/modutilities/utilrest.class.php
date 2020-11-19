<?php

	class Utilrest extends xPDOSimpleObject
	{
		public function set($k = NULL, $v = NULL, $vType = '')
		{
			if (is_array($v) or is_object($v)) {
				$v = @json_encode($v, 256);
			}
			parent::set($k, $v, $vType);
		}
		public function getProperty($k, $default = NULL)
		{
			return (!empty($this->get($k)) and $this->get($k) != NULL) ? $this->get($k) : $default;
		}
	}