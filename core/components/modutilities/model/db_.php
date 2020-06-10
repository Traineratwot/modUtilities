<?php
	return [
		'modutil_utilrest' => "CREATE TABLE `modutil_utilrest` (
				`id` INT(10) NOT NULL AUTO_INCREMENT,
				`permission` JSON NULL DEFAULT NULL,
				`url` VARCHAR(50) NOT NULL COLLATE 'utf8_general_ci',
				`snippet` LONGTEXT NOT NULL COLLATE 'utf8_general_ci',
				`param` JSON NULL DEFAULT NULL,
				PRIMARY KEY (`id`) USING BTREE,
				UNIQUE INDEX `url` (`url`) USING BTREE
			)
			COLLATE='utf8_general_ci'
			ENGINE=InnoDB;
		",
		'modutil_utilreststats' => "CREATE TABLE `modutil_utilreststats` (
			`id` INT(10) NULL DEFAULT NULL,
			`stats` JSON NULL DEFAULT NULL,
			`log` JSON NULL DEFAULT NULL,
			INDEX `FK__modutilitiesrest` (`id`) USING BTREE,
			CONSTRAINT `FK__modutilitiesrest` FOREIGN KEY (`id`) REFERENCES `modutil_utilrest` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
		)
		COLLATE='utf8mb4_0900_ai_ci'
		ENGINE=InnoDB;
		",
		'modutil_utilrestcategory' => "CREATE TABLE `modutil_utilrestcategory` (
			`id` INT(10) NOT NULL AUTO_INCREMENT,
			`name` VARCHAR(20) NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
			`permission` JSON NULL DEFAULT NULL,
			`param` JSON NULL DEFAULT NULL,
			`allowMethod` SET('GET','POST','PUT','DELETE','PATH','CONNECT','HEAD','OPTIONS','TRACE') NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`BASIC_autch` TINYINT(1) NULL DEFAULT NULL,
			PRIMARY KEY (`id`) USING BTREE
		)
		COLLATE='utf8mb4_0900_ai_ci'
		ENGINE=InnoDB
		AUTO_INCREMENT=2
		;
		insert into modutil_utilrestcategory (`id`, `name`) values (1,'default');
		",
	];