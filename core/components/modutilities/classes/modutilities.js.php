<?php
/** @var modX $modx */
/** @var modResource $resource */
if (!class_exists('modutilities')) {
	return FALSE;
}
$resource = $modx->getObject('modResource', $modx->resourceIdentifier);
$res = $resource->toArray('', TRUE);
unset($res['content']);
ob_start();

//https://regex101.com/r/vrH6XK/1/ cookie
//https://regex101.com/r/vrH6XK/2 trim
?>
class modX {

	constructor() {
		this.resource = <?=json_encode($res, 256)?>;
		this.util = new modutilities(this)
	}
}
class modutilities {
	constructor(modx) {
		this.modx = modx
		this.mouseX = 0
		this.mouseY = 0
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
		this.translitRule = <?=json_encode(modutilities::translitRule)?>;
		//class constant
		this.FirstLetter = 1
		this.EveryWord = 2
		this.AfterDot = 3
		//
		this.Device()
	}


	static get FirstLetter() {return 1}


	static get EveryWord() {return 2}


	static get AfterDot() {return 3}


	/**
	 * @param {string} string
	 * @param {number} mode
	 * @param {boolean} otherLower
	 */
	mb_ucfirst(string = '', mode = modutilities.FirstLetter, otherLower = true) {
		if(string && string.constructor.name == 'String') {
			switch(mode) {
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
	mbUcfirst(string = '', mode = modutilities.FirstLetter, otherLower = true) {
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
	trim(str = '', L = 's', R = false, replace = '') {
		if(!R) {
			R = L
		}
		var reg1 = new RegExp('(^\\\\'+L+'+)')
		var reg2 = new RegExp('(\\\\'+R+'+$)')
		return str.replace(reg1, replace).replace(reg2, replace)
	}


	getCookie(name = false, id = false) {
		var cookies = document.cookie.match(/([^\s]+?;)/g)
		var cookie = new Object()
		for(let _cookie of cookies) {
			var cookie_ = _cookie.split('=')

			var test = cookie_[0].match(/(\[(.+)?\])/)
			if(!test) {
				let cookie_name = cookie_[0]
				cookie[cookie_name] =  this.trim(cookie_[1],'s',';')
			} else {
				let cookie_name = cookie_[0].replace(test[0], '')
				let cookie_id = test[2]
				if(typeof cookie[cookie_name] == 'undefined') {
					cookie[cookie_name] = {}
				}
				cookie[cookie_name][cookie_id] = this.trim(cookie_[1],'s',';')
			}

		}
		this.cookie = cookie;
		var out = cookie
		if(name && typeof cookie[name] != 'undefined'){
			out = cookie[name]
		}
		if(id && typeof out[id] != 'undefined'){
			out = out[id]
		}
		return out
	}
}
modx = new modX()
window.addEventListener('resize', function(event) {
	modx.util.Device()
}, true)
window.addEventListener('mousemove', function(event) {
	modx.util.mouseX = event.clientX
	modx.util.mouseY = event.clientY
}, true)
<?php
return ob_get_clean();
