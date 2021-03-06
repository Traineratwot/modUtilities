<?php
	if (!class_exists('modUtilities')) {
		include MODX_CORE_PATH . 'components/modutilities/classes/modutilities.class.php';
	} else {
		//$modx->log(MODX::LOG_LEVEL_WARN, 'can`t load class "modutilities" already declared!');
	}
	/** @var modX $modx */
	switch ($modx->event->name) {
		case 'OnMODXInit':
			if ($modx->config['use_modUtilities'] == TRUE) {
				if (class_exists('modUtilities') and !isset($modx->util)) {
					$modx->lexicon->load('modutilities:default');
					$modx->getService('util', 'modutilities');
				} else {
					//$modx->log(MODX::LOG_LEVEL_WARN, 'can`t load class "modutilities" already declared');
				}
			}
			break;
		case 'pdoToolsOnFenomInit':
			if (version_compare(PHP_VERSION, '5.6', '>=')) {
				if ($modx->config['use_modUtilities'] == TRUE and $modx->config['use_modUtilFenom'] == TRUE) {
					if (isset($fenom)) {
						$modx->getService('fenomUtil', 'modutilities');
						$modx->fenomUtil->isFenom = TRUE;
						$fenom->addFunction("util", function ($params = []) use ($modx) {
							$method = array_shift($params);
							if (method_exists($modx->fenomUtil, $method)) {
								return $modx->fenomUtil->$method(...$params);
							} else {
								return eval('/** @var modX $modx */return $modx->fenomUtil->' . $method . ';');
							}
							$modx->log(MODX::LOG_LEVEL_WARN, 'can`t run $modx->fenomUtil->' . $method . ' ');
							return FALSE;
						});
						$fenom->addModifier('util', function ($input, $option = []) use ($modx) {
							$method = $input;
							if (method_exists($modx->fenomUtil, $method)) {
								return $modx->fenomUtil->$method(...$option);
							} else {
								return eval('/** @var modX $modx */return $modx->fenomUtil->' . $method . ';');
							}
							$modx->log(MODX::LOG_LEVEL_WARN, 'can`t found $modx->fenomUtil->' . $method . ' ');
							return FALSE;
						});
					}
				}
			}
			break;
		case 'OnHandleRequest':
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
		case 'OnPageNotFound':
			if ($modx->config['use_modUtilFrontJs'] == TRUE) {
				$key = $modx->getOption('fastrouter.paramsKey', NULL, FALSE);
				if ($key) {
					$plain = include MODX_CORE_PATH . 'components/modutilities/classes/modutilities.js.php';
					$src = $modx->getOption('assets_url') . 'components/modutilities/js/web/modUtilities.js';
					$script = <<<EOD
<script type="text/javascript" class="modutilities frontend">
	$plain
</script>
<script src="$src" type="text/javascript" class="modutilities frontend"></script>
EOD;
					$modx->regClientStartupScript($script);
				}
			}
			break;
		case 'OnWebPageInit':
			if ($modx->config['use_modUtilFrontJs'] == TRUE) {
				$plain = include MODX_CORE_PATH . 'components/modutilities/classes/modutilities.js.php';
				$src = $modx->getOption('assets_url') . 'components/modutilities/js/web/modUtilities.js';
				$script = <<<EOD
<script type="text/javascript" class="modutilities frontend">
	$plain
</script>
<script src="$src" type="text/javascript" class="modutilities frontend"></script>
EOD;
				$modx->regClientStartupScript($script);
			}
			break;
	}