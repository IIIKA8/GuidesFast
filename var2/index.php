<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ИС — вход</title>
    <link href="./frontend/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container min-vh-100 align-items-center justify-content-center d-flex flex-column">
    <div class="h5 title mb-4 fw-bold">Информационная система</div>
    <form class="d-flex flex-column gap-2 w-100" style="max-width: 320px" method="POST" action="/api/auth.php">
        <div class="form-floating">
            <input class="form-control" placeholder="Логин" type="text" name="login" id="login" required />
            <label for="login">Логин</label>
        </div>
        <div class="form-floating">
            <input class="form-control" placeholder="Пароль" type="password" name="password" id="password" required />
            <label for="password">Пароль</label>
        </div>
        <button class="mt-2 btn btn-primary" type="submit">Войти</button>
        <a class="btn btn-outline-secondary" href="/frontend/register.php">Регистрация</a>
    </form>
    <?php if (!empty($_GET['error'])) { ?>
        <div class="alert alert-danger mt-3">Неверный логин или пароль</div>
    <?php } ?>
    <?php if (!empty($_GET['registered'])) { ?>
        <div class="alert alert-success mt-3">Регистрация успешна. Войдите.</div>
    <?php } ?>
</body>

</html>
