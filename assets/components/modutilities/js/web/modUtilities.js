var __spreadArrays = (this && this.__spreadArrays) || function () {
    for (var s = 0, i = 0, il = arguments.length; i < il; i++) s += arguments[i].length;
    for (var r = Array(s), k = 0, i = 0; i < il; i++)
        for (var a = arguments[i], j = 0, jl = a.length; j < jl; j++, k++)
            r[k] = a[j];
    return r;
};
var _a;
// @ts-ignore
modUtilities.miniModX = /** @class */ (function () {
    function class_1() {
        // @ts-ignore
        this.resource = new modUtilities.miniResource(this);
        // @ts-ignore
        this.util = new modUtilities.util(this);
        this.util.Exception = function Exception(message, code) {
            var _this = this;
            this.message = message;
            this.code = code;
            this.getCode = function () {
                return _this.code;
            };
            this.getMessage = function () {
                return _this.message;
            };
        };
        // @ts-ignore
        this.user = new modUtilities.miniUser(this);
        // @ts-ignore
        this.lang = modUtilities.lang;
        window.addEventListener('resize', function () {
            modx.util.Device();
        }, true);
        window.addEventListener('mousemove', function (event) {
            modx.util.mouse.mouseX = event.clientX;
            modx.util.mouse.mouseY = event.clientY;
            modx.util.mouse.pageX = event.pageX;
            modx.util.mouse.pageY = event.pageY;
        }, true);
    }
    class_1.prototype.lexicon = function (s, v) {
        if (s === void 0) { s = ''; }
        if (v === void 0) { v = {}; }
        if (v != null && typeof (v) == 'object') {
            var t = '' + this.lang[s];
            for (var k in v) {
                // @ts-ignore
                t = t.replace('[[+' + k + ']]', v[k]);
            }
            if (typeof t == 'undefined' || t == 'undefined') {
                return s;
            }
            return t;
        }
        return s;
    };
    class_1.prototype.lex = function (key, params) {
        if (key === void 0) { key = ''; }
        if (params === void 0) { params = []; }
        // @ts-ignore
        return this.lexicon.apply(this, arguments);
    };
    class_1.prototype._ = function (key, params) {
        if (key === void 0) { key = ''; }
        if (params === void 0) { params = []; }
        // @ts-ignore
        return this.lexicon.apply(this, arguments);
    };
    class_1.prototype.sendRedirect = function (uri, mode) {
        if (uri === void 0) { uri = ''; }
        if (mode === void 0) { mode = 'native'; }
        if (!uri) {
            uri = document.location.href;
        }
        else {
            uri = uri;
        }
        switch (mode) {
            case 'noHistory':
                window.location.replace(uri);
                return true;
            case 'withoutCache':
                window.location.reload(false);
                return true;
            default:
            case 'native':
                var a = document.createElement('a');
                a.href = uri;
                a.click();
                return true;
        }
    };
    return class_1;
}());
// @ts-ignore
modUtilities.miniResource = /** @class */ (function () {
    function class_2(modx) {
        this.modx = modx;
        // @ts-ignore
        Object.assign(this, modUtilities.resource);
    }
    return class_2;
}());
// @ts-ignore
modUtilities.miniUser = /** @class */ (function () {
    function class_3(modx) {
        this.modx = modx;
        // @ts-ignore
        Object.assign(this, modUtilities.user);
        this.settings = {};
        this.getSettings();
    }
    /**
     * @param {string} key
     */
    class_3.prototype.detSetting = function (key) {
        this.getSettings();
        // @ts-ignore
        this.settings[key] = null;
        return this.modx.util.setLocalStorage('modUtil.settings', this.settings);
    };
    /**
     * @param {string} key
     * @param {*} value
     */
    class_3.prototype.setSetting = function (key, value) {
        this.getSettings();
        // @ts-ignore
        this.settings[key] = value;
        return this.modx.util.setLocalStorage('modUtil.settings', this.settings);
    };
    /**
     * @param {string} key
     */
    class_3.prototype.getSetting = function (key) {
        if (this.getSettings()) {
            // @ts-ignore
            return this.settings[key];
        }
        return false;
    };
    class_3.prototype.getSettings = function () {
        var settings = this.modx.util.getLocalStorage('modUtil.settings', false, true);
        if (settings instanceof Object || settings instanceof Array) {
            this.settings = settings;
            return settings;
        }
        return false;
    };
    return class_3;
}());
// @ts-ignore
modUtilities.ConverterUnits = (_a = /** @class */ (function () {
        function class_4() {
        }
        class_4.prototype.convert = function (n, type, from, to) {
            if (typeof n == 'undefined')
                n = 0;
            if (typeof type == 'undefined')
                type = 'byte';
            if (typeof from == 'undefined')
                from = 'SI';
            if (typeof to == 'undefined')
                to = 'best';
            try {
                //validate input start
                var out = false;
                var size = [];
                var i = 1;
                // @ts-ignore
                n = parseFloat(n);
                if (isNaN(n)) {
                    throw new modx.util.Exception('invalid number', 0);
                }
                // @ts-ignore
                if (typeof modUtilities.ConverterUnits.converterRule[type] != 'undefined') {
                    // @ts-ignore
                    var converterRule = modUtilities.ConverterUnits.converterRule[type];
                    var SI = converterRule['SI'][1];
                }
                else {
                    throw new modx.util.Exception('invalid type', 0);
                }
                if (to != 'best' && to != 'SI') {
                    if (!modx.util.in_array(to, modx.util.array_keys(converterRule[0])) && !modx.util.in_array(to, modx.util.array_keys(converterRule[1])) && to != SI) {
                        to = 'best';
                    }
                }
                //validate input end
                if (to == from && to != 'SI') {
                    throw new modx.util.Exception('easy )', 1);
                }
                // @ts-ignore
                n = modUtilities.ConverterUnits.ToSi(n, type, from);
                if (isNaN(n)) {
                    throw new modx.util.Exception('invalid "from" unit', 2);
                }
                if (to == 'SI' || to == SI) {
                    throw new modx.util.Exception('easy )', 2);
                }
                if (to != 'best') {
                    if (modx.util.in_array(to, modx.util.array_keys(converterRule[0]))) {
                        var g;
                        g = 0;
                    }
                    else if (modx.util.in_array(to, modx.util.array_keys(converterRule[1]))) {
                        g = 1;
                    }
                    else {
                        throw new modx.util.Exception('invalid "to" unit', 2);
                    }
                }
                else {
                    var g;
                    if (n >= converterRule['SI'][0]) {
                        g = 1;
                    }
                    else {
                        g = 0;
                    }
                }
                var key;
                __loop1: for (key in converterRule[g]) {
                    var rule;
                    rule = converterRule[g][key];
                    if (n >= rule[0]) {
                        n /= rule[0];
                        size = [
                            // @ts-ignore
                            n.toFixed(i),
                            key
                        ];
                    }
                    else {
                        if (to == 'best') {
                            break;
                        }
                    }
                    if (to != 'best' && to == key) {
                        break;
                    }
                    i++;
                }
                if (!out && size instanceof Array && size.hasOwnProperty(0) && size.hasOwnProperty(1)) {
                    out = size;
                }
                else {
                    out = [
                        n,
                        SI
                    ];
                }
            }
            catch (__e__) {
                var e;
                if (__e__ instanceof modx.util.Exception) {
                    e = __e__;
                    __loop1: switch (e.getCode()) {
                        case 1:
                            return {
                                // @ts-ignore
                                0: n.toFixed(i),
                                1: from
                            };
                        case 2:
                            return {
                                // @ts-ignore
                                0: n.toFixed(i),
                                1: SI
                            };
                        default:
                            return e.getMessage();
                    }
                }
                else {
                    throw __e__;
                }
            }
            return out;
        };
        class_4.ToSi = function (n, type, from) {
            if (typeof type == 'undefined')
                type = 'byte';
            if (typeof from == 'undefined')
                from = 'SI';
            // @ts-ignore
            if (typeof modUtilities.ConverterUnits.converterRule[type] != 'undefined') {
                var converterRule;
                // @ts-ignore
                converterRule = modUtilities.ConverterUnits.converterRule[type];
                var SI;
                SI = converterRule['SI'][1];
            }
            else {
                return false;
            }
            if (from == 'SI' || from == SI) {
                return n;
            }
            var g;
            if (modx.util.in_array(from, modx.util.array_keys(converterRule[0]))) {
                g = 0;
            }
            else if (modx.util.in_array(from, modx.util.array_keys(converterRule[1]))) {
                g = 1;
            }
            else {
                return false;
            }
            __loop1: while (from != SI && typeof converterRule[g][from] != 'undefined') {
                var f_;
                f_ = converterRule[g][from];
                n *= f_[0];
                from = f_[1];
            }
            return n;
        };
        return class_4;
    }()),
    // @ts-ignore
    _a.converterRule = modUtilities.converterRule,
    _a);
