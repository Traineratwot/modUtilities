<?php
class Utilreststats extends xPDOObject {
	public function getProperty($k, $default = NULL)
	{
		return (!empty($this->get($k)) AND $this->get($k) != NULL) ? $this->get($k) : $default;
	}
}