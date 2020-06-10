<?php
	if (!class_exists('modUtilities')) {
		include_once MODX_CORE_PATH . 'components/modutilities/classes/modUtilities.class.php';
	} else {
		$modx->log(MODX::LOG_LEVEL_ERROR, 'can`t load class "modUtilities" already declared!');
	}
	switch ($modx->event->name) {
		case 'OnMODXInit':
			if (class_exists('modutilities') and !isset($modx->util)) {
				$modx->util = new modUtilities($modx);
			} else {
				$modx->log(MODX::LOG_LEVEL_ERROR, 'can`t load class "modutilities" already declared');
			}
			break;
		case 'pdoToolsOnFenomInit':
			if (isset($fenom)) {
				$fenom->addFunction("util", function ($params) {
					global $modx;
					$method = array_shift($params);
					if (method_exists($modx->util, $method)) {
						return $modx->util->$method(...$params);
					} else {
						return eval('return $modx->util->' . $method . ';');
					}
					$modx->log(MODX::LOG_LEVEL_WARN, 'can`t run $modx->util->' . $method . ' ');
					return FALSE;
				});
			}
			break;
	}