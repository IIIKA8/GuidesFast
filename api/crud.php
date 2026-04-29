<?php // crud.php
require 'config.php';

function select($query)
{
    try {
        $result = $_SERVER['db']->query($query, MYSQLI_USE_RESULT);
        $result = $result->fetch_all(MYSQLI_ASSOC);
        return $result;
    } catch (Exception $err) {
        return ['status' => 'failed', 'message' => $err];
        // return [];
    }
}

function insert($query)
{
    try {
        $result = $_SERVER['db']->query($query, MYSQLI_USE_RESULT);
        return $result ? ['status' => 'success'] : ['status' => 'failed'];
    } catch (Exception $err) {
        return ['status' => 'failed', 'message' => $err];
    }
}

function update($query)
{
    try {
        $result = $_SERVER['db']->query($query, MYSQLI_USE_RESULT);
        return $result ? ['status' => 'success'] : ['status' => 'failed'];
    } catch (Exception $err) {
        return ['status' => 'failed', 'message' => $err];
    }
}

function delete($query)
{
    try {
        $result = $_SERVER['db']->query($query, MYSQLI_USE_RESULT);
        return $result ? ['status' => 'success'] : ['status' => 'failed'];
    } catch (Exception $err) {
        return ['status' => 'failed', 'message' => $err];
    }
}

function db_esc($value)
{
    return $_SERVER['db']->real_escape_string((string) $value);
}
