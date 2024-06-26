<?php
require_once 'incfiles/config.php';

// Проверяем наличие класса и метода, и подключаем его
$class = ucfirst(htmlspecialchars($_GET['route']));
$action = htmlspecialchars($_GET['action']);

if(file_exists('Class/'.$class.'.php')) {

    // Поддерживаемые классы и методы
    $classArr = [
        "Advert" => [
            "showAd",
            "showAllAds",
            "storeAd"
        ]
    ];

    // Если все норм, вызываем данный класс и метод
    if(in_array($action,$classArr[$class])){
        $database = new Database();
        $db = $database->connect();
        $class = new $class($db);
        $class->$action();
    }


}

