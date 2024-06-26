<?php
require_once 'incfiles/config.php';
?>
<h1>Простейшее API для вывода, добавления обьявления</h1>

<a href="api.php?page=advert&action=showAd&id=<?=rand(1,5)?>">Показать случайное обьявление обьявление</a><br>
<a href="api.php?page=advert&action=showAllAds">Показать все обьявления</a><br>