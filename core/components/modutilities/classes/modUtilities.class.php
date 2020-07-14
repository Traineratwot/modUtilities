<?php

	/**
	 * Created by Kirill Nefediev.
	 * Date: 27.05.2020
	 * Time: 11:51
	 */
	class modUtilities
	{
		/**
		 * @var modX
		 */
		protected $modx;
		/**
		 * @var constant $constant_
		 */
		private $constant;
		public $prefix;
		public $constant_;
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

		const FirstLetter = 1;
		const EveryWord = 2;
		const AfterDot = 3;

		/**
		 * utilities constructor.
		 * @param modX $modx
		 */
		public function __construct(modX &$modx)
		{
			$this->modx = $modx;
			$this->prefix = $this->modx->getOption('table_prefix');
		}

		public function __isset($name): bool
		{
			return isset($this->$name);
		}

		public function __set($name, $value)
		{
			if (isset($this->$name) === FALSE) {
				$this->$name = $value;
			}
		}

		public function __get($name)
		{
			switch ($name) {
				case 'constant':
					if (!isset($this->constant_)) {
						$this->constant_ = $this->loadClass('modUtilitiesConstant');
					}
					return $this->constant_;
				default :
					return FALSE;
			}
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
							$obj = $this->objectInfo($v);
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
		 * @param object $v
		 * @return array|false
		 */
		public function objectInfo(&$v)
		{
			if (is_object($v)) {
				$obj = [];
				$obj['name'] = get_class($v);
				$obj['methods'] = get_class_methods($v);
				$class_vars = array_keys(get_class_vars($obj['name']) ?: []);
				$object_vars = array_keys(get_object_vars($v) ?: []);
				$obj['vars'] = array_unique(array_merge($class_vars, $object_vars));
				return $obj;
			}
			return gettype($v);
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
		 * @param string $string     input
		 * @param int    $mode       = 1
		 * @param bool   $otherLower = 1
		 * @param string $enc        = 'UTF-8'
		 * @return string
		 */
		public function mb_ucfirst($string = '', $mode = modUtilities::FirstLetter, $otherLower = TRUE, $enc = 'UTF-8'): ?string
		{
			switch ($mode) {
				case 3:
					$words = preg_split('#[\.\?\!]#', $string, 0, PREG_SPLIT_NO_EMPTY);
					foreach ($words as $word) {
						$string = str_ireplace($word, $this->mb_ucfirst($word, 1, $enc), $string);
					}
					return $string;
				case 2:
					$words = preg_split('#[\s]#', $string, 0, PREG_SPLIT_NO_EMPTY);
					foreach ($words as $word) {
						$string = str_ireplace($word, $this->mb_ucfirst($word, 1, $enc), $string);
					}
					return $string;
				case 1:
				default:
					if ($otherLower) {
						$string = mb_strtolower($string);
					}
					$count = (mb_strlen($string, $enc) - mb_strlen(ltrim($string, $enc))) + 1;
					return mb_strtoupper(mb_substr($string, 0, $count, $enc), $enc) .
						mb_substr($string, $count, mb_strlen($string, $enc), $enc);
			}
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
			if (is_array($data)) {
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
		 * smart makeUrl
		 * @param int    $id
		 * @param string $alt
		 * @param string $context
		 * @param string $args
		 * @param int    $scheme
		 * @param array  $options
		 * @return array|mixed|string|NULL
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
		 * member
		 * @param modUser|integer|string|NULL $id
		 * @param integer|string|NULL         $group
		 * @param integer|string|NULL         $role
		 * @return array|bool
		 */
		public function member($id = NULL, $group = NULL, $role = NULL)
		{
			$userGroup = [];
			if (!$id) {
				if ((int)$this->modx->user->get('id') > 0) {
					$user = $this->modx->user;
				} else {
					return FALSE;
				}
			} else {
				if (is_object($id) and get_class($id) == 'modUser') {
					$user = $id;
				} else {
					/** @var modUser $user */
					$user = $this->modx->getObject('modUser', [
						'id:LIKE' => $id,
						'OR:username:LIKE' => $id,
					]);
				}
			}
			if ($group) {
				$isMember = FALSE;
				$groups = $this->member($id);
				$this->output[__FUNCTION__] = $groups;
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
					SELECT `group`.`id` 		as `groupId`, 
					       `group`.`name` 		as `groupName`, 
					       `role`.`id` 			as `roleId`, 
					       `role`.`name` 		as `roleName` , 
					       `role`.`authority` 	as `roleAuthority` FROM 
					                 `{$this->prefix}member_groups` 		as `member` 
					      INNER JOIN `{$this->prefix}user_group_roles` 	as `role` 	on `role`.`id`  = `member`.`role` 
					      INNER JOIN `{$this->prefix}membergroup_names` 	as `group` 	on `group`.`id` = `member`.`user_group` 
						  WHERE `member`.`member` = :userId
					");
			if ($q->execute(['userId' => $user->get('id')]) and $q->rowCount() > 0) {
				return $q->fetchAll(PDO::FETCH_ASSOC);
			}
			return FALSE;
		}

		/**
		 * simple plural
		 * @param int   $n
		 * @param array $forms 'арбуз', 'арбуза', 'арбузов'
		 * @return mixed
		 */
		public function plural($n = 0, $forms = [])
		{
			$n = explode(',', (string)$n);
			$n[1] = str_replace('0', '', $n[1]);
			$n = abs((float)implode('', $n));
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
				0 => [
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
		public function convert($n = 0, $type = 'byte', $from = 'SI', $to = 'best')
		{
			try {
				//validate input start
				$out = FALSE;
				$size = [];
				$i = 1;
				$n = (float)$n;
				if (!$n) {
					throw new Exception('invalid number', 0);
				}
				if (isset($this->converterRule[$type])) {
					$converterRule = $this->converterRule[$type];
					$SI = $converterRule['SI'][1];

				} else {
					throw new Exception('invalid type', 0);
				}
				if ($to != 'best' and $to != 'SI') {
					if (!in_array($to, array_keys($converterRule[0])) and !in_array($to, array_keys($converterRule[1]))
						and $to != $SI) {
						$to = 'best';
					}
				}
				//validate input end
				if ($to == $from and $to != 'SI') {
					throw new Exception('easy )', 1);
				}
				$n = $this->ToSi($n, $type, $from);
				if (!$n) {
					throw new Exception('invalid "from" unit', 2);
				}
				if ($to == 'SI' or $to == $SI) {
					throw new Exception('easy )', 2);
				}

				if ($to != 'best') {
					if (in_array($to, array_keys($converterRule[0]))) {
						$g = 0;
					} elseif (in_array($to, array_keys($converterRule[1]))) {
						$g = 1;
					} else {
						throw new Exception('invalid "to" unit', 2);
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
				switch ($e->getCode()) {
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
			if ($from == 'SI' or $from == $SI) {
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
		final public function __toString()
		{
			return (string)json_encode($this, 256);
		}

		/**
		 * header("Content-type: application/json; charset=utf-8");
		 */
		public function headerJson()
		{
			header("Content-type: application/json; charset=utf-8");
		}

		/**
		 * Empty
		 * @param $var
		 * @return bool
		 */
		public function empty($var): bool
		{
			switch (gettype($var)) {
				case "array":
					if (count($var) == 0) {
						return 0;
					}
					break;
				case "string":
					return (trim($var) == '') ? 0 : 1;
				case "NULL":
				case "resource (closed)":
					return 0;
				case "boolean":
				case "integer":
				case "resource":
					return 1;
				default:
					return (int)!empty($var);
			}
			$score = 0;
			foreach ($var as $k => $v) {
				$score += $this->empty($v);
			}
			return !(bool)$score;
		}

		/**
		 * isAssoc
		 * @param array $arr
		 * @return bool
		 */
		public function isAssoc(&$arr = []): bool
		{
			if (is_array($arr)) {
				$c = count($arr);
				if ($c > 10) {
					return !(array_key_exists(0, $arr) and array_key_exists(random_int(0, $c - 1), $arr) and array_key_exists($c - 1, $arr));
				} elseif ($c > 0) {
					return !(range(0, count($arr) - 1) === array_keys($arr));
				}
			}
			return FALSE;
		}

		/**
		 * test srt
		 * @param $str
		 * @return bool
		 */
		public function strTest(): bool
		{
			$score = 0;
			$args = func_get_args();
			$str = (string)array_shift($args);
			foreach ($args as $arg) {
				switch (gettype($arg)) {
					case 'string':
					case 'integer':
						$score += (int)(strpos($str, (string)$arg) !== FALSE);
						break;
					case 'array':
						$sc_ = 0;
						foreach ($arg as $a) {
							$sc_ += (int)(strpos($str, (string)$a) !== FALSE);
						}
						$score += (int)($sc_ === count($arg));
						break;
				}
			}
			$this->output[__FUNCTION__] = $score;
			return $score;
		}

		/**
		 * get user avatar
		 * @param modUser|string|int $id
		 * @param bool               $alt
		 * @param int                $width
		 * @param int                $height
		 * @param string             $r
		 * @param string             $default
		 * @return string
		 */
		public function getUserPhoto($id = 0, $alt = FALSE, $width = 128, $height = 128, $r = 'g', $default = '404'): string
		{
			$alt = $alt ?: 'https://placehold.it/' . $width . 'x' . $height . '?text=avatar';
			if ($id) {
				if (is_object($id) and get_class($id) == 'modUser_mysql') {
					$user = $id;
				} else {
					/** @var modUser $user */
					$user = $this->modx->getObject('modUser', [
						'id:LIKE' => $id,
						'OR:username:LIKE' => $id,
					]);
				}
			} elseif ((int)($this->modx->user->get('id')) > 0) {
				$user = $this->modx->user;
			} else {
				return $alt;
			}
			if ($user) {
				$img = $user->getProfilePhoto($width, $height);
				if ($this->modx->getOption('enable_gravatar') and empty($img)) {
					$Profile = $user->getOne('Profile');
					$img = $this->getGravatar($Profile->get('email'), $width, $r, $default);
					if (strpos(get_headers($img, 1)[0], '200') === FALSE) {
						$img = $alt;
					}
				}
			}
			return $img ?: $alt;
		}

		/**
		 * copy modx function modUser::getGravatar
		 * @param string $email The email address
		 * @param string $s     Size in pixels, defaults to 80px [ 1 - 2048 ]
		 * @param string $d     Default imageset to use [ 404 | mp | identicon | monsterid | wavatar ]
		 * @param string $r     Maximum rating (inclusive) [ g | pg | r | x ]
		 * @return String containing either just a URL or a complete image tag
		 * @source https://gravatar.com/site/implement/images/php/
		 */
		public function getGravatar($email, $size = 128, $r = 'g', $default = '404'): string
		{
			$gravatarEmail = md5(strtolower(trim($email)));
			return 'https://www.gravatar.com/avatar/' . $gravatarEmail . "?s={$size}&r={$r}&d={$default}";
		}

		/**
		 * return database set column option
		 * @param object|string $table
		 * @param string        $column
		 * @return false|string[]
		 */
		public function getSetOption($table = '', $column = '')
		{
			if (is_object($table)) {
				$table = $table->_table;
			}
			if (empty($table) or empty($column)) {
				return FALSE;
			}
			if (!($ret = $this->modx->query("SHOW COLUMNS FROM $table LIKE '$column'"))) {
				return FALSE;
			}
			$line = $ret->fetch(PDO::FETCH_ASSOC);
			$set = rtrim(ltrim(preg_replace('@^[setnum]+@', '', $line['Type']), "('"), "')");
			return preg_split("/','/", $set);
		}

		/**
		 * return all values by tv id
		 * @param int|string $id
		 * @return array|bool
		 */
		public function getAllTvValue($tv = 0)
		{
			$id = 0;
			if (is_numeric($tv)) {
				$id = $tv;
			} elseif (is_string($tv) and !is_numeric($tv)) {
				/** @var modTemplateVar $tv */
				$tv = $this->modx->getObject('modTemplateVar', ['name' => $tv]);
				$id = $tv->get('id');
			} elseif (is_object($id) and $id instanceof modTemplateVar) {
				$id = $id->get('id');
			} else {
				return FALSE;
			}
			$prefix = $this->modx->getOption('table_prefix');
			$sql = "SELECT GROUP_CONCAT(`contentid`) as `contentid`,`value` FROM `{$prefix}site_tmplvar_contentvalues` WHERE `tmplvarid` = :id GROUP BY `value`";
			$statement = $this->modx->prepare($sql);
			if ($statement->execute(['id' => $id])) {
				$result = $statement->fetchAll(PDO::FETCH_KEY_PAIR);
				$responce = [];
				foreach ($result as $k => $v) {
					$responce[$v] = explode(',', $k);
				}
				return $responce;
			}
			return FALSE;
		}

		/**
		 * @param int|modResource $id
		 * @return array|bool
		 */
		public function getAllTvResource($id = 0)
		{
			if (is_int($id)) {
				$id = $this->modx->getObject('modResource', $id);
			} elseif (is_object($id) and $id instanceof modResource) {

			} else {
				return FALSE;
			}
			$response = [];
			/** @var modResource $id */
			$template = (int)$id->get('template');
			$q = $this->modx->prepare("SELECT tmplvarid FROM {$this->prefix}site_tmplvar_templates WHERE templateid = :template");
			$q->execute([
				"template" => $template,
			]);
			while ($tvId = $q->fetch(PDO::FETCH_COLUMN)) {
				$response[$tvId] = $id->getTVValue($tvId);
			}
			return $response;
		}

		/**
		 * get csv class
		 * @return modUtilitiesCsv
		 */
		final public function csv($Params = [])
		{
			return $this->loadClass('modUtilitiesCsv', $Params);
		}

		/**
		 * @param array $Params
		 * @return bool|modUtilitiesRest
		 */
		final public function REST($Params = [])
		{
			return $this->loadClass('modUtilitiesRest', $Params);
		}

		/**
		 * load class
		 * @param string $name
		 * @param array  $Params
		 * @return bool|Class
		 */
		final protected function loadClass($name, $Params = [])
		{
			$path = MODX_CORE_PATH . 'components/modutilities/classes/' . mb_strtolower($name) . '.class.php';
			if (file_exists($path)) {
				if (!class_exists($name)) {
					include_once $path;
				}
				if (class_exists($name)) {
					return new $name($this->modx, $this, $Params);
				} else {
					$this->modx->log(MODX::LOG_LEVEL_ERROR, 'can`t load class "' . $name . '" class not found');
					return FALSE;
				}
			}
			$this->modx->log(MODX::LOG_LEVEL_ERROR, 'can`t load class "' . $name . '" file not found');
			return FALSE;
		}

		/**
		 * return client IP
		 * @return false|string IP
		 */
		public function getIP()
		{
			if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
				$ip = $_SERVER['HTTP_CLIENT_IP'];
			} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
				$ip = $_SERVER['REMOTE_ADDR'];
			}

			return filter_var($ip, FILTER_VALIDATE_IP) ? (string)$ip : FALSE;
		}

		/**
		 * @param int|modResource $id
		 * @return bool|array
		 */
		public function getResourceChildren($id)
		{
			if (is_int($id)) {
				$id = $this->modx->getObject('modResource', $id);
				if (!$id) return FALSE;
			} elseif (is_object($id) and $id instanceof modResource) {

			} else {
				return FALSE;
			}
			$q = $this->modx->prepare("SELECT `id` FROM {$this->prefix}site_content WHERE `parent` = :id");
			if ($q && $q->execute(['id' => $id->get('id')])) {
				return $q->fetchAll(PDO::FETCH_COLUMN);
			}
		}

		/**
		 * convert array to sql IN()
		 * @param array $arr
		 * @return string
		 */
		public function arrayToSqlIn(array $arr): string
		{
			$dop = array_fill(0, count($arr), 256);
			return implode(',', array_map('json_encode', $arr, $dop));
		}
	}