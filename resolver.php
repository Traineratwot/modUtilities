<?php
	/** @var object $object */
	if (!isset($modx) and $object->xpdo) {
		$modx =& $object->xpdo;
	}

	if ($modx instanceof xPDO) {
		/** @var array $options */
		$prefix = $modx->config['table_prefix'];
		switch ($options[xPDOTransport::PACKAGE_ACTION]) {
			case xPDOTransport::ACTION_UPGRADE:
				sendAuthorStat(['action' => 'UPGRADE']);

				$modx->addPackage('modutilities', MODX_CORE_PATH . 'components/modutilities/model/');
				$p = $modx->getObject('modPlugin', ['name' => 'utilities']);
				if ($p) {
					if (strpos($p->get('content'), 'utilities') !== FALSE) {
						$p->remove();
					}
				}
				$p = $modx->getObject('modPlugin', ['name' => 'UtilitiesPathGen']);
				if ($p) {
					if (strpos($p->get('content'), 'modutilities') !== FALSE) {
						$p->remove();
					}
				}
				$Utilrests = $modx->getIterator('Utilrest');
				/** @var Utilrest $rs */
				foreach ($Utilrests as $rs) {
					$cat = $rs->get('category');
					if (is_numeric($cat)) {
						$cat_ = $modx->getObject('Utilrestcategory', $cat);
						$rs->set('category', $cat_->get('name'));
						$rs->save();
					}
				}
				break;
			case xPDOTransport::ACTION_INSTALL:
				sendAuthorStat(['action' => 'INSTALL']);

				$modx->addPackage('modutilities', MODX_CORE_PATH . 'components/modutilities/model/');
				$modx->addExtensionPackage('modutilities', MODX_CORE_PATH . 'components/modutilities/model/');
				/** @var xPDOManager_mysql $manager */
				$manager = $modx->getManager();
				$manager->createObjectContainer('Utilrestcategory');
				$manager->createObjectContainer('Utilrest');
				$manager->createObjectContainer('Utilreststats');
				$count = $modx->getCount('Utilrestcategory', ['name' => 'default']);
				if ($count == 0) {
					$cat = $modx->newObject('Utilrestcategory');
					$cat->set('name', 'default');
					$cat->set('allowMethod', 'GET,POST');
					$cat->set('permission', '{"allow": {"usergroup": "all"}}');
					$cat->set('param', '{"scriptProperties":[],"headers":[],"httpResponseCode":200}');
					$cat->save();
				}
				$count = $modx->getCount('Utilrest');
				if ($count == 0) {
					if (!isset($cat) or !$cat) {
						$cat = $modx->getObject('Utilrestcategory', ['name' => 'default']);
					}
					if ($cat) {
						$r = $modx->newObject('Utilrest');
						$r->set('url', 'test');
						$r->set('snippet', '{core_path}/components/modutilities/docs/restprocessor.php');
						$r->set('category', $cat->get('name'));
						$r->save();
					}
				}
				$Utilrests = $modx->getIterator('Utilrest');
				/** @var Utilrest $rs */
				foreach ($Utilrests as $rs) {
					$cat = $rs->get('category');
					if (is_numeric($cat)) {
						$cat_ = $modx->getObject('Utilrestcategory', $cat);
						$rs->set('category', $cat_->get('name'));
						$rs->save();
					}
				}
				break;

			case xPDOTransport::ACTION_UNINSTALL:
				sendAuthorStat(['action' => 'UNINSTALL']);

				$modx->removeExtensionPackage('modutilities');
				break;

		}
	}
	// отправляет мне информацию об установках.
	// Зачем? - Я не знаю, мне просто нравится видеть что моим кодом кто-то пользуется :)
	function sendAuthorStat($data)
	{
		$curl = curl_init();
		$data = array_merge(['componentName' => 'modutilities', 'site' => $_SERVER['SERVER_NAME']], $data);

		$data = json_encode($data);
		curl_setopt_array($curl, [
			CURLOPT_URL => 'http://traineratwot.aytour.ru/component/stat',
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 10,
			CURLOPT_SSL_VERIFYHOST => FALSE,
			CURLOPT_SSL_VERIFYPEER => FALSE,
			CURLOPT_AUTOREFERER => TRUE,
			CURLOPT_FOLLOWLOCATION => TRUE,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => $data,
			CURLOPT_HEADER => 0,
		]);

		curl_exec($curl);
		curl_close($curl);
	}

	return '';
