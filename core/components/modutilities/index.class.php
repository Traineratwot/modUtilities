<?php
	ini_set('display_errors', 1);

	class modutilitiesIndexManagerController extends modExtraManagerController
	{
		public function getPageTitle()
		{
			return 'modutilitiesRest';
		}

		public function getTemplateFile()
		{
			return MODX_ASSETS_PATH . '/components/modutilities/home.tpl';
		}

		public function loadCustomCssJs()
		{
			$assets = $this->modx->getOption('assets_url');
			$this->addCss($assets . 'components/modutilities/css/mgr/snippet_rest.tab.css');
			$this->addCss($assets . 'components/modutilities/css/mgr/widgets/github.css');

			$this->addJavascript('https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js');

			$this->addJavascript($assets . 'components/modutilities/js/mgr/jsonHighlighter.min.js');
			$this->addJavascript($assets . 'components/modutilities/js/mgr/widgets/highlight.pack.js');

			$this->addJavascript($assets . 'components/modutilities/js/mgr/snippet_rest.tab.js?');

			$this->addHtml('<script type="text/javascript">var modUtilConnector_url = "' . $assets . 'components/modutilities/connector.php";</script>');
		}
	}
