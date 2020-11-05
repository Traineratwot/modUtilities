<?php
	/**
	 * Created by Kirill Nefediev.

	 * Date: 27.05.2020
	 * Time: 20:31
	 */

	class translitProcessor extends modProcessor{
		public function process(){
			$str = (string)$this->getProperty('str');
			$res = $this->modx->util->translit($str);
			return $this->success($res) ;
		}
	}
	return 'translitProcessor';