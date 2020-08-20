<?php
	if (!isset($modx) and $object->xpdo) {
		$modx =& $object->xpdo;
	}

	if ($modx instanceof xPDO) {
		/** @var array $options */
		switch ($options[xPDOTransport::PACKAGE_ACTION]) {
			case xPDOTransport::ACTION_UPGRADE:
				$prefix = $modx->config['table_prefix'];
				$modx->query("RENAME TABLE modutil_utilrest TO {$prefix}utilrest");
				$modx->query("RENAME TABLE modutil_utilrestcategory TO {$prefix}utilrest");
				$modx->query("RENAME TABLE modutil_utilreststats TO {$prefix}utilrest");
				$p = $modx->getObject('modPlugin',['name'=>'utilities']);
				if($p){
					if(strpos($p->get('content'),'utilities') !== false){
						$p->remove();
					}
				}
				$p = $modx->getObject('modPlugin',['name'=>'UtilitiesPathGen']);
				if($p){
					if(strpos($p->get('content'),'modutilities') !== false){
						$p->remove();
					}
				}
			case xPDOTransport::ACTION_INSTALL:
				$modx->addPackage('modutilities', MODX_CORE_PATH . 'components/modutilities/model/');
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
					if(!isset($cat) or !$cat) {
						$cat = $modx->getObject('Utilrestcategory', ['name' => 'default']);
					}
					if($cat) {
						$r = $modx->newObject('Utilrest');
						$r->set('url', 'test');
						$r->set('snippet', '{core_path}/components/modutilities/docs/restprocessor.php');
						$r->set('category', $cat->get('id'));
						$r->save();
					}
				}
				break;

			case xPDOTransport::ACTION_UNINSTALL:
				break;

		}
	}
	return '';