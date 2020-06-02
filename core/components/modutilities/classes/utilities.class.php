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
		public $modx;
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
					if (is_array($v) || is_object($v)) {
						if (!$echo) {
							$echo .= '(' . gettype($v) . '): ' . json_encode($v, 256);
						} else {
							$echo .= ', (' . gettype($v) . '): ' . json_encode($v, 256);
						}
						continue;
					} else {
						switch (gettype($v)) {
							case 'boolean':
								$v_ = $v ? 'TRUE' : 'FALSE';
								break;
							case 'resource':
								$v_ = '[' . get_resource_type($v) . '] ' . $v;
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
			$value = strtr($value, $this::converter);
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
				$user = $this->modx->user;
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

		public function __toString()
		{
			return json_encode($this);
		}
	}