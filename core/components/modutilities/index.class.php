<?php
	ini_set('display_errors', 1);
	ini_set('display_errors', 1);
	if (file_exists(MODX_CORE_PATH . 'components/extraext/model/extraext.include.php')) {
		include_once MODX_CORE_PATH . 'components/extraext/model/extraext.include.php';
	}
	if (class_exists('extraExtManagerController')) {
		//Основной контроллер
		class modutilitiesIndexManagerController extends extraExtManagerController
		{

			public $componentName = 'modutilities'; // название компонента так как называется его папка в assets/components/: по умолчанию равно namespace
			public $devMode = TRUE;

			public function getLanguageTopics()
			{
				return [
					'modutilities:default',
				];
			}

			public function getPageTitle()
			{
				return 'modutilitiesRest';
			}

			public function loadCustomCssJs()
			{

				$assets = $this->modx->getOption('assets_url');
				$this->addCss('css/mgr/snippet_rest.tab.css', $this->componentUrl);

				if ($this->modx->config['friendly_urls'] == FALSE) {
					$this->addJavascript('js/mgr/error.tab.js',$this->componentUrl);
				} else {
					$this->addJavascript('ajax/libs/jquery/3.5.1/jquery.min.js', 'https://ajax.googleapis.com/', TRUE);
					$this->addJavascript('js/extraext/main.js', $this->componentUrl);

				}
			}
		}
	} else {
		//Запасной контроллер
		class modutilitiesIndexManagerController extends modExtraManagerController
		{
			public function getLanguageTopics()
			{
				return [
					'modutilities:default',
				];
			}

			public function getPageTitle()
			{
				return $this->modx->lexicon('modutilitiesRest');
			}

			public function getTemplateFile()
			{
				return MODX_ASSETS_PATH . '/components/modutilities/home.tpl';
			}

			public function loadCustomCssJs()
			{

				$assets = $this->modx->getOption('assets_url');
				$this->addCss($assets . 'components/modutilities/css/mgr/snippet_rest.tab.css?t=' . time());
//			$this->addCss($assets . 'components/modutilities/css/mgr/widgets/github.css');

				if ($this->modx->config['friendly_urls'] == FALSE) {
					$this->addJavascript($assets . 'components/modutilities/js/mgr/error.tab.js?');

				} else {
					$this->addCss($assets . 'components/modutilities/js/highlight/styles/github.css');
					$this->addJavascript($assets . 'components/modutilities/js/highlight/highlight.pack.js');
					$this->addJavascript('https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js');
					$this->addJavascript($assets . 'components/modutilities/js/mgr/jsonHighlighter.min.js');
					$this->addJavascript($assets . 'components/modutilities/js/mgr/widgets/highlight.pack.js');
					$this->addJavascript($assets . 'components/modutilities/js/mgr/snippet_rest.tab.js?');
					$this->addHtml('<script type="text/javascript">var modUtilConnector_url = "' . $assets . 'components/modutilities/connector.php";</script>');
				}
			}
		}
	}
