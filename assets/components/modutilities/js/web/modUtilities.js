"use strict";
var _a;
// @ts-ignore
modUtilities.miniModX = class {
    constructor() {
        // @ts-ignore
        this.resource = new modUtilities.miniResource(this);
        // @ts-ignore
        this.util = new modUtilities.util(this);
        this.util.Exception = function Exception(message, code) {
            this.message = message;
            this.code = code;
            this.getCode = () => {
                return this.code;
            };
            this.getMessage = () => {
                return this.message;
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
    lexicon(s = '', v = {}) {
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
    }
    lex(key = '', params = []) {
        // @ts-ignore
        return this.lexicon(...arguments);
    }
    _(key = '', params = []) {
        // @ts-ignore
        return this.lexicon(...arguments);
    }
    sendRedirect(uri = '', mode = 'native') {
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
    }
};
// @ts-ignore
modUtilities.miniResource = class {
    constructor(modx) {
        this.modx = modx;
        // @ts-ignore
        Object.assign(this, modUtilities.resource);
    }
};
// @ts-ignore
modUtilities.miniUser = class {
    constructor(modx) {
        this.modx = modx;
        // @ts-ignore
        Object.assign(this, modUtilities.user);
        this.settings = {};
        this.getSettings();
    }
    /**
     * @param {string} key
     */
    detSetting(key) {
        this.getSettings();
        // @ts-ignore
        this.settings[key] = null;
        return this.modx.util.setLocalStorage('modUtil.settings', this.settings);
    }
    /**
     * @param {string} key
     * @param {*} value
     */
    setSetting(key, value) {
        this.getSettings();
        // @ts-ignore
        this.settings[key] = value;
        return this.modx.util.setLocalStorage('modUtil.settings', this.settings);
    }
    /**
     * @param {string} key
     */
    getSetting(key) {
        if (this.getSettings()) {
            // @ts-ignore
            return this.settings[key];
        }
        return false;
    }
    getSettings() {
        var settings = this.modx.util.getLocalStorage('modUtil.settings', false, true);
        if (settings instanceof Object || settings instanceof Array) {
            this.settings = settings;
            return settings;
        }
        return false;
    }
};
// @ts-ignore
modUtilities.ConverterUnits = (_a = class {
        convert(n, type, from, to) {
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
        }
        static ToSi(n, type, from) {
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
        }
    },
    // @ts-ignore
    _a.converterRule = modUtilities.converterRule,
    _a);
// @ts-ignore
modUtilities.clipboard = class {
    constructor() {
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
    __fallback_write(data = '') {
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
    }
    __fallback_read() {
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
    }
    __navigator_write(data = '') {
        this.response = undefined;
        document.body.focus();
        navigator.clipboard.writeText(data).then((data) => {
            this.response = data;
        }, (e) => {
            console.warn(e);
            this.response = false;
        });
    }
    __navigator_read() {
        this.response = undefined;
        document.body.focus();
        navigator.clipboard.readText().then((data) => {
            this.response = data;
        }, (e) => {
            console.warn(e);
            this.response = false;
        });
    }
    __notSupport_write(data = '') {
        return false;
    }
    __notSupport_read() {
        return false;
    }
    write(data = '') {
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
    }
    read() {
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
    }
    permissions() {
        try {
            // @ts-ignore
            navigator.permissions.query({ name: 'clipboard-write' }).then(result => {
                if (result.state == 'granted' || result.state == 'prompt') {
                    this.permission = true;
                }
                else {
                    this.permission = false;
                }
            });
            // @ts-ignore
            navigator.permissions.query({ name: 'clipboard-read' }).then(result => {
                if (result.state == 'granted' || result.state == 'prompt') {
                    this.permission = true;
                }
                else {
                    this.permission = false;
                }
            });
            return true;
        }
        catch (e) {
            return false;
        }
    }
};
// @ts-ignore
modUtilities.util = class {
    constructor(modx) {
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
    in_array($k, $a) {
        for (const $aKey in $a) {
            if ($k == $a[$aKey]) {
                return true;
            }
        }
        return false;
    }
    array_keys($a) {
        var arr = [];
        for (const $aKey in $a) {
            arr.push($aKey);
        }
        return arr;
    }
    /**
     * @param {string} message
     * @param {number} code
     */
    static get FirstLetter() {
        return 1;
    }
    static get EveryWord() {
        return 2;
    }
    static get AfterDot() {
        return 3;
    }
    /**
     * @param {string} string
     * @param {number} mode
     * @param {boolean} otherLower
     */
    // @ts-ignore
    mb_ucfirst(string = '', mode = modUtilities.FirstLetter, otherLower = true) {
        if (string && string.constructor.name == 'String') {
            switch (mode) {
                case 3:
                    var words = string.split(new RegExp('[\.\?\!]'));
                    for (var word of words) {
                        word = word.trim();
                        string = string.replace(word, this.mb_ucfirst(word));
                    }
                    return string;
                case 2:
                    var words = string.split(new RegExp('[\s]'));
                    for (var word of words) {
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
    }
    /**
     * @param {string} string
     * @param {number} mode
     * @param {boolean} otherLower
     */
    // @ts-ignore
    mbUcfirst(string = '', mode = modUtilities.FirstLetter, otherLower = true) {
        return this.mb_ucfirst(string, mode, otherLower);
    }
    getMouse() {
        return { x: this.mouse.mouseX, y: this.mouse.mouseY };
    }
    Device() {
        var d = 'mobile';
        if (window.innerWidth > 560) {
            d = 'tabled';
        }
        if (window.innerWidth > 1200) {
            d = 'pc';
        }
        this.device = d;
        return d;
    }
    /**
     * @param {*} str
     * @param {boolean|string} L
     * @param {string} R
     * @param {string|((substring: string, ...args: any[]) => string)} replace
     */
    trim(str = '', L = '\s', R = '', replace = '') {
        if (!R) {
            R = L;
        }
        var reg1 = new RegExp('(^' + L + '+)');
        var reg2 = new RegExp('(' + R + '+$)');
        return str.replace(reg1, replace).replace(reg2, replace);
    }
    setCookie(name, value, options = { path: '/' }) {
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
        let updatedCookie = encodeURIComponent(name) + '=' + encodeURIComponent(value);
        for (let optionKey in options) {
            updatedCookie += '; ' + optionKey;
            let optionValue = options[optionKey];
            if (optionValue !== true) {
                updatedCookie += '=' + optionValue;
            }
        }
        document.cookie = updatedCookie;
        return true;
    }
    deleteCookie(name) {
        /*
        Deletes a cookie with specified name.
        Returns true when cookie was successfully deleted, otherwise false
        */
        return this.setCookie(name, null, {
            // @ts-ignore
            expires: new Date(0),
            path: '/'
        });
    }
    /**
     * @param {*} source
     * @param {*} name
     * @param {string} parent_selector
     * @param {boolean}     */
    include(source, name = false, parent_selector = false, async = true) {
        if (typeof this.included[source] == 'undefined') {
            this.included[source] = {
                source,
                name
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
            script.onload = script.onreadystatechange = (e, isAbort) => {
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
                const event = new Event('modx.util.included');
                // @ts-ignore
                event.sucses = isAbort;
                // @ts-ignore
                event.source = source;
                document.dispatchEvent(event);
                this.included[source].sucses = isAbort;
                this.included[source].e = e;
            };
            script.src = source;
            // @ts-ignore
            prior.append(script);
            return true;
        }
        return false;
    }
    /**
     * @param {number} name
     * @param {number} id
     */
    getCookie(name = false, id = false, json = false) {
        var _a;
        var cookies = document.cookie + ';';
        // @ts-ignore
        cookies = (_a = cookies.match(/([^\s]+?;)/g)) !== null && _a !== void 0 ? _a : [];
        var cookie = new Object();
        for (let _cookie of cookies) {
            var cookie_ = _cookie.split('=');
            var test = cookie_[0].match(/(\[(.+)?\])/);
            if (!test) {
                let cookie_name = decodeURIComponent(cookie_[0]);
                cookie[cookie_name] = this.trim(decodeURIComponent(cookie_[1]), '\s', ';');
            }
            else {
                let cookie_name = decodeURIComponent(cookie_[0]).replace(decodeURIComponent(test[0]), '');
                let cookie_id = decodeURIComponent(test[2]);
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
    }
    getLocalStorage(name) {
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
    }
    /**
     * @param {*} name
     * @param {{}} value
     */
    setLocalStorage(name, value = {}) {
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
    }
    /**
     * @param Array<any> a
     */
    uniqueArray(a) {
        try {
            // @ts-ignore
            return [...new Set(a)];
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
    }
    id(len = 5) {
        var id = '';
        var symbols = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!№;%:?*()_+=';
        for (var i = 0; i < len; i++) {
            id += symbols.charAt(Math.floor(Math.random() * symbols.length));
        }
        return id;
    }
};
// @ts-ignore
var modx = new modUtilities.miniModX();
//# sourceMappingURL=modUtilities.js.map