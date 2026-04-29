<?php
session_start();
require_once '../api/crud.php';
if (!isset($_SESSION['userid'])) {
    header('Location: /');
    exit;
}

$me = select('SELECT * FROM user WHERE userid=' . (int) $_SESSION['userid']);
if (!$me || !isset($me[0]) || (int) $me[0]['userroleid'] !== 1) {
    http_response_code(403);
    exit('Доступ запрещён');
}

$tab = $_GET['tab'] ?? 'orders';
$allowed = ['orders', 'users', 'shifts', 'roles', 'userlist'];
if (!in_array($tab, $allowed, true)) {
    $tab = 'orders';
}

// --- POST: CRUD ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_order'])) {
        $id = (int) ($_POST['orderid'] ?? 0);
        if ($id > 0) {
            delete('DELETE FROM `order` WHERE orderid=' . $id);
        }
        header('Location: /frontend/view_1.php?tab=orders');
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
        header('Location: /frontend/view_1.php?tab=orders');
        exit;
    }

    if (isset($_POST['delete_user'])) {
        $id = (int) ($_POST['userid'] ?? 0);
        if ($id > 0 && $id !== (int) $_SESSION['userid']) {
            delete('DELETE FROM user WHERE userid=' . $id);
        }
        header('Location: /frontend/view_1.php?tab=users');
        exit;
    }
    if (isset($_POST['save_user'])) {
        $uid = (int) ($_POST['userid'] ?? 0);
        $status = db_esc((string) ($_POST['status'] ?? 'Работает'));
        $lastname = db_esc((string) ($_POST['lastname'] ?? ''));
        $firstname = db_esc((string) ($_POST['firstname'] ?? ''));
        $middlename = db_esc((string) ($_POST['middlename'] ?? ''));
        $login = db_esc((string) ($_POST['login'] ?? ''));
        $password = db_esc((string) ($_POST['password'] ?? ''));
        $userroleid = (int) ($_POST['userroleid'] ?? 1);
        if ($login !== '' && $lastname !== '' && $firstname !== '') {
            if ($uid > 0) {
                if ($password !== '') {
                    update("UPDATE user SET status='$status', lastname='$lastname', firstname='$firstname', middlename='$middlename', login='$login', password='$password', userroleid=$userroleid WHERE userid=$uid");
                } else {
                    update("UPDATE user SET status='$status', lastname='$lastname', firstname='$firstname', middlename='$middlename', login='$login', userroleid=$userroleid WHERE userid=$uid");
                }
            } else {
                if ($password !== '') {
                    insert("INSERT INTO user (status, lastname, firstname, middlename, login, password, userroleid) VALUES ('$status','$lastname','$firstname','$middlename','$login','$password',$userroleid)");
                }
            }
        }
        header('Location: /frontend/view_1.php?tab=users');
        exit;
    }
    if (isset($_POST['fire_user'])) {
        $id = (int) ($_POST['userid'] ?? 0);
        if ($id > 0) {
            update("UPDATE user SET status='Уволен' WHERE userid=$id");
        }
        header('Location: /frontend/view_1.php?tab=users');
        exit;
    }

    if (isset($_POST['delete_shift'])) {
        $id = (int) ($_POST['shiftid'] ?? 0);
        if ($id > 0) {
            delete('DELETE FROM shift WHERE shiftid=' . $id);
        }
        header('Location: /frontend/view_1.php?tab=shifts');
        exit;
    }
    if (isset($_POST['save_shift'])) {
        $sid = (int) ($_POST['shiftid'] ?? 0);
        $ds = db_esc((string) ($_POST['datestart'] ?? ''));
        $de = db_esc((string) ($_POST['dateend'] ?? ''));
        if ($sid > 0) {
            update("UPDATE shift SET datestart='$ds', dateend='$de' WHERE shiftid=$sid");
        } else {
            insert("INSERT INTO shift (datestart, dateend) VALUES ('$ds','$de')");
        }
        header('Location: /frontend/view_1.php?tab=shifts');
        exit;
    }

    if (isset($_POST['delete_role'])) {
        $id = (int) ($_POST['userroleid'] ?? 0);
        if ($id > 0) {
            delete('DELETE FROM userrole WHERE userroleid=' . $id);
        }
        header('Location: /frontend/view_1.php?tab=roles');
        exit;
    }
    if (isset($_POST['save_role'])) {
        $rid = (int) ($_POST['userroleid'] ?? 0);
        $name = db_esc((string) ($_POST['namerole'] ?? ''));
        if ($name !== '') {
            if ($rid > 0) {
                update("UPDATE userrole SET namerole='$name' WHERE userroleid=$rid");
            } else {
                insert("INSERT INTO userrole (namerole) VALUES ('$name')");
            }
        }
        header('Location: /frontend/view_1.php?tab=roles');
        exit;
    }

    if (isset($_POST['delete_userlist'])) {
        $uid = (int) ($_POST['userid'] ?? 0);
        $sid = (int) ($_POST['shiftid'] ?? 0);
        if ($uid > 0 && $sid > 0) {
            delete("DELETE FROM userlist WHERE userid=$uid AND shiftid=$sid");
        }
        header('Location: /frontend/view_1.php?tab=userlist');
        exit;
    }
    if (isset($_POST['save_userlist'])) {
        $uid = (int) ($_POST['userid'] ?? 0);
        $sid = (int) ($_POST['shiftid'] ?? 0);
        if ($uid > 0 && $sid > 0) {
            insert("INSERT INTO userlist (userid, shiftid) VALUES ($uid,$sid)");
        }
        header('Location: /frontend/view_1.php?tab=userlist');
        exit;
    }
}

