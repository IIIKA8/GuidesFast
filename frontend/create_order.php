<?php
session_start();
require_once '../api/crud.php';
if (!isset($_SESSION['userid'])) {
    header('Location: /');
    exit;
}

if ((int) ($_SESSION['userroleid'] ?? 0) !== 2) {
    http_response_code(403);
    exit('Доступ запрещён');
}

if (
    isset($_POST['roomnumber']) && $_POST['roomnumber'] !== '' &&
    isset($_POST['amountclients']) && $_POST['amountclients'] !== ''
) {
    $roomnumber = db_esc((string) $_POST['roomnumber']);
    $amountclients = (int) $_POST['amountclients'];
    $hotelservices = db_esc((string) ($_POST['hotelservices'] ?? ''));
    $orderstatus = db_esc((string) ($_POST['orderstatus'] ?? 'создан'));
    $paymentstatus = db_esc((string) ($_POST['paymentstatus'] ?? 'не принят'));
    $datecreation = db_esc((string) ($_POST['datecreation'] ?? ''));
    insert("INSERT INTO `order` (roomnumber,amountclients,hotelservices,datecreation,paymentstatus,orderstatus) VALUES ('$roomnumber',$amountclients,'$hotelservices','$datecreation','$paymentstatus','$orderstatus')");
    header('Location: /frontend/view_2.php?tab=orders');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Новый заказ</title>
    <link rel="stylesheet" href="./bootstrap.min.css">
</head>
<body class="container py-4" style="max-width: 520px">
    <h4 class="mb-3">Создание заказа</h4>
    <p><a href="/frontend/view_2.php?tab=orders">← Назад к заказам</a></p>
    <form class="d-flex flex-column gap-2" method="POST">
        <div class="form-floating"><input name="roomnumber" id="roomnumber" class="form-control" required><label for="roomnumber">Комната / название</label></div>
        <div class="form-floating"><input name="amountclients" id="amountclients" class="form-control" type="number" value="1" required><label for="amountclients">Клиентов</label></div>
        <div class="form-floating"><input name="hotelservices" id="hotelservices" class="form-control"><label for="hotelservices">Услуги</label></div>
        <div class="form-floating"><input name="orderstatus" id="orderstatus" class="form-control" value="создан"><label for="orderstatus">Статус заказа</label></div>
        <div class="form-floating"><input name="paymentstatus" id="paymentstatus" class="form-control" value="не принят"><label for="paymentstatus">Оплата</label></div>
        <div class="form-floating"><input name="datecreation" id="datecreation" class="form-control" type="date" required><label for="datecreation">Дата</label></div>
        <button type="submit" class="btn btn-primary">Создать</button>
    </form>
</body>
</html>
