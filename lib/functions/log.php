<?php

function logMessage($connection,)
{
    $timestamp = date('Y-m-d H:i:s');
    $query = "INSERT INTO logs (timestamp, message) VALUES (?, ?)";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, 'ss', $timestamp, $message);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}
