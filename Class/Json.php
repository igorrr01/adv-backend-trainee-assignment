<?php

class Json{

    public function __construct()
    {
        header("Access-Control-Allow-Origin: http://localhost/");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST");
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    }

    public function sendJson($json)
    {
        echo json_encode($json);
    }

}