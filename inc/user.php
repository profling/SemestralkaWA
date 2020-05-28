<?php

session_start(); //spustíme session

require_once 'db.php'; //načteme připojení k databázi



#region kontrola, jestli je přihlášený uživatel platný
if (!empty($_SESSION['user_id'])){
    $userQuery=$db->prepare('SELECT id FROM uzivatele WHERE id=:id LIMIT 1;');
    $userQuery->execute([
        ':id'=>$_SESSION['user_id']
    ]);
    if ($userQuery->rowCount()!=1){
        //uživatel už není v DB, nebo není aktivní => musíme ho odhlásit
        unset($_SESSION['user_id']);
        unset($_SESSION['user_name']);
        header('Location: index.php');
        exit();
    }
}