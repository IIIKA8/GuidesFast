<?php
require_once '../api/crud.php';

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim((string) ($_POST['login'] ?? ''));
    $password = trim((string) ($_POST['password'] ?? ''));
    $lastname = trim((string) ($_POST['lastname'] ?? ''));
    $firstname = trim((string) ($_POST['firstname'] ?? ''));
    $middlename = trim((string) ($_POST['middlename'] ?? ''));

    if ($login === '' || $password === '' || $lastname === '' || $firstname === '') {
        $err = 'Заполните обязательные поля.';
    } else {
        $login = db_esc($login);
        $password = db_esc($password);
        $lastname = db_esc($lastname);
        $firstname = db_esc($firstname);
        $middlename = db_esc($middlename);
        // Новый пользователь — роль «Исполнитель» (3)
        $q = "INSERT INTO user (status, lastname, firstname, middlename, login, password, userroleid)
              VALUES ('Работает', '$lastname', '$firstname', '$middlename', '$login', '$password', 3)";
        $ins = insert($q);
        if (isset($ins['status']) && $ins['status'] === 'success') {
            header('Location: /?registered=1');
            exit;
        }
        $err = 'Не удалось зарегистрировать (логин занят или ошибка БД).';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <link rel="stylesheet" href="./bootstrap.min.css">
</head>

<body class="container py-4" style="max-width: 480px">
    <h4 class="mb-3">Регистрация</h4>
    <?php if ($err !== '') { ?>
        <div class="alert alert-danger"><?= htmlspecialchars($err) ?></div>
    <?php } ?>
    <form class="d-flex flex-column gap-2" method="POST">
        <div class="form-floating">
            <input name="login" id="login" class="form-control" type="text" required>
            <label for="login">Логин</label>
        </div>
        <div class="form-floating">
            <input name="password" id="password" class="form-control" type="password" required>
            <label for="password">Пароль</label>
        </div>
        <div class="form-floating">
            <input name="lastname" id="lastname" class="form-control" type="text" required>
            <label for="lastname">Фамилия</label>
        </div>
        <div class="form-floating">
            <input name="firstname" id="firstname" class="form-control" type="text" required>
            <label for="firstname">Имя</label>
        </div>
        <div class="form-floating">
            <input name="middlename" id="middlename" class="form-control" type="text">
            <label for="middlename">Отчество</label>
        </div>
        <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
        <a class="btn btn-outline-secondary" href="/">На страницу входа</a>
    </form>
</body>

</html>
