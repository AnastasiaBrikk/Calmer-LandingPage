<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['from_name'];
    $phone = $_POST['phone_number'];
    $message = $_POST['message'];

    $to = "nashua.proskuryakova.98@gmail.com"; // Email для отправки сообщений "ooo-calmer@bk.ru"
    $subject = "Новая заявка с сайта";
    $body = "Имя: $name\nТелефон: $phone\nСообщение: $message";
    $headers = "From: no-reply@calmer-spb.ru";

    if (mail($to, $subject, $body, $headers)) {
        echo "Message sent successfully!";
    } else {
        echo "Error sending message.";
    }
}
?>