<?php
include_once MODX_CORE_PATH . 'components/utilities/classes/utilities.class.php';
	switch ($modx->event->name) {
		case 'OnMODXInit':
			if (class_exists('utilities') and !isset($modx->util)) {
				$modx->util = new utilities($modx);
			}
			break;
		case 'pdoToolsOnFenomInit':
			if (isset($fenom)) {
				$fenom->addFunction("util", function ($params) {
					global $modx;
					$method = array_shift($params);
					if (method_exists($modx->util,$method)) {
						return $modx->util->$method(...$params);
					}else if(property_exists($modx->util,$method)){
						return $modx->util->$method;
					}
					return false;
				});
			}
			break;
	}