## modUtilities
 это компонент добавляющий разные полезные, часто используемые, и просто интересные функции, для облегчения и стандартизации програмирования на modx
 поддерживает fenom
 
   ```fenom
  //fenom
   тег    метод    аргументы...
  {util 'makeUrl' 24}
   тег    переменная
  {util 'constant->kb'} 
  ```

чтобы работали подсказки в IDE вы можете добавить этот фрагмент в класс 'modX'
это точно никак не навредит вашему проекту
```
/**
* @var utilities
*/
public $util;
```

#### Константы и переменные
 ```php
$modx->util->constant->kb       //килобайт  в байтах
$modx->util->constant->mb       //мегобайт  в байтах    
$modx->util->constant->gb       //гигобайт  в байтах
$modx->util->constant->tb       //теробайт  в байтах
$modx->util->constant->min      //минут     в секундах
$modx->util->constant->hour     //час       в секундах
$modx->util->constant->day      //день      в секундах
$modx->util->constant->week     //неделя    в секундах
$modx->util->output['function']   //сюда попадает побочный вывод некоторых функций 
 ```
#### Плагины
- **modUtilities** - основной плагин расширяющий объекты класса modX и FenomX
    _срабатывает на события `OnMODXInit`,`pdoToolsOnFenomInit`_
- **modUtilitiesPathGen** - автоматически генерирует пути для статичных ресурсов: чанков снипетов шаблонов и плагинов
    срабатывает при включении чекбокса `Статичный` и пустом поле `Статичный файл`
    _срабатывает на события `OnChunkFormPrerender`,`OnDocFormPrerender`,`OnPluginFormPrerender`,`OnSnipFormPrerender`,`OnTempFormPrerender`_

#### Функции

- **print_n** - возвращает информацию переданную через аргументы с переносом строки
    обратите внимане функция возвращает форатированную строку но не выводит ее, не забудте добавиь `echo`
    _что-то среднее между echo и var_dump_
    ```php
    $arr = [
        'фрукт'=>'апельсин',
        'ягода'=>'арбуз',
        ];
    echo $modx->util->print_n('у меня есть',$arr,8);
    //(string): у меня есть, (array): {"фрукт":"апельсин","ягода":"арбуз"}, (integer): 8
    ```

- **mb_ucfirst** - возвращает и строку с заглавной буквы для любой кодировки
    _функция простая но объявлять ее в каждом снипете не хочется_
    ```php
    $modx->util->mb_ucfirst('у меня есть'); //У меня есть
    ```
- **translit** - транслитерирует текст с помощью установленного у вас транслитератора alias например `yTranslit`
    если такого нет то просто транслитерирует текст как для url
    _~~моя любимая функция~~_
    ```php
    //с установленным "yTranslit"
    $modx->util->translit('у меня есть'); //i-have
    $modx->util->cpuTranslit('у меня есть'); //u-menya-est
    $modx->util->basicTranslit('у меня есть'); //u menya est
    ```

- **console** - возвращает переданную во втором параметр инфомацию в вид js         скрипта     console.{действи}
    обратите внимане функция возвращает форатированную строку но не выводит ее, не забудте добавиь `echo`
    _полезно при работе на рабочем сайте для вывда debug информации_
    
    ```php
    $arr = [
        'фрукт'=>'апельсин',
        'ягода'=>'арбуз',
        ];
    echo $modx->util->console('log','у меня есть'); 
    //<script>console.log('(string): у меня есть');</script>
    echo $modx->util->console('debug',$arr); 
    //<script>console.debug({"фрукт":"апельсин","ягода":"арбуз"});</script>
    echo $modx->util->console('table',$arr); 
    //<script>console.table({"фрукт":"апельсин","ягода":"арбуз"});</script>
    ```

- **dateFormat** - меняет формат даты
    _полезно если приходится проделывать это много раз_
    ```php
    $modx->util->dateFormat('d.m.Y','29.01.2020','Y-m-d');//2020-01-29
    ```

- **rawText** - оставляет в строке только буквы и цифры
    _полезно когда нужно сравнить две строки с не большими отличиями_
    ```php
    $modx->util->rawText('Abs_#)/\(_De');//absde
    ```
- **likeString** - сравнивает две строки либо массив строк со строкой
    и выводит самую похожую строку и процент похожести
    ```php
    $arr = [
        'Пивет',
        'Прлвeт',
        'Приет',
        'тивирТ',
        'привт',
        'досвидания',
    ];
    var_dump($modx->util->likeString($arr,'Привет'));
    // array (size=3)
    //   'one' => string 'Приет' (length=10)
    //   'two' => string 'Привет' (length=12)
    //   'score' => float 90,909090909091
    
    $modx->util->data['likeString']; //здесь будет массив с остальными словами и их процентом схожести
    ```

- **makeUrl** - делает тоже что и обычный makeUrl только умнее: ссылка всегда начинается с '/', если страница равна ссылке подставляется параметр alt по умолчанию '#top' 
    ```php
    //php
    $modx->util->makeUrl(1,'#top');
    ```
    ```fenom
    //fenom
    {util 'makeUrl' 24}
    ```

- **or** - выдает первый не пустой аргумент
    _я использовал это вместе с fenom что бы не писать кучу условий_
    
    ```php
    //php
    $modx->util->or(0,false, null,'',' ', 'привет','кит'); //привет
    ```
    ```fenom
    //fenom
    {util 'or' 0 false null '' ' '  'привет' 'кит'}//привет
    ```


- **member** - функция для получения информации о группе пользователя

    *первый аргумент* - id пользователя если пустой то текущий авторезированный пользоваеть если авторизован

    *второй аргумент* - имя группы если установленно то, функция выдаст массив с ролью пользователя в этой группе или `false` если его в ней нет
    если yt установленно то, функция выдаст массив групп в которых состоит пользователь и его роль в них или `false`

    *третий аргумент* - можно заполнятьтолько вместе со вторым,
    функция выдаст `true` или `false`
 
    ```php
    //php
   $answer = $modx->util->member(1); 
   $answer = [
                {"groupId":"1","groupName":"Administrator","roleId":"2",    "roleName":"Super User"}
              ]
   $answer = $modx->util->member(1,'Administrator'); 
   $answer = {"roleId":"2","roleName":"Super User"}
   $answer = $modx->util->member(1,1,'Super User'); 
   $answer = true
    ```

- **plural** - функция для локализации перечисяемых объектов
    ```php
    echo $modx->util->plural(0.5,['арбуз', 'арбуза', 'арбузов']); // арбузов;
	echo $modx->util->plural(51,['арбуз', 'арбуза', 'арбузов']);  // арбуз;
	echo $modx->util->plural(-11,['арбуз', 'арбуза', 'арбузов']); // арбузов;
	echo $modx->util->plural(2,['арбуз', 'арбуза', 'арбузов']);   // арбуза;
    ```

- **convert** - функция для конфертации часто использемых едениц измерения
    *первый аргумент* - число 
    *второй аргумент* - тип единици измерения
    *третий аргумент* - еденица измерения по умолчанию SI(СИ)
    *четвертый аргумент* в какую еденицну нужно конвертировать по умолчанию best
    ```php
	echo $modx->util->convert(4000,'byte');                 // [3.9,"kb"]
	echo $modx->util->convert(10,'mass',  'T', 'kg');       // [10000,"kg"]
	echo $modx->util->convert(1000000,'length','SI', 'km'); // [1000,"km"]
	echo $modx->util->convert(0.5,'time',  'h', 'min');     // [30,"min"]
    ```

- **empty** - проверяет пустой ли многомерный массив считает пустыми занчения '',null и пустые массивы
    ```php
    $arr = [
        [
            [
                null
            ],
            ''
        ]
    ];
    $modx->util->empty($arr) // true
    ```
- **isAssoc** проверяет ассоциативный ли массив (не прожорливый)
    _у больших массивах count() > 10 проверяет первый последний и рандомный элемент на ассоциативность_
     ```php
    $arr1 = [
        '0'=>2,
        3,
        4
    ];
    $arr2 = [
        'a'=>2,
        'b'=> 3,
         2=>8
    ];
    $modx->util->isAssoc($arr1) // false
    $modx->util->isAssoc($arr2) // true
    ```
- **strTest** проверяет наличие подстрок или символов в строке
    _перый аргумент строка в которой искать остальные параметры искомые строки или массивы строк_

     - между элемнтами массива будет "И" 
     - между аргуметами будет "ИЛИ" 
     - чуствителе к регистру
     - не учитывает порядо
     - не использует регулярные выражения

    ```php
	$str = "Привет , ? /";
	$modx->util->strTest($str, ['п', 'р', 'т']) //true
	$modx->util->strTest($str, ['п', '5', 'т']) //false
	$modx->util->strTest($str, '6', '/')        //true
	$modx->util->strTest($str, '?', '/')        //true
    $modx->util->strTest($str, '99')            //false
    $modx->util->output['strTest'] // колличество совпадений
    ```
#### Классы
 - **Csv** 
    удобный класс для создания csv 
    особенности:
   - умеет заполнять по строкам или по столбцам 
   - устанавливать шапку
   - изменять конкретную ячейку 
   - умеет читать csv
   - по умолчания настроен под "ms Exel" 
   - умеент ковертировать в Html таблицу

   **запись**
   ```php
    $csv = $modx->util->csv();
    $csv->setHead('c','b','a');
    $csv->addRow([
          'a'=>'1',
          'b'=>'2',
          'c'=>'3',
        ]);
        $csv->addRow(5,6,7);
        $csv->setCell('b',0,4444);
    echo $csv;
    /**
    c;b;a
    3;4444;1
    5;6;7
    */
    //OR
    $csv = $modx->util->csv();
    $csv->setHead('c','b','a');
    $csv->addCol([
        'a'=>'1',
         'b'=>'2',
          'c'=>'3',
        ]);
        $csv->addCol(5,6,7);
    echo $csv->toCsv();
    /**
    a;1;7
    b;2;6
    c;3;5
    */
    ```
    **чтение**
    ```php
    $csv = $modx->util->csv();
    $csv->readCsv(MODX_BASE_PATH.'table.csv');
    echo $csv->getCell(1,1);
    //or
    $csv = $modx->util->csv();
	$csv->csv = MODX_BASE_PATH.'table.csv';
    echo $csv;
    //or
    $csv = $modx->util->csv();
    $csv->csv = MODX_BASE_PATH.'table.csv';
    $csv->addRow(5,6,7);
    echo $csv;
    //or
    $fn = fopen(MODX_BASE_PATH.'table.csv');
    $csv = $modx->util->csv();
	$csv->csv = $fn;
    echo $csv;
    //or
    $csv_string = "1;2;3 \n a,b,s";
    $csv = $modx->util->csv();
	$csv->csv = $csv_string;
	echo $csv->toHtml('class');
    ```