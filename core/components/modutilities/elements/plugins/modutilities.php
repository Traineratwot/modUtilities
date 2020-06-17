<?php
if (!class_exists('modUtilities')) {
		include MODX_CORE_PATH . 'components/modutilities/classes/modutilities.class.php';
	} else {
		$modx->log(MODX::LOG_LEVEL_WARN, 'can`t load class "modUtilities" already declared!');
	}
	switch ($modx->event->name) {
		case 'OnMODXInit':
			if (class_exists('modutilities') and !isset($modx->util)) {
				$modx->util = new modUtilities($modx);
			} else {
				$modx->log(MODX::LOG_LEVEL_WARN, 'can`t load class "modutilities" already declared');
			}
			break;
		case 'pdoToolsOnFenomInit':
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
			}
			break;
		case 'OnPageNotFound':
			$alias = $modx->context->getOption('request_param_alias', 'q');
			if (!isset($_REQUEST[$alias])) {return false;}
			$REST = $modx->util->REST([
				'url'=>$_REQUEST[$alias],
			]);
			break;
	}