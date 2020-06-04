<?php

	/**
	 * Created by Kirill Nefediev.
	 * Date: 27.05.2020
	 * Time: 11:51
	 */
	class utilities
	{
		/**
		 * @var modX
		 */
		protected $modx;
		/**
		 * @var constant
		 */
		public $constant;
		/**
		 * function other output
		 * @var array
		 */
		public $output = [];
		/**
		 * array translit
		 * @const array
		 */
		const translitRule = [
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
		
		/**
		 * utilities constructor.
		 * @param modX $modx
		 */
		public function __construct(modX &$modx)
		{
			$this->modx = $modx;
			$this->constant = new constant;
		}

		/**
		 * return $args<br>\n;
		 * @param string|array $args
		 * @return string
		 */
		public function print($args = NULL)
		{
			$arr = @func_get_args();
			if (!empty($arr)) {
				$echo = '';
				foreach ($arr as $v) {
					switch (gettype($v)) {
						case 'boolean':
							$v_ = $v ? 'TRUE' : 'FALSE';
							break;
						case 'resource':
							$v_ = '[' . get_resource_type($v) . '] ' . $v;
							break;
						case 'object':
							$obj = [];
							$obj['methods'] = get_class_methods($v);
							$obj['vars'] = array_keys(get_object_vars($v)) ?: get_class_vars($v);
							$v_ = '[' . get_class($v) . '] ' . json_encode($obj, 256);
							break;
						case 'array':
							$v_ = json_encode($v, 256);
							break;
						case 'NULL':
							$v_ = 'NULL';
							break;
						default:
							$v_ = $v;
					}

					if (!$echo) {
						$echo .= '(' . gettype($v) . '): ' . $v_;
					} else {
						$echo .= ', (' . gettype($v) . '): ' . $v_;
					}
					continue;
				}
				return $echo . '<br>' . PHP_EOL;
			}
			return FALSE;
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
		public function mb_ucfirst($string = '', $enc = 'UTF-8')
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
		public function basicTranslit($value = '')
		{
			$value = strtr($value, $this::translitRule);
			return $value;
		}

		/**
		 * output information to the browser using "console.log"
		 * @param string              $do 'log','info','debug','warn','error'
		 * @param string|array|object $data
		 * @return string
		 */
		public function console($do = 'log', $data = '')
		{
			$echo = '';
			if (array_search($do, ['log', 'info', 'debug', 'warn', 'error', 'table', 'object']) === FALSE) {
				$do = 'log';
			}
			if (is_array($data) || is_object($data)) {
				$echo = "<script type='text/javascript'>console.{$do}(" . json_encode($data, 256) . ");</script>";

			} else {
				$data_ = str_replace('<br>', '', $this->print($data));
				$echo = "<script type='text/javascript'>console.{$do}(`$data_`);</script>";
			}
			return $echo;
		}

		/**
		 * format date
		 * @param string $inputFormat
		 * @param string $date
		 * @param string $outputFormat
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
			return mb_strtolower(preg_replace('@[^A-zА-я0-9]|[\/_\\\.\,]@u', '', (string)$a));
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
				$this->output[__FUNCTION__] = $array;
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

		/**
		 * @param int    $id
		 * @param string $alt
		 * @param string $context
		 * @param string $args
		 * @param int    $scheme
		 * @param array  $options
		 * @return array|mixed|string|null
		 */
		public function makeUrl($id = 0, $alt = '#top', $context = 'web', $args = '', $scheme = -1, $options = [])
		{
			if (isset($this->modx->resource)) {
				$current_id = $this->modx->resource->get('id');
			} else {
				$current_id = 0;
			}
			if ($id == $current_id) {
				return $alt;
			} else {
				if (!$id or $id == $this->modx->getOption('site_start')) {
					return $this->modx->getOption('base_url');
				} else {
					$url = trim(str_replace($_SERVER['HTTP_ORIGIN'], NULL, $this->modx->makeUrl($id, $context, $args, $scheme, $options)), '/');
					return $this->modx->getOption('base_url') . $url;
				}
			}

		}

		/**
		 * @return mixed
		 */
		public function or()
		{
			$arr = func_get_args();
			foreach ($arr as $a) {
				if (!empty($a) and $a) {
					return $a;
				}
			}
		}

		/**
		 * @param integer|null        $id
		 * @param integer|string|null $group
		 * @param integer|string|null $role
		 * @return array|bool
		 */
		public function member($id = NULL, $group = NULL, $role = NULL)
		{
			$userGroup = [];
			if (!$id) {
				if ($this->modx->user->isAuthenticated()) {
					$user = $this->modx->user;
				} else {
					return FALSE;
				}
			} else {
				$user = $this->modx->getObject('modUser', $id);
			}
			if ($group) {
				$isMember = FALSE;
				$groups = $this->member($id);
				if ($groups) {
					if ($role) {
						foreach ($groups as $g) {
							if (((int)$g['groupId'] == (int)$group or (string)$g['groupName'] === (string)$group) and
								((int)$g['roleId'] == (int)$role or (string)$g['roleName'] === (string)$role)
							) {
								$isMember = TRUE;
								break;
							}
						}
						return $isMember;
					}
					foreach ($groups as $g) {
						if ((int)$g['groupId'] == (int)$group or (string)$g['groupName'] === (string)$group) {
							$isMember = [
								'roleId' => $g['roleId'],
								'roleName' => $g['roleName'],
							];
							break;
						}
					}
				}
				return $isMember;
			}
			$q = $this->modx->prepare("
					SELECT `group`.`id` 	as `groupId`, 
					       `group`.`name` 	as `groupName`, 
					       `role`.`id` 		as `roleId`, 
					       `role`.`name` 	as `roleName` FROM 
					                 `{$this->modx->getOption('table_prefix')}member_groups` 		as `member` 
					      INNER JOIN `{$this->modx->getOption('table_prefix')}user_group_roles` 	as `role` 	on `role`.`id`  = `member`.`role` 
					      INNER JOIN `{$this->modx->getOption('table_prefix')}membergroup_names` 	as `group` 	on `group`.`id` = `member`.`user_group` 
						  WHERE `member`.`member` = :userId
					");
			if ($q->execute(['userId' => $user->get('id')]) and $q->rowCount() > 0) {
				return $q->fetchAll(PDO::FETCH_ASSOC);
			}
			return FALSE;
		}

		/**
		 * @param int   $n
		 * @param array $forms 'арбуз', 'арбуза', 'арбузов'
		 * @return mixed
		 */
		public function plural($n = 0, $forms = [])
		{
			return $n % 10 == 1 && $n % 100 != 11 ? $forms[0] : ($n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 10 || $n % 100 >= 20) ? $forms[1] : $forms[2]);
		}

		/**
		 * array for converter
		 * 'type'=>[
		 *      0(меньше CИ)[
		 *          'ед'=> [сколько, чего]
		 *      ]
		 *      1(больше CИ)[
		 *          'ед'=> [сколько, чего]
		 *      ]
		 *      CИ => [1, ед]
		 * ]
		 * @var array[]
		 */
		public $converterRule = [
			'byte' => [
				0=>[
					'bit' => [0.125, 'b'],
				],
				1 => [
					'kb' => [1024, 'b'],
					'mb' => [1024, 'kb'],
					'gb' => [1024, 'mb'],
					'tb' => [1024, 'gb'],
				],
				'SI' => [1, 'b'],
			],
			'mass' => [
				0 => [
					'g' => [0.001, 'kg'],
					'mg' => [0.001, 'g'],
				],
				1 => [
					'T' => [1000, 'kg'],
				],
				'SI' => [1, 'kg'],
			],
			'length' => [
				0 => [
					'mm' => [0.001, 'm'],
					'cm' => [10, 'mm'],
					'dm' => [10, 'dm'],
				],
				1 => [
					'km' => [1000, 'm'],
				],
				'SI' => [1, 'm'],
			],
			'time' => [
				0 => [
					'ms' => [0.001, 's'],
				],
				1 => [
					'min' => [60, 's'],
					'h' => [60, 'min'],
					'day' => [24, 'h'],
				],
				'SI' => [1, 's'],
			],
		];

		/**
		 * converter any units
		 * @param int|float $n
		 * @param string    $type byte,mass,length,time
		 * @param string    $from unit|'SI'
		 * @param string    $to   unit|'best'
		 * @return array|false
		 */
		public function converter($n = 0, $type = 'byte', $from = 'SI', $to = 'best')
		{
			try {
				//validate input start
				$out = FALSE;
				$size = [];
				$i = 0;
				$n = (float)$n;
				if (!$n) {
					throw new Exception('invalid number',0);
				}
				if (isset($this->converterRule[$type])) {
					$converterRule = $this->converterRule[$type];
					$SI = $converterRule['SI'][1];

				} else {
					throw new Exception('invalid type',0);
				}
				if ($to != 'best' AND $to != 'SI') {
					if (!in_array($to, array_keys($converterRule[0])) and !in_array($to, array_keys($converterRule[1]))
						and $to != $SI) {
						$to = 'best';
					}
				}
				//validate input end
				if ($to == $from and $to != 'SI') {
					throw new Exception('easy )',1);
				}
				$n = $this->ToSi($n, $type, $from);
				if(!$n){
					throw new Exception('invalid "from" unit',2);
				}
				if ($to == 'SI' OR $to == $SI) {
					throw new Exception('easy )',2);
				}

				if ($to != 'best') {
					if (in_array($to, array_keys($converterRule[0]))) {
						$g = 0;
					} elseif (in_array($to, array_keys($converterRule[1]))) {
						$g = 1;
					} else {
						throw new Exception('invalid "to" unit',2);
					}
				} else {
					if ($n >= $converterRule['SI'][0]) {
						$g = 1;
					} else {
						$g = 0;
					}
				}
				foreach ($converterRule[$g] as $key => $rule) {
					if ($n >= $rule[0]) {
						$n /= $rule[0];
						$size = [round($n, $i), $key];
					} else {
						if ($to == 'best') {
							break;
						}
					}
					if ($to != 'best' and $to == $key) {
						break;
					}
					$i++;
				}
				if (!$out and !empty($size)) {
					$out = $size;
				} else {
					$out = [$n, $SI];
				}

			} catch (Exception $e) {
				echo $this->console('log', $e->getMessage());
				switch ($e->getCode()){
					case 1:
						return [round($n, $i), $from];
					case 2:
						return [round($n, $i), $SI];
					default:
						return $e->getMessage();
				}
			}
			return $out;
		}

		/**
		 * converter any units to SI
		 * @param        $n
		 * @param string $type
		 * @param string $from
		 * @return bool|mixed
		 */
		public function ToSi($n, $type = 'byte', $from = 'SI')
		{
			if (isset($this->converterRule[$type])) {
				$converterRule = $this->converterRule[$type];
				$SI = $converterRule['SI'][1];
			} else {
				return FALSE;
			}
			if ($from == 'SI' OR $from == $SI) {
				return $n;
			}
			if (in_array($from, array_keys($converterRule[0]))) {
				$g = 0;
			} elseif (in_array($from, array_keys($converterRule[1]))) {
				$g = 1;
			} else {
				return FALSE;
			}
			while ($from != $SI and isset($converterRule[$g][$from])) {
				$f_ = $converterRule[$g][$from];
				$n *= $f_[0];
				$from = $f_[1];
			}
			return $n;
		}

		/**
		 * @return string
		 */
		public function __toString()
		{
			return (string)json_encode($this, 256);
		}
	}

	/**
	 * @property int kb
	 * @property int mb
	 * @property int gb
	 * @property int tb
	 * @property int min
	 * @property int hour
	 * @property int day
	 * @property int week
	 */
	class constant
	{
		/**
		 * @var int
		 */
		public $kb = 1024;
		/**
		 * @var int
		 */
		public $min = 60;

		/**
		 * constant constructor.
		 */
		public function __construct()
		{
			$this->mb = $this->kb * 1024;
			$this->gb = $this->mb * 1024;
			$this->tb = $this->gb * 1024;
			$this->hour = $this->min * 60;
			$this->day = $this->hour * 24;
			$this->week = $this->day * 7;
		}

		/**
		 * @return string
		 */
		public function __toString()
		{
			return (string)json_encode($this, 256);
		}
	}