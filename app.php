<?php

require 'test_db/db.php';

ReadResponseData();

function ReadResponseData() {
    $input = file_get_contents('php://input');
    $input = json_decode($input);
    
    if ($input && isset($input->query) && isset($input->limit)) {
        // Вызов обработчика запроса клиента
        ResponseHandler($input);
    }
    else {
        // Вызов функции отправки ответа c ошибкой
        ResponseSender(null, "HTTP/1.0 400 Bad Request");
    }
}

function ResponseHandler($input) {
    db::connect(DB_NAME);
    if (db::is_error()) {
        ResponseSender(null, "HTTP/1.0 500 Internal Server Error");
    }
    else {
        // Выполняем запрос к БД
        $response = db::query($input->query, $input->limit);
        // Проверяем успешность запроса
        if (!$response || !count($response)) $response = array("No results.");
        // Вызов функции отправки ответа
        ResponseSender($response);
    }    
}

function ResponseSender($response, $err = null) {
    if ($err) {
        header($err);
    }
    else {
        header("Content-type: application/json; charset=utf-8");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        echo(json_encode($response));
    }
}

