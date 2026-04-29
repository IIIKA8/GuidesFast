<?php // auth.php
session_start();
require_once __DIR__ . '/crud.php';

function check_user()
{
    $login = trim((string) ($_POST['login'] ?? ''));
    $password = trim((string) ($_POST['password'] ?? ''));
    if ($login === '' || $password === '') {
        return [];
    }
    $login = db_esc($login);
    $password = db_esc($password);
    return select("SELECT * FROM user WHERE login='$login' AND password='$password'");
}

$user = check_user();
$user_checked = is_array($user) && count($user) > 0 && !isset($user['status']);

if ($user_checked) {
    $_SESSION['userid'] = (int) $user[0]['userid'];
    $_SESSION['userroleid'] = (int) $user[0]['userroleid'];
    $rid = (int) $user[0]['userroleid'];
    header("Location: /frontend/view_$rid.php");
    exit;
}

header('Location: /?error=1');
exit;
