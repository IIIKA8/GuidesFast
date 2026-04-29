<?php
session_start();
require_once '../api/crud.php';
if (!isset($_SESSION['userid'])) {
    header('Location: /');
    exit;
}

if ((int) ($_SESSION['userroleid'] ?? 0) !== 1) {
    http_response_code(403);
    exit('Доступ запрещён');
}

$users = select('SELECT userid, lastname, firstname, login FROM user ORDER BY userid');

if (isset($_POST['datestart']) && $_POST['datestart'] !== '' && isset($_POST['dateend']) && $_POST['dateend'] !== '') {
    $ds = db_esc((string) $_POST['datestart']);
    $de = db_esc((string) $_POST['dateend']);
    $ins = insert("INSERT INTO shift (datestart,dateend) VALUES ('$ds','$de')");
    if (isset($ins['status']) && $ins['status'] === 'success') {
        $idRow = select('SELECT LAST_INSERT_ID() AS id');
        $sid = (int) ($idRow[0]['id'] ?? 0);
        if ($sid > 0 && !empty($_POST['userList'])) {
            $parts = array_filter(array_map('trim', explode(',', (string) $_POST['userList'])));
            foreach ($parts as $uid) {
                $uid = (int) $uid;
                if ($uid > 0) {
                    insert("INSERT INTO userlist (userid,shiftid) VALUES ($uid,$sid)");
                }
            }
        }
    }
    header('Location: /frontend/view_1.php?tab=shifts');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Новая смена</title>
    <link rel="stylesheet" href="./bootstrap.min.css">
</head>
<body class="container py-4" style="max-width: 520px">
    <h4 class="mb-3">Создание смены</h4>
    <p><a href="/frontend/view_1.php?tab=shifts">← Назад к сменам</a></p>
    <form class="d-flex flex-column gap-2" method="POST">
        <div class="form-floating"><input required name="datestart" id="datestart" class="form-control" type="date"><label for="datestart">Дата с</label></div>
        <div class="form-floating"><input required name="dateend" id="dateend" class="form-control" type="date"><label for="dateend">Дата по</label></div>
        <div class="form-floating"><input name="userList" id="userList" class="form-control" readonly placeholder="1,2,3"><label for="userList">ID сотрудников (через запятую)</label></div>
        <div class="form-floating">
            <select id="users" class="form-select">
                <option selected disabled>Выберите сотрудников</option>
                <?php
                if (is_array($users) && count($users) && !isset($users['status'])) {
                    foreach ($users as $row) {
                        echo "<option value='" . (int) $row['userid'] . "'>" . htmlspecialchars($row['userid'] . ' — ' . $row['login'], ENT_QUOTES, 'UTF-8') . '</option>';
                    }
                }
                ?>
            </select>
            <label for="users">Сотрудники</label>
        </div>
        <button type="submit" class="btn btn-primary">Создать</button>
    </form>
    <script>
        document.getElementById('users').addEventListener('change', function (e) {
            const v = e.target.value;
            const listEl = document.getElementById('userList');
            let list = listEl.value === '' ? [] : listEl.value.split(',').filter(Boolean);
            if (list.includes(v)) list = list.filter(x => x !== v);
            else list.push(v);
            listEl.value = list.join(',');
        });
    </script>
</body>
</html>
