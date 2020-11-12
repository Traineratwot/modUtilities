<?php

	class modutilitiesProgressBar
	{
		public $sys = '';
		/**
		 * @var int|mixed
		 */
		public $total = 0;
		public $width = 0;
		public $height = 0;
		public $logs = [];
		public $times = [];
		public $speedModes =[
			'its'=>'it/s',
			'it/s'=>'it/s',
			'sit'=>'s/it',
			's/it'=>'s/it',
		];

		public function __construct(modX &$modx, modUtilities &$util, $param = [])
		{
			ob_end_flush();
			//$this->modx = $modx;
			$this->util = $util;
			$this->match = $util->math();
			$this->total = $param['total'] ?? 0;
			$this->speedMode = $param['speed'] ?? 'it/s';

			$sys = $this->util->rawText(php_uname('s'));
			if (strpos($sys, 'windows') !== FALSE) {
				$this->sys = 'win';
			}
			if (strpos($sys, 'linux') !== FALSE) {
				$this->sys = 'nix';
			}
		}

		public function log($level = NULL, $msg = NULL, $object = NULL)
		{
			$this->logs[] = '[' . $level . '] - ' . $msg . ' ' . ($this->util->varsInfo($object) ?: NULL) . PHP_EOL;
		}

		public function progress($current = 1, $total = FALSE)
		{

			$this->clear();
			if ($total) {
				$this->total = $total;
			}
			if (!$this->total) {
				printf("[%10s]\n", 'invalid total');
				return FALSE;
			}
			$s = $this->match->percent($this->total, '=%', $current);
			if ($s > 100) {
				$s = 100;
			}
			if ($s <= 0) {
				$s = 1;
			}
			$this->getTerminalSize();
			$width = $this->width - 22;
			$space = floor($this->match->percent($width, '-%', $s));
			$output = str_pad('', $space, '_', STR_PAD_LEFT);
			$output = str_pad('', $width - $space, '#', STR_PAD_LEFT) . $output;
			$output = '[' . $output . ']';

			$output .= "[{$s}%]";
			if($this->speedMode) {
				$this->times[$current] = microtime(TRUE);
				$speed = $this->calcSpeed();
				$output .= "[{$this->speedModes[$this->speedMode]} {$speed[$this->speedModes[$this->speedMode]]}]";
			}
			$logs = array_slice($this->logs, -$this->height + 3);
			print implode('', $logs);
			print $output . PHP_EOL;
			flush();
		}

		public function calcSpeed(){
			$start = (float)$this->times[array_keys($this->times)[0]];
			$i =0;
			$itog = [];
			foreach ($this->times as $k=>$time){
				$i++;
				$t =round(abs($start - (float)$time));
				if($t > 1000000000){
					$itog[] =$i;
					$start = $time;
					$i =0;
					continue;
				}
			}
			if(empty($itog)){
				return['its'=>'...','sit'=>'...'];
			}else{
				$delta = round(array_sum($itog)/count($itog),2);
				return['its'=>$delta,'sit'=>1/$delta];
			}
		}

		public function clear()
		{
			switch ($this->sys) {
				case 'win':
					print exec('cls');
					break;
				case 'nix':
					print exec('clear');
					break;
			}
			flush();
		}

		public function getTerminalSize()
		{
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
	}