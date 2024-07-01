# Определение кастомных модулей

Продукты *"Битрикс 24"* и *"1С-Битрикс: Управление сайтом"* имеют модульную структуру.

**Модуль** ⎯ объёмная часть программного кода, отвечающая за определённый функционал на сайте. Каждый модуль отвечает за управление определенными элементами и параметрами портала: информационным наполнением и структурой, форумами, рекламой, рассылкой, распределением прав между группами пользователей, сбором статистики посещений, оценкой эффективности рекламных кампаний и так далее.

Примеры модулей (стандартных), которые можно присоединить к проекту: [*Администрирование > Настройки > Настройки продукта > Модули*].  
  
![Модули в разделе Администрирование](https://github.com/totomiPo/Practice-Bitrix24/blob/main/src/img/Стандартные%20модули.png)

> Установлен – модуль и его элементы доступны для использования;  
> Не установлен – модуль не доступен для использования в системе.

Файлы стандартных модулей находятся в **/bitrix/admin/module_admin.php**.

Если модули "из коробки" не удовлетворяют некоторым требованиям или не покрывают весь необходимый функционал, то можно создать собственные **кастомные модули**. Файлы кастомных модулей находятся в **/bitrix/admin/partner_modules.php**  
Все кастомные модули должны иметь ID модуля.  
  
**ID модуля** ⎯ полный код партнерского модуля, который задается в формате: **код_партнера.код_модуля**.

Часть *код_партнера* постоянна для партнера. Часть *код_модуля* вводится партнером при добавлении нового модуля. Эти коды должны быть алфавитно-цифровыми, но первым символом не может быть цифра, и код неким образом должен соответствовать сути модуля. Например, для модуля форума желательно задать код forum. Тогда полный код будет mycompany.forum.  
  
> Использование точки для разделения кода партнера и кода модуля необходимо, иначе ваш модуль не будет виден в списке установленных решений Marketplace, а попадет в список системных модулей, что является некорректной ситуацией.
 
---
### Создание модуля для тестирования гипотезы

Проведем эксперимент. Создадим кастомный модуль (отображение погоды) и добавим его в систему под разными названиями, чтобы проверить гипотезу о том, что название модуля влияет на добавление его в конкретную директорию.  


#### Описание модуля

Модули имеют следующую стандартную файловую структуру. 

> include.php  
> options.php  
> install  
> > index.php  
> > version.php
>  
> lib  
> > utils.php  
  
В реальности разработчики сторонних модулей используют чаще всего модуль как инсталлятор для своих либо кастомизированных компонентов. Основной файл, код которого отвечает собственно за инсталляцию/деинсталляцию модуля, ⎯ это **/install/index.php**.  

```php
Class myhello extends CModule
    {
    var $MODULE_ID = "myhello";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;
    var $MODULE_GROUP_RIGHTS = "Y";

    public function __construct()
    {
        $arModuleVersion = array();

        include(__DIR__.'/version.php');

        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

        $this->MODULE_NAME = "Погода";
        $this->MODULE_DESCRIPTION = "Этот модуль показывает погоду в Москве";
    }

    function DoInstall()
    {
        $this->InstallFiles();
        $this->InstallDB(false);
    }

    function DoUninstall()
    {
        ;$this->UnInstallDB(false);
    }
}
```
В этом файле декларируется новый класс — класс нашего модуля как потомок CModule. Далее идет определение конструктора, в которой происходит определение переменных для вывода информации о модуле в списке модулей Bitrix24. Метод *DoInstall()* вызывается при установке модуля из Панели управления, метод *DoUninstall()*, соответственно, при деинсталляции модуля.  
  
Файл **/lib/utils.php** содержит основной функционал модуля, в текущем случае вывод погоды.  
```php
$apiKey = "1291a5ecb1b186fe5acce6c23714408a";
$cityId = "524901";
$apiUrl = "https://api.openweathermap.org/data/2.5/weather?id=" . $cityId . "&lang=ru&units=metric&APPID=" . $apiKey;

$options = [
    "http" => [
        "header" => "Content-Type: application/json\r\n",
        "method" => "GET",
        "ignore_errors" => true
    ],
    "ssl" => [
        "verify_peer" => false,
        "verify_peer_name" => false,
    ]
];

$context = stream_context_create($options);
$response = file_get_contents($apiUrl, false, $context);

if ($response === FALSE) {
    die('Error occurred');
}

$data = json_decode($response);
```
Получение актуальных данных о погоде через API.  
```js
let modal = document.getElementById("weatherModal");

    let span = document.getElementsByClassName("close")[0];

    span.onclick = function() {
        modal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    }

    modal.style.display = "block";

    fetch('https://api.openweathermap.org/data/2.5/weather?id=524901&lang=ru&appid=1291a5ecb1b186fe5acce6c23714408a')
        .then(function(resp) { return resp.json() })
        .then(function(data) {
            document.querySelector('.weather__title').textContent = "Погода в городе " + data.name;
            document.querySelector('.weather__time').textContent = new Date().toLocaleTimeString();
            document.querySelector('.weather__date').textContent = new Date().toLocaleDateString('ru-RU', { day: 'numeric', month: 'long', year: 'numeric' });
            document.querySelector('.weather__status').textContent = data.weather[0].description;
            document.querySelector('.weather__min').textContent = data.main.temp_min + '°C';
            document.querySelector('.weather__max').textContent = data.main.temp_max + '°C';
            document.querySelector('.weather__humidity').textContent = "Влажность: " + data.main.humidity + ' %';
            document.querySelector('.weather__wind').textContent = "Ветер: " + data.wind.speed + ' км/ч';
        })
        .catch(function() {
        });
```  
Формирование полученных данных для вывода в модальное окно после загрузки страницы. 

---------
#### 1. Определение кастомного модуля к системным  
Созданный модуль загрузим в папку **/local/modules/** с названием как *weatherInMoscow*.  
  
![Определение к стандартным модулям](https://github.com/totomiPo/Practice-Bitrix24/blob/main/src/img/Определение%20модуля%20к%20стандартным.gif)  

Как видно из примера, кастомный модуль определился к системным по причине того, что не соблюдается ID модуля, как **код_партнера.код_модуля**.

#### 2. Определение модуля к кастомным   
Созданный модуль загрузим в папку **/local/modules/** с названием как *dub.weather*.  

![Определение к кастомным модулям](https://github.com/totomiPo/Practice-Bitrix24/blob/main/src/img/Определение%20модуля%20к%20кастомным.gif)    
  
В этом случае, когда соблюдается формат ID модуля, то определяется в верную категорию - кастомные модули.
  
Проверим корректную работу загруженного модуля. Загрузим его в систему, чтобы можно было использовать функционал. Добавим вывод модального окна с погодой в раздел сайта *Контакты*. Очевидно, модуль работает так, как и задумано.  
  
![Работа кастомного модуля](https://github.com/totomiPo/Practice-Bitrix24/blob/main/src/img/Проверка%20работы%20модуля.gif)  

-----  
### Выводы  
В процессе загрузки модуля мы убедились, что вид идентификатора (ID) определяет, к какой категории будет отнесён модуль: стандартным или кастомным. Если есть такая возможность, в систему можно загрузить вредоносные или уязвимые модули, что представляет угрозу для безопасности. Поэтому важно контролировать, где находятся кастомные модули, и согласовывать загрузку модулей в систему.
  
#### Полезные ссылки
- <https://clck.ru/3Bcx7v>  
- <https://clck.ru/3BcxBa>  
- <https://clck.ru/3BcxEz>  
- <https://clck.ru/3BcxNL>  
