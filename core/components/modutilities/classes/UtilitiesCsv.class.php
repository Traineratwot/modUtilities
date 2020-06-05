<?php
	/**
	 * Created by Kirill Nefediev..
	 * Date: 04.06.2020
	 * Time: 18:04
	 */

	class utilitiesCsv
	{
		/* @var modX $modX */
		public $modx;
		/* @var utilities $util */
		public $util;
		/* @var string chr(239) . chr(187) . chr(191) */
		public $utf8bom;
		/* @var string Charset */
		public $inputCharset;
		/* @var string output csv string */
		protected $csv;
		/* @var array */
		public $matrix;
		/* @var array */
		protected $head = [];
		protected $appendType = FALSE;
		protected $delimiter;
		protected $line_delimiter;

		/**
		 * UtilitiesCsv constructor
		 * param [
		 *  inputCharset | 'utf8'
		 *    woBom - without BOM | false
		 *    delimiter |';'
		 *    line_delimiter | PHP_EOL
		 * ]
		 * @param modX      $modx
		 * @param utilities $util
		 * @param array     $param
		 */
		public function __construct(modX &$modx, utilities &$util, $param)
		{
			$this->inputCharset = isset($param['inputCharset']) ? $param['inputCharset'] : 'utf8';
			$this->modx = $modx;
			$this->util = $util;

			$this->utf8bom = (isset($param['woBom']) and $param['woBom'] = TRUE) ? NULL : chr(239) . chr(187) . chr(191);
			$this->delimiter = isset($param['delimiter']) ? $param['delimiter'] : ';';
			$this->line_delimiter = isset($param['line_delimiter']) ? $param['line_delimiter'] : PHP_EOL;
		}

		/**
		 * add row to csv
		 * @return $this|bool
		 */
		public function addRow()
		{
			if (!$this->appendType or !$this->util->notEmpty(($this->matrix))) {
				$this->appendType = 'row';
			}
			if ($this->appendType != 'row') {
				return FALSE;
			}
			$args = func_get_args();
			if (count($args) == 1 and is_array($args[0])) {
				$args = $args[0];
			}
			$head = array_flip($this->head);
			$isAssoc = $this->util->isAssoc($args);
			$args_ = [];
			foreach ($args as $k => $art) {
				if ($isAssoc) {
					if (!is_string($art) and !is_numeric($art)) {
						$args_[$head[$k]] = NULL;
					} else {
						$args_[$head[$k]] = (string)$art;
					}
				} else {
					if (!is_string($art) and !is_numeric($art)) {
						$args_[$k] = NULL;
					} else {
						$args_[$k] = (string)$art;
					}
				}
			}
			$this->matrix[] = $args_;
			return $this;
		}

		/**
		 * add column to csv
		 * @return $this|bool
		 */
		public function addCol()
		{
			if (!$this->appendType or !$this->util->notEmpty(($this->matrix))) {
				$this->appendType = 'column';
			}
			if ($this->appendType != 'column') {
				return FALSE;
			}
			$args = func_get_args();
			if (count($args) == 1 and is_array($args[0])) {
				$args = $args[0];
			}
			$head = array_flip($this->head);
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
			foreach ($args as $k => $art) {
				if (!is_string($art) and !is_numeric($art)) {
					$args[$k] = NULL;
				} else {
					$args[$k] = (string)$art;
				}
			}
			$this->head = $args;
			return $this;
		}

		/**
		 * @param string|integer $x column
		 * @param string|integer $y row
		 * @param string|integer $value
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
		 *generate csv string
		 */
		public function _buildCsv()
		{
			$this->csv = $this->utf8bom;
			$len = [];
			$head = $this->head;
			$len[] = count($head);
			foreach ($this->matrix as $row) {
				$len[] = count($row);
			}
			$len = max($len);
			if ($this->appendType == 'row') {
				$this->csv .= implode($this->delimiter, $head);
			} else {
				foreach ($this->head as $k => $h) {
					array_unshift($this->matrix[$k], $h);
				}
			}
			foreach ($this->matrix as $key => $row) {
				$_row = [];
				for ($i = 0; $i < $len; $i++) {
					$_row[$i] = (isset($row[$i])) ? $row[$i] : '';
				}
				$this->csv .= $this->line_delimiter;
				$this->csv .= implode($this->delimiter, $_row);
			}
		}

		/**
		 * @return csvString
		 */
		public function toCsv()
		{
			$this->_buildCsv();
			return (string)$this->csv;
		}

		/**
		 * @return csvString|string
		 */
		final public function __toString()
		{
			return $this->toCsv();
		}
	}