<?php

	class modutilitiesPostFile
	{
		/**
		 * @var modX $modx
		 */
		public $modx;
		public $name;
		public $path;
		public $ext;
		public $type;
		/**
		 * @var int
		 */
		public $size;
		public $content;
		/**
		 * @var modutilities $util
		 */
		public $util;
		/**
		 * @var bool
		 */
		private $saved = FALSE;
		/**
		 * @var array
		 */
		private $pathInfo = [];

		/**
		 * modutilitiesPostFile constructor.
		 * @param array $data
		 */
		public function __construct(&$modx, modutilities &$util, $data)
		{
			$this->modx = $modx;
			$this->util = $util;
			$this->data = $data;
			if (!isset($data['name']) or !isset($data['tmp_name'])) {
				throw new Exception('name or path not found');
			} elseif ($data['error'] != 'UPLOAD_ERR_OK') {
				throw new Exception('upload error: "' . $data['error'] . '"');
			} else {
				$this->name = $data['name'];
				$this->path = $data['tmp_name'];
				$ext = explode('.', $this->name);
				$this->ext = mb_strtolower(end($ext));
				$this->type = isset($data['type']) ? $data['type'] : NULL;
				$this->size = isset($data['size']) ? $data['size'] : NULL;
			}
		}

		/**
		 * @return bool|string
		 */
		public function getContent()
		{
			$this->content = file_get_contents($this->path);
			return $this->content;
		}

		public function save($path = '')
		{
			if ($path and !$this->saved) {
				if (!is_dir(dirname($path))) {
					if (!mkdir($concurrentDirectory = dirname($path), $this->modx->config['new_file_permissions'], TRUE) && !is_dir($concurrentDirectory)) {
						throw new Exception(sprintf('Directory "%s" was not created', $concurrentDirectory));
						return FALSE;
					}
				}
				if (move_uploaded_file($this->path, $path) and file_exists($path)) {
					$this->saved = TRUE;
					$this->path = $path;
					return TRUE;
				} else {
					return FALSE;
				}
			} else {
				return FALSE;
			}
		}

		public function toArray()
		{
			return [
				'name' => $this->name,
				'path' => $this->path,
				'ext' => $this->ext,
				'type' => $this->type,
				'size' => $this->size,
			];
		}

		public function fromJson($flag = JSON_UNESCAPED_UNICODE)
		{
			if ($this->ext == 'json') {
				return json_decode($this->getContent(), $flag);
			}
		}

		public function __debugInfo()
		{
			return $this->toArray();
		}
	}

	class modutilitiesPostFiles
	{
		public $FILES = [];

		function __construct($modx, $util)
		{
			$this->_FILES = $_FILES;
			$this->_fields = array_keys($this->_FILES);
			if (is_array($this->_FILES[$this->_fields[0]])) {
				if (array_key_exists('name',$this->_FILES[$this->_fields[0]]) and array_key_exists('tmp_name',$this->_FILES[$this->_fields[0]]) and array_key_exists('type',$this->_FILES[$this->_fields[0]])) {
					$this->_FILES = $this->multiply_files($this->_FILES);
				}else{
					$this->_FILES = $this->default_files($this->_FILES);
				}
			}
			foreach ($this->_FILES as $input=> $file) {
				foreach ($file as $value) {
					$this->FILES[$input][] = new modutilitiesPostFile($modx, $util, $value);
				}
			}
		}
		public function default_files($files){
			$filesByInput =[];
			foreach ($files as $input=>$value){
				$filesByInput[$input][0] = $value;
			}
			return $filesByInput;
		}
		public function multiply_files($files) {
			$filesByInput = [];
			foreach ($files as $input => $infoArr) {
				foreach ($infoArr as $key => $valueArr) {
					if (is_array($valueArr)) { // file input "multiple"
						foreach($valueArr as $i=>$value) {
							$filesByInput[$input][$i][$key] = $value;
						}
					}
					else { // -> string, normal file input
						$filesByInput[$input][0] = $infoArr;
						break;
					}
				}

			}
			return $filesByInput;
		}
	}