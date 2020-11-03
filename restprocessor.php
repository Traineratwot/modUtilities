<?php

	class test extends modUtilRestProcessor
	{

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
			return '
			<H1>Демонстрационный тест REST процессора</H1>
			<h4>путь: core/components/modutilities/docs/restprocessor.php </h4>
			<h5>эта форма отправить на этот же url post запрос</h5>
			<form enctype="multipart/form-data" method="post">
				<input type="file" name="test1[0]">
				<input type="file" name="test1[1]">
				<input type="submit" value="ok">
			</form>
			<span>$this->GET  : </span>'.$this->util->dump($this->GET).'
			<span>$this->POST : </span>'.$this->util->dump($this->POST).'
			<span>$this->url  : </span>'.$this->util->dump($this->url).'
          ';

		}

		public function POST()
		{
			foreach ($this->FILES as $input_name => $input_value) {
				/** @var modutilitiesPostFile $v */
				foreach ($input_value as $v) {
					print_r($input_name, $v->toArray());
				}

			}
			$file = $this->FILES['test1'][0];
			/** @var modutilitiesPostFile|null $file */
			if($file) {
				echo $file->fromCsv()->toHtmlTable();
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