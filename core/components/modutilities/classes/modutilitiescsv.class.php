<?php
	/**
	 * Created by Kirill Nefediev..
	 * Date: 04.06.2020
	 * Time: 18:04
	 */

	class modutilitiesCsv
	{
		/* @var modX $modX */
		public $modx;
		/* @var modUtilities $util */
		public $util;
		/* @var string chr(239) . chr(187) . chr(191) */
		public $utf8bom;
		/* @var string Charset */
		public $inputCharset;
		/* @var string output csv string */
		protected $csv;
		/* @var string output html string */
		protected $html;
		/* @var array */
		protected $matrix = [];
		/* @var array */
		protected $head = [];
		protected $_head = [];
		protected $appendType = FALSE;
		protected $str_delimiter;
		protected $line_delimiter;
		protected $escape;
		public $currentRow = -1;
		public $clearRegexp = '@[^[:ascii:]A-я]+@u';
		public $currentCol = -1;
		/**
		 * @var bool|mixed|string
		 */
		private $mode;

		/**
		 * @var bool|mixed|resource
		 */
		private $output_file;
		/**
		 * @var bool|mixed
		 */
		private $_output_file;
		/**
		 * @var array|bool
		 */
		private $sort;
		/**
		 * @var array|bool
		 */
		private $limits = [
			'l' => ['min' => 30, 'max' => 60],
			's' => ['min' => 80, 'max' => 100],
		];

		/**
		 * UtilitiesCsv constructor
		 *
		 * ###params:
		 *  - inputCharset = 'utf8'
		 *
		 *  - woBom
		 *
		 *  - delimiter = ';'
		 *
		 *  - line_delimiter = \n
		 *
		 *  - mode = default | fast
		 *
		 *  - output_file
		 * @param modX         $modx
		 * @param modutilities $util
		 * @param array        $param
		 * @throws Exception
		 */
		public function __construct(modX &$modx, modUtilities &$util, $param = [])
		{
			$this->inputCharset = isset($param['inputCharset']) ? $param['inputCharset'] : 'utf8';
			$this->modx = $modx;
			$this->util = $util;

			$this->utf8bom        = $this->modx->getOption('woBom'         , $param, chr(239) . chr(187) . chr(191));
			$this->str_delimiter  = $this->modx->getOption('delimiter'     , $param, ';');
			$this->line_delimiter = $this->modx->getOption('line_delimiter', $param, "\n");
			$this->escape         = $this->modx->getOption('line_delimiter', $param, '"');
			$this->output_file    = $this->modx->getOption('output_file'   , $param, NULL);
			$this->mode           = $this->modx->getOption('mode'          , $param, 'default');

			if ($this->mode == 'fast') {
				if ($this->output_file and $this->utf8bom) {
					fwrite($this->output_file, $this->utf8bom);
				}
			}

		}

		/**
		 * @param array $param
		 * @return $this
		 */
		public function reset($param = [])
		{
			$this->inputCharset = isset($param['inputCharset']) ? $param['inputCharset'] : $this->inputCharset;
			$this->utf8bom = (isset($param['woBom']) and $param['woBom'] = TRUE) ? NULL : $this->utf8bom;
			$this->str_delimiter = isset($param['delimiter']) ? $param['delimiter'] : $this->str_delimiter;
			$this->line_delimiter = isset($param['line_delimiter']) ? $param['line_delimiter'] : $this->line_delimiter;
			$this->csv = NULL;
			$this->html = NULL;
			$this->matrix = NULL;
			$this->head = [];
			$this->appendType = FALSE;
			return $this;
		}

		/**
		 * add row to csv
		 * @return $this|bool
		 */
		public function addRow()
		{

			if (!$this->appendType) {
				if (!$this->util->isEmpty(($this->matrix))) {
					$this->appendType = 'row';
				}
			}

			if ($this->appendType != 'row') {
				return FALSE;
			}
			$args = func_get_args();

			if (empty($this->_head)) {
				return $this->setHead(...$args);
			}

			if (count($args) == 1 and is_array($args[0])) {
				$args = $args[0];
			}

			$head = array_flip($this->_head);

			$isAssoc = $this->util->isAssoc($args);
			$args_ = [];

			foreach ($args as $k => $art) {
				$k = $this->clearString($k);
				$art = $this->clearString($art);
				if ($isAssoc) {
					if (!is_string($art) and !is_numeric($art)) {
						$args_[$head[$k]] = NULL;
					} else {
						$args_[$head[$k]] = $art;
					}
				} else {
					if (!is_string($art) and !is_numeric($art)) {
						$args_[$k] = NULL;
					} else {
						$args_[$k] = $art;
					}
				}
			}
			if ($this->mode == 'fast') {
				ksort($args_);
				foreach ($args_ as $i => $v) {
					$args_[$i] = $this->escape . $v . $this->escape;
				}
				$text = implode($this->str_delimiter, $args_) . $this->line_delimiter;
				$this->writeFile($text);
			} else {
				$this->matrix[] = $args_;
			}

			return $this;
		}

		/**
		 * add column to csv
		 * @return $this|bool
		 */
		public function addCol()
		{
			if (!$this->appendType or !$this->util->isEmpty(($this->matrix))) {
				$this->appendType = 'column';
			}
			if ($this->appendType != 'column') {
				return FALSE;
			}
			$args = func_get_args();

			if (count($args) == 1 and is_array($args[0])) {
				$args = $args[0];
			}
			$head = array_flip($this->_head);
			$isAssoc = $this->util->isAssoc($args);
			foreach ($args as $k => $art) {
				if (!is_string($art) and !is_numeric($art)) {
					$art = NULL;
				} else {
					$art = (string)$art;
				}
				if ($isAssoc) {
					$this->matrix[$head[$k]][] = $art;
				} else {
					$this->matrix[$k][] = $art;
				}
			}

			return $this;
		}

		/**
		 * add header for csv
		 * @return $this
		 */
		public function setHead()
		{
			$args = func_get_args();
			if (count($args) == 1 and is_array($args[0])) {
				$args = $args[0];
			}
			$_args = [];
			foreach ($args as $k => $art) {
				$k = $this->clearString($k);
				$art = $this->clearString($art);
				if (!is_string($art) and !is_numeric($art)) {
					$args[$k] = NULL;
					$_args[$k] = NULL;
				} else {
					$_args[$k] = $art;
					$args[$k] = $art;
				}
			}
			if ($this->mode == 'fast') {
				$this->_head = $_args;
				foreach ($args as $i => $v) {
					$args[$i] = $this->escape . $v . $this->escape;
				}
				$text = implode($this->str_delimiter, $args) . $this->line_delimiter;
				$this->writeFile($text);
			} else {
				$this->head = $args;
				$this->_head = $_args;
				$this->matrixFix();
			}
			return $this;
		}

		/**
		 * @param string|int $x column
		 * @param string|int $y row
		 * @param string|int $value
		 */
		public function setCell($x = 0, $y = 0, $value = '')
		{
			if (!empty($this->head)) {
				switch ($this->appendType) {
					case 'row':
						if (in_array($x, $this->head)) {
							$head = array_flip($this->head);
							$x = $head[$x];
						}
						break;
					case 'column':
						if (in_array($y, $this->head)) {
							$head = array_flip($this->head);
							$y = $head[$y];
						}
						break;
					default:
						return FALSE;
				}
			}
			$x = (int)$x;
			$y = (int)$y;
			for ($i = 0; $i <= $y; $i++) {
				if (!array_key_exists($i, $this->matrix)) {
					$this->matrix[$i] = [];
				}
			}
			for ($i = 0; $i <= $x; $i++) {
				if ($i == $x) {
					$this->matrix[$y][$i] = $value;
				}
				if (!array_key_exists($i, $this->matrix[$y])) {
					$this->matrix[$y][$i] = '';
				}
			}
			return $this;
		}

		/**
		 * @param string|int $x
		 * @param string|int $y
		 * @return bool|mixed
		 */
		public function getCell($x = 0, $y = 0)
		{
			if (!empty($this->head)) {
				switch ($this->appendType) {
					case 'row':
						if (in_array($x, $this->head)) {
							$head = array_flip($this->head);
							$x = $head[$x];
						}
						break;
					case 'column':
						if (in_array($y, $this->head)) {
							$head = array_flip($this->head);
							$y = $head[$y];
						}
						break;
					default:
						return FALSE;
				}
			}
			$x = (int)$x;
			$y = (int)$y;
			if (isset($this->matrix[$y]) and isset($this->matrix[$y][$x])) {
				return $this->matrix[$y][$x];
			}
			return FALSE;
		}

		private function matrixFix()
		{
			$lenCol[] = count($this->head);
			if (is_array($this->matrix)) {
				foreach ($this->matrix as $row) {
					$lenCol[] = count($row);
				}
				$lenCol = max($lenCol);
				foreach ($this->matrix as $k => $row) {
					for ($i = 0; $lenCol > $i; $i++) {
						if (!isset($row[$i])) {
							$this->matrix[$k][$i] = NULL;
						}
					}
				}
			}
		}

		/**
		 *generate csv string
		 */
		public function _buildCsv()
		{
			$this->matrixFix();
			$this->sort();
			$this->csv = $this->utf8bom;
			$len = [];
			$head = $this->head;
			$len[] = count($head);
			foreach ($this->matrix as $row) {
				$len[] = count($row);
			}
			foreach ($head as $i => $v) {
				$head[$i] = $this->escape . $v . $this->escape;
			}
			$len = max($len);
			if (!empty($head)) {
				if ($this->appendType == 'row') {
					$this->csv .= implode($this->str_delimiter, $head);
				} else {
					foreach ($this->head as $k => $h) {
						array_unshift($this->matrix[$k], $h);
					}
				}
			}
			foreach ($this->matrix as $key => $row) {
				$_row = [];
				for ($i = 0; $i < $len; $i++) {
					$_row[$i] = (isset($row[$i])) ? $this->escape . $row[$i] . $this->escape : '';
				}
				if (!$this->util->isEmpty($_row)) {
					$this->csv .= $this->line_delimiter;
					$this->csv .= implode($this->str_delimiter, $_row);
				}
			}
		}

		/**
		 *generate html table string
		 */
		public function _buildHtmlTable($cls = '', $rainbow = FALSE)
		{
			$this->matrixFix();
			$this->sort();
			$this->html = "<table class=\"$cls\">";
			$len = [];
			$head = $this->head;
			$len[] = count($head);
			foreach ($this->matrix as $row) {
				$len[] = count($row);
			}
			$len = max($len);
			if (!empty($head)) {
				if ($this->appendType == 'row') {
					$this->html .= "<tr>";
					foreach ($head as $k => $h) {
						$style = '';
						if ($rainbow) {
							$style = 'style="color:' . $this->util->randomColor(['salt' => $k, 'limits' => $this->limits]) . ';"';
						}
						$this->html .= "<th $style>$h</th>";
					}
					$this->html .= "</tr>";
				} else {
					foreach ($this->head as $k => $h) {
						if (isset($this->matrix[$k]) and is_array($this->matrix[$k])) {
							array_unshift($this->matrix[$k], $h);
						}
					}
				}
			}
			foreach ($this->matrix as $key => $row) {
				$_row = [];
				for ($i = 0; $i < $len; $i++) {
					$_row[$i] = (isset($row[$i])) ? $row[$i] : '';
				}
				if (!$this->util->isEmpty($_row)) {
					$this->html .= "<tr>";
					$i = 0;
					foreach ($row as $k => $r) {
						$style = '';
						if ($rainbow) {
							$style = 'style="color:' . $this->util->randomColor(['salt' => $k, 'limits' => $this->limits]) . ';"';
						}
						$i++;
						if ($this->head and $this->appendType == 'column' and $i == 1) {
							$this->html .= "<th $style>$r</th>";
						} else {
							$this->html .= "<td $style>$r</td>";
						}
					}
					$this->html .= "</tr>";
				}
			}
			$this->html .= '</table>';
		}

		/**
		 * @param string $cls
		 * @param string $delimiter
		 * @param string $item li|ol
		 */
		public function _buildHtmlList($cls = '', $delimiter = ' ', $item = 'li', $rainbow = FALSE)
		{
			$this->matrixFix();
			$this->sort();
			$this->html = "<ul class=\"$cls\">";
			$len = [];
			$head = $this->head;
			$len[] = count($head);
			foreach ($this->matrix as $row) {
				$len[] = count($row);
			}
			$len = max($len);
			if (!empty($head)) {
				if ($this->appendType == 'row') {
					$this->html .= "<$item>";
					foreach ($head as $k => $h) {
						$style = '';
						if ($rainbow) {
							$style = 'style="color:' . $this->util->randomColor(['salt' => $k, 'limits' => $this->limits]) . ';"';
						}
						$this->html .= "<strong $style>$h</strong>" . $delimiter;
					}
					$this->html .= "</$item>";
				} else {
					foreach ($this->head as $k => $h) {
						array_unshift($this->matrix[$k], $h);
					}
				}
			}
			foreach ($this->matrix as $key => $row) {
				$_row = [];
				for ($i = 0; $i < $len; $i++) {
					$_row[$i] = (isset($row[$i])) ? $row[$i] : '';
				}
				if (!$this->util->isEmpty($_row)) {
					$this->html .= "<$item>";
					$i = 0;
					foreach ($row as $k => $r) {
						$style = '';
						if ($rainbow) {
							$style = 'style="color:' . $this->util->randomColor(['salt' => $k, 'limits' => $this->limits]) . ';"';
						}
						$i++;
						if ($this->head and $this->appendType == 'column' and $i == 1) {
							$this->html .= "<strong $style>$r</strong>" . $delimiter;
						} else {
							$this->html .= "<span $style>$r</span>" . $delimiter;
						}
					}
					$this->html .= "</$item>";
				}
			}
			$this->html .= '</ul>';
		}

		/**
		 * @return String
		 */
		public function toCsv()
		{
			$this->_buildCsv();
			return (string)$this->csv;
		}

		/**
		 * @throws Exception
		 */
		public function save()
		{
			if ($this->mode != 'fast') {
				throw new Exception('useless in default mod');
				return FALSE;
			}
			if (fclose($this->output_file)) {
				$this->output_file = NULL;
			}
			return $this;
		}

		/**
		 * @param string $cls
		 * @return String
		 */
		public function toHtml($cls = '', $rainbow = FALSE)
		{
			$this->_buildHtmlTable($cls, $rainbow);
			return (string)$this->html;
		}

		/**
		 * @param string $cls
		 * @return String
		 */
		public function toHtmlTable($cls = '', $rainbow = FALSE)
		{
			$this->_buildHtmlTable($cls, $rainbow);
			return (string)$this->html;
		}

		/**
		 * @param string $cls
		 * @param string $delimiter
		 * @param string $item UL, OL, LI и DL
		 * @return String
		 */
		public function toHtmlList($cls = '', $delimiter = '; ', $item = 'li', $rainbow = FALSE)
		{
			$this->_buildHtmlList($cls, $delimiter, $item, $rainbow);
			return $this->html;
		}

		/**
		 * @param resource|string $source
		 * @return $this|false
		 * @filesource
		 */
		public function readCsv($source)
		{
			switch (gettype($source)) {
				case 'string':
					if (!$this->util->strTest($source, "\n", [$this->line_delimiter, $this->str_delimiter]) and file_exists($source)) {
						$source = @fopen($source, 'r');
						return $this->_readCsvResource($source);
					} else {
						return $this->_readCsvString($source);
					}
				case'resource':
					return $this->_readCsvResource($source);
				default:
					return FALSE;
			}
		}

		/**
		 * @param resource $source
		 * @return $this
		 */
		final private function _readCsvResource($source)
		{
			if (is_resource($source)) {
				$i = 0;
				while (($row = fgetcsv($source, 10240, $this->str_delimiter))) {
					$i++;
					foreach ($row as $i2 => $v) {
						$row[$i2] = trim($v, $this->escape);
					}
					if ($i === 1) {
						$this->setHead($row);
						continue;
					}

					$this->addRow($row);

				}
				fclose($source);
			}
			return $this;
		}

		/**
		 * @param string $source
		 * @return $this
		 */
		final private function _readCsvString($source)
		{
			$i = 0;
			//$rows = str_getcsv($source,$this->str_delimiter);
			$rows = explode($this->line_delimiter, $source);
			if (is_array($rows)) {
				foreach ($rows as $row) {
					$i++;
					if (is_array($row)) {
						foreach ($row as $i => $v) {
							$row[$i] = $this->escape . $v . $this->escape;
						}
					}
					$row = explode($this->str_delimiter, $row);
					if ($i === 1) {
						$this->setHead($row);
						continue;
					}
					$this->addRow($row);
				}
			}
			return $this;
		}

		/**
		 * @return array
		 */
		public function toArray()
		{
			return $this->matrix;
		}

		public function getRow($currentCol = FALSE, $assoc = FALSE)
		{
			if ($currentCol !== FALSE) {
				$this->currentCol = $currentCol;
			}
			if ($this->currentRow == -1) {
				$this->currentRow++;
				return $this->head;
			}
			$row = (isset($this->matrix[$this->currentRow])) ? $this->matrix[$this->currentRow] : FALSE;
			if ($row) {
				$this->currentRow++;
				$_row = [];
				if ($assoc) {
					foreach ($row as $k => $v) {
						$_row[$this->head[$k]] = $v;
					}
				} else {
					foreach ($row as $k => $v) {
						$_row[$k] = $v;
					}
				}
				return $_row;
			} else {
				$this->currentRow = 0;
				return FALSE;
			}
		}

		public function getCol($currentCol = FALSE)
		{
			if ($currentCol !== FALSE) {
				$this->currentCol = $currentCol;
			}
			if ($this->currentCol == -1) {
				$this->currentCol++;
				return $this->head;
			}
			$col = [];
			foreach ($this->matrix as $v) {
				if (isset($v[$this->currentCol])) {
					$col[] = $v[$this->currentCol];
				}
			}
			if ($col) {
				$this->currentCol++;
				return $col;
			} else {
				$this->currentCol = 0;
				return FALSE;
			}
		}

		public function getAssoc($type = 'row')
		{
			$this->sort();
			$response = [];
			switch ($type) {
				case 'row':
					$key = NULL;
					foreach ($this->matrix as $row) {
						foreach ($row as $i => $cell) {
							if ($i == 0) {
								$key = $cell;
								continue;
							}
							if ($key) {
								if (count($row) > 2) {
									if (empty($response[$key])) {
										$response[$key] = [$cell];
									} else {
										array_push($response[$key], $cell);
									}
								} else {
									$response[$key] = $cell;
								}
							} else {
								return FALSE;
							}
						}
					}
					break;
				case 'col':
				case 'head':
					$s = $this->currentCol;
					foreach ($this->head as $key => $head) {
						$col = $this->getCol($key);
						$response[$head] = $col;
					}
					$this->currentCol = $s;
					break;
			}
			if (!empty($response)) {
				return $response;
			}
			return FALSE;
		}

		/**
		 * @return csvString|string
		 */
		final public function __toString()
		{
			return (string)$this->toCsv();
		}

		/**
		 * @param $name
		 * @return bool
		 */
		final public function __isset($name)
		{
			return TRUE;
		}

		/**
		 * @param $name
		 * @return bool
		 */
		final public function __get($name)
		{
			switch ($name) {
				case 'matrix':
					return $this->matrix;

				default:
					return FALSE;

			}
		}

		private function sort()
		{
			ksort($this->head);
			foreach ($this->matrix as $k => $v) {
				ksort($this->matrix[$k]);
			}
		}

		/**
		 * @param $text string
		 */
		private function writeFile($text)
		{
			if (gettype($this->output_file) == 'resource') {
				fwrite($this->output_file, $text);
			} else {
				if ($this->_output_file) {
					file_put_contents($this->_output_file, $text, FILE_APPEND);
				} else {
					throw new Exception('you call "save" too early');
				}
			}
		}

		/**
		 * @param $name
		 * @param $value
		 * @return $this|false|string
		 */
		final public function __set($name, $value)
		{
			switch ($name) {
				case 'matrix':
					$this->matrix = $value;
				case 'csv':
					return $this->readCsv($value);
			}
			return FALSE;
		}

		public function rowCount()
		{
			return count($this->matrix);
		}

		public function colCount()
		{
			return count($this->head);
		}

		public function clearString($a = '')
		{
			return preg_replace($this->clearRegexp, '', (string)$a);
		}
	}
