<?php

	class modutilitiesPostFile
	{
		/**
		 * @var modX $modx
		 */
		public $modx;
		public $name = NULL;
		public $path = NULL;
		public $ext = NULL;
		public $fullName = NULL;
		public $type = NULL;
		/**
		 * @var int
		 */
		public $size;
		public $content;
		/**
		 * @var modUtilities  $util
		 */
		public $util;
		/**
		 * @var bool
		 */
		private $saved = FALSE;
		/**
		 * @var bool|modutilitiesCsv
		 */
		public $csv;
		/**
		 * @var mixed
		 */
		private $error;


		/**
		 * modutilitiesPostFile constructor.
		 * @param modX         $modx
		 * @param modutilities $util
		 * @param array        $data
		 * @throws Exception
		 */
		public function __construct(modX &$modx, modUtilities &$util, $data)
		{
			$this->modx = $modx;
			$this->util = $util;
			$this->data = $data;
			if (!isset($data['name']) or !isset($data['tmp_name'])) {
				throw new Exception('name or path not found');
			} elseif ($data['error'] != 'UPLOAD_ERR_OK') {
				$this->error = $data['error'];
				throw new Exception('upload error: "' . $data['error'] . '"');
			} else {
				$this->name = $this->util->baseName($data['name']);
				$this->fullName = $data['name'];
				$this->ext = mb_strtolower($this->util->baseExt($data['name']));
				$this->path = $data['tmp_name'];
				$this->type = isset($data['type']) ? $data['type'] : NULL;
				$this->size = isset($data['size']) ? $data['size'] : NULL;
			}
		}

		/**
		 * @return bool|string
		 */
		public function getContent()
		{
			$this->content = @file_get_contents($this->path);
			return $this->content;
		}

		/**
		 * @param string $path
		 * @param false  $overwrite
		 * @return TRUE|errorMsg
		 * @throws Exception
		 */
		public function save($path = '', $overwrite = FALSE)
		{
			if ($path and !$this->saved) {
				if (!is_dir(dirname($path))) {
					if (!mkdir($concurrentDirectory = dirname($path), 0777, TRUE) && !is_dir($concurrentDirectory)) {
						throw new Exception(sprintf('Directory "%s" was not created', $concurrentDirectory));
						return 'can`t create directory';
					}
				}
				$converter = [
					'{name}' => $this->name,
					'{fullName}' => $this->fullName,
					'{ext}' => $this->ext,
					'{size}' => $this->size,
					'{type}' => $this->type,
				];
				$path = strtr($path, $converter);
				if (!$overwrite and file_exists($path)) {
					throw new Exception('file already exist');
					return 'file already exist';
				}
				if (move_uploaded_file($this->path, $path) and file_exists($path) and filesize($path) > 0) {
					$this->saved = TRUE;
					$this->path = $path;
					return TRUE;
				} else {
					throw new Exception('can`t save file');
					return 'can`t save file';
				}
			} else {
				throw new Exception('empty path or this already saved');
				return 'empty path or this already saved';
			}
		}

		public function toArray()
		{
			return [
				'name' => $this->name,
				'fullName' => $this->fullName,
				'path' => $this->path,
				'ext' => $this->ext,
				'type' => $this->type,
				'size' => $this->size,
			];
		}

		public function __toString()
		{
			return json_encode($this->toArray());
		}

		public function __invoke()
		{
			return $this->toArray();
		}

		public function fromJson($flag = JSON_UNESCAPED_UNICODE)
		{
			if ($this->ext == 'json') {
				return json_decode($this->getContent(), $flag);
			}
			return FALSE;
		}

		/**
		 * @return bool|modutilitiesCsv
		 */
		public function fromCsv($param = [])
		{
			if ($this->ext == 'csv') {
				$csv = $this->util->csv($param);
				$csv->readCsv($this->path);
				$this->csv = $csv;
				return $csv;
			}
			return FALSE;
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
				if (array_key_exists('name', $this->_FILES[$this->_fields[0]]) and array_key_exists('tmp_name', $this->_FILES[$this->_fields[0]]) and array_key_exists('type', $this->_FILES[$this->_fields[0]])) {
					$this->_FILES = $this->multiply_files($this->_FILES);
				} else {
					$this->_FILES = $this->default_files($this->_FILES);
				}
			}
			foreach ($this->_FILES as $input => $file) {
				foreach ($file as $value) {
					$this->FILES[$input][] = new modutilitiesPostFile($modx, $util, $value);
				}
			}
		}

		public function default_files($files)
		{
			$filesByInput = [];
			foreach ($files as $input => $value) {
				$filesByInput[$input][0] = $value;
			}
			return $filesByInput;
		}

		public function multiply_files($files)
		{
			$filesByInput = [];
			foreach ($files as $input => $infoArr) {
				foreach ($infoArr as $key => $valueArr) {
					if (is_array($valueArr)) { // file input "multiple"
						foreach ($valueArr as $i => $value) {
							$filesByInput[$input][$i][$key] = $value;
						}
					} else { // -> string, normal file input
						$filesByInput[$input][0] = $infoArr;
						break;
					}
				}

			}
			return $filesByInput;
		}
	}