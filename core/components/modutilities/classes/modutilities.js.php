<?php
$res = [];
$user = [];
/** @var modX $modx */
/** @var modResource $resource */
if (!class_exists('modUtilities')) {
	return FALSE;
}
if ($modx->config['use_modUtilFrontJs_resource'] == TRUE) {
	$r = $modx->newQuery('modResource');
	$r->where($modx->resourceIdentifier);
	$r->limit(1);
	$r->select('alias,pagetitle,properties,type,template,show_in_tree,published,searchable,pub_date,parent,menuindex,longtitle,link_attributes,isfolder,introtext,id,hidemenu,hide_children_in_tree,description,createdon,cacheable,class_key,contentType');
	if ($r->prepare() and $r->stmt->execute() and $res = $r->stmt->fetch(PDO::FETCH_ASSOC)) {
		$res['properties'] = json_decode($res['properties']);
	}
	$res['id'] = $modx->resourceIdentifier;
}
if ($modx->config['use_modUtilFrontJs_user'] == TRUE) {
	if ($modx->user instanceof modUser) {
		/** @var modUser $user */
		$user = $modx->user->_fields;
		if ($user['id']) {
			$user['member'] = $modx->util->member($user)[0];
			$p = $modx->newQuery('modUserProfile');
			$p->select('address,extended,email,country,city,photo,state,phone,gender,fullname,fax,mobilephone,website,zip');
			$p->limit(1);
			$p->where(['internalKey' => $user['id']]);
			if ($p->prepare() and $p->stmt->execute() and $profile = $p->stmt->fetch(PDO::FETCH_ASSOC)) {
				$user['profile'] = $profile;
				$user['profile']['extended'] = json_decode($user['profile']['extended']);
			}
			unset($user['salt']);
			unset($user['password']);
			unset($user['remote_data']);
			unset($user['remote_key']);
			unset($user['session_stale']);
			unset($user['sudo']);
			unset($user['hash_class']);
		} else {
			$user = [];
		}
	}
}
ob_start();

//https://regex101.com/r/vrH6XK/1/ cookie
//https://regex101.com/r/vrH6XK/2 trim
?>
class miniModX {
	constructor() {
		this.resource = new miniResource(this)
		this.util = new modUtilities(this)
		this.user = new miniUser(this)
		window.addEventListener('resize', function() {
			modx.util.Device()
		}, true)
		window.addEventListener('mousemove', function(event) {
			modx.util.mouse.mouseX = event.clientX
			modx.util.mouse.mouseY = event.clientY
			modx.util.mouse.pageX = event.pageX
			modx.util.mouse.pageY = event.pageY
		}, true)
	}


	sendRedirect(uri = false, mode = 'native') {
		if(!uri) {
			uri = this.resource.uri
		} else {
			uri = uri
		}
		switch( mode ) {
			case 'noHistory':
				window.location.replace(uri)
				return true
			case 'withoutCache':
				window.location.reload(false)
				return true
			case 'location':
				location = uri
				return true
			default:
			case 'native':
				var a = document.createElement('a')
				a.href = uri
				a.click()
				return true
		}

	}
}
class miniResource {
	constructor(modx) {
		this.modx = modx
		Object.assign(this, <?=json_encode($res, 256)?>)
	}
}
class miniUser {
	constructor(modx) {
		this.modx = modx
		Object.assign(this, <?=json_encode($user, 256)?>)
		this.settings = {}
		this.getSettings()
	}


	setSetting(key, value) {
		this.getSettings()
		this.settings[key] = value
		return this.modx.util.setCookie('modUtil.settings', this.settings)
	}


	getSetting(key) {
		if(this.getSettings()) {
			return this.settings[key]
		}
		return false
	}


	getSettings() {
		var settings = this.modx.util.getCookie('modUtil.settings', false, true)
		if(settings instanceof Object || settings instanceof Array) {
			this.settings = settings
			return settings
		}
		return false
	}
}
class ConverterUnits {
	static converterRule = <?=json_encode($modx->util->converterRule, 256)?>


