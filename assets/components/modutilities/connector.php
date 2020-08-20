<?php
$base_path = __DIR__;
// Ищем MODX
while (!file_exists($base_path . '/config.core.php')) {
	$base_path = dirname($base_path);
}

// Подключаем MODX
require_once $base_path . '/config.core.php';
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';

// Указываем путь к папке с процессорами и заставляем MODX работать
$modx->addPackage('modutilities', MODX_CORE_PATH . 'components/modutilities/model/');
$modx->request->handleRequest([
	'processors_path' => MODX_CORE_PATH . 'components/modutilities/processors/',
	'location' => '',
]);
