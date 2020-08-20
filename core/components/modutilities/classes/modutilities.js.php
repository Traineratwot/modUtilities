<?php

/** @var modResource $resource */

$resource = $modx->getObject('modResource', $modx->resourceIdentifier);
$res = $resource->toArray('', TRUE);
unset($res['content']);
ob_start();
?>
class modX {

constructor() {
var self = this;
this.resource = <?= json_encode($res, 256) ?>;
=======
$(document).mousemove(function(e){
// положение курсора внутри элемента
self.pageX = e.pageX
self.pageY = e.pageY
});
this.util = new modUtilities(this)
>>>>>>> bc1ab55a2408a28335555e3b983bd8519f61af47
}
}
class modutilities {
constructor(modx) {
this.modx = modx
this.device = '';
this.constant = {};
this.constant.kb = 1024;
this.constant.min = 60;
this.constant.mb = this.constant.kb * 1024;
this.constant.gb = this.constant.mb * 1024;
this.constant.tb = this.constant.gb * 1024;
this.constant.hour = this.constant.min * 60;
this.constant.day = this.constant.hour * 24;
this.constant.week = this.constant.day * 7;
this.translitRule = <?= json_encode(modutilities::translitRule) ?>;
//class constant
this.FirstLetter = 1;
this.EveryWord = 2;
this.AfterDot = 3;
//
this.Device();
}


static get FirstLetter() {return 1;}


static get EveryWord() {return 2;}


static get AfterDot() {return 3;}


mb_ucfirst(string = '', mode = modutilities.FirstLetter, otherLower = true) {
if(string && string.constructor.name == 'String') {
switch( mode ) {
case 3:
var words = string.split(new RegExp('[\.\?\!]'));
for(var word of words) {
word = word.trim()
string = string.replace(word, this.mb_ucfirst(word))
}
return string
case 2:
var words = string.split(new RegExp('[\s]'));
for(var word of words) {
word = word.trim()
string = string.replace(word, this.mb_ucfirst(word))
}
return string
case 1:
default:
if(otherLower) {
string = string.toLowerCase();
}
return string[0].toUpperCase() + string.slice(1);
break
}
}
return false
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
}
modx = new modX();
window.addEventListener('resize', function(event) {
modx.util.Device()
}, true);
<?php
return ob_get_clean();