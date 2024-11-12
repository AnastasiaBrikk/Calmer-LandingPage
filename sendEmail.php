<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['from_name'];
    $phone = $_POST['phone_number'];
    $message = $_POST['message'];

    $to = "ooo-calmer@bk.ru";
    $subject = "Новая заявка с сайта";
    $body = "Имя: $name\nТелефон: $phone\nСообщение: $message";
    $headers = "From: info@calmer-spb.ru";

    if (mail($to, $subject, $body, $headers)) {
        echo json_encode(["status" => "success", "message" => "Сообщение успешно отправлено!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Ошибка при отправке сообщения."]);
    }
}