// Данные для форм редактирования
$edit_order = null;
if (!empty($_GET['edit_order'])) {
    $eid = (int) $_GET['edit_order'];
    $rows = select('SELECT * FROM `order` WHERE orderid=' . $eid);
    $edit_order = $rows[0] ?? null;
}
$edit_user = null;
if (!empty($_GET['edit_user'])) {
    $eid = (int) $_GET['edit_user'];
    $rows = select('SELECT * FROM user WHERE userid=' . $eid);
    $edit_user = $rows[0] ?? null;
}
$edit_shift = null;
if (!empty($_GET['edit_shift'])) {
    $eid = (int) $_GET['edit_shift'];
    $rows = select('SELECT * FROM shift WHERE shiftid=' . $eid);
    $edit_shift = $rows[0] ?? null;
}
$edit_role = null;
if (!empty($_GET['edit_role'])) {
    $eid = (int) $_GET['edit_role'];
    $rows = select('SELECT * FROM userrole WHERE userroleid=' . $eid);
    $edit_role = $rows[0] ?? null;
}

$orders = select('SELECT * FROM `order` ORDER BY orderid');
$users = select('SELECT u.*, r.namerole FROM user u LEFT JOIN userrole r ON r.userroleid=u.userroleid ORDER BY u.userid');
$shifts = select('SELECT * FROM shift ORDER BY shiftid');
$roles = select('SELECT * FROM userrole ORDER BY userroleid');
$userlist = select('SELECT ul.*, u.login, s.datestart, s.dateend FROM userlist ul JOIN user u ON u.userid=ul.userid JOIN shift s ON s.shiftid=ul.shiftid ORDER BY ul.shiftid, ul.userid');

function h($s)
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Администратор</title>
    <link rel="stylesheet" href="./bootstrap.min.css">
</head>
<body class="bg-light">
<a href="/logout.php" class="btn btn-sm btn-outline-secondary position-fixed top-0 end-0 m-3">Выход</a>
<div class="container py-4">
    <h4 class="mb-3">Панель администратора</h4>
    <ul class="nav nav-tabs mb-3">
        <?php
        $tabs = [
            'orders' => 'Заказы',
            'users' => 'Пользователи',
            'shifts' => 'Смены',
            'roles' => 'Роли',
            'userlist' => 'Назначения на смены',
        ];
