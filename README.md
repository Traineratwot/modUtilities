# modUtilities
Если вы уже пользуетесь моим компонентом вы можете оставить свой отзыв или предложения здесь

###[Опрос](https://forms.gle/mHHs27xKC4LdkRWA9)

 это компонент добавляющий разные полезные, часто используемые, и просто интересные функции, для облегчения и стандартизации програмирования на modx
 поддерживает fenom
 
   ```fenom
     //fenom
      тег    метод    аргументы...
     {util 'makeUrl' 24}
      тег    переменная
     {util 'constant->kb'} 
     ----------
       метод     модификатор  набор переменных по порядку(без ассоцативного массива)
     {'makeUrl' |  util :       [24]}
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
  $modx->util->constant->kb;         //килобайт  в байтах
  $modx->util->constant->mb;         //мегобайт  в байтах    
  $modx->util->constant->gb;         //гигобайт  в байтах
  $modx->util->constant->tb;         //теробайт  в байтах
  $modx->util->constant->min;        //минут     в секундах
  $modx->util->constant->hour;       //час       в секундах
  $modx->util->constant->day;        //день      в секундах
  $modx->util->constant->week;       //неделя    в секундах
  $modx->util->output['function'];   //сюда попадает побочный вывод некоторых функций 
 ```
#### Плагины
- **modutilities** - основной плагин расширяющий объекты класса modX и FenomX
    _срабатывает на события `OnMODXInit`,`pdoToolsOnFenomInit`,`OnWebPageInit`,`OnPageNotFound`_
- **modutilitiesPathGen** - автоматически генерирует пути для статичных ресурсов: чанков снипетов шаблонов и плагинов
    срабатывает при включении чекбокса `Статичный` и пустом поле `Статичный файл`
    _срабатывает на события `OnChunkFormPrerender`,`OnDocFormPrerender`,`OnPluginFormPrerender`,`OnSnipFormPrerender`,`OnTempFormPrerender`_

#### Функции

- **dump** - возвращает информацию переданную через аргументы с переносом строки
    обратите внимане функция возвращает форатированную строку но не выводит ее, не забудте добавиь `echo`
    _что-то среднее между echo и var_dump_
    
    ```php
    $arr = [
        'фрукт'=>'апельсин',
        'ягода'=>'арбуз',
        ];
    echo $modx->util->dump('у меня есть',$arr,8);
    //(string): у меня есть, (array): {"фрукт":"апельсин","ягода":"арбуз"}, (integer): 8
    ```

- **mb_ucfirst** - возвращает и строку с заглавной буквы для любой кодировки
   - второй параметр mod
     - FirstLetter (по умолчанию)
     - EveryWord каждое слово в сторке с заглавной букывы (разделитель слов `\s` )
     - AfterDot каждое предложение с большой буквы (разделитель предложений `[\.\!\?]` )
   - третий параметр `otherLower` уменьшать ли остальные символы
    ```php
     $modx->util->mb_ucfirst('привет'); // 'Привет'
     $modx->util->mb_ucfirst('есть, груТ',modutilities::EveryWord,true); //'Я Есть, Грут'
     $modx->util->mb_ucfirst('привет. я есть, груТ? да! да!',modutilities::AfterDot);//'Привет. Я есть, груТ? Да! Да!'
        
    ```
- **translit** - транслитерирует текст с помощью установленного у вас транслитератора alias например `yTranslit`
    если такого нет то просто транслитерирует текст как для url
    ```php
    //с установленным "gTranslit"
    $modx->util->translit('у меня есть',$isCpu = true ); //i-have
    $modx->util->cpuTranslit('у меня есть'); //u-menya-est
    $modx->util->basicTranslit('у меня есть'); //u menya est
    ```

- **console** - возвращает переданную во втором параметр инфомацию в виде js         скрипта     console.{действи}
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
    
    $modx->util->output['likeString']; //здесь будет массив с остальными словами и их процентом схожести
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

- **ifElse** - выдает первый не пустой аргумент
    _я использовал это вместе с fenom что бы не писать кучу условий_
    _синонимы: OR_ 
    ```php
    //php
    $modx->util->ifElse(0,false, null,'',' ', 'привет','кит'); //привет
    ```
    ```fenom
    //fenom
    {util 'or' 0 false null '' ' '  'привет' 'кит'}//привет
    ```


