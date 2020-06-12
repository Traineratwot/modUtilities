<?php
	return [
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
			`id` INT(10) NULL DEFAULT NULL,
			`stats` JSON NULL DEFAULT NULL,
			`log` JSON NULL DEFAULT NULL,
			INDEX `FK__modutilitiesrest` (`id`) USING BTREE,
			CONSTRAINT `FK__modutilitiesrest` FOREIGN KEY (`id`) REFERENCES `modutil_utilrest` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
		)
		COLLATE='utf8_general_ci'
		ENGINE=InnoDB;
		",
	];