// @ts-ignore
modUtilities.clipboard = /** @class */ (function () {
    function class_5() {
        this.timeLimit = 100;
        this.mode = 'navigator';
        this.response = undefined;
        this.permission = false;
        if (window.hasOwnProperty('navigator') && navigator.clipboard) {
            this.mode = 'navigator';
        }
        else if (typeof document.queryCommandSupported === 'function') {
            this.mode = 'fallback';
        }
        else {
            this.mode = 'notSupport';
        }
    }
    class_5.prototype.__fallback_write = function (data) {
        if (data === void 0) { data = ''; }
        var textArea = document.createElement('textarea');
        textArea.value = data;
        // Avoid scrolling to bottom
        textArea.style.top = '0';
        textArea.style.left = '0';
        textArea.style.position = 'fixed';
        textArea.style.opacity = '1';
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        try {
            var successful = document.execCommand('copy');
            this.response = successful ? true : false;
        }
        catch (err) {
            this.response = false;
        }
        document.body.removeChild(textArea);
    };
    class_5.prototype.__fallback_read = function () {
        var textArea = document.createElement('textarea');
        // Avoid scrolling to bottom
        textArea.style.top = '0';
        textArea.style.left = '0';
        textArea.style.position = 'fixed';
        textArea.style.opacity = '1';
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        try {
            var successful = document.execCommand('paste');
            if (successful) {
                this.response = textArea.value;
            }
            else {
                this.response = false;
            }
        }
        catch (err) {
            this.response = false;
        }
        document.body.removeChild(textArea);
    };
    class_5.prototype.__navigator_write = function (data) {
        var _this = this;
        if (data === void 0) { data = ''; }
        this.response = undefined;
        document.body.focus();
        navigator.clipboard.writeText(data).then(function (data) {
            _this.response = data;
        }, function (e) {
            console.warn(e);
            _this.response = false;
        });
    };
    class_5.prototype.__navigator_read = function () {
        var _this = this;
        this.response = undefined;
        document.body.focus();
        navigator.clipboard.readText().then(function (data) {
            _this.response = data;
        }, function (e) {
            console.warn(e);
            _this.response = false;
        });
    };
    class_5.prototype.__notSupport_write = function (data) {
        if (data === void 0) { data = ''; }
        return false;
    };
    class_5.prototype.__notSupport_read = function () {
        return false;
    };
    class_5.prototype.write = function (data) {
        if (data === void 0) { data = ''; }
        if (!this.permission) {
            this.permissions();
        }
        try {
            // @ts-ignore
            if (this['__' + this.mode + '_write'] instanceof Function) {
                // @ts-ignore
                this['__' + this.mode + '_write'](data);
            }
            return this.response;
        }
        catch (e) {
            return false;
        }
    };
    class_5.prototype.read = function () {
        if (!this.permission) {
            this.permissions();
        }
        try {
            // @ts-ignore
            if (this['__' + this.mode + '_read'] instanceof Function) {
                // @ts-ignore
                this['__' + this.mode + '_read']();
            }
            return this.response;
        }
        catch (e) {
            return false;
        }
    };
    class_5.prototype.permissions = function () {
        var _this = this;
        try {
            // @ts-ignore
            navigator.permissions.query({ name: 'clipboard-write' }).then(function (result) {
                if (result.state == 'granted' || result.state == 'prompt') {
                    _this.permission = true;
                }
                else {
                    _this.permission = false;
                }
            });
            // @ts-ignore
            navigator.permissions.query({ name: 'clipboard-read' }).then(function (result) {
                if (result.state == 'granted' || result.state == 'prompt') {
                    _this.permission = true;
                }
                else {
                    _this.permission = false;
                }
            });
            return true;
        }
        catch (e) {
            return false;
        }
    };
    return class_5;
}());
// @ts-ignore
modUtilities.util = /** @class */ (function () {
    function class_6(modx) {
        this.modx = modx;
        // @ts-ignore
        this.mouse = {};
        this.mouse.mouseX = 0;
        this.mouse.mouseY = 0;
        this.mouse.pageX = 0;
        this.mouse.pageY = 0;
        this.device = '';
        // @ts-ignore
        this.constant = {};
        this.constant.kb = 1024;
        this.constant.min = 60;
        this.constant.mb = this.constant.kb * 1024;
        this.constant.gb = this.constant.mb * 1024;
        this.constant.tb = this.constant.gb * 1024;
        this.constant.hour = this.constant.min * 60;
        this.constant.day = this.constant.hour * 24;
        this.constant.week = this.constant.day * 7;
        // @ts-ignore
        this.convert_ = new modUtilities.ConverterUnits;
        // @ts-ignore
        this.clipboard = new modUtilities.clipboard;
        this.convert = this.convert_.convert;
        // @ts-ignore
        this.translitRule = modUtilities.translitRule;
        //class constant
        this.FirstLetter = 1;
        this.EveryWord = 2;
        this.AfterDot = 3;
        //
        this.Device();
        this.included = {};
    }
    /**
     * @param {string} $k
     * @param {[]} $a
     */
    class_6.prototype.in_array = function ($k, $a) {
        for (var $aKey in $a) {
            if ($k == $a[$aKey]) {
                return true;
            }
        }
        return false;
    };
    class_6.prototype.array_keys = function ($a) {
        var arr = [];
        for (var $aKey in $a) {
            arr.push($aKey);
        }
        return arr;
    };
    Object.defineProperty(class_6, "FirstLetter", {
        /**
         * @param {string} message
         * @param {number} code
         */
        get: function () {
            return 1;
        },
        enumerable: false,
        configurable: true
    });
    Object.defineProperty(class_6, "EveryWord", {
        get: function () {
            return 2;
        },
        enumerable: false,
        configurable: true
    });
    Object.defineProperty(class_6, "AfterDot", {
        get: function () {
            return 3;
        },
        enumerable: false,
        configurable: true
    });
    /**
     * @param {string} string
     * @param {number} mode
     * @param {boolean} otherLower
     */
    // @ts-ignore
    class_6.prototype.mb_ucfirst = function (string, mode, otherLower) {
        if (string === void 0) { string = ''; }
        if (mode === void 0) { mode = modUtilities.FirstLetter; }
        if (otherLower === void 0) { otherLower = true; }
        if (string && string.constructor.name == 'String') {
            switch (mode) {
                case 3:
                    var words = string.split(new RegExp('[\.\?\!]'));
                    for (var _i = 0, words_1 = words; _i < words_1.length; _i++) {
                        var word = words_1[_i];
                        word = word.trim();
                        string = string.replace(word, this.mb_ucfirst(word));
                    }
                    return string;
                case 2:
                    var words = string.split(new RegExp('[\s]'));
                    for (var _a = 0, words_2 = words; _a < words_2.length; _a++) {
                        var word = words_2[_a];
                        word = word.trim();
                        string = string.replace(word, this.mb_ucfirst(word));
                    }
                    return string;
                case 1:
                default:
                    if (otherLower) {
                        string = string.toLowerCase();
                    }
                    return string[0].toUpperCase() + string.slice(1);
                    break;
            }
        }
        return false;
    };
    /**
     * @param {string} string
     * @param {number} mode
     * @param {boolean} otherLower
     */
    // @ts-ignore
    class_6.prototype.mbUcfirst = function (string, mode, otherLower) {
        if (string === void 0) { string = ''; }
        if (mode === void 0) { mode = modUtilities.FirstLetter; }
        if (otherLower === void 0) { otherLower = true; }
        return this.mb_ucfirst(string, mode, otherLower);
    };
    class_6.prototype.getMouse = function () {
        return { x: this.mouse.mouseX, y: this.mouse.mouseY };
    };
    class_6.prototype.Device = function () {
        var d = 'mobile';
        if (window.innerWidth > 560) {
            d = 'tabled';
        }
        if (window.innerWidth > 1200) {
            d = 'pc';
        }
        this.device = d;
        return d;
    };
    /**
     * @param {*} str
     * @param {boolean|string} L
     * @param {string} R
     * @param {string|((substring: string, ...args: any[]) => string)} replace
     */
    class_6.prototype.trim = function (str, L, R, replace) {
        if (str === void 0) { str = ''; }
        if (L === void 0) { L = '\s'; }
        if (R === void 0) { R = ''; }
        if (replace === void 0) { replace = ''; }
        if (!R) {
            R = L;
        }
        var reg1 = new RegExp('(^' + L + '+)');
        var reg2 = new RegExp('(' + R + '+$)');
        return str.replace(reg1, replace).replace(reg2, replace);
    };
    class_6.prototype.setCookie = function (name, value, options) {
        if (options === void 0) { options = { path: '/' }; }
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
        if (!name) {
            return false;
        }
        // @ts-ignore
        if (value instanceof Object || value instanceof Array) {
            try {
                value = JSON.stringify(value);
            }
            catch (e) {
                return e;
            }
        }
        // @ts-ignore
        options = options || {};
        // @ts-ignore
        if (options.expires instanceof Date) {
            // @ts-ignore
            options.expires = options.expires.toUTCString();
        }
        var updatedCookie = encodeURIComponent(name) + '=' + encodeURIComponent(value);
        for (var optionKey in options) {
            updatedCookie += '; ' + optionKey;
            var optionValue = options[optionKey];
            if (optionValue !== true) {
                updatedCookie += '=' + optionValue;
            }
        }
        document.cookie = updatedCookie;
        return true;
    };
    class_6.prototype.deleteCookie = function (name) {
        /*
        Deletes a cookie with specified name.
        Returns true when cookie was successfully deleted, otherwise false
        */
        return this.setCookie(name, null, {
            // @ts-ignore
            expires: new Date(0),
            path: '/'
        });
    };
    /**
     * @param {*} source
     * @param {*} name
     * @param {string} parent_selector
     * @param {boolean}     */
    class_6.prototype.include = function (source, name, parent_selector, async) {
        var _this = this;
        if (name === void 0) { name = false; }
        if (parent_selector === void 0) { parent_selector = false; }
        if (async === void 0) { async = true; }
        if (typeof this.included[source] == 'undefined') {
            this.included[source] = {
                source: source,
                name: name
            };
            var script = document.createElement('script');
            if (!parent_selector) {
                var prior = document.querySelector('head');
            }
            else {
                // @ts-ignore
                var prior = document.querySelector(parent_selector);
            }
            script.async = async;
            // @ts-ignore
            script.onload = script.onreadystatechange = function (e, isAbort) {
                // @ts-ignore
                if (isAbort || !script.readyState || /loaded|complete/.test(script.readyState)) {
                    isAbort = true;
                }
                else {
                    isAbort = false;
                }
                // @ts-ignore
                script.onload = script.onreadystatechange = null;
                script = undefined;
                var event = new Event('modx.util.included');
                // @ts-ignore
                event.sucses = isAbort;
                // @ts-ignore
                event.source = source;
                document.dispatchEvent(event);
                _this.included[source].sucses = isAbort;
                _this.included[source].e = e;
            };
            script.src = source;
            // @ts-ignore
            prior.append(script);
            return true;
        }
        return false;
    };
    /**
     * @param {number} name
     * @param {number} id
     */
    class_6.prototype.getCookie = function (name, id, json) {
        var _a;
        if (name === void 0) { name = false; }
        if (id === void 0) { id = false; }
        if (json === void 0) { json = false; }
        var cookies = document.cookie + ';';
        // @ts-ignore
        cookies = (_a = cookies.match(/([^\s]+?;)/g)) !== null && _a !== void 0 ? _a : [];
        var cookie = new Object();
        for (var _i = 0, cookies_1 = cookies; _i < cookies_1.length; _i++) {
            var _cookie = cookies_1[_i];
            var cookie_ = _cookie.split('=');
            var test = cookie_[0].match(/(\[(.+)?\])/);
            if (!test) {
                var cookie_name = decodeURIComponent(cookie_[0]);
                cookie[cookie_name] = this.trim(decodeURIComponent(cookie_[1]), '\s', ';');
            }
            else {
                var cookie_name = decodeURIComponent(cookie_[0]).replace(decodeURIComponent(test[0]), '');
                var cookie_id = decodeURIComponent(test[2]);
                if (typeof cookie[cookie_name] == 'undefined') {
                    cookie[cookie_name] = {};
                }
                cookie[cookie_name][cookie_id] = this.trim(decodeURIComponent(cookie_[1]), '\s', ';');
            }
        }
        // @ts-ignore
        this.cookie = cookie;
        // @ts-ignore
        var out = this.cookie;
        if (name) {
            // @ts-ignore
            if (typeof cookie[name] != 'undefined') {
                // @ts-ignore
                out = cookie[name];
            }
            else {
                out = '';
            }
        }
        if (id && typeof out[id] != 'undefined') {
            out = out[id];
        }
        if (out && json) {
            try {
                return JSON.parse(out);
            }
            catch (e) {
                return false;
            }
        }
        return out;
    };
    class_6.prototype.getLocalStorage = function (name) {
        name = name.toString();
        try {
            // @ts-ignore
            this.localStorageSize = new Blob(Object.values(localStorage[name])).size;
        }
        catch (e) {
        }
        if (name) {
            try {
                if (typeof localStorage[name] != 'undefined') {
                    var value = localStorage.getItem(name);
                    try {
                        return JSON.parse(value);
                    }
                    catch (e) {
                        return value;
                    }
                }
                else {
                    return false;
                }
            }
            catch (e) {
                if (this.devMode) {
                    console.warn(e);
                }
                return false;
            }
        }
    };
    /**
     * @param {*} name
     * @param {{}} value
     */
    class_6.prototype.setLocalStorage = function (name, value) {
        if (value === void 0) { value = {}; }
        name = name.toString();
        var store = this.getLocalStorage(name);
        try {
            // @ts-ignore
            if (value instanceof Object || value instanceof Array) {
                if (store instanceof Object || store instanceof Array) {
                    // @ts-ignore
                    value = Object.assign(store, value);
                }
                value = JSON.stringify(value);
            }
            // @ts-ignore
            localStorage.setItem(name, value);
            return true;
        }
        catch (e) {
            if (this.devMode) {
                console.warn(e);
            }
            return false;
        }
        return false;
    };
    /**
     * @param Array<any> a
     */
    class_6.prototype.uniqueArray = function (a) {
        try {
            // @ts-ignore
            return __spreadArrays(new Set(a));
        }
        catch (e) {
            var j = {};
            a.forEach(function (v) {
                // @ts-ignore
                j[v + '::' + typeof v] = v;
            });
            return Object.keys(j).map(function (v) {
                // @ts-ignore
                return j[v];
            });
        }
    };
    class_6.prototype.id = function (len) {
        if (len === void 0) { len = 5; }
        var id = '';
        var symbols = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!№;%:?*()_+=';
        for (var i = 0; i < len; i++) {
            id += symbols.charAt(Math.floor(Math.random() * symbols.length));
        }
        return id;
    };
    return class_6;
}());
// @ts-ignore
var modx = new modUtilities.miniModX();
//# sourceMappingURL=modUtilities.js.map