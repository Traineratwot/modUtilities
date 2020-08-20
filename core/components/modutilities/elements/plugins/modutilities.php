<?php
if (!class_exists('modutilities')) {
		include MODX_CORE_PATH . 'components/modutilities/classes/modutilities.class.php';
	} else {
		//$modx->log(MODX::LOG_LEVEL_WARN, 'can`t load class "modutilities" already declared!');
	}
	switch ($modx->event->name) {
		case 'OnMODXInit':
			if($modx->config['use_modUtilities'] == true) {
				if (class_exists('modutilities') and !isset($modx->util)) {
					$modx->util = new modUtilities($modx);
				} else {
					//$modx->log(MODX::LOG_LEVEL_WARN, 'can`t load class "modutilities" already declared');
				}
			}
			break;
		case 'pdoToolsOnFenomInit':
			if($modx->config['use_modUtilities'] == true and $modx->config['use_modUtilFenom'] == true  ) {
				if (isset($fenom)) {
					$fenom->addFunction("util", function ($params) use ($modx) {
						$method = array_shift($params);
						if (method_exists($modx->util, $method)) {
							return $modx->util->$method(...$params);
						} else {
							return eval('return $modx->util->' . $method . ';');
						}
						$modx->log(MODX::LOG_LEVEL_WARN, 'can`t run $modx->util->' . $method . ' ');
						return FALSE;
					});
					$fenom->addModifier('util', function ($input, $option) use ($modx) {
						$method = $input;
						if (method_exists($modx->util, $method)) {
							return $modx->util->$method(...$option);
						} else {
							return eval('return $modx->util->' . $method . ';');
						}
						$modx->log(MODX::LOG_LEVEL_WARN, 'can`t run $modx->util->' . $method . ' ');
						return FALSE;
					});
				}
			}
			break;
		case 'OnPageNotFound':
			if($modx->config['use_modUtilitiesRest'] == true ) {
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
			if($modx->config['use_modUtilFrontJs'] == true ) {
				$script = include MODX_CORE_PATH . 'components/modutilities/classes/modutilities.js.php';
				$script = '<script type="text/javascript" class="modutilities">' . $script . '</script>';
				$modx->regClientStartupScript($script);
			}
			break;
	}