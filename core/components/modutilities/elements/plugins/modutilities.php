<?php
if (!class_exists('modutilities')) {
		include MODX_CORE_PATH . 'components/modutilities/classes/modutilities.class.php';
	} else {
		//$modx->log(MODX::LOG_LEVEL_WARN, 'can`t load class "modutilities" already declared!');
	}
	/** @var modX $modx */
	switch ($modx->event->name) {
		case 'OnMODXInit':
			if ($modx->config['use_modUtilities'] == TRUE) {
				if (class_exists('modutilities') and !isset($modx->util)) {
					$modx->lexicon->load('modutilities:default');
					$modx->util = new modUtilities($modx);
				} else {
					//$modx->log(MODX::LOG_LEVEL_WARN, 'can`t load class "modutilities" already declared');
				}
			}
			break;
		case 'pdoToolsOnFenomInit':
			if ($modx->config['use_modUtilities'] == TRUE and $modx->config['use_modUtilFenom'] == TRUE) {
				if (isset($fenom)) {
					$fenom->addFunction("util", function ($params) use ($modx) {
						$method = array_shift($params);
						if (method_exists($modx->util, $method)) {
							return $modx->util->$method(...$params);
						} else {
							return eval('/** @var modX $modx */return $modx->util->' . $method . ';');
						}
						$modx->log(MODX::LOG_LEVEL_WARN, 'can`t run $modx->util->' . $method . ' ');
						return FALSE;
					});
					$fenom->addModifier('util', function ($input, $option) use ($modx) {
						$method = $input;
						if (method_exists($modx->util, $method)) {
							return $modx->util->$method(...$option);
						} else {
							return eval('/** @var modX $modx */return $modx->util->' . $method . ';');
						}
						$modx->log(MODX::LOG_LEVEL_WARN, 'can`t found $modx->util->' . $method . ' ');
						return FALSE;
					});
				}
			}
			break;
		case 'OnPageNotFound':
			if ($modx->config['use_modUtilitiesRest'] == TRUE) {
				$alias = $modx->context->getOption('request_param_alias', 'q');
				if (!isset($_REQUEST[$alias])) {
					break;
				}
				$REST = $modx->util->REST([
					'url' => $_REQUEST[$alias],
				]);
			}
			break;
		case 'OnWebPageInit':
			if ($modx->config['use_modUtilFrontJs'] == TRUE) {
				$script = include MODX_CORE_PATH . 'components/modutilities/classes/modutilities.js.php';
				$script = '<script type="text/javascript" class="modutilities">' . $script . '</script>';
				$modx->regClientStartupScript($script);
			}
			break;
	}