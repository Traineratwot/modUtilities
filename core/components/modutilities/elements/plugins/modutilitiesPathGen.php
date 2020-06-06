<?php
	switch ($modx->event->name) {
		case 'OnSnipFormPrerender':
			$folder = 'core/elements/snippets';
			$class = 'snippet';
			$static = "modx-$class-static";
			$name = "modx-$class-name";
			$category = "modx-$class-category";
			$path = "modx-$class-static-file";
			$ext = '.php';
			break;
		case 'OnPluginFormPrerender':
			$folder = 'core/elements/plugins';
			$class = 'plugin';
			$static = "modx-$class-static";
			$name = "modx-$class-name";
			$category = "modx-$class-category";
			$path = "modx-$class-static-file";
			$ext = '.php';
			break;
		case 'OnChunkFormPrerender':
			$folder = 'core/elements/chunks';
			$class = 'chunk';
			$static = "modx-$class-static";
			$name = "modx-$class-name";
			$category = "modx-$class-category";
			$path = "modx-$class-static-file";
			$ext = '.tpl';
			break;
		case 'OnTempFormPrerender':
			$folder = 'core/elements/templates';
			$class = 'template';
			$static = "modx-$class-static";
			$name = "modx-$class-templatename";
			$category = "modx-$class-category";
			$path = "modx-$class-static-file";
			$ext = '.tpl';
			break;
		default:
			$folder = 'core/elements/files';
			$class = '';
			$ext = '.txt';
			break;
	}
	$jqueryScript = '<script type="text/javascript" class="addJq2">';
	$jqueryScript .= "\n";
	$jqueryScript .= 'if(typeof jQuery == "undefined"){';
	$jqueryScript .= "\n";
	$jqueryScript .= 'document.head.appendChild(Ext.Loader.buildScriptTag(\'/assets/js/lib/jquery.min.js\'))';
	$jqueryScript .= "\n";
	$jqueryScript .= '}';
	$jqueryScript .= "\n";
	$jqueryScript .= '</script>';
	$jqueryScript .= "\n";

	$modx->regClientStartupScript($jqueryScript, TRUE);
	$connector = '/assets/components/modutilities/connector.php';
	$modx->regClientStartupScript("<script>
var I_change_it = false
Ext.onReady(function(){
	if('$static' != ''){
Ext.get('$static').on('change',(e)=>{
	console.log(e);
	if (e.target.checked) {
		path_change();
	}
});
}
var pathGen = {
	name: null,
	category: null,
	folder: '$folder',
	new_path: null,
}

function path_change() {
	var static = document.getElementById('$static').checked;
	if (static) {
		var path = document.getElementById('$path').value;
		if (!path || I_change_it) {
			pathGenStart()
		}
	}
}

function translate(key, str) {
	MODx.Ajax.request({
		url: '$connector',
		params: {
			action: 'mgr/translit',
			str: str
		},
		listeners: {
			success: { // при успешном запросе
				fn: function (res) {
					pathGen[key] = res.message;
					pathGenStart()
				}
			}
		}
	})
}
function pathGenStart() {
	if (!pathGen.name) {
		pathGen.name = document.getElementById('$name').value;
	}
	if (/[а-яА-Я]/.test(pathGen.name)) {
		translate('name', pathGen.name)
		return false;
	}
	if (!pathGen.category) {
		pathGen.category = document.getElementById('$category').value;
	}
	if (/[а-яА-Я]/.test(pathGen.category) && ['не_указано','ne-ukazano'].indexOf(pathGen.category.toLowerCase()) == -1 ) {
		translate('category', pathGen.category)
		return false;
	}
	new_path = pathGen.folder + '/' + pathGen.category + '/' + pathGen.name + '$ext';
	I_change_it = true;
	new_path = new_path.replace(/\s/g, '_').replace(/_—_/g, '/').replace('not-specified/', '')
	document.getElementById('$path').value = new_path;
}})
	</script>");