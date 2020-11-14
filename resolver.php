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
				$modx->addPackage('modutilities', MODX_CORE_PATH . 'components/modutilities/model/');

				$modx->query("RENAME TABLE modutil_utilrest TO {$prefix}utilrest");
				$modx->query("RENAME TABLE modutil_utilrestcategory TO {$prefix}utilrest");
				$modx->query("RENAME TABLE modutil_utilreststats TO {$prefix}utilrest");
				$modx->query("update {$prefix}namespaces set name = 'modutilities' where `name` like 'modUtilities'");
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
				break;
			case xPDOTransport::ACTION_INSTALL:
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
						$r->set('category', $cat->get('id'));
						$r->save();
					}
				}

				$modx->query("ALTER TABLE `{$prefix}utilrest`
				CHANGE COLUMN `url` `url` VARCHAR(100) NULL DEFAULT NULL AFTER `permission`,
				CHANGE COLUMN `category` `category` VARCHAR(50) NULL DEFAULT NULL AFTER `BASIC_auth`;");
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
				break;

		}
	}
	return '';