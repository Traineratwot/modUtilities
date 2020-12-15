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
		/**
		 * @var string $prefix
		 */
		public $prefix = 'modx_';
		public $constant_;
		/**
		 * function other output
		 * @var array
		 */
		public $output = [];
		private $cache = [];
		private $devMode = FALSE;
		/**
		 * @var modTransliterate $translitClass
		 */
		private $translitClass = NULL;
		/**
		 * @var modCacheManager|null
		 */
		public $cacheManager = NULL;
		/**
		 * array translit
		 * @const array
		 */
		const translitRule = [
			'&' => 'and', '%' => '', '\'' => '', 'À' => 'A', 'À' => 'A', 'Á' => 'A', 'Á' => 'A', 'Â' => 'A', 'Â' => 'A', 'Ã' => 'A',
			'Ã' => 'A', 'Ä' => 'e', 'Ä' => 'A', 'Å' => 'A', 'Å' => 'A', 'Æ' => 'e', 'Æ' => 'E', 'Ā' => 'A', 'Ą' => 'A', 'Ă' => 'A',
			'Ç' => 'C', 'Ç' => 'C', 'Ć' => 'C', 'Č' => 'C', 'Ĉ' => 'C', 'Ċ' => 'C', 'Ď' => 'D', 'Đ' => 'D', 'È' => 'E', 'È' => 'E',
			'É' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ë' => 'E', 'Ē' => 'E', 'Ę' => 'E', 'Ě' => 'E', 'Ĕ' => 'E',
			'Ė' => 'E', 'Ĝ' => 'G', 'Ğ' => 'G', 'Ġ' => 'G', 'Ģ' => 'G', 'Ĥ' => 'H', 'Ħ' => 'H', 'Ì' => 'I', 'Ì' => 'I', 'Í' => 'I',
			'Í' => 'I', 'Î' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ï' => 'I', 'Ī' => 'I', 'Ĩ' => 'I', 'Ĭ' => 'I', 'Į' => 'I', 'İ' => 'I',
			'Ĳ' => 'J', 'Ĵ' => 'J', 'Ķ' => 'K', 'Ľ' => 'K', 'Ĺ' => 'K', 'Ļ' => 'K', 'Ŀ' => 'K', 'Ñ' => 'N', 'Ñ' => 'N', 'Ń' => 'N',
			'Ň' => 'N', 'Ņ' => 'N', 'Ŋ' => 'N', 'Ò' => 'O', 'Ò' => 'O', 'Ó' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Ô' => 'O', 'Õ' => 'O',
			'Õ' => 'O', 'Ö' => 'e', 'Ö' => 'e', 'Ø' => 'O', 'Ø' => 'O', 'Ō' => 'O', 'Ő' => 'O', 'Ŏ' => 'O', 'Œ' => 'E', 'Ŕ' => 'R',
			'Ř' => 'R', 'Ŗ' => 'R', 'Ś' => 'S', 'Ş' => 'S', 'Ŝ' => 'S', 'Ș' => 'S', 'Ť' => 'T', 'Ţ' => 'T', 'Ŧ' => 'T', 'Ț' => 'T',
			'Ù' => 'U', 'Ù' => 'U', 'Ú' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Û' => 'U', 'Ü' => 'e', 'Ū' => 'U', 'Ü' => 'e', 'Ů' => 'U',
			'Ű' => 'U', 'Ŭ' => 'U', 'Ũ' => 'U', 'Ų' => 'U', 'Ŵ' => 'W', 'Ŷ' => 'Y', 'Ÿ' => 'Y', 'Ź' => 'Z', 'Ż' => 'Z', 'à' => 'a',
			'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'e', 'ä' => 'e', 'å' => 'a', 'ā' => 'a', 'ą' => 'a', 'ă' => 'a', 'å' => 'a',
			'æ' => 'e', 'ç' => 'c', 'ć' => 'c', 'č' => 'c', 'ĉ' => 'c', 'ċ' => 'c', 'ď' => 'd', 'đ' => 'd', 'è' => 'e', 'é' => 'e',
			'ê' => 'e', 'ë' => 'e', 'ē' => 'e', 'ę' => 'e', 'ě' => 'e', 'ĕ' => 'e', 'ė' => 'e', 'ƒ' => 'f', 'ĝ' => 'g', 'ğ' => 'g',
			'ġ' => 'g', 'ģ' => 'g', 'ĥ' => 'h', 'ħ' => 'h', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ī' => 'i', 'ĩ' => 'i',
			'ĭ' => 'i', 'į' => 'i', 'ı' => 'i', 'ĳ' => 'j', 'ĵ' => 'j', 'ķ' => 'k', 'ĸ' => 'k', 'ł' => 'l', 'ľ' => 'l', 'ĺ' => 'l',
			'ļ' => 'l', 'ŀ' => 'l', 'ñ' => 'n', 'ń' => 'n', 'ň' => 'n', 'ņ' => 'n', 'ŉ' => 'n', 'ŋ' => 'n', 'ò' => 'o', 'ó' => 'o',
			'ô' => 'o', 'õ' => 'o', 'ö' => 'e', 'ö' => 'e', 'ø' => 'o', 'ō' => 'o', 'ő' => 'o', 'ŏ' => 'o', 'œ' => 'e', 'ŕ' => 'r',
			'ř' => 'r', 'ŗ' => 'r', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'e', 'ū' => 'u', 'ü' => 'e', 'ů' => 'u', 'ű' => 'u',
			'ŭ' => 'u', 'ũ' => 'u', 'ų' => 'u', 'ŵ' => 'w', 'ÿ' => 'y', 'ŷ' => 'y', 'ż' => 'z', 'ź' => 'z', 'ß' => 's', 'ſ' => 's',
			'Α' => 'A', 'Ά' => 'A', 'Β' => 'B', 'Γ' => 'G', 'Δ' => 'D', 'Ε' => 'E', 'Έ' => 'E', 'Ζ' => 'Z', 'Η' => 'I', 'Ή' => 'I',
			'Θ' => 'TH', 'Ι' => 'I', 'Ί' => 'I', 'Ϊ' => 'I', 'Κ' => 'K', 'Λ' => 'L', 'Μ' => 'M', 'Ν' => 'N', 'Ξ' => 'KS', 'Ο' => 'O',
			'Ό' => 'O', 'Π' => 'P', 'Ρ' => 'R', 'Σ' => 'S', 'Τ' => 'T', 'Υ' => 'Y', 'Ύ' => 'Y', 'Ϋ' => 'Y', 'Φ' => 'F', 'Χ' => 'X',
			'Ψ' => 'PS', 'Ω' => 'O', 'Ώ' => 'O', 'α' => 'a', 'ά' => 'a', 'β' => 'b', 'γ' => 'g', 'δ' => 'd', 'ε' => 'e', 'έ' => 'e',
			'ζ' => 'z', 'η' => 'i', 'ή' => 'i', 'θ' => 'th', 'ι' => 'i', 'ί' => 'i', 'ϊ' => 'i', 'ΐ' => 'i', 'κ' => 'k', 'λ' => 'l',
			'μ' => 'm', 'ν' => 'n', 'ξ' => 'ks', 'ο' => 'o', 'ό' => 'o', 'π' => 'p', 'ρ' => 'r', 'σ' => 's', 'τ' => 't', 'υ' => 'y',
			'ύ' => 'y', 'ϋ' => 'y', 'ΰ' => 'y', 'φ' => 'f', 'χ' => 'x', 'ψ' => 'ps', 'ω' => 'o', 'ώ' => 'o', 'А' => 'a', 'Б' => 'b',
			'В' => 'v', 'Г' => 'g', 'Д' => 'd', 'Е' => 'e', 'Ё' => 'yo', 'Ж' => 'zh', 'З' => 'z', 'И' => 'i', 'Й' => 'j', 'К' => 'k',
			'Л' => 'l', 'М' => 'm', 'Н' => 'n', 'О' => 'o', 'П' => 'p', 'Р' => 'r', 'С' => 's', 'Т' => 't', 'У' => 'u', 'Ф' => 'f',
			'Х' => 'x', 'Ц' => 'cz', 'Ч' => 'ch', 'Ш' => 'sh', 'Щ' => 'shh', 'Ъ' => '', 'Ы' => 'yi', 'Ь' => '', 'Э' => 'e', 'Ю' => 'yu',
			'Я' => 'ya', 'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'ж' => 'zh', 'з' => 'z',
			'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's',
			'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'x', 'ц' => 'cz', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'shh', 'ъ' => '', 'ы' => 'yi',
			'ь' => '', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
		];

		const FirstLetter = 1;
		const EveryWord = 2;
		const AfterDot = 3;

		/**
		 * @var array
		 */
		private $prepare = [];

		/**
		 * utilities constructor.
		 * @param modX $modx
		 */
		public function __construct(modX &$modx)
		{
			$this->modx = $modx;
			$this->cacheManager = $this->modx->getCacheManager();
			$this->prefix = (string)$this->modx->getOption('table_prefix', NULL, 'modx_', FALSE);
		}

		/**
		 * @param $name
		 * @return bool
		 */
		final public function __isset($name)
		{
			return isset($this->$name);
		}

		/**
		 * @param $name
		 * @param $value
		 */
		final public function __set($name, $value)
		{
			if (isset($this->$name) === FALSE) {
				$this->$name = $value;
			}
		}

		/**
		 * @param $name
		 * @return bool|Class
		 */
		final public function __get($name)
		{
			switch ($name) {
				case 'constant':
					if (!isset($this->constant_)) {
						$this->constant_ = $this->loadClass('modutilitiesConstant');
					}
					return $this->constant_;
				default :
					return FALSE;
			}
		}

		/**
		 * @return string
		 */
		final public function __toString()
		{
			return (string)$this->dump($this);
		}

		/**
		 * @param null $args
		 * @return false|string
		 */
		public function dump($args = NULL)
		{
			$arr = @func_get_args();
			$echo = $this->varsInfo(...$arr);
			if ($echo) {
				return $echo . '<br>' . PHP_EOL;
			} else {
				return FALSE;
			}
		}

		public function varsInfo($args = NULL)
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
				}
				return $echo;
			}
			return FALSE;
		}

		/**
		 * @param object $v
		 * @return array|false
		 */
		public static function objectInfo(&$v)
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
		public function screenShot($url, $path = FALSE, $razr = '1024x768', $razm = '600', $form = 'jpeg')
		{
			$toapi = "http://mini.s-shot.ru/" . $razr . "/" . $razm . "/" . $form . "/?" . $url;
			$this->output[__FUNCTION__]['api'] = $toapi;
			return $this->_download($toapi, $path);
		}

		/**
		 * makes the first letter capital (in Russian)
		 * @param string $string     input
		 * @param int    $mode       = 1
		 * @param bool   $otherLower = 1
		 * @param string $enc        = 'UTF-8'
		 * @return string
		 */
		public function mbUcfirst($string = '', $mode = modutilities::FirstLetter, $otherLower = TRUE, $enc = 'UTF-8')
		{
			return $this->mb_ucfirst($string, $mode, $otherLower, $enc);
		}

		/**
		 * makes the first letter capital (in Russian)
		 * @param string $string     input
		 * @param int    $mode       = 1
		 * @param bool   $otherLower = 1
		 * @param string $enc        = 'UTF-8'
		 * @return string
		 */
		public function mb_ucfirst($string = '', $mode = modutilities::FirstLetter, $otherLower = TRUE, $enc = 'UTF-8')
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
		 * @param array  $options
		 * @return string|bool
		 */
		public function translit($str = '', $isCpu = TRUE)
		{
			if ($str) {
				if (!$this->translitClass) {
					$translitClass = $this->modx->getOption('friendly_alias_translit_class', NULL, 'translit.modTransliterate');
					$translitClassPath = $this->modx->getOption('friendly_alias_translit_class_path', NULL, $this->modx->getOption('core_path', NULL, MODX_CORE_PATH) . 'components/');
					if (!$this->translitClass = $this->modx->getService('translit', $translitClass, $translitClassPath, [])) {
						return FALSE;
					}
				}
				$iconv = function_exists('iconv');
				$translit = $this->modx->getOption('friendly_alias_translit', NULL, $iconv ? 'iconv' : 'none');
				$str = $this->translitClass->translate($str, $translit);
				if ($isCpu) {
					$str = mb_strtolower($str);
					$str = preg_replace('/[^-0-9a-z]/', '-', $str);
					$str = preg_replace('/[\-]+/', '-', $str);
					$str = trim($str, '-');
				}
				return $str;
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
				$str = preg_replace('/[^-0-9a-z]/', '-', $str);
				$str = preg_replace('/[\-]+/', '-', $str);
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
			if (@array_search($do, ['log', 'info', 'debug', 'warn', 'error', 'table', 'object']) === FALSE) {
				$do = 'log';
			}
			if (is_array($data)) {
				$echo = "<script type='text/javascript'>console.{$do}(" . json_encode($data, 256) . ");</script>";
			} else {
				$data_ = str_replace('<br>', '', $this->dump($data));
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
		public static function rawText($a = '')
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
				if ($a == $b) {
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
			if (is_numeric($id)) {
			} elseif (is_string($id) and !is_numeric($id)) {
				/** @var modResource $res */
				$res = $this->modx->getObject('modResource', ['pagetitle' => $id]);
				$id = $res->get('id');
			} elseif (is_object($id) and $id instanceof modResource) {
				$id = $id->get('id');
			} else {
				return FALSE;
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
		public function or($arr)
		{
			$a = func_get_args();
			return $this->ifElse(...$a);
		}

		/**
		 * @param $arr
		 * @return false|mixed
		 */
		public static function ifElse($arr)
		{
			$arr = is_array($arr) ? $arr : func_get_args();
			if (is_array($arr)) {
				foreach ($arr as $a) {
					if (!empty($a) and $a) {
						return $a;
					}
				}
				return end($arr);
			}
			return FALSE;
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
			$_args = func_get_args();
			if (count($_args) == 1 and is_array($_args[0])) {
				extract($_args[0], EXTR_OVERWRITE);
			}
			$userGroup = [];
			if (!$id) {
				if ((int)$this->modx->user->get('id') > 0) {
					$user = $this->modx->user;
				} else {
					return FALSE;
				}
			} else {
				if (is_object($id) and $id instanceof modUser) {
					$user = $id;
				} else {
					/** @var modUser $user */
					$user = $this->modx->getObject('modUser', [
						'id:LIKE' => $id,
						'OR:username:LIKE' => $id,
					]);
				}
			}
			if ($user) {
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
					                 `{$this->prefix}member_groups` 	as `member` 
					      INNER JOIN `{$this->prefix}user_group_roles` 	as `role` 	on `role`.`id`  = `member`.`role` 
					      INNER JOIN `{$this->prefix}membergroup_names` as `group` 	on `group`.`id` = `member`.`user_group` 
						  WHERE `member`.`member` = :userId
					");

				if ($q->execute(['userId' => $user->get('id')]) and $q->rowCount() > 0) {
					return $q->fetchAll(PDO::FETCH_ASSOC);
				}
			}
			return FALSE;
		}

		/**
		 * simple plural
		 * @param int   $n
		 * @param array $forms 'арбуз', 'арбуза', 'арбузов'
		 * @return mixed
		 */
		public static function plural($n = 0, $forms = [])
		{
			$n = explode(',', (string)$n);
			$n[1] = str_replace('0', '', $n[1]);
			$n = abs((float)@implode('', $n));
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
			$_args = func_get_args();
			if (count($_args) == 1 and is_array($_args[0])) {
				extract($_args[0], EXTR_OVERWRITE);
			}
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
		 * header("Content-type: application/json; charset=utf-8");
		 */
		public static function headerJson()
		{
			@header("Content-type: application/json; charset=utf-8");
		}

		/**
		 * Empty
		 * @param $var
		 * @return bool
		 */
		public function isEmpty($var)
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
				$score += $this->isEmpty($v);
			}
			return !(bool)$score;
		}

		/**
		 * isAssoc
		 * @param array $arr
		 * @return bool
		 */
		public static function isAssoc(&$arr = [])
		{
			if (is_array($arr)) {
				$c = count($arr);
				if ($c > 10) {
					return !(array_key_exists(0, $arr) and array_key_exists(rand(0, $c - 1), $arr) and array_key_exists($c - 1, $arr));
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
		public function strTest()
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
		public function getUserPhoto($id = 0, $alt = FALSE, $width = 128, $height = 128, $r = 'g', $default = '404')
		{
			$alt = $alt ?: 'https://placehold.it/' . $width . 'x' . $height . '?text=avatar';
			if ($id) {
				if (is_object($id) and $id instanceof modUser) {
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
					if (!$this->ping($img)) {
						$img = $alt;
					}
				}
			}
			return $img ?: $alt;
		}

		/**
		 * test connect to remote resource (not return real ping)
		 * @param string $host
		 * @param bool   $useSocket
		 * @param int    $timeout
		 * @param int    $port
		 * @return bool
		 */
		public function ping($host = '', $useSocket = FALSE, $timeout = 2, $port = 80)
		{
			$_args = func_get_args();
			if (count($_args) == 1 and is_array($_args[0])) {
				extract($_args[0], EXTR_OVERWRITE);
			}
			if ($host) {
				$sock = FALSE;
				if ($useSocket) {
					$sock = @fsockopen($host, $port, $errno, $errStr, $timeout);
				}
				if (!$sock) {
					$this->output[__FUNCTION__]['error'] = [$errno, $errStr];
					if (!$useSocket or $errStr == 'Unable to find the socket transport "https" - did you forget to enable it when you configured PHP?') {
						$opts['http']['timeout'] = $timeout;
						$opts['https']['timeout'] = $timeout;
						if (version_compare(PHP_VERSION, '7.1.0', '>=')) {
							$context = stream_context_create($opts);
							$headers = @get_headers($host, 1, $context); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
						} else {
							stream_context_set_default($opts);
							$headers = @get_headers($host, 1); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
						}
						preg_match('@HTTP\/\d+.\d+\s([2-3]\d+)?\s@', $headers[0], $math);
						if (isset($math[1]) and $math[1]) {
							return TRUE;
						} else {
							return FALSE;
						}
					}
					return FALSE;
				} else {
					return TRUE;
				}
			}
			return FALSE;
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
		public function getGravatar($email, $size = 128, $r = 'g', $default = '404')
		{
			$_args = func_get_args();
			if (count($_args) == 1 and is_array($_args[0])) {
				extract($_args[0], EXTR_OVERWRITE);
			}
			$gravatarEmail = md5(strtolower(trim($email)));
			$url = 'https://www.gravatar.com/avatar/' . $gravatarEmail . "?s={$size}&r={$r}&d={$default}";
			return $this->ping($url) ? $url : FALSE;
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
			} elseif (is_string($table)) {
				$table_ = $this->modx->newObject($table);
				if ($table_) {
					$table = $table_->_table;
				} else {
					unset($table_);
				}
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
		 * @param string     $mode = res|id
		 * @return array|bool
		 */
		public function getAllTvValue($tv = 0, $mode = 'res')
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
			switch ($mode) {
				case 'res':
					$sql = "SELECT GROUP_CONCAT(`contentid`) as `contentid`,`value` FROM `{$this->prefix}site_tmplvar_contentvalues` WHERE `tmplvarid` = :id GROUP BY `value`";
					$statement = $this->modx->prepare($sql);

					if ($statement->execute(['id' => $id])) {
						$result = $statement->fetchAll(PDO::FETCH_KEY_PAIR);
						$response = [];
						foreach ($result as $k => $v) {

							$response[$v] = explode(',', $k);
						}
						return $response;
					}
					return FALSE;
				case 'id':
					$sql = "SELECT `id`,`value`,`contentid` FROM `{$this->prefix}site_tmplvar_contentvalues` WHERE `tmplvarid` = :id";
					$statement = $this->modx->prepare($sql);
					if ($statement->execute(['id' => $id])) {
						return $statement->fetchALL(PDO::FETCH_UNIQUE);
					}
					return FALSE;
				default:
					return FALSE;
			}
		}

		/**
		 * @param int    $id
		 * @param string $type id|name|caption|full
		 * @return array|false
		 */
		public function getAllTvResource($id = 0, $type = 'id')
		{
			if (is_int($id)) {
				if (!isset($this->cache[__FUNCTION__]['newQuery'])) {
					$this->cache[__FUNCTION__]['newQuery'] = $this->modx->newQuery('modResource');
					$this->cache[__FUNCTION__]['newQuery']->select('id', 'template');
				}
				$this->cache[__FUNCTION__]['newQuery']->where(['id' => $id]);
				$id = $this->modx->getObject('modResource', $this->cache[__FUNCTION__]['newQuery']);
			} elseif (is_object($id) and $id instanceof modResource) {

			} else {
				return FALSE;
			}
			$response = [];
			/** @var modResource $id */
			if ($id instanceof modResource) {
				$template = (int)$id->get('template');
				$resId = (int)$id->get('id');
				if ($template) {
					$q = $this->modx->prepare("SELECT `tp`.`tmplvarid` as `id`,`tv`.`name`,`tv`.`caption` FROM {$this->prefix}site_tmplvar_templates as tp LEFT JOIN {$this->prefix}site_tmplvars AS tv ON tv.id = tp.tmplvarid WHERE templateid = :template");
					$q->execute([
						"template" => $template,
					]);
					$getTv = $this->modx->prepare("SELECT `value` FROM {$this->prefix}site_tmplvar_contentvalues WHERE contentid = :contentid and tmplvarid = :tmplvarid");

					while ($tvId = $q->fetch(PDO::FETCH_ASSOC)) {
						$getTv->execute([
							'tmplvarid' => $tvId['id'],
							'contentid' => $resId,
						]);
						if ($getTv) {
							$row = $getTv->fetch(PDO::FETCH_ASSOC);
							switch ($type) {
								default :
									$response[$tvId['id']] = $row['value'];
									break;
								case 'name':
									$response[$tvId['name']] = $row['value'];
									break;
								case 'caption':
									$response[$tvId['caption']] = $row['value'];
									break;
								case 'full':
									$tvId['value'] = $row['value'];
									$response[] = $tvId;
									break;
							}
						}
					}
					return $response;
				}
				$this->output[__FUNCTION__]['error'][] = 'modResource::template not found';
				return FALSE;
			}
			$this->output[__FUNCTION__]['error'][] = 'modResource not found';
			return FALSE;
		}

		/**
		 * get csv class
		 * @return modutilitiesCsv
		 */
		final public function csv($Params = [])
		{
			return $this->loadClass('modutilitiesCsv', $Params);
		}

		/**
		 * get csv class
		 * @return modUtilitiesPython
		 */
		final public function py($Params = [])
		{
			return $this->loadClass('modUtilitiesPython', $Params);
		}

		/**
		 * @return bool|modutilitiesPostFiles
		 */
		final public function files()
		{
			return $this->loadClass('modutilitiesPostFiles');
		}

		/**
		 * @return modutilitiesMath|Class
		 */
		final public function math()
		{
			return $this->loadClass('modutilitiesMath');
		}

		/**
		 * **$Params[fast]**
		 *  - 0 very slow
		 *  - 1 normal
		 *  - 2 faster
		 *
		 * **$Params[speed]**
		 *  - it/s iterate by second
		 *  - s/it second for 1 iterate
		 *  - left remaining time in second
		 *
		 * **$Params[colored]**
		 *  - true
		 *  - false
		 *
		 * **$Params[smooth]**
		 *  - true
		 *  - false
		 * @return modutilitiesProgressBar|Class
		 */
		final public function progressBar($Params = [])
		{
			return $this->loadClass('modutilitiesProgressBar', $Params);
		}

		/**
		 * @param array $Params
		 * @return bool|modutilitiesRest
		 */
		final public function REST($Params = [])
		{
			return $this->loadClass('modutilitiesRest', $Params);
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
		public static function getIP()
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
		public function arrayToSqlIn($arr = [])
		{
			$dop = array_fill(0, count($arr), 256);
			foreach ($arr as $key => $value) {
				$arr[$key] = trim($value, "'");
			}
			return @implode(',', array_map('json_encode', $arr, $dop));
		}

		/**
		 * @param string $file
		 * @param string $outPath
		 * @param bool   $update
		 * @param int    $timeout
		 * @param bool   $useCurl
		 * @return bool
		 * @throws Exception
		 */
		public function download($file = '', $outPath = '', $update = TRUE, $timeout = 2, $useCurl = FALSE)
		{
			$_args = func_get_args();
			if (count($_args) == 1 and is_array($_args[0])) {
				extract($_args[0], EXTR_OVERWRITE);
			}
			try {
				$permissions = (int)($this->modx->config['new_file_permissions'] ?: 0777);
				$this->output[__FUNCTION__] = ['$file' => $file, '$outPath' => $outPath, '$timeout' => $timeout, '$update' => $update,];
				if (!file_exists(dirname($outPath)) or !is_dir(dirname($outPath))) {
					if (!mkdir($concurrentDirectory = dirname($outPath), $permissions, TRUE) && !is_dir($concurrentDirectory)) {
						throw new RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
					}
				}
				if ($useCurl) {
					if ($file) {
						$ch = curl_init($file);
						if ($outPath) {
							if (!is_dir(dirname($outPath))) {
								if (!mkdir($concurrentDirectory = dirname($outPath), $this->modx->config['new_file_permissions'], TRUE) && !is_dir($concurrentDirectory)) {
									throw new Exception(sprintf('Directory "%s" was not created', $concurrentDirectory));
									return FALSE;
								}
							}
							if (!$update and file_exists($outPath)) {
								return TRUE;
							}
							$fp = @fopen($outPath, 'w');
							curl_setopt($ch, CURLOPT_FILE, $fp);
						}
						curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
						curl_setopt($ch, CURLOPT_HEADER, FALSE);
						curl_setopt($ch, CURLOPT_SSLVERSION, 3);

						$tmp = curl_exec($ch);
						curl_close($ch);
						if ($outPath) {
							@fclose($fp);
						} elseif ($tmp) {
							return $tmp;
						} else {
							return $this->_download($file, $outPath, $timeout, $update);
						}
						if (file_exists($outPath) and filesize($outPath) > 0) {
							return TRUE;
						} else {
							$this->_download($file, $outPath, $timeout, $update);
						}
					}
				} else {
					return $this->_download($file, $outPath, $update, $timeout);
				}
			} catch (Exception $e) {
				$this->modx->log(modX::LOG_LEVEL_ERROR, $e->getMessage(), '', __METHOD__ ?: __FUNCTION__, __FILE__, __LINE__);
				return FALSE;
			}
			return FALSE;
		}

		/**
		 * @param string $file
		 * @param string $outPath
		 * @param int    $timeout
		 * @param bool   $update
		 * @return bool|string
		 */
		public  function _download($file = '', $outPath = '', $update = TRUE, $timeout = 2)
		{
			$this->output[__FUNCTION__] = ['$file' => $file, '$outPath' => $outPath, '$timeout' => $timeout, '$update' => $update,];
			if (!$update and file_exists($outPath) and filesize($outPath) > 0) {
				return TRUE;
			}
			$opts = [
				'http' => [
					'timeout' => $timeout,
				],
				'https' => [
					'timeout' => $timeout,
				],
			];
			if (version_compare(PHP_VERSION, '7.1.0', '>=')) {
				$ctx = stream_context_create($opts);
				if ($outPath) {
					@file_put_contents($outPath, @file_get_contents($file, 0, $ctx));
				} else {
					return @file_get_contents($file, 0, $ctx);
				}
			} else {
				stream_context_set_default($opts);
				if ($outPath) {
					@file_put_contents($outPath, @file_get_contents($file, 0));
				} else {
					return @file_get_contents($file, 0);
				}
			}

			if (file_exists($outPath) and filesize($outPath) > 0) {
				return TRUE;
			} else {
				return FALSE;
			}
		}

		/**
		 * @param string $file
		 * @return mixed
		 */
		public static function baseExt($file = '')
		{
			$_tmp = explode('.', basename($file));
			return end($_tmp);
		}

		/**
		 * @param string $file
		 * @return string
		 */
		public static function baseName($file = '')
		{
			$_tmp = explode('.', basename($file));
			array_pop($_tmp);
			return implode('', $_tmp);
		}

		/**
		 * @param string $str
		 * @param string $L
		 * @param string $R
		 * @return string
		 */
		public static function expandBrackets($str = '', $L = '(', $R = ')')
		{
			if ($L != '(' and $R == ')') {
				$R = $L;
			}
			if ($R == $L) {
				return trim($str, $L);
			}
			return rtrim(ltrim($str, $L), $R);
		}

		/**
		 * @param     $value
		 * @param int $resource
		 * @param int $tv
		 * @return bool
		 */
		public function updateTv($value, $resource = 0, $tv = 0)
		{
			if (!$resource or !$tv) {
				return FALSE;
			}
			try {
				if (!isset($this->prepare[__FUNCTION__]['upd'])) {
					$this->prepare[__FUNCTION__]['upd'] = $this->modx->prepare("INSERT INTO {$this->prefix}site_tmplvar_contentvalues (`contentid`,`tmplvarid`,`value`) VALUES (:res,:tv,:val) ON DUPLICATE KEY UPDATE `value` = :val");
				}
				$this->prepare[__FUNCTION__]['upd']->execute([
					'val' => $value,
					'res' => $resource,
					'tv' => $tv,
				]);
			} catch (Exception $e) {
				return $e->getMessage();
			}

			return TRUE;
		}

		/**
		 * @param array $options
		 * @return array|string
		 */
		public function randomColor($options = [])
		{
			$options = array_merge([
				'limits' => [],
				'salt' => FALSE,
				'format' => 'hsl',
				'type' => 'css',
			], $options);
			if ($options['salt']) {
				if (!is_numeric($options['salt'])) {
					$seed = (int)preg_replace("/[^0-9]/", '', md5($options['salt']));
				} else {
					$seed = (int)($options['salt'] * 100);
				}
				srand($seed);
			}

			if (isset($this->cache[__FUNCTION__][$options['format']][$options['type']][$options['salt']])) {
				return $this->cache[__FUNCTION__][$options['format']][$options['type']][$options['salt']];
			}
			if (isset($options['limits']['l'])) {
				if (isset($options['limits']['l']['max'])) {
					$options['limits']['l']['max'] = $options['limits']['l']['max'] >= 100 ? 100 : $options['limits']['l']['max'];
					$options['limits']['l']['max'] = $options['limits']['l']['max'] < 0 ? 0 : $options['limits']['l']['max'];
				}
				if (isset($options['limits']['l']['min'])) {
					$options['limits']['l']['min'] = $options['limits']['l']['min'] >= 100 ? 100 : $options['limits']['l']['min'];
					$options['limits']['l']['min'] = $options['limits']['l']['min'] < 0 ? 0 : $options['limits']['l']['min'];
				}
			}
			if (isset($options['limits']['s'])) {
				if (isset($options['limits']['l']['max'])) {
					$options['limits']['s']['max'] = $options['limits']['s']['max'] >= 100 ? 100 : $options['limits']['s']['max'];
					$options['limits']['s']['max'] = $options['limits']['s']['max'] < 0 ? 0 : $options['limits']['s']['max'];
				}
				if (isset($options['limits']['l']['min'])) {
					$options['limits']['s']['min'] = $options['limits']['s']['min'] >= 100 ? 100 : $options['limits']['s']['min'];
					$options['limits']['s']['min'] = $options['limits']['s']['min'] < 0 ? 0 : $options['limits']['s']['min'];
				}
			}
			if (isset($options['limits']['h'])) {
				if (isset($options['limits']['l']['max'])) {
					$options['limits']['h']['max'] = $options['limits']['h']['max'] >= 360 ? 360 : $options['limits']['h']['max'];
					$options['limits']['h']['max'] = $options['limits']['h']['max'] < 0 ? 0 : $options['limits']['h']['max'];
				}
				if (isset($options['limits']['l']['min'])) {
					$options['limits']['h']['min'] = $options['limits']['h']['min'] >= 360 ? 360 : $options['limits']['h']['min'];
					$options['limits']['h']['min'] = $options['limits']['h']['min'] < 0 ? 0 : $options['limits']['h']['min'];
				}
			}

			$h = rand($options['limits']['h']['min'] ?? 0, $options['limits']['h']['max'] ?? 360);
			$s = rand($options['limits']['s']['min'] ?? 0, $options['limits']['s']['max'] ?? 100);
			$l = rand($options['limits']['l']['min'] ?? 0, $options['limits']['l']['max'] ?? 100);
			switch (mb_strtolower($options['format'])) {
				case 'hex';
					$this->hsl2rgb($h, $s, $l);
					return $this->rgb2hex($h, $s, $l);
				case 'rgb';
					$this->hsl2rgb($h, $s, $l);
					if (mb_strtolower($options['type']) == 'css') {

						return "rgb($h, $s, $l)";
					}
					return [
						'r' => $h,
						'g' => $s,
						'b' => $l,
					];
				default:

					if (mb_strtolower($options['type']) == 'css') {
						$this->cache[__FUNCTION__][$options['format']][$options['type']][$options['salt']] = "hsl($h, $s%, $l%)";
						return "hsl($h, $s%, $l%)";
					}
					$this->cache[__FUNCTION__][$options['format']][$options['type']][$options['salt']] = [
						'h' => $h,
						's' => $s,
						'l' => $l,
					];
					return [
						'h' => $h,
						's' => $s,
						'l' => $l,
					];
			}

		}

		/**
		 * @param $rH
		 * @param $gS
		 * @param $bV
		 * @return array
		 */
		public function hsv2rgb(&$rH, &$gS, &$bV)
		{
			if ($rH < 0) $rH = 0;   // Hue:
			if ($rH > 360) $rH = 360; //   0-360
			if ($gS < 0) $gS = 0;   // Saturation:
			if ($gS > 100) $gS = 100; //   0-100
			if ($bV < 0) $bV = 0;   // Lightness:
			if ($bV > 100) $bV = 100; //   0-100

			$dS = $gS / 100.0; // Saturation: 0.0-1.0
			$dV = $bV / 100.0; // Lightness:  0.0-1.0
			$dC = $dV * $dS;   // Chroma:     0.0-1.0
			$dH = $rH / 60.0;  // H-Prime:    0.0-6.0
			$dT = $dH;       // Temp variable

			while ($dT >= 2.0) $dT -= 2.0; // php modulus does not work with float
			$dX = $dC * (1 - abs($dT - 1));     // as used in the Wikipedia link

			switch (floor($dH)) {
				case 0:
					$dR = $dC;
					$dG = $dX;
					$dB = 0.0;
					break;
				case 1:
					$dR = $dX;
					$dG = $dC;
					$dB = 0.0;
					break;
				case 2:
					$dR = 0.0;
					$dG = $dC;
					$dB = $dX;
					break;
				case 3:
					$dR = 0.0;
					$dG = $dX;
					$dB = $dC;
					break;
				case 4:
					$dR = $dX;
					$dG = 0.0;
					$dB = $dC;
					break;
				case 5:
					$dR = $dC;
					$dG = 0.0;
					$dB = $dX;
					break;
				default:
					$dR = 0.0;
					$dG = 0.0;
					$dB = 0.0;
					break;
			}

			$dM = $dV - $dC;
			$dR += $dM;
			$dG += $dM;
			$dB += $dM;
			$dR *= 255;
			$dG *= 255;
			$dB *= 255;
			$rH = round($dR);
			$gS = round($dG);
			$bV = round($dB);
			return [round($dR), round($dG), round($dB)];
		}

		/**
		 * @param $rH
		 * @param $gS
		 * @param $bL
		 * @return array
		 */
		public function hsl2rgb(&$rH, &$gS, &$bL)
		{

			$c = (1 - abs(2 * $bL - 1)) * $gS;
			$x = $c * (1 - abs(fmod(($rH / 60), 2) - 1));
			$m = $bL - ($c / 2);

			if ($rH < 60) {
				$r = $c;
				$g = $x;
				$b = 0;
			} elseif ($rH < 120) {
				$r = $x;
				$g = $c;
				$b = 0;
			} elseif ($rH < 180) {
				$r = 0;
				$g = $c;
				$b = $x;
			} elseif ($rH < 240) {
				$r = 0;
				$g = $x;
				$b = $c;
			} elseif ($rH < 300) {
				$r = $x;
				$g = 0;
				$b = $c;
			} else {
				$r = $c;
				$g = 0;
				$b = $x;
			}

			$rH = floor(($r + $m) * 255);
			$gS = floor(($g + $m) * 255);
			$bL = floor(($b + $m) * 255);

			return [$rH, $gS, $bL];
		}

		/**
		 * @param $R
		 * @param $G
		 * @param $B
		 * @return string
		 */
		public function rgb2hex(&$R, &$G, &$B)
		{

			$R = dechex($R);
			if (strlen($R) < 2)
				$R = '0' . $R;

			$G = dechex($G);
			if (strlen($G) < 2)
				$G = '0' . $G;

			$B = dechex($B);
			if (strlen($B) < 2)
				$B = '0' . $B;

			return '#' . $R . $G . $B;
		}

		/**
		 * @param     $string
		 * @param int $depth
		 * @return bool|mixed
		 */
		public function jsonValidate($string, $assoc = TRUE, $depth = 1024)
		{
			$this->output[__FUNCTION__]['input'] = $string;
			$this->output[__FUNCTION__]['error'] = NULL;
			$this->output[__FUNCTION__]['result'] = FALSE;
			if (!is_string($string)) {
				return $string;
			}
			try {
				$error = 0;
				// decode the JSON data
				if (version_compare(PHP_VERSION, '7.3.0', '>=')) {
					$result = json_decode($string, (bool)$assoc, $depth, JSON_THROW_ON_ERROR);
				} else {
					$result = json_decode($string, (bool)$assoc, $depth);
				}

				// switch and check possible JSON errors
				switch (json_last_error()) {
					case JSON_ERROR_NONE:
						$error = 0; // JSON is valid // No error has occurred
						break;
					case JSON_ERROR_DEPTH:
						$error = 'The maximum stack depth has been exceeded.';
						break;
					case JSON_ERROR_STATE_MISMATCH:
						$error = 'Invalid or malformed JSON.';
						break;
					case JSON_ERROR_CTRL_CHAR:
						$error = 'Control character error, possibly incorrectly encoded.';
						break;
					case JSON_ERROR_SYNTAX:
						$error = 'Syntax error, malformed JSON.';
						break;
					// PHP >= 5.3.3
					case JSON_ERROR_UTF8:
						$error = 'Malformed UTF-8 characters, possibly incorrectly encoded.';
						break;
					// PHP >= 5.5.0
					case JSON_ERROR_RECURSION:
						$error = 'One or more recursive references in the value to be encoded.';
						break;
					// PHP >= 5.5.0
					case JSON_ERROR_INF_OR_NAN:
						$error = 'One or more NAN or INF values in the value to be encoded.';
						break;
					case JSON_ERROR_UNSUPPORTED_TYPE:
						$error = 'A value of a type that cannot be encoded was given.';
						break;
					default:
						$error = 'Unknown JSON error occurred.';
						break;
				}
				if (!$error) {
					$this->output[__FUNCTION__]['result'] = $result;
					return $result;
				}
				$this->output[__FUNCTION__]['error'] = $error;
				$this->output[__FUNCTION__]['result'] = FALSE;
				return FALSE;
			} catch (JsonException $e) {
				$this->output[__FUNCTION__]['error'] = $e->getMessage();
				$this->output[__FUNCTION__]['result'] = FALSE;
				return FALSE;
			}

		}

		/**
		 * remove tv values for deleted resources
		 */
		public function clearDeletedTv()
		{
			$this->modx->query("DELETE FROM {$this->prefix}site_tmplvar_contentvalues WHERE id in (SELECT tv.id FROM {$this->prefix}site_tmplvar_contentvalues AS tv WHERE (SELECT COUNT(id) FROM {$this->prefix}site_content AS res WHERE res.id = tv.contentid) = 0)");
		}

		/**
		 * @param array $prop ["scheme" => "https",
		 *                    "host" => $_SERVER['SERVER_NAME'],
		 *                    "path" => 'русский text',
		 *                    "query" => ['v'=>'test'],]
		 * @return string
		 */
		public static function urlBuild($prop = [])
		{
			$query = isset($prop['query']) ? $prop['query'] : [];
			$prop = array_merge([
				"scheme" => "https",
				"host" => $_SERVER['SERVER_NAME'],
				"path" => '',
			], $prop);
			foreach ($prop as $k => $v) {
				if (is_string($v)) {
					$prop[$k] = trim(trim($v), "/");
				}
			}
			$url = $prop['scheme'] . '://' . $prop['host'] . '/';
			if ($prop['path']) {
				$prop['path'] = explode('/', $prop['path']);
				foreach ($prop['path'] as $k => $v) {
					$prop['path'][$k] = rawurlencode($v);
				}

				$prop['path'] = implode('/', $prop['path']);
				$url .= $prop['path'];
			}
			if (!empty($query)) {
				$url .= '?' . http_build_query($query);
			}
			return $url;
		}

		/**
		 * @param bool $v
		 */
		public function setDevMode($v = TRUE)
		{
			$this->devMode = $v;
		}

		/**
		 * @param       $script
		 * @param null  $path
		 * @param       $key
		 * @param array $options
		 */
		public function _addHead($script, $path = NULL, $key, $options = [])
		{
			$_args = func_get_args();
			if (count($_args) == 1 and is_array($_args[0])) {
				extract($_args[0], EXTR_OVERWRITE);
			}
			$options = array_merge([
				'plaintext' => FALSE,
				'Startup' => FALSE,
				'cache' => FALSE,
				'media' => NULL,
				'query' => [],
			], $options);

			if ($options['plaintext']) {
				$finalPath = $script;
			} else {
				$finalPath = '';
				if (!is_null($path)) {

					$finalPath = rtrim($path, '/') . '/' . ltrim($script, '/');
					if ($path === FALSE) {
						$this->modx->log(modX::LOG_LEVEL_ERROR, "can`t load script \"{$finalPath}\"", '', __METHOD__, __FILE__, __LINE__);
					}
				} else {
					$finalPath = $script;
				}
				$t = strpos($finalPath, '//');
				$remote = ($t !== FALSE and $t <= 10) ? TRUE : FALSE;
				$finalPath_raw = $finalPath;
				if (!empty($options['query'])) {
					$finalPath .= '?' . http_build_query($options['query']);
				}
				if ($options['cache'] and $remote) {
					try {
						if (!$this->cacheManager) {
							$this->cacheManager = $this->modx->getCacheManager();
						}
						$hash = md5($finalPath);
						$cachePaths = $this->cacheManager->get('includes', [xPDO::OPT_CACHE_KEY => 'modUtilities']);
						if (is_array($cachePaths) and array_key_exists($hash, $cachePaths)) {
							if (file_exists($cachePaths[$hash])) {
								throw new Exception($cachePaths[$hash], 1);
							}
						}
						if (!is_array($cachePaths)) {
							$cachePaths = [];
						}

						$ext = $this->baseExt($finalPath_raw);
						$tmp = "cache/" . $hash . '.' . $ext;
						if ($this->download($finalPath, MODX_ASSETS_PATH . $tmp)) {
							$assets = rtrim($this->modx->getOption('assets_url'), '/');
							$cachePaths[$hash] = $assets . '/' . ltrim($tmp, '/');
							$this->cacheManager->set('includes', $cachePaths, 0, [xPDO::OPT_CACHE_KEY => 'modUtilities']);
							throw new Exception($cachePaths[$hash], 1);
						} else {
							throw new Exception('', 0);
						}
					} catch (Exception $e) {
						if ($e->getCode() == 0) {
							$finalPath = $script;
						} elseif ($e->getCode() == 1) {
							$finalPath = $e->getMessage();
						}
					}
				}
				if ($this->devMode and !$remote and in_array($key, ['js', 'css'])) {
					$absolutPath = ltrim($finalPath, $this->assets);
					$v = md5_file(MODX_ASSETS_PATH . $absolutPath);
					$v = $v ?: time();
					$finalPath .= "?v=" . $v;
				}
			}
			//------------
			if ($key == 'css') {
				$this->modx->regClientCSS($finalPath, $options['media']);
			} elseif ($key == 'js') {
				if ($options['Startup']) {
					if ($options['plaintext']) {
						$this->modx->regClientStartupScript($finalPath, TRUE);
					} else {
						$out = '<script type="text/javascript" class="modUtilities" src="' . $finalPath . '"></script>';
						$this->modx->regClientStartupScript($out, TRUE);
					}
				} else {
					if ($options['plaintext']) {
						$this->modx->regClientScript($finalPath, TRUE);
					} else {
						$out = '<script type="text/javascript" class="modUtilities" src="' . $finalPath . '"></script>';
						$this->modx->regClientScript($finalPath, TRUE);
					}
				}
			}

		}

		/**
		 * @param       $script
		 * @param null  $path
		 * @param false $cache
		 * @param array $query
		 */
		public function addJs($script, $path = NULL, $cache = FALSE, $query = [])
		{
			$this->_addHead($script, $path, 'js', [
				'cache' => $cache,
				'query' => $query,
			]);
		}

		/**
		 * @param string $html
		 */
		public function addJsText($html = '')
		{
			$this->_addHead($html, NULL, 'js', [
				'plaintext' => TRUE,
			]);
		}

		public function addHtml($html = '')
		{
			$this->_addHead($html, NULL, 'js', [
				'plaintext' => TRUE,
			]);
		}

		/**
		 * @param string $script
		 * @param null   $path
		 * @param false  $cache
		 * @param array  $query
		 */
		public function addStartupJs($script = '', $path = NULL, $cache = FALSE, $query = [])
		{
			$_args = func_get_args();
			if (count($_args) == 1 and is_array($_args[0])) {
				extract($_args[0], EXTR_OVERWRITE);
			}
			$this->_addHead($script, $path, 'js', [
				'cache' => $cache,
				'Startup' => TRUE,
				'query' => $query,
			]);
		}

		/**
		 * @param string $html
		 */
		public function addStartupJsText($html = '')
		{
			$this->_addHead($html, NULL, 'js', [
				'plaintext' => TRUE,
				'Startup' => TRUE,
			]);
		}

		/**
		 * @param string $html
		 */
		public function addStartupHtml($html = '')
		{
			$this->_addHead($html, NULL, 'js', [
				'plaintext' => TRUE,
				'Startup' => TRUE,
			]);
		}

		/**
		 * @param string $script
		 * @param null   $path
		 * @param null   $media
		 * @param false  $cache
		 * @param array  $query
		 */
		public function addCss($script = '', $path = NULL, $media = NULL, $cache = FALSE, $query = [])
		{
			$_args = func_get_args();
			if (count($_args) == 1 and is_array($_args[0])) {
				extract($_args[0], EXTR_OVERWRITE);
			}
			$this->_addHead($script, $path, 'css', [
				'cache' => $cache,
				'media' => $media,
				'query' => $query,
			]);
		}

		/**
		 * @param       $folder
		 * @param false $recursive
		 * @param bool  $skipDirs
		 * @param array $replace
		 * @return array
		 */
		public function getFilesFromFolder($folder, $recursive = FALSE, $skipDirs = TRUE, $replace = [])
		{
			$_args = func_get_args();
			if (count($_args) == 1 and is_array($_args[0])) {
				extract($_args[0], EXTR_OVERWRITE);
			}
			$folder = rtrim($folder, '/') . '/';
			$response = [];
			if (!$recursive) {
				if (is_dir($folder)) {
					$scan = scandir($folder);
					foreach ($scan as $c) {
						$c = ltrim($c, '/');
						if (($skipDirs and is_dir($folder . $c)) or array_search($c, ['..', '.']) !== FALSE) {
							continue;
						}
						$response[] = $folder . $c;
					}
				}
			} else {
				$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($folder, RecursiveDirectoryIterator::SKIP_DOTS));
				/** @var FilesystemIterator $info */
				foreach ($iterator as $info) {
					if ($skipDirs and $info->isDir()) {
						continue;
					}
					$response[] = $info->getPathname();
				}
			}
			asort($response);
			if (!empty($replace)) {
				foreach ($response as $k => $v) {
					$response[$k] = strtr($v, $replace);
				}
			}
			return $response;
		}

		/**
		 * @param int $length
		 * @return string
		 * @throws Exception
		 */
		public static function id($length = 5)
		{
			if (version_compare(PHP_VERSION, '7.0.0', '>=')) {
				return bin2hex(random_bytes($length));
			} else {
				$chars = 'abdefhiknrstyzABDEFGHKNQRSTYZ23456789';
				$numChars = strlen($chars);
				$string = '';
				for ($i = 0; $i < $length; $i++) {
					$string .= substr($chars, rand(1, $numChars) - 1, 1);
				}
				return $string;
			}
		}

	}