foreach ($tabs as $key => $label) {
    $active = $tab === $key ? ' active' : '';
    echo '<li class="nav-item"><a class="nav-link' . $active . '" href="/frontend/view_1.php?tab=' . $key . '">' . h($label) . '</a></li>';
}
?>
    </ul>

    <?php if ($tab === 'orders') { ?>
        <h5>Заказы</h5>
        <?php if ($edit_order) { ?>
            <div class="card mb-3"><div class="card-body">
                <h6>Редактирование заказа #<?= (int) $edit_order['orderid'] ?></h6>
                <form method="POST" class="row g-2">
                    <input type="hidden" name="orderid" value="<?= (int) $edit_order['orderid'] ?>">
                    <div class="col-md-3"><input class="form-control" name="orderstatus" value="<?= h($edit_order['orderstatus']) ?>" placeholder="Статус заказа"></div>
                    <div class="col-md-2"><input class="form-control" name="roomnumber" value="<?= h($edit_order['roomnumber']) ?>" placeholder="Комната"></div>
                    <div class="col-md-2"><input class="form-control" type="number" name="amountclients" value="<?= (int) $edit_order['amountclients'] ?>"></div>
                    <div class="col-md-3"><input class="form-control" name="hotelservices" value="<?= h($edit_order['hotelservices']) ?>" placeholder="Услуги"></div>
                    <div class="col-md-2"><input class="form-control" name="paymentstatus" value="<?= h($edit_order['paymentstatus']) ?>" placeholder="Оплата"></div>
                    <div class="col-md-3"><input class="form-control" type="date" name="datecreation" value="<?= h($edit_order['datecreation']) ?>"></div>
                    <div class="col-12"><button class="btn btn-primary" type="submit" name="save_order" value="1">Сохранить</button>
                    <a class="btn btn-link" href="/frontend/view_1.php?tab=orders">Отмена</a></div>
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
                        <td><?= h($o['orderstatus']) ?></td>
                        <td><?= h($o['roomnumber']) ?></td>
                        <td><?= (int) $o['amountclients'] ?></td>
                        <td><?= h($o['hotelservices']) ?></td>
                        <td><?= h($o['paymentstatus']) ?></td>
                        <td><?= h($o['datecreation']) ?></td>
                        <td class="text-nowrap">
                            <a class="btn btn-sm btn-outline-primary" href="/frontend/view_1.php?tab=orders&edit_order=<?= (int) $o['orderid'] ?>">Изменить</a>
                            <form method="POST" class="d-inline" onsubmit="return confirm('Удалить заказ?');">
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
        <h6>Добавить заказ</h6>
        <form method="POST" class="row g-2 mb-4">
            <div class="col-md-3"><input class="form-control" name="orderstatus" placeholder="Статус заказа" value="создан"></div>
            <div class="col-md-2"><input class="form-control" name="roomnumber" placeholder="Комната" required></div>
            <div class="col-md-2"><input class="form-control" type="number" name="amountclients" value="1" required></div>
            <div class="col-md-3"><input class="form-control" name="hotelservices" placeholder="Услуги"></div>
            <div class="col-md-2"><input class="form-control" name="paymentstatus" placeholder="Оплата" value="не принят"></div>
            <div class="col-md-3"><input class="form-control" type="date" name="datecreation" required></div>
            <div class="col-12"><button class="btn btn-success" type="submit" name="save_order" value="1">Добавить</button></div>
        </form>
    <?php } ?>

    <?php if ($tab === 'users') { ?>
        <h5>Пользователи</h5>
        <?php if ($edit_user) { ?>
            <div class="card mb-3"><div class="card-body">
                <h6>Пользователь #<?= (int) $edit_user['userid'] ?></h6>
                <form method="POST" class="row g-2">
                    <input type="hidden" name="userid" value="<?= (int) $edit_user['userid'] ?>">
                    <div class="col-md-2"><input class="form-control" name="status" value="<?= h($edit_user['status']) ?>"></div>
                    <div class="col-md-2"><input class="form-control" name="lastname" value="<?= h($edit_user['lastname']) ?>" required></div>
                    <div class="col-md-2"><input class="form-control" name="firstname" value="<?= h($edit_user['firstname']) ?>" required></div>
                    <div class="col-md-2"><input class="form-control" name="middlename" value="<?= h($edit_user['middlename']) ?>"></div>
                    <div class="col-md-2"><input class="form-control" name="login" value="<?= h($edit_user['login']) ?>" required></div>
                    <div class="col-md-2"><input class="form-control" name="password" placeholder="Новый пароль (необязательно)"></div>
                    <div class="col-md-2"><input class="form-control" type="number" name="userroleid" value="<?= (int) $edit_user['userroleid'] ?>" required></div>
                    <div class="col-12"><button class="btn btn-primary" type="submit" name="save_user" value="1">Сохранить</button>
                    <a class="btn btn-link" href="/frontend/view_1.php?tab=users">Отмена</a></div>
                </form>
            </div></div>
        <?php } ?>
        <table class="table table-sm table-bordered bg-white">
            <thead><tr><th>№</th><th>Статус</th><th>ФИО</th><th>Логин</th><th>Роль</th><th></th></tr></thead>
            <tbody>
            <?php if (is_array($users) && count($users) && !isset($users['status'])) {
                foreach ($users as $u) { ?>
                    <tr>
                        <td><?= (int) $u['userid'] ?></td>
                        <td><?= h($u['status']) ?></td>
                        <td><?= h($u['lastname'] . ' ' . $u['firstname'] . ' ' . $u['middlename']) ?></td>
                        <td><?= h($u['login']) ?></td>
                        <td><?= h($u['namerole'] ?? '') ?> (<?= (int) $u['userroleid'] ?>)</td>
                        <td class="text-nowrap">
                            <a class="btn btn-sm btn-outline-primary" href="/frontend/view_1.php?tab=users&edit_user=<?= (int) $u['userid'] ?>">Изменить</a>
                            <form method="POST" class="d-inline" onsubmit="return confirm('Уволить?');">
                                <input type="hidden" name="userid" value="<?= (int) $u['userid'] ?>">
                                <button class="btn btn-sm btn-outline-warning" type="submit" name="fire_user" value="1">Уволить</button>
                            </form>
                            <?php if ((int) $u['userid'] !== (int) $_SESSION['userid']) { ?>
                            <form method="POST" class="d-inline" onsubmit="return confirm('Удалить пользователя из БД?');">
                                <input type="hidden" name="userid" value="<?= (int) $u['userid'] ?>">
                                <button class="btn btn-sm btn-outline-danger" type="submit" name="delete_user" value="1">Удалить</button>
                            </form>
                            <?php } ?>
                        </td>
                    </tr>
                <?php }
                } else { ?>
                <tr><td colspan="6">Нет записей</td></tr>
            <?php } ?>
            </tbody>
        </table>
        <h6>Добавить пользователя</h6>
        <form method="POST" class="row g-2 mb-4">
            <div class="col-md-2"><input class="form-control" name="status" value="Работает"></div>
            <div class="col-md-2"><input class="form-control" name="lastname" placeholder="Фамилия" required></div>
            <div class="col-md-2"><input class="form-control" name="firstname" placeholder="Имя" required></div>
            <div class="col-md-2"><input class="form-control" name="middlename" placeholder="Отчество"></div>
            <div class="col-md-2"><input class="form-control" name="login" placeholder="Логин" required></div>
            <div class="col-md-2"><input class="form-control" name="password" placeholder="Пароль" required></div>
            <div class="col-md-2"><input class="form-control" type="number" name="userroleid" value="3" title="1 админ, 2 менеджер, 3 исполнитель"></div>
            <div class="col-12"><button class="btn btn-success" type="submit" name="save_user" value="1">Добавить</button></div>
        </form>
    <?php } ?>

    <?php if ($tab === 'shifts') { ?>
        <h5>Смены</h5>
        <p class="small"><a href="/frontend/create_shift.php">Создать смену с выбором сотрудников</a></p>
        <?php if ($edit_shift) { ?>
            <div class="card mb-3"><div class="card-body">
                <form method="POST" class="row g-2">
                    <input type="hidden" name="shiftid" value="<?= (int) $edit_shift['shiftid'] ?>">
                    <div class="col-md-4"><input class="form-control" type="date" name="datestart" value="<?= h($edit_shift['datestart']) ?>" required></div>
                    <div class="col-md-4"><input class="form-control" type="date" name="dateend" value="<?= h($edit_shift['dateend']) ?>" required></div>
                    <div class="col-12"><button class="btn btn-primary" type="submit" name="save_shift" value="1">Сохранить</button>
                    <a class="btn btn-link" href="/frontend/view_1.php?tab=shifts">Отмена</a></div>
                </form>
            </div></div>
        <?php } ?>
        <table class="table table-sm table-bordered bg-white">
            <thead><tr><th>№</th><th>С</th><th>По</th><th></th></tr></thead>
            <tbody>
            <?php if (is_array($shifts) && count($shifts) && !isset($shifts['status'])) {
                foreach ($shifts as $s) { ?>
                    <tr>
                        <td><?= (int) $s['shiftid'] ?></td>
                        <td><?= h($s['datestart']) ?></td>
                        <td><?= h($s['dateend']) ?></td>
                        <td>
                            <a class="btn btn-sm btn-outline-primary" href="/frontend/view_1.php?tab=shifts&edit_shift=<?= (int) $s['shiftid'] ?>">Изменить</a>
                            <form method="POST" class="d-inline" onsubmit="return confirm('Удалить смену?');">
                                <input type="hidden" name="shiftid" value="<?= (int) $s['shiftid'] ?>">
                                <button class="btn btn-sm btn-outline-danger" type="submit" name="delete_shift" value="1">Удалить</button>
                            </form>
                        </td>
                    </tr>
                <?php }
                } else { ?>
                <tr><td colspan="4">Нет записей</td></tr>
            <?php } ?>
            </tbody>
        </table>
        <h6>Добавить смену</h6>
        <form method="POST" class="row g-2 mb-4">
            <div class="col-md-4"><input class="form-control" type="date" name="datestart" required></div>
            <div class="col-md-4"><input class="form-control" type="date" name="dateend" required></div>
            <div class="col-12"><button class="btn btn-success" type="submit" name="save_shift" value="1">Добавить</button></div>
        </form>
    <?php } ?>

    <?php if ($tab === 'roles') { ?>
        <h5>Роли</h5>
        <?php if ($edit_role) { ?>
            <div class="card mb-3"><div class="card-body">
                <form method="POST" class="row g-2">
                    <input type="hidden" name="userroleid" value="<?= (int) $edit_role['userroleid'] ?>">
                    <div class="col-md-6"><input class="form-control" name="namerole" value="<?= h($edit_role['namerole']) ?>" required></div>
                    <div class="col-12"><button class="btn btn-primary" type="submit" name="save_role" value="1">Сохранить</button>
                    <a class="btn btn-link" href="/frontend/view_1.php?tab=roles">Отмена</a></div>
                </form>
            </div></div>
        <?php } ?>
        <table class="table table-sm table-bordered bg-white">
            <thead><tr><th>№</th><th>Название</th><th></th></tr></thead>
            <tbody>
            <?php if (is_array($roles) && count($roles) && !isset($roles['status'])) {
                foreach ($roles as $r) { ?>
                    <tr>
                        <td><?= (int) $r['userroleid'] ?></td>
                        <td><?= h($r['namerole']) ?></td>
                        <td>
                            <a class="btn btn-sm btn-outline-primary" href="/frontend/view_1.php?tab=roles&edit_role=<?= (int) $r['userroleid'] ?>">Изменить</a>
                            <form method="POST" class="d-inline" onsubmit="return confirm('Удалить роль?');">
                                <input type="hidden" name="userroleid" value="<?= (int) $r['userroleid'] ?>">
                                <button class="btn btn-sm btn-outline-danger" type="submit" name="delete_role" value="1">Удалить</button>
                            </form>
                        </td>
                    </tr>
                <?php }
                } else { ?>
                <tr><td colspan="3">Нет записей</td></tr>
            <?php } ?>
            </tbody>
        </table>
        <h6>Добавить роль</h6>
        <form method="POST" class="row g-2 mb-4">
            <div class="col-md-6"><input class="form-control" name="namerole" placeholder="Название роли" required></div>
            <div class="col-12"><button class="btn btn-success" type="submit" name="save_role" value="1">Добавить</button></div>
        </form>
    <?php } ?>

    <?php if ($tab === 'userlist') { ?>
        <h5>Назначения сотрудников на смены</h5>
        <table class="table table-sm table-bordered bg-white">
            <thead><tr><th>Смена</th><th>Пользователь</th><th>Период</th><th></th></tr></thead>
            <tbody>
            <?php if (is_array($userlist) && count($userlist) && !isset($userlist['status'])) {
                foreach ($userlist as $ul) { ?>
                    <tr>
                        <td><?= (int) $ul['shiftid'] ?></td>
                        <td><?= h($ul['login']) ?> (<?= (int) $ul['userid'] ?>)</td>
                        <td><?= h($ul['datestart']) ?> — <?= h($ul['dateend']) ?></td>
                        <td>
                            <form method="POST" onsubmit="return confirm('Удалить назначение?');">
                                <input type="hidden" name="userid" value="<?= (int) $ul['userid'] ?>">
                                <input type="hidden" name="shiftid" value="<?= (int) $ul['shiftid'] ?>">
                                <button class="btn btn-sm btn-outline-danger" type="submit" name="delete_userlist" value="1">Удалить</button>
                            </form>
                        </td>
                    </tr>
                <?php }
                } else { ?>
                <tr><td colspan="4">Нет записей</td></tr>
            <?php } ?>
            </tbody>
        </table>
        <h6>Добавить назначение</h6>
        <form method="POST" class="row g-2 mb-4">
            <div class="col-md-4"><input class="form-control" type="number" name="userid" placeholder="ID пользователя" required></div>
            <div class="col-md-4"><input class="form-control" type="number" name="shiftid" placeholder="ID смены" required></div>
            <div class="col-12"><button class="btn btn-success" type="submit" name="save_userlist" value="1">Добавить</button></div>
        </form>
    <?php } ?>
</div>
</body>
</html>
