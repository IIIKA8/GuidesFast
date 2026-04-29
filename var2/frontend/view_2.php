<?php
session_start();
require_once '../api/crud.php';
if (!isset($_SESSION['userid'])) {
    header('Location: /');
    exit;
}

$me = select('SELECT * FROM user WHERE userid=' . (int) $_SESSION['userid']);
if (!$me || !isset($me[0]) || (int) $me[0]['userroleid'] !== 2) {
    http_response_code(403);
    exit('Доступ запрещён');
}

$tab = $_GET['tab'] ?? 'orders';
if (!in_array($tab, ['orders', 'shifts'], true)) {
    $tab = 'orders';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $tab === 'orders') {
    if (isset($_POST['delete_order'])) {
        $id = (int) ($_POST['orderid'] ?? 0);
        if ($id > 0) {
            delete('DELETE FROM `order` WHERE orderid=' . $id);
        }
        header('Location: /frontend/view_2.php?tab=orders');
        exit;
    }
    if (isset($_POST['save_order'])) {
        $oid = (int) ($_POST['orderid'] ?? 0);
        $orderstatus = db_esc((string) ($_POST['orderstatus'] ?? ''));
        $roomnumber = db_esc((string) ($_POST['roomnumber'] ?? ''));
        $amountclients = (int) ($_POST['amountclients'] ?? 0);
        $hotelservices = db_esc((string) ($_POST['hotelservices'] ?? ''));
        $paymentstatus = db_esc((string) ($_POST['paymentstatus'] ?? ''));
        $datecreation = db_esc((string) ($_POST['datecreation'] ?? ''));
        if ($oid > 0) {
            update("UPDATE `order` SET orderstatus='$orderstatus', roomnumber='$roomnumber', amountclients=$amountclients, hotelservices='$hotelservices', paymentstatus='$paymentstatus', datecreation='$datecreation' WHERE orderid=$oid");
        } else {
            insert("INSERT INTO `order` (orderstatus, roomnumber, amountclients, hotelservices, paymentstatus, datecreation) VALUES ('$orderstatus','$roomnumber',$amountclients,'$hotelservices','$paymentstatus','$datecreation')");
        }
        header('Location: /frontend/view_2.php?tab=orders');
        exit;
    }
}

$edit_order = null;
if ($tab === 'orders' && !empty($_GET['edit_order'])) {
    $eid = (int) $_GET['edit_order'];
    $rows = select('SELECT * FROM `order` WHERE orderid=' . $eid);
    $edit_order = $rows[0] ?? null;
}

$orders = select('SELECT * FROM `order` ORDER BY orderid');
$shifts = select('SELECT shiftid, datestart, dateend FROM shift ORDER BY shiftid');

function h2($s)
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Менеджер</title>
    <link rel="stylesheet" href="./bootstrap.min.css">
</head>
<body class="bg-light">
<a href="/logout.php" class="btn btn-sm btn-outline-secondary position-fixed top-0 end-0 m-3">Выход</a>
<div class="container py-4">
    <h4 class="mb-3">Панель менеджера</h4>
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item"><a class="nav-link<?= $tab === 'orders' ? ' active' : '' ?>" href="/frontend/view_2.php?tab=orders">Заказы</a></li>
        <li class="nav-item"><a class="nav-link<?= $tab === 'shifts' ? ' active' : '' ?>" href="/frontend/view_2.php?tab=shifts">Смены (просмотр)</a></li>
    </ul>

    <?php if ($tab === 'orders') { ?>
        <p class="small mb-2"><a class="btn btn-sm btn-outline-secondary" href="/frontend/create_order.php">Форма создания заказа</a></p>
        <?php if ($edit_order) { ?>
            <div class="card mb-3"><div class="card-body">
                <h6>Редактирование заказа #<?= (int) $edit_order['orderid'] ?></h6>
                <form method="POST" class="row g-2">
                    <input type="hidden" name="orderid" value="<?= (int) $edit_order['orderid'] ?>">
                    <div class="col-md-3"><input class="form-control" name="orderstatus" value="<?= h2($edit_order['orderstatus']) ?>"></div>
                    <div class="col-md-2"><input class="form-control" name="roomnumber" value="<?= h2($edit_order['roomnumber']) ?>"></div>
                    <div class="col-md-2"><input class="form-control" type="number" name="amountclients" value="<?= (int) $edit_order['amountclients'] ?>"></div>
                    <div class="col-md-3"><input class="form-control" name="hotelservices" value="<?= h2($edit_order['hotelservices']) ?>"></div>
                    <div class="col-md-2"><input class="form-control" name="paymentstatus" value="<?= h2($edit_order['paymentstatus']) ?>"></div>
                    <div class="col-md-3"><input class="form-control" type="date" name="datecreation" value="<?= h2($edit_order['datecreation']) ?>"></div>
                    <div class="col-12"><button class="btn btn-primary" type="submit" name="save_order" value="1">Сохранить</button>
                    <a class="btn btn-link" href="/frontend/view_2.php?tab=orders">Отмена</a></div>
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
                        <td><?= h2($o['orderstatus']) ?></td>
                        <td><?= h2($o['roomnumber']) ?></td>
                        <td><?= (int) $o['amountclients'] ?></td>
                        <td><?= h2($o['hotelservices']) ?></td>
                        <td><?= h2($o['paymentstatus']) ?></td>
                        <td><?= h2($o['datecreation']) ?></td>
                        <td class="text-nowrap">
                            <a class="btn btn-sm btn-outline-primary" href="/frontend/view_2.php?tab=orders&edit_order=<?= (int) $o['orderid'] ?>">Изменить</a>
                            <form method="POST" class="d-inline" onsubmit="return confirm('Удалить?');">
                                <input type="hidden" name="orderid" value="<?= (int) $o['orderid'] ?>">
                                <button class="btn btn-sm btn-outline-danger" type="submit" name="delete_order" value="1">Удалить</button>
                            </form>
                        </td>
                    </tr>
                <?php }
                } else { ?>
                <tr><td colspan="8">Нет записей</td></tr>
            <?php } ?>
            </tbody>
        </table>
        <h6>Новый заказ</h6>
        <form method="POST" class="row g-2">
            <div class="col-md-3"><input class="form-control" name="orderstatus" value="создан"></div>
            <div class="col-md-2"><input class="form-control" name="roomnumber" required></div>
            <div class="col-md-2"><input class="form-control" type="number" name="amountclients" value="1" required></div>
            <div class="col-md-3"><input class="form-control" name="hotelservices"></div>
            <div class="col-md-2"><input class="form-control" name="paymentstatus" value="не принят"></div>
            <div class="col-md-3"><input class="form-control" type="date" name="datecreation" required></div>
            <div class="col-12"><button class="btn btn-success" type="submit" name="save_order" value="1">Добавить</button></div>
        </form>
    <?php } else { ?>
        <table class="table table-sm table-bordered bg-white">
            <thead><tr><th>№</th><th>С даты</th><th>По дату</th></tr></thead>
            <tbody>
            <?php if (is_array($shifts) && count($shifts) && !isset($shifts['status'])) {
                foreach ($shifts as $s) {
                    echo '<tr><td>' . (int) $s['shiftid'] . '</td><td>' . h2($s['datestart']) . '</td><td>' . h2($s['dateend']) . '</td></tr>';
                }
            } else {
                echo '<tr><td colspan="3">Нет смен</td></tr>';
            } ?>
            </tbody>
        </table>
    <?php } ?>
</div>
</body>
</html>
