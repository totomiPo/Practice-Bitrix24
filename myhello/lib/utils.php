<?php

namespace Bitrix\Myhello;
class Utils {
    public static function getWeather() {
        return "";
    }
}

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
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Погода</title>
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%; /* Уменьшена ширина модального окна */
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .weather {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .weather__title, .weather__time, .weather__forecast, .weather__humidity, .weather__wind {
            margin: 10px 0;
        }
    </style>
</head>
<body>

<div id="weatherModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <div class="weather">
            <h2 class="weather__title">Погода в городе <?php echo $data->name; ?></h2>
            <div class="weather__forecast">
                <span class="weather__min">Минимальная температура: <?php echo $data->main->temp_min; ?>°C</span>
                <br>
                <span class="weather__max">Максимальная температура: <?php echo $data->main->temp_max; ?>°C</span>
            </div>
            <p class="weather__humidity">Влажность: <?php echo $data->main->humidity; ?> %</p>
            <p class="weather__wind">Ветер: <?php echo $data->wind->speed; ?> км/ч</p>
        </div>
    </div>
</div>

<script>
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
</script>

</body>
</html>
