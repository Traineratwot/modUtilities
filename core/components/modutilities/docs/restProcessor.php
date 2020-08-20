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
		 var_dump($this->url);
		 var_dump($this->POST);
		 var_dump($this->PUT);
		 return '<form enctype="multipart/form-data" method="post">
			<input type="file" name="test1[0]">
			<input type="file" name="test1[1]">
			<input type="submit" value="ok">
			</form>
		';

	 }

	 public function POST()
	 {
	 	foreach ($this->FILES as $input_name => $input_value){
		    /** @var modutilitiesPostFile $v */
		    foreach ($input_value as $v){
			    var_dump($input_name,$v->toArray());
		    }
	    }
		 return 'ответ для POST запроса';
	 }
	 public function HEAD()
	 {
		 return 'это сообщение никогда не будет показано, так как для этого метода доступны только GET,POST,PUT и DELETE запросы';
	 }
 }
 //обязательно необходимо вернуть имя исполняемого класса
 return 'test';