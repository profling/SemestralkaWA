<?php
require_once 'inc/user.php'; //pripojeni k databayi a incicialiyace session
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <title>Trénuj sval, buchty bal!</title>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
</head>
<body>
<header class="container bg-dark">
    <h1 class="text-white py-4 px-2">Jídelníček</h1>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
                <a class="nav-link" href="index.php">Domů <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="jidelnicek.php">Jídelníčky</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="jidlo.php">Přidat jídlo</a>
            </li>
        </ul>
    <div class="navbar-text">
        <?php
        if (!empty($_SESSION['user_id'])){
            echo '<strong>'.htmlspecialchars($_SESSION['user_name']).'</strong>';
            echo ' - ';
            echo '<a href="logout.php" class="text-white">odhlásit se</a>';
        }else{
            echo '<a href="login.php" class="text-white">přihlásit se</a>';
        }
        ?>
    </div>
    </nav>
</header>
<main class="container pt-2">
