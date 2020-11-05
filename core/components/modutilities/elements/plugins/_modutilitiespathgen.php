<?php
	/** @var modx $modx */
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
			return false;
			$folder = 'core/elements/files';
			$class = '';
			$ext = '.txt';
			break;
	}
	$jqueryScript = '<script type="text/javascript" class="addJq2">';
	$jqueryScript .= "\n";
	$jqueryScript .= 'if(typeof jQuery == "undefined" || typeof $ == "undefined"){';
	$jqueryScript .= "\n";
	$jqueryScript .= 'document.head.appendChild(Ext.Loader.buildScriptTag(\'https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js\'))';
	$jqueryScript .= "\n";
	$jqueryScript .= '}';
	$jqueryScript .= "\n";
	$jqueryScript .= '</script>';
	$jqueryScript .= "\n";

	$modx->regClientStartupScript($jqueryScript, TRUE);
	$connector = '/assets/components/modutilities/connector.php';
	$modx->regClientStartupScript("<script>
	var modUtilitiesPathGen = new Object();
	modUtilitiesPathGen.I_change_it = false
	modUtilitiesPathGen.new_path = ''
	Ext.onReady(function(){
	if('$static' != ''){
		Ext.get('$static').on('change',(e)=>{
			if (e.target.checked) {
				path_change();
			}
		});
	}
	modUtilitiesPathGen.pathGen = {
		name: null,
		category: null,
		folder: '$folder',
		new_path: null,
	}
	
	function path_change() {
		var static = Ext.getCmp('$static').checked;
		if (static) {
			var path = Ext.getCmp('$path').getValue();
			if (!path || modUtilitiesPathGen.I_change_it) {
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
						modUtilitiesPathGen.pathGen[key] = res.message;
						pathGenStart()
					}
				}
			}
		})
	}
	function pathGenStart() {
		if (!modUtilitiesPathGen.pathGen.name) {
			modUtilitiesPathGen.pathGen.name = Ext.getCmp('$name').getValue();
		}
		if (/[а-яА-Я]/.test(modUtilitiesPathGen.pathGen.name)) {
			translate('name', modUtilitiesPathGen.pathGen.name);
			return false;
		}
		if (!modUtilitiesPathGen.pathGen.category ) {
			if(Ext.getCmp('$category').getValue() != '0'){
				modUtilitiesPathGen.pathGen.category = Ext.getCmp('$category').lastSelectionText;
			}else{
				modUtilitiesPathGen.pathGen.category = '';
			}
		}
		if (/[а-яА-Я]/.test(modUtilitiesPathGen.pathGen.category)) {
			translate('category', modUtilitiesPathGen.pathGen.category);
			return false;
		}
		if(Ext.getCmp('$category').getValue() == '0'){
			modUtilitiesPathGen.pathGen.category = '';
		}
		modUtilitiesPathGen.new_path = modUtilitiesPathGen.pathGen.folder + '/' + modUtilitiesPathGen.pathGen.category + '/' + modUtilitiesPathGen.pathGen.name + '$ext';
		modUtilitiesPathGen.I_change_it = true;
		modUtilitiesPathGen.new_path = modUtilitiesPathGen.new_path.replace(/\s/g, '_').replace(/(_—_)|(\-\—\-)/g, '/').replace('not-specified/', '').replace(/\/{2,}/, '/')
		Ext.getCmp('$path').setValue(modUtilitiesPathGen.new_path) ;
		modUtilitiesPathGen.pathGen = {
			name: null,
			category: null,
			folder: '$folder',
			new_path: null,
		}
		modUtilitiesPathGen.new_path = null;
	}})
	</script>", TRUE);