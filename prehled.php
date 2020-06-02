<?php
require_once 'inc/user.php'; //pripojeni k databayi a incicialiyace session
require_once 'inc/header.php';
#nacteni snedenzch jidel
$querySnezeno = $db->prepare('SELECT * FROM den WHERE uzivatel_id=:iduzivatele;');
$querySnezeno->execute([':iduzivatele'=>$_SESSION['user_id']]);
$snezeno = $querySnezeno->fetchAll(PDO::FETCH_ASSOC);
























require_once 'inc/footer.php';