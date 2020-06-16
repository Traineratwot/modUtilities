<?php
 class test extends modUtilRestProcessor {

	 /**
	  * массив с заголовкам
	  * @var array
	  */
	 public $headers = [];
	 public function initialize()
	 {
	 	'здесь можно проверить входящие данные или изменить заголовки';
		 return TRUE;
		 return FALSE;
	 }

	 public function GET()
	 {
		 return 'ответ для браузера';
	 }

	 public function POST()
	 {
		 return 'ответ для POST запроса';
	 }
	 public function HEAD()
	 {
		 return 'это сообщение никогда не будет показано, так как для этого метода доступны только GET,POST,PUT и DELETE запросы';
	 }
 }
 //обязательно необходимо вернуть имя исполняемого класса
 return 'test';