	convert(n, type, from, to) {
		if(typeof n == 'undefined') n = 0
		if(typeof type == 'undefined') type = 'byte'
		if(typeof from == 'undefined') from = 'SI'
		if(typeof to == 'undefined') to = 'best'
		try {
			//validate input start
			var out = false
			var size = {}
			var i = 1
			n = parseFloat(n)
			if(!n) {
				throw new modx.util.Exception('invalid number', 0)
			}
			if(typeof ConverterUnits.converterRule[type] != 'undefined') {
				var converterRule = ConverterUnits.converterRule[type]
				var SI = converterRule['SI'][1]
			} else {
				throw new modx.util.Exception('invalid type', 0)
			}
			if(to != 'best' && to != 'SI') {
				if(!modx.util.in_array(to, modx.util.array_keys(converterRule[0])) && !modx.util.in_array(to, modx.util.array_keys(converterRule[1])) && to != SI) {
					to = 'best'
				}
			}
			//validate input end
			if(to == from && to != 'SI') {
				throw new modx.util.Exception('easy )', 1)
			}
			n = ConverterUnits.ToSi(n, type, from)
			if(!n) {
				throw new modx.util.Exception('invalid "from" unit', 2)
			}
			if(to == 'SI' || to == SI) {
				throw new modx.util.Exception('easy )', 2)
			}
			if(to != 'best') {
				if(modx.util.in_array(to, modx.util.array_keys(converterRule[0]))) {
					var g
					g = 0
				} else if(modx.util.in_array(to, modx.util.array_keys(converterRule[1]))) {
					g = 1
				} else {
					throw new modx.util.Exception('invalid "to" unit', 2)
				}
			} else {
				var g
				if(n >= converterRule['SI'][0]) {
					g = 1
				} else {
					g = 0
				}
			}
			var key
			__loop1:
				for(key in converterRule[g]) {
					var rule
					rule = converterRule[g][key]
					if(n >= rule[0]) {
						n /= rule[0]
						size = {
							0: n.toFixed(i),
							1: key
						}
					} else {
						if(to == 'best') {
							break
						}
					}
					if(to != 'best' && to == key) {
						break
					}
					i++
				}
			if(!out && size instanceof Object) {
				out = size
			} else {
				out = {
					0: n,
					1: SI
				}
			}
		} catch(__e__) {
			var e
			if(__e__ instanceof modx.util.Exception) {
				e = __e__
				console.log(e.getMessage())
				__loop1:
					switch( e.getCode() ) {
						case 1:
							return {
								0: n.toFixed(i),
								1: from
							}
						case 2:
							return {
								0: n.toFixed(i),
								1: SI
							}
						default:
							return e.getMessage()
					}
			} else {
				throw __e__
			}
		}
		return out
	}


	static ToSi(n, type, from) {
		if(typeof type == 'undefined') type = 'byte'
		if(typeof from == 'undefined') from = 'SI'
		if(typeof ConverterUnits.converterRule[type] != 'undefined') {
			var converterRule
			converterRule = ConverterUnits.converterRule[type]
			var SI
			SI = converterRule['SI'][1]
		} else {
			return false
		}
		if(from == 'SI' || from == SI) {
			return n
		}
		var g

		if(modx.util.in_array(from, modx.util.array_keys(converterRule[0]))) {
			g = 0
		} else if(modx.util.in_array(from, modx.util.array_keys(converterRule[1]))) {
			g = 1
		} else {
			return false
		}
		__loop1:
			while(from != SI && typeof converterRule[g][from] != 'undefined') {
				var f_
				f_ = converterRule[g][from]
				n *= f_[0]
				from = f_[1]
			}
		return n
	}

}
class modUtilities {
	constructor(modx) {
		this.modx = modx
		this.mouse = {}
		this.mouse.mouseX = 0
		this.mouse.mouseY = 0
		this.mouse.pageX = 0
		this.mouse.pageY = 0
		this.device = ''
		this.constant = {}
		this.constant.kb = 1024
		this.constant.min = 60
		this.constant.mb = this.constant.kb * 1024
		this.constant.gb = this.constant.mb * 1024
		this.constant.tb = this.constant.gb * 1024
		this.constant.hour = this.constant.min * 60
		this.constant.day = this.constant.hour * 24
		this.constant.week = this.constant.day * 7
		this.convert_ = new ConverterUnits
		this.convert = this.convert_.convert
		this.translitRule = <?=json_encode(modutilities::translitRule)?>;
		//class constant
		this.FirstLetter = 1
		this.EveryWord = 2
		this.AfterDot = 3
		//
		this.Device()
		this.included = {}
	}


