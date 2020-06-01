<?php

	/**
	 * Created by Kirill Nefediev.
	 * Date: 27.05.2020
	 * Time: 11:51
	 */
	class utilities
	{
		public $modx;
		public $data = [];
		const converter = [
			'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',
			'е' => 'e', 'ё' => 'e', 'ж' => 'zh', 'з' => 'z', 'и' => 'i',
			'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n',
			'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
			'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch',
			'ш' => 'sh', 'щ' => 'sch', 'ь' => '', 'ы' => 'y', 'ъ' => '',
			'э' => 'e', 'ю' => 'yu', 'я' => 'ya',

			'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D',
			'Е' => 'E', 'Ё' => 'E', 'Ж' => 'Zh', 'З' => 'Z', 'И' => 'I',
			'Й' => 'Y', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N',
			'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T',
			'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C', 'Ч' => 'Ch',
			'Ш' => 'Sh', 'Щ' => 'Sch', 'Ь' => '', 'Ы' => 'Y', 'Ъ' => '',
			'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya',
		];


		public function __construct(&$modx)
		{
			$this->modx = $modx;
			$this->constant = new constant;
		}

		/**
		 * echo $args<br>\n;
		 * @param string|array $args
		 * @return string
		 */
		public function print_n($args)
		{
			$arr = func_get_args();
			$echo = '';
			foreach ($arr as $v) {
				if (is_array($v) || is_object($v)) {
					if (!$echo) {
						$echo .= '('.gettype($v) . '): ' . json_encode($v, 256);
					} else {
						$echo .= ', (' . gettype($v) . '): ' . json_encode($v, 256);
					}
					continue;
				} else {
					if (!$echo) {
						$echo .= '('.gettype($v) . '): ' . $v;
					} else {
						$echo .= ', (' . gettype($v) . '): ' . $v;
					}
					continue;
				}
			}
			echo $echo . '<br>' . PHP_EOL;
			return $echo;
		}


		/**
		 * takes a screenshot of the passed page <br>
		 * screenShot("http://habr.ru", "1024x768", "600", "jpeg",MODX_ASSETS_PATH.'test')
		 * @param string $url
		 * @param string $razr
		 * @param string $razm
		 * @param string $form
		 * @param string $path
		 * @return bool|false|string
		 */
		public function screenShot($url, $razr = '1024x768', $razm = '600', $form = 'jpeg', $path = FALSE)
		{
			$toapi = "http://mini.s-shot.ru/" . $razr . "/" . $razm . "/" . $form . "/?" . $url;
			$scim = file_get_contents($toapi);
			if ($path != FALSE) {
				file_put_contents($path . '.' . $form, $scim);
			}
			return $scim;
		}

		/**
		 * makes the first letter capital (in Russian)
		 * @param        $string
		 * @param string $enc = 'UTF-8'
		 * @return string
		 */
		public function mb_ucfirst($string, $enc = 'UTF-8')
		{
			return mb_strtoupper(mb_substr($string, 0, 1, $enc), $enc) .
				mb_substr($string, 1, mb_strlen($string, $enc), $enc);
		}

		/**
		 * transliterates text using your "Alias" translit package  if available
		 * @param string $str
		 * @return string|bool
		 */
		public function translit($str = '')
		{
			if ($str) {
				$translit = $this->modx->call('modResource', 'filterPathSegment', [&$this->modx, $str]);
				if (!$translit or $translit == $str) {
					return $this->cpuTranslit($str);
				}
				return $translit;
			} else {
				return FALSE;
			}
		}
		/**
		 * transliterates url
		 * @param string $str
		 * @return string|bool
		 */
		public function cpuTranslit($str = '')
		{
			if ($str) {
				$str = mb_strtolower($str);
				$str = $this->basicTranslit($str);
				$str = mb_ereg_replace('[^-0-9a-z]', '-', $str);
				$str = mb_ereg_replace('[-]+', '-', $str);
				$str = trim($str, '-');
				return $str;
			} else {
				return FALSE;
			}
		}

		/**
		 * transliterates text
		 * @param string $str
		 * @return string|bool
		 */
		public function basicTranslit($value)
		{
			$value = strtr($value, $this::converter);
			return $value;
		}

		/**
		 * output information to the browser using "console.log"
		 * @param string              $do 'log','info','debug','warn','error'
		 * @param string|array|object $data
		 * @return bool
		 */
		public function console($do, $data)
		{
			$echo  = '';
			if (array_search($do, ['log', 'info', 'debug', 'warn', 'error','table']) === FALSE) {
				return FALSE;
			}
			if (is_array($data) || is_object($data)) {
				if ($do == 'table') {
					$echo = "<script>console.table(".json_encode($data, 256).");</script>";
				}else {
					$echo = "<script>console.{$do}('(" .gettype($data) . "):" . json_encode($data, 256) . "');</script>";
				}
			} else {
				$data = escapeshellcmd($data);
				$echo = "<script>console.{$do}('(" .gettype($data) . "): $data');</script>";
			}
			echo $echo;
			return $echo;
		}

		/**
		 * format date
		 * @param string $inputFormat
		 * @param string $outputFormat
		 * @param string $date
		 * @return string
		 */
		public function dateFormat($inputFormat, $date, $outputFormat)
		{
			$dt = DateTime::createFromFormat($inputFormat, $date);
			return $dt->format($outputFormat);
		}

		/**
		 * removes all extra special characters from the string
		 * @param string $a
		 * @return false|string
		 */
		public function rawText($a = '')
		{
			return mb_strtolower(preg_replace('@[^A-zА-я0-9]|[\/_\\\.\,]@', '', (string)$a));
		}

		/**
		 * Compares line 1 with line 2 without special characters, and so on. If the rows are not identical, the
		 * percentage of their similarity will be displayed. can work with an array
		 *
		 * @param array|string $a
		 * @param string       $b
		 * @return array|mixed
		 */
		public function likeString($a, $b = '')
		{
			if (gettype($a) == 'array') {
				$array = [];
				foreach ($a as $c) {
					$x = $this->likeString($c, $b);
					$array[$x['score']] = $x;
				}
				$this->data[__FUNCTION__] = $array;
				return $array[max(array_keys($array))];
			} elseif (gettype($a) == 'string') {
				if ($this->rawText($a) == $this->rawText($b)) {
					return [
						'one' => $a,
						'two' => $b,
						'score' => 100,
					];
				} else {
					similar_text($a, $b, $score);
					return [
						'one' => $a,
						'two' => $b,
						'score' => $score,
					];
				}
			}
		}

		public function makeUrl($id = 0,$alt='#top'){
			if (isset($this->modx->resource)) {
				$current_id = $this->modx->resource->get('id');
			}else{
				$current_id = 0;
			}
			if ($id) {
			    if($id == $current_id){
				    return $alt;
			    }else{
			    	if ($id == $this->modx->getOption('site_start')) {
			    	    return '/';
			    	}else{
					    return '/'.$this->modx->makeUrl($id);
				    }
			    }
			}else{
				return  $this->modx->makeUrl($current_id);
			}

		}
		public function or(){
			$arr = func_get_args();
			foreach ($arr as $a){
				if(!empty($a) and $a){
					return $a;
				}
			}
		}

	}

	class constant
	{
		const kb = 1024;
		const mb = 1024 * 1024;
		const gb = 1024 * 1024 * 1024;
		const tb = 1024 * 1024 * 1024 * 1024;
		const min = 60;
		const hour = 60 * 60;
		const day = 24 * 60 * 60;
		const week = 7 * 24 * 60 * 60;
	}