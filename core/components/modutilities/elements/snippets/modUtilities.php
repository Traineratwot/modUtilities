<?php
$method = $scriptProperties['method']??null;
	$params = $scriptProperties['params']??null;
	if(!is_array($params)){
		$params = explode(',',$params);
		$params = array_values($params);
	}
	if (!empty($method)) {
		/** @var modX $modx */
		if (method_exists($modx->util, $method)) {
			return $modx->util->$method(...$params);
		} else {
			return eval('/** @var modX $modx */return $modx->util->' . $method . ';');
		}
		$modx->log(MODX::LOG_LEVEL_WARN, 'can`t run $modx->util->' . $method . ' ');

	}
	return FALSE;