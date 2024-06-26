<?php
require 'vendor/autoload.php';

spl_autoload_register(function ($class_name) {
    include 'Class/' . $class_name . '.php';
});

$database = new Database();
$db = $database->connect();
