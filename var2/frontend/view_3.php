<?php
session_start();
require_once '../api/crud.php';
if (!isset($_SESSION['userid'])) {
    header('Location: /');
    exit;
}

$me = select('SELECT * FROM user WHERE userid=' . (int) $_SESSION['userid']);
if (!$me || !isset($me[0]) || (int) $me[0]['userroleid'] !== 3) {
    http_response_code(403);
    exit('Доступ запрещён');
}

$tab = $_GET['tab'] ?? 'orders';
if ($tab !== 'orders') {
    $tab = 'orders';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_order'])) {
        $oid = (int) ($_POST['orderid'] ?? 0);
        $orderstatus = db_esc((string) ($_POST['orderstatus'] ?? ''));
        $roomnumber = db_esc((string) ($_POST['roomnumber'] ?? ''));
        $amountclients = (int) ($_POST['amountclients'] ?? 0);
        $hotelservices = db_esc((string) ($_POST['hotelservices'] ?? ''));
        $paymentstatus = db_esc((string) ($_POST['paymentstatus'] ?? ''));
        $datecreation = db_esc((string) ($_POST['datecreation'] ?? ''));
        if ($oid > 0) {
            update("UPDATE `order` SET orderstatus='$orderstatus', roomnumber='$roomnumber', amountclients=$amountclients, hotelservices='$hotelservices', paymentstatus='$paymentstatus', datecreation='$datecreation' WHERE orderid=$oid AND paymentstatus='принят'");
        }
        header('Location: /frontend/view_3.php');
        exit;
    }
    if (isset($_POST['delete_order'])) {
        $id = (int) ($_POST['orderid'] ?? 0);
        if ($id > 0) {
            delete("DELETE FROM `order` WHERE orderid=$id AND paymentstatus='принят'");
        }
        header('Location: /frontend/view_3.php');
        exit;
    }
}

$edit_order = null;
if (!empty($_GET['edit_order'])) {
    $eid = (int) $_GET['edit_order'];
    $rows = select("SELECT * FROM `order` WHERE orderid=$eid AND paymentstatus='принят'");
    $edit_order = $rows[0] ?? null;
}

$orders = select("SELECT * FROM `order` WHERE paymentstatus='принят' ORDER BY orderid");

function h3($s)
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Исполнитель</title>
    <link rel="stylesheet" href="./bootstrap.min.css">
</head>
<body class="bg-light">
<a href="/logout.php" class="btn btn-sm btn-outline-secondary position-fixed top-0 end-0 m-3">Выход</a>
<div class="container py-4">
    <h4 class="mb-3">Панель исполнителя</h4>
    <p class="text-muted small">Отображаются только заказы с оплатой «принят». Здесь можно изменить статус выполнения и другие поля либо удалить запись.</p>

    <?php if ($edit_order) { ?>
        <div class="card mb-3"><div class="card-body">
            <h6>Заказ #<?= (int) $edit_order['orderid'] ?></h6>
            <form method="POST" class="row g-2">
                <input type="hidden" name="orderid" value="<?= (int) $edit_order['orderid'] ?>">
                <div class="col-md-3"><input class="form-control" name="orderstatus" value="<?= h3($edit_order['orderstatus']) ?>"></div>
                <div class="col-md-2"><input class="form-control" name="roomnumber" value="<?= h3($edit_order['roomnumber']) ?>"></div>
                <div class="col-md-2"><input class="form-control" type="number" name="amountclients" value="<?= (int) $edit_order['amountclients'] ?>"></div>
                <div class="col-md-3"><input class="form-control" name="hotelservices" value="<?= h3($edit_order['hotelservices']) ?>"></div>
                <div class="col-md-2"><input class="form-control" name="paymentstatus" value="<?= h3($edit_order['paymentstatus']) ?>" readonly title="Исполнитель работает только с оплаченными заказами"></div>
                <div class="col-md-3"><input class="form-control" type="date" name="datecreation" value="<?= h3($edit_order['datecreation']) ?>"></div>
                <div class="col-12"><button class="btn btn-primary" type="submit" name="save_order" value="1">Сохранить</button>
                <a class="btn btn-link" href="/frontend/view_3.php">Отмена</a></div>
            </form>
        </div></div>
    <?php } ?>

    <table class="table table-sm table-bordered bg-white">
        <thead><tr><th>№</th><th>Статус</th><th>Комната</th><th>Клиентов</th><th>Услуги</th><th>Оплата</th><th>Дата</th><th></th></tr></thead>
        <tbody>
        <?php if (is_array($orders) && count($orders) && !isset($orders['status'])) {
            foreach ($orders as $o) { ?>
                <tr>
                    <td><?= (int) $o['orderid'] ?></td>
                    <td><?= h3($o['orderstatus']) ?></td>
                    <td><?= h3($o['roomnumber']) ?></td>
                    <td><?= (int) $o['amountclients'] ?></td>
                    <td><?= h3($o['hotelservices']) ?></td>
                    <td><?= h3($o['paymentstatus']) ?></td>
                    <td><?= h3($o['datecreation']) ?></td>
                    <td class="text-nowrap">
                        <a class="btn btn-sm btn-outline-primary" href="/frontend/view_3.php?edit_order=<?= (int) $o['orderid'] ?>">Изменить</a>
                        <form method="POST" class="d-inline" onsubmit="return confirm('Удалить заказ из списка?');">
                            <input type="hidden" name="orderid" value="<?= (int) $o['orderid'] ?>">
                            <button class="btn btn-sm btn-outline-danger" type="submit" name="delete_order" value="1">Удалить</button>
                        </form>
                    </td>
                </tr>
            <?php }
            } else { ?>
            <tr><td colspan="8">Нет заказов с оплатой «принят»</td></tr>
        <?php } ?>
        </tbody>
    </table>
</div>
</body>
</html>