- **member** - функция для получения информации о группе пользователя
    _игнорирует контекст_
    
    - *первый аргумент* - имя/id пользователя если пустой то текущий авторезированный пользоваеть если авторизован
   - *второй аргумент* - имя/id группы если установленно то, функция выдаст массив с ролью пользователя в этой группе или `false` если его в ней нет,
    если не установленно то, функция выдаст массив групп в которых состоит пользователь и его роль в них или `false`

    - *третий аргумент* - имя/id роли, можно заполнять только вместе со вторым,
    функция выдаст `true` или `false`
 
    ```php
    //php
     $answer = $modx->util->member(1); 
     $answer = '[
                  {"groupId":"1","groupName":"Administrator","roleId":"2",    "roleName":"Super User"}
                ]';
     $answer = $modx->util->member(1,'Administrator'); 
     $answer = '{"roleId":"2","roleName":"Super User"}';
     $answer = $modx->util->member(1,1,'Super User'); 
     $answer = true;
    ```

- **plural** - функция для локализации перечисяемых объектов
    ```php
     echo $modx->util->plural(0.5,['арбуз', 'арбуза', 'арбузов']); // арбузов;
     echo $modx->util->plural(51,['арбуз', 'арбуза', 'арбузов']);  // арбуз;
     echo $modx->util->plural(-11,['арбуз', 'арбуза', 'арбузов']); // арбузов;
     echo $modx->util->plural(2,['арбуз', 'арбуза', 'арбузов']);   // арбуза;
    ```

- **convert** - функция для конфертации часто использемых едениц измерения
    - *первый аргумент* - число 
    - *второй аргумент* - тип единици измерения
    - *третий аргумент* - еденица измерения по умолчанию SI(СИ)
    - *четвертый аргумент* в какую еденицну нужно конвертировать по умолчанию best
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
    $modx->util->isEmpty($arr); // true
    ```
- **isAssoc** проверяет ассоциативный ли массив 
    _у больших массивах count() > 10 проверяет первый последний и рандомный элемент на ассоциативность_
    - фунция быстрая и не прожерливая
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
      $modx->util->isAssoc($arr1); // false
      $modx->util->isAssoc($arr2); // true
    ```
- **strTest** проверяет наличие подстрок или символов в строке
    _перый аргумент строка в которой искать остальные параметры искомые строки или массивы строк_

     - между элементами массива будет "И" 
     - между аргуметами будет "ИЛИ" 
     - чуствителен к регистру
     - не учитывает порядок
     - не использует регулярные выражения

    ```php
     $str = "Привет , ? /";
     $modx->util->strTest($str, ['п', 'р', 'т']); //true
     $modx->util->strTest($str, ['п', '5', 'т']); //false
     $modx->util->strTest($str, '6', '/');        //true
     $modx->util->strTest($str, '?', '/');        //true
     $modx->util->strTest($str, '99');            //false
     $modx->util->output['strTest']; // количество совпадений
    ```
- **getUserPhoto** почти полная копия стандартной функции $modx->user->getPhoto() но с 2 существенными плюсами
    
    - работает в fenome
    - дает больше настроек для Gravatar
   
    ```php
     //php
      /**
      * @param modUser/int    id - id пользователя если 0 то текущий
      * @param string $alt    альтернативная картика
      * @param int    $width  ширина 
      * @param int    $height высота для Gravatar = width
      * @return string 
      */
      echo $modx->util->getUserPhoto($id = 0, $alt = FALSE, $width = 128, $height = 128,$r='g', $default = '404'); //аватар текущего пользователя
      echo $modx->util->getUserPhoto(2,'/path/no_ava.png'); // заглушка так как у пользователя 2 нет аватара
    ```
    ```fenom
     //fenom
     {util 'getUserPhoto' 2 '/path/no_ava.png'}
    ```
- **getSetOption** выдает массив возможных значений колонке с типом данных set,enum
   
    ```php
    /**
     * return database set column option
     * @param object|string $table 
     * @param string $column
     * @return false|string[]
     */
    $table = $modx->newObject('modUser');
    $modx->util->getSetOption($table,'id');
    $modx->util->getSetOption('modx_users','id');
    ```
  
- **getAllTvValue** возвращает массив с уникальными значениями tv по его id, где ключ массива значение tv, а значение - массив ресурсов с этим значением 
    ```php
      $modx->util->getAllTvValue(1,'res'); // ['5'=>[2,4,5]]
      $modx->util->getAllTvValue(1,'id'); // [1=>['value'=>5,'contentid'=>1]]
   ```
    