	in_array($k, $a) {
		for(const $aKey in $a) {
			if($k == $a[$aKey]) {return true}
		}
		return false
	}


	array_keys($a) {
		var arr = []
		for(const $aKey in $a) {
			arr.push($aKey)
		}
		return arr
	}


	Exception(message, code) {
		this.message = message
		this.code = code
		this.getCode = () => {
			return this.code
		}
		this.getMessage = () => {
			return this.message
		}
	}


	static get FirstLetter() {return 1}


	static get EveryWord() {return 2}


	static get AfterDot() {return 3}


	/**
	 * @param {string} string
	 * @param {number} mode
	 * @param {boolean} otherLower
	 */
	mb_ucfirst(string = '', mode = modUtilities.FirstLetter, otherLower = true) {
		if(string && string.constructor.name == 'String') {
			switch( mode ) {
				case 3:
					var words = string.split(new RegExp('[\.\?\!]'))
					for(var word of words) {
						word = word.trim()
						string = string.replace(word, this.mb_ucfirst(word))
					}
					return string
				case 2:
					var words = string.split(new RegExp('[\s]'))
					for(var word of words) {
						word = word.trim()
						string = string.replace(word, this.mb_ucfirst(word))
					}
					return string
				case 1:
				default:
					if(otherLower) {
						string = string.toLowerCase()
					}
					return string[0].toUpperCase() + string.slice(1)
					break
			}
		}
		return false
	}


	/**
	 * @param {string} string
	 * @param {number} mode
	 * @param {boolean} otherLower
	 */
	mbUcfirst(string = '', mode = modUtilities.FirstLetter, otherLower = true) {
		return this.mb_ucfirst(string, mode, otherLower)
	}


	getMouse() {
		return {x: this.mouseX, y: this.mouseY}
	}


	Device() {
		var d = 'mobile'
		if(window.innerWidth > 560) {
			d = 'tabled'
		}
		if(window.innerWidth > 1200) {
			d = 'pc'
		}
		this.device = d
		return d
	}


	/**
	 * @param {*} str
	 * @param {boolean|string} L
	 * @param {string} R
	 * @param {string|((substring: string, ...args: any[]) => string)} replace
	 */
	trim(str = '', L = '\s', R = false, replace = '') {
		if(!R) {
			R = L
		}
		var reg1 = new RegExp('(^' + L + '+)')
		var reg2 = new RegExp('(' + R + '+$)')
		return str.replace(reg1, replace).replace(reg2, replace)
	}


	setCookie(name, value, options = {path: '/'}) {
		/*
		Sets a cookie with specified name (str), value (str) & options (dict)
		options keys:
		  - path (str) - URL, for which this cookie is available (must be absolute!)
		  - domain (str) - domain, for which this cookie is available
		  - expires (Date object) - expiration date&time of cookie
		  - max-age (int) - cookie lifetime in seconds (alternative for expires option)
		  - secure (bool) - if true, cookie will be available only for HTTPS.
							IT CAN'T BE FALSE
		  - samesite (str) - XSRF protection setting.
							 Can be strict or lax
							 Read https://web.dev/samesite-cookies-explained/ for details
		  - httpOnly (bool) - if true, cookie won't be available for using in JavaScript
							  IT CAN'T BE FALSE
		*/
		if(!name) {
			return false
		}
		if(value instanceof Object || value instanceof Array) {
			try {
				value = JSON.stringify(value)
			} catch(e) {
				return e
			}
		}
		options = options || {}

		if(options.expires instanceof Date) {
			options.expires = options.expires.toUTCString()
		}
		let updatedCookie = encodeURIComponent(name) + '=' + encodeURIComponent(value)
		for(let optionKey in options) {
			updatedCookie += '; ' + optionKey
			let optionValue = options[optionKey]
			if(optionValue !== true) {
				updatedCookie += '=' + optionValue
			}
		}
		document.cookie = updatedCookie
		return true
	}


	deleteCookie(name) {
		/*
		Deletes a cookie with specified name.
		Returns true when cookie was successfully deleted, otherwise false
		*/
		return this.setCookie(name, null, {
			expires: new Date(0),
			path: '/'
		})
	}


	include(source, name = false, parent_selector = false, async = true) {
		if(typeof this.included[source] == 'undefined') {
			this.included[source] = {
				source,
				name
			}
			var script = document.createElement('script')
			if(!parent_selector) {
				var prior = document.querySelector('head')
			} else {
				var prior = document.querySelector(parent_selector)
			}
			script.async = async

			script.onload = script.onreadystatechange = (e, isAbort) => {
				if(isAbort || !script.readyState || /loaded|complete/.test(script.readyState)) {
					isAbort = true
				} else {
					isAbort = false
				}
				script.onload = script.onreadystatechange = null
				script = undefined
				const event = new Event('modx.util.included')
				event.sucses = isAbort
				event.source = source
				document.dispatchEvent(event)
				this.included[source].sucses = isAbort
				this.included[source].e = e
			}

			script.src = source
			prior.append(script)
			return true
		}
		return false
	}


	getCookie(name = false, id = false, json = false) {
		var cookies = document.cookie + ';'
		cookies = cookies.match(/([^\s]+?;)/g) ?? []
		var cookie = new Object()
		for(let _cookie of cookies) {
			var cookie_ = _cookie.split('=')

			var test = cookie_[0].match(/(\[(.+)?\])/)
			if(!test) {
				let cookie_name = decodeURIComponent(cookie_[0])
				cookie[cookie_name] = this.trim(decodeURIComponent(cookie_[1]), '\s', ';')
			} else {
				let cookie_name = decodeURIComponent(cookie_[0]).replace(decodeURIComponent(test[0]), '')
				let cookie_id = decodeURIComponent(test[2])
				if(typeof cookie[cookie_name] == 'undefined') {
					cookie[cookie_name] = {}
				}
				cookie[cookie_name][cookie_id] = this.trim(decodeURIComponent(cookie_[1]), '\s', ';')
			}

		}
		this.cookie = cookie
		var out = this.cookie
		if(name) {
			if(typeof cookie[name] != 'undefined') {
				out = cookie[name]
			} else {
				out = ''
			}
		}
		if(id && typeof out[id] != 'undefined') {
			out = out[id]
		}
		if(out && json) {
			try {
				return JSON.parse(out)
			} catch(e) {
				return false
			}
		}
		return out.substring(0, out.length - 1)
	}


	getLocalStorage(name) {
		name = name.toString()
		if(name) {
			try {
				return JSON.parse(localStorage[name])
			} catch(e) {
				if(typeof localStorage[name] != 'undefined') {
					return localStorage[name]
				} else {
					return false
				}
			}
		}
	}


	setLocalStorage(name, value = {}) {
		name = name.toString()
		var store = this.getLocalStorage(name)
		try {
			if(value instanceof Object || value instanceof Array) {
				if(store instanceof Object || store instanceof Array) {
					value = Object.assign(store, value)
				}
				value = JSON.stringify(value)
			}

			localStorage[name] = value
			return true
		} catch(e) {
			return e
		}
		return false
	}


	uniqueArray(a) {
		try {
			return [...new Set(a)]
		} catch(e) {
			var j = {}
			a.forEach(function(v) {
				j[v + '::' + typeof v] = v
			})

			return Object.keys(j).map(function(v) {
				return j[v]
			})
		}
	}

}
var modx = new miniModX()
<?php
return ob_get_clean();
