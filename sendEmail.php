<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!empty($_POST['website'])) {
        exit; // бот
    }
    $name = $_POST['from_name'];
    $phone = $_POST['phone_number'];
    $message = $_POST['message'];

    $to = "info@smrmetall.ru";
    $subject = "Новая заявка с сайта";
    $body = "Имя: $name\nТелефон: $phone\nСообщение: $message";
    $headers = "From: info@smrmetall.ru\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8";

    if (mail($to, $subject, $body, $headers)) {
        echo json_encode(["status" => "success", "message" => "Сообщение успешно отправлено!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Ошибка при отправке сообщения."]);
    }
}
