<?php
	ini_set('display_errors', 1);

	class modutilitiesIndexManagerController extends modExtraManagerController
	{
		public function getLanguageTopics(){
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

				$this->addJavascript('https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js');

				$this->addJavascript($assets . 'components/modutilities/js/mgr/jsonHighlighter.min.js');
				$this->addJavascript($assets . 'components/modutilities/js/mgr/widgets/highlight.pack.js');

				$this->addJavascript($assets . 'components/modutilities/js/mgr/snippet_rest.tab.js?');

				$this->addHtml('<script type="text/javascript">var modUtilConnector_url = "' . $assets . 'components/modutilities/connector.php";</script>');
			}
		}
	}
