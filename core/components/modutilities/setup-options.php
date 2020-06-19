<?php
	$db = [
		'modutil_utilrestcategory' => "CREATE TABLE `modutil_utilrestcategory` (
			`id` INT(10) NOT NULL AUTO_INCREMENT,
			`name` VARCHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`permission` JSON NULL DEFAULT NULL,
			`param` JSON NULL DEFAULT NULL,
			`allowMethod` SET('GET','POST','PUT','DELETE','PATH','CONNECT','HEAD','OPTIONS','TRACE') NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`BASIC_auth` TINYINT(1) NULL DEFAULT NULL,
			PRIMARY KEY (`id`) USING BTREE
		)
		COLLATE='utf8_general_ci'
		ENGINE=InnoDB
		;
		insert into modutil_utilrestcategory (`id`, `name`,'allowMethod',`permission`,`param`) values (1,'default','GET,POST,PUT,DELETE','{\"allow\": \"all\"}','{\"headers\": [], \"httpResponseCode\": 200, \"scriptProperties\": []}');
		",
		'modutil_utilrest' => "CREATE TABLE `modutil_utilrest` (
				`id` INT(10) NOT NULL AUTO_INCREMENT,
				`permission` JSON NULL DEFAULT NULL,
				`url` VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`snippet` LONGTEXT NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`param` JSON NULL DEFAULT NULL,
				`allowMethod` SET('GET','POST','PUT','DELETE','PATH','CONNECT','HEAD','OPTIONS','TRACE') NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`BASIC_auth` TINYINT(1) NULL DEFAULT '0',
				`category` INT(10) NULL DEFAULT '1',
				PRIMARY KEY (`id`) USING BTREE,
				UNIQUE INDEX `url` (`url`) USING BTREE,
				INDEX `FK_modutil_utilrest_modutil_utilrestcategory` (`category`) USING BTREE,
				CONSTRAINT `FK_modutil_utilrest_modutil_utilrestcategory` FOREIGN KEY (`category`) REFERENCES `modutil_utilrestcategory` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION
			)
			COLLATE='utf8_general_ci'
			ENGINE=InnoDB
			;
		",
		'modutil_utilreststats' => "CREATE TABLE `modutil_utilreststats` (
			`id` INT(10) NOT NULL AUTO_INCREMENT,
			`rest_id` INT(10) NOT NULL DEFAULT '0',
			`input` LONGTEXT NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`output` LONGTEXT NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`user` LONGTEXT NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`time` FLOAT(12,6) UNSIGNED NULL DEFAULT NULL,
			`datetime` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (`id`) USING BTREE,
			INDEX `FK_modutil_utilreststats_modutil_utilrest` (`rest_id`) USING BTREE,
			CONSTRAINT `FK_modutil_utilreststats_modutil_utilrest` FOREIGN KEY (`rest_id`) REFERENCES `ay`.`modutil_utilrest` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
		)
		COLLATE='utf8_general_ci'
		ENGINE=InnoDB
		",
	];
	/** @var array $options */
	switch ($options[xPDOTransport::PACKAGE_ACTION]) {
		case xPDOTransport::ACTION_UPGRADE:
		case xPDOTransport::ACTION_INSTALL:
		$modx->addPackage('modUtilities',MODX_CORE_PATH.'components/modutilities/model/','modutil_');
			$manager = $modx->getManager();
			$manager->createObjectContainer('utilrestcategory');
			$manager->createObjectContainer('utilreststats');
			$manager->createObjectContainer('Utilrest');
			try {
				$result = $this->modx->query("SELECT 1 FROM modutil_utilrestcategory LIMIT 1"); // формальный запрос
			} catch (Exception $e) {
				$result = FALSE;
			}
			if ($result !== FALSE) {
					$this->modx->query("insert into modutil_utilrestcategory (`id`, `name`,'allowMethod',`permission`,`param`) values (1,'default','GET,POST,PUT,DELETE','{\"allow\": \"all\"}','{\"headers\": [], \"httpResponseCode\": 200, \"scriptProperties\": []}');");
			}
			foreach ($db as $table => $create) {
				try {
					$result = $this->modx->query("SELECT 1 FROM $table LIMIT 1"); // формальный запрос
				} catch (Exception $e) {
					$result = FALSE;
				}
				if ($result === FALSE) {
					$create = explode(';',$create);
					foreach ($create as $q){
						$this->modx->query($q);
					}
				}
			}
			break;

		case xPDOTransport::ACTION_UNINSTALL:
			break;
	}
	return TRUE;