- **getAllTvResource** возвращает массив все tv перегонного ресурса или id, где ключ массива id tv , а значение - значение tv
   ```php
      $modx->util->getAllTvResource(1,'id');      // ["1"=>"value"]
      $modx->util->getAllTvResource(1,'name');    // ["test"=>"value"]
      $modx->util->getAllTvResource(1,'caption'); // ["тестовый tv"=>"value"]
      $modx->util->getAllTvResource(1,'full');    // [["id"=>"1","name"=>"test","caption"=>"тестовый tv","value"=>"value"]]
   ```

- **getResourceChildren** возвращает массив c id дочерних элементов
   ```php
      $modx->util->getResourceChildren(1); // [2,3,4]
   ```

- **arrayToSqlIn** превращает массив в строку подходящюю для sql запроса в IN()
    ```php
        $modx->util->arrayToSqlIn(['v1','v2']); // "v1", "v2"
    ```
 - **download** скачивает и сохраняет файл. 
    - может использовать curl для скачивани больших файлов сразу в файл минуя запись в оперативную память
    - ограничивает время подключения 
     ```php
            /**
               * @param string $file
               * @param string $outPath
               * @param bool   $update
               * @param int    $timeout
               * @param bool   $useCurl
               * @return bool
               * @throws Exception
               */
              $modx->util->download($file = '', $outPath = '', $update = TRUE, $timeout = 2, $useCurl = FALSE);
    ``` 
 - **randomColor** генератор случайного цвета
    - генерирует случайный цвет в формате HSV
    - можно устанавливать пределы для всех трех состовляющих HSV
    - может возвращать как строку для css так и массив 
    - принимает текст в качестве random_seed
     ```php
       /**
         * @param string $file
         * @param string $outPath
         * @param int    $timeout
         * @return bool
         * @throws Exception
         */
         $modx->util->randomColor([
            'limits' => [
               'h'=>['min' => 40, 'max' => 60],
               's'=>['min' => 40, 'max' => 60],
               'v'=>['min' => 40, 'max' => 60],
                   ],
            'salt' => FALSE|'string'|'int',
            'format' => 'hsv'|'hex'|'rgb',
            'type' => 'css'|'',
         ]);
     ``` 
- **getIP** - возвращает ip пользователя
    ```php
      $modx->util->getIP();//127.0.0.1
    ```
- **baseExt** - возващяет расширение файла
    ```php
      $modx->util->baseExt('assets/test.json');//json
    ```
- **baseName** - возващяет имя файла без расширения 
    ```php
      $modx->util->baseName('assets/test.json');//test
    ```
- **expandBrackets** - раскрывает скобки
    ```php
      $modx->util->expandBrackets('(test)');//test
      $modx->util->expandBrackets('{test]','{',']');//test
    ```
- **updateTv** - обновляет Tv параметр, гараздо быстрее чем через объект modResource
    ```php
      $resId = 1;
      $tvId = 1;
      $modx->util->updateTv('value',$resId,$tvId);//true
    ```
  - **jsonValidate** - проверяет яаляетсяя ли строка JSON или нет, если да возвращяет массив, если нет false 
      ```php
        $modx->util->jsonValidate('["a":1]');//false
        echo $modx->util->output['jsonValidate']['input']; // '["a":1]'
        echo $modx->util->output['jsonValidate']['error']; // Syntax error
        echo $modx->util->output['jsonValidate']['result']; // false
      ```
  - **urlBuild** - создает url
      ```php
        echo $modx->util->urlBuild([
                        "scheme" => "https",
                        "host" => $_SERVER['SERVER_NAME'],
                        "path" => 'русский text',
                        "query" => ['v'=>'test'],
                        ]
            );
           //https://{HOST}/%D1%80%D1%83%D1%81%D1%81%D0%BA%D0%B8%D0%B9%20text?v=test
      ```
#### Классы
 - **Csv** 
    удобный класс для создания csv 
    особенности:
   - умеет заполнять по строкам или по столбцам 
   - устанавливать шапку
   - в случаи ошибки делает throw new Exception
   - изменять конкретную ячейку 
   - умеет читать csv
   - умеет возвращать массив по строкам колонкам и целиком
   - по умолчания настроен под "ms Exel" 
   - умеет конвертировать в Html таблицу
   - умеет конвертировать в Html список
   - fast mod позволяет писать csv на лету что на МНОГО быстрее, испорльзуйте его для записи больших файлов, файл будет записан даже при краше
   
   **быстрая запись**
   ```php
    $csv = $modx->util->csv(['mode' => 'fast', 'output_file' => 'output/output.csv']);
    $csv->addRow();
   ```
   **вывод**
   ```php
    $csv->getCell(2,1);
    $csv->toCsv();
    $csv->toHtmlTable($cls = 'table', $rainbow = FALSE); //возвращяет html талицу, 
    $csv->toHtmlList($cls = '', $delimiter = '; ', $item = 'li', $rainbow = FALSE);
    $csv->getRow(); //возвращаяет одну строку массивом и перемещает указатель на следующую, можно использовать для работы с csv в цикле
    while ($row = $csv->getRow() ){
        print_r($row);
    }
    $csv->getCol();// аналогично getRow только для колонок
    $csv->getAssoc('row');// возвращяет таблицу в виде ассоциативного массива где ключ - 1 первый елемент строки, а значения все остальные значение строки или второй елемент строки
    $csv->getAssoc('head'|'cell');// возвращяет таблицу в виде ассоциативного массива где ключ - заголовок столбца а значения все остальные значение столбца
   ```
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
     echo $csv->toHtmlTable('class',/* rainbow */ true);
     //or
     $csv_string = "1;2;3 \n a,b,s";
     $csv = $modx->util->csv();
     $csv->csv = $csv_string;
     echo $csv->toHtmlList('class');

    ```

 - **Rest** 
 
   ###### ДЛЯ РАБОТЫ ТРЕБУЕТСЯ ВКЛЮЧИТЬ "Дружественные URL"
    позволяет быстро и безопасно настроить rest для вашего сайта
    особенности:
   - полностью кастомные url
   - работает как со снипетами так и с файлами(процессорами)
   - basic auth (в качестве пользователя использует пользователей modX)
   - гибкая настройка безопасности по пользователям, по группам и ролям пользователей, по http методам и ip
   - поддержка почти всех REST методов
   - удобный класс для работы с $_FILES
   - интерфейс в админке
   - лог 1000 последних запросов (настройка modUtilRestlogLimit)

    пример json массива с правилами безопасноти
    ```json
     {
         "deny": {
             "userIds": [
                 2
             ],
             "usergroup": 2,
             "ip": ["12.12.485.87","45.45.87.89"],
         },
         "allow": {
             "userIds": [
                 1
             ],
             "usergroup": {"administrator":0}, 
            }
     }
    ```
# Frontend
```js
modx.util.mouse = {mouseX: 156, mouseY: 321, pageX: 156, pageY: 321}
modx.util.constant = {kb: 1024, min: 60, mb: 1048576, gb: 1073741824, tb: 1099511627776, …}
//аналогично $modx->util->mbUcfirst()
modx.util.mbUcfirst()

//name - имя cookie
//value - значиние cookie
modx.util.setCookie(name, value, options = {path: '/'})
//name - имя cookie
//id id cookie
modx.util.getCookie(name, id, json = false)
//name - имя cookie
modx.util.deleteCookie(name)
//str - строка
// L - тело регулярного вырожения например: "\s", для левой части
// R - тело регулярного вырожения например: "\s", для правой части, если не указанно то L
// replace на что заменять
modx.util.trim(str = '', L = 's', R = false, replace = '')
//функция для вставки на страницу js кода, после загрузки скрипта срабатывает событие "modx.util.included"
// source - ссылка на скрипт 
// name - удобное для вас имя с которым придет событие 
// parent_selector - после какого элемента вставить script по умолчанию head
modx.util.include(source, name = false, parent_selector = false, async = true);
//пременная для определения устройства принимает значения "mobile,"tabled","pc"
// определяет по ширине экрана 
modx.util.device
//класс содержащий информацию о пользователе если он авторезирован
//также этот клас позволяt хранить и использовать "настройки" пользователя
//например сохранять открытые спойлеры или данные формы после перезагрузки страници
modx.user
//устанавливает значение "настройки"
modx.user.setSetting(key,value)
//получает значение "настройки"
modx.user.getSetting(key)
//получает все значения настроек
modx.user.getSettings()
//класс содержащий информацию о текущей странице
modx.resource
```