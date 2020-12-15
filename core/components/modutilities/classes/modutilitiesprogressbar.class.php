<?php

	class modutilitiesProgressBar
	{
		public $sys = '';
		/**
		 * @var int|mixed
		 */
		public $total = 0;
		/**
		 * **fast level**
		 *  - 0 very slow
		 *  - 1 normal
		 *  - 2 faster
		 * @var false|int|mixed
		 **/
		public static $width = 30;
		public $height = 0;
		public $logs = [];
		public $times = [];
		/**
		 * **style speed couters**
		 * - it/s iterate by second
		 * - s/it second for 1 iterate
		 * - left remaining time in second
		 * @var mixed|string
		 */
		public $speed = '';
		/**
		 * @var bool
		 */
		public $colored = TRUE;
		public $smooth = TRUE;
		public $skin = [
			'body' => '=',
			'head' => '>',
			'track' => ' ',
			'HorizontalDelimiter' => '-',
		];
		const foreground_colors = [
			'black' => '0;30',
			'dark_gray' => '1;30',
			'blue' => '0;34',
			'light_blue' => '1;34',
			'green' => '0;32',
			'light_green' => '1;32',
			'cyan' => '0;36',
			'light_cyan' => '1;36',
			'red' => '0;31',
			'light_red' => '1;31',
			'purple' => '0;35',
			'light_purple' => '1;35',
			'brown' => '0;33',
			'yellow' => '1;33',
			'light_gray' => '0;37',
			'white' => '1;37',
		];
		const background_colors = [
			'black' => '40',
			'red' => '41',
			'green' => '42',
			'yellow' => '43',
			'blue' => '44',
			'magenta' => '45',
			'cyan' => '46',
			'light_gray' => '47',
		];

		public function __construct(modX &$modx, modUtilities &$util, $param = [])
		{
			//инициализация
			$this->modx  = $modx;
			$this->util  = $util;
			$this->match = $util->math();
			$this->getSystem();
			//установка параметров
			$this->total  = $this->modx->getOption('total' , $param, 0);
			$this->speed  = $this->modx->getOption('speed' , $param, 'it/s');
			$this->fast   = $this->modx->getOption('fast'  , $param, 1);
			$this->smooth = $this->modx->getOption('smooth', $param, TRUE);
			if ($this->sys == 'win') {
				$this->colored = $this->modx->getOption('colored', $param, FALSE);
			} else {
				$this->colored = $this->modx->getOption('colored', $param, TRUE);
			}
			//определение длин
			$this->getTerminalSize();
			$this->percent_length = strlen("[100%]");
			$this->speed_length = 0;
			if ($this->fast <= 2) {
				$this->speed_length = strlen("[left 0,00]");
			}
			$this->bar_length = strlen("[]");
			//очистка буфера
			ob_end_flush();
		}

		/**
		 * you can write log
		 * @param null $level
		 * @param null $msg
		 * @param null $object
		 * @return false
		 */
		public function log($level = NULL, $msg = NULL, $object = NULL)
		{
			if (PHP_SAPI != 'cli') {
				return FALSE;
			}
			if ($object) {
				$object = $this->util->varsInfo($object) ?: NULL;
			}
			$this->logs[] = '[' . $level . '] - ' . $msg . ' ' . $object . PHP_EOL;
		}

		/**
		 * @param int   $current
		 * @param false $total
		 * @return false
		 * @throws Exception
		 */
		public function progress($current = 1, $total = FALSE)
		{
			if (PHP_SAPI != 'cli') {
				return FALSE;
			}
			//$this->clear();
			if ($total) {
				$this->total = $total;
			}
			if (!$this->total) {
				printf("[%10s]\n", 'invalid total');
				return FALSE;
			}
			if ($current == $total) {
				echo PHP_EOL;
			}
			if ($current >= $total) {
				return FALSE;
			}
			if (!$this->smooth) {
				$this->clear();
			}
			$s = ceil($this->match->percent($this->total, '=%', $current, FALSE)) + 1;
			if ($s > 100) {
				$s = 100;
			}
			if ($s <= 0) {
				$s = 1;
			}
			if ($this->fast <= 0) {
				$this->getTerminalSize();
			}

			$width = $this->width - ($this->percent_length + $this->speed_length + $this->bar_length);
			$space = floor($this->match->percent($width, '-%', $s));
			$track = str_pad('', $space, $this->skin['track'], STR_PAD_LEFT);
			$body = str_pad('', $width - $space - 1, $this->skin['body'], STR_PAD_LEFT);
			if ($this->colored) {
				if ($this->skin['track'] != ' ') {
					$track = $this->getColoredString($track, 'red');
				}
				$this->skin['head'] = $this->getColoredString($this->skin['head'], 'light_green');
				$body = $this->getColoredString($body, 'green');
			}
			$bar = $body . $this->skin['head'] . $track;
			$output = '[' . $bar . ']';

			$output .= str_pad("[{$s}%]", $this->percent_length);
			if ($this->fast <= 2) {
				$this->times[$current] = round(microtime(TRUE), 3);
				$speed = $this->calcSpeed($current, $total);
				switch ($this->speed) {
					case 'its':
					case 'it/s':
						$speed = "it/s {$speed}";
						break;
					case 'sit':
					case 's/it':
						$speed = "s/it {$speed}";
						break;
					case 'left':
						if ($s >= 99) {
							$speed = "ready";
						} else {
							$speed = "left {$speed}";
						}
						break;
				}
				$speed = str_pad($speed, $this->speed_length - 2, ' ', STR_PAD_BOTH);
				if ($this->colored) {
					if ($s >= 99) {
						$speed = $this->getColoredString($speed, 'green');
					}
				}
				$output .= '[' . $speed . ']';
			}

			if ($this->fast <= 0) {
				if (!empty($this->logs)) {
					$logs = array_slice($this->logs, -$this->height + 5);
					$output .= "\n" . str_pad('', $width, $this->skin['HorizontalDelimiter']);
					$output .= "\n" . implode('', $logs);
					$output .= "\n" . str_pad('', $width, $this->skin['HorizontalDelimiter']);
				}
			}
			if ($s >= 100) {
				$output .= PHP_EOL;
			}
			echo "\r" . $output;
			flush();
		}

		/**
		 * @param int $current
		 * @param int $total
		 * @return float|string
		 */
		public function calcSpeed($current = 0, $total = 0)
		{
			$start = current($this->times);
			$i = 0;
			$itog = [];
			if (!$this->fast) {
				foreach ($this->times as $k => $time) {
					$i++;
					$t = round(abs($start - (float)$time));
					if ($t > 0.1) {
						$itog[] = $i;
						$start = $time;
						$i = 0;
						continue;
					}
				}
				if (empty($itog)) {
					return '...';
				}
				$rate = round(array_sum($itog) / count($itog), 2);
			} else {
				$rate = $current / (round(microtime(TRUE), 3) - $start);
			}
			switch ($this->speed) {
				case 'its':
				case 'it/s':
					$out = $rate;
					break;
				case 'sit':
				case 's/it':
					$out = 1 / $rate;
					break;
				case 'left':
					$out = round((1 / $rate) * ($total - $current), 2);
					break;
			}
			return round($out, 2) ?: '...';


		}

		public function clear()
		{
			switch ($this->sys) {
				case 'win':
					print exec('cls');
					break;
				case 'nix':
				default:
					print exec('clear');
					break;

			}
			flush();
		}

		/**
		 * # very slow function
		 * @return false|int[]
		 */
		public function getTerminalSize()
		{
			if (PHP_SAPI != 'cli') {
				return FALSE;
			}
			$size = ['width' => 0, 'height' => 0];
			switch ($this->sys) {
				case 'win':
					$output = [];
					exec('mode', $output);
					foreach ($output as $line) {
						$matches = [];
						$w = preg_match('/^\s*columns\:?\s*(\d+)\s*$/i', $line, $matches);
						if ($w) {
							$size['width'] = (int)$matches[1];
						} else {
							$h = preg_match('/^\s*lines\:?\s*(\d+)\s*$/i', $line, $matches);
							if ($h) {
								$size['height'] = (int)$matches[1];
							}
						}
						if ($size['width'] and $size['height']) {
							break;
						}
					}
					$this->width = $size['width'];
					$this->height = $size['height'];
					return $size;
				case 'nix':
					$width = getenv('COLUMNS');
					$height = getenv('LINES');
					if ($width !== FALSE and FALSE !== $height) {
						$size['width'] = (int)trim($width);
						$size['height'] = (int)trim($height);
					} else {
						$_str = strtolower(exec('stty -a |grep columns'));
						preg_match("/rows.([0-9]+);.columns.([0-9]+);/", $_str, $output);
						if (count($output) == 3) {
							$size['width'] = (int)$output[2];
							$size['height'] = (int)$output[1];
						} else {
							$size['width'] = 100;
							$size['height'] = 50;
						}
					}
					$this->width = $size['width'];
					$this->height = $size['height'];
					return $size;
			}
		}

		/**
		 * @return string
		 */
		public function getSystem()
		{
			$sys = $this->util->rawText(php_uname('s'));
			if (strpos($sys, 'windows') !== FALSE) {
				return $this->sys = 'win';
			}
			if (strpos($sys, 'linux') !== FALSE) {
				return $this->sys = 'nix';
			}
			return $this->sys = 'nix';
		}

		/**
		 * # simple progressBar
		 * @param $done
		 * @param $total
		 * @deprecated
		 */
		public static function progressBar($done, $total)
		{
			static $start_time;
			$size = modutilitiesProgressBar::$width;
			// if we go over our bound, just ignore it
			if ($done > $total) return;

			if (empty($start_time)) $start_time = time();
			$now = time();

			$perc = (double)($done / $total);

			$bar = floor($perc * $size);

			$status_bar = "\r[";
			$status_bar .= str_repeat("=", $bar);
			if ($bar < $size) {
				$status_bar .= ">";
				$status_bar .= str_repeat(" ", $size - $bar);
			} else {
				$status_bar .= "=";
			}

			$disp = number_format($perc * 100, 0);

			$status_bar .= "] $disp%  $done/$total";

			$rate = ($now - $start_time) / $done;
			$left = $total - $done;
			$eta = round($rate * $left, 2);

			$elapsed = $now - $start_time;

			$status_bar .= " remaining: " . number_format($eta) . " sec.  elapsed: " . number_format($elapsed) . " sec.";

			echo "$status_bar  ";

			flush();

			// when done, send a newline
			if ($done == $total) {
				echo "\n";
			}

		}

		public static function getColoredString($string, $foreground_color = NULL, $background_color = NULL)
		{
			//$w = strlen($string);
			$colored_string = "";

			// Check if given foreground color found
			if (isset(modutilitiesProgressBar::foreground_colors[$foreground_color])) {
				$colored_string .= "\033[" . modutilitiesProgressBar::foreground_colors[$foreground_color] . "m";
			}
			// Check if given background color found
			if (isset(modutilitiesProgressBar::background_colors[$background_color])) {
				$colored_string .= "\033[" . modutilitiesProgressBar::background_colors[$background_color] . "m";
			}

			// Add string and end coloring
			$colored_string .= $string . "\033[0m";
			//$width += strlen($colored_string) - $w;
			return $colored_string;
		}

		// Returns all foreground color names
		public static function getTextColors()
		{
			return array_keys(modutilitiesProgressBar::foreground_colors);
		}

		// Returns all background color names
		public static function getBackgroundColors()
		{
			return array_keys(modutilitiesProgressBar::background_colors);
		}
	}