<?php
require_once 'inc/user.php'; //pripojeni k databayi a incicialiyace session
require_once "inc/header.php";
#nacteni jidelnicku
$queryJidelnicek= $db->prepare('SELECT * FROM jidelnicek WHERE id=:idjidelnicek;');
$queryJidelnicek->execute([':idjidelnicek'=>$_GET['idjidelnicek']]);
$jidelnicek= $queryJidelnicek->fetch(PDO::FETCH_ASSOC);
#vzhledani doporucenzych jidel
$queryDopo = $db->prepare('SELECT jidlo_id FROM jidlakjidelnicku WHERE jidelnicek_id=:idjidelnicek;');
$queryDopo->execute([':idjidelnicek'=>$_GET['idjidelnicek']]);
$dopojidla = $queryDopo->fetchAll(PDO::FETCH_ASSOC);
#vyhledani popisu a infu o dopo jidel
$prikaz = 'SELECT * FROM jidlo WHERE id=-1';#vzdy neplatna podminka, ale ve for dikz tomu muzu zacit orem vzdy;
foreach ($dopojidla as $dopojidlo){
    $prikaz = $prikaz.' or id='.$dopojidlo['jidlo_id'];
}
$prikaz = $prikaz. ';';
$queryJidla = $db->prepare($prikaz);
$queryJidla->execute();
$jidla = $queryJidla->fetchAll(PDO::FETCH_ASSOC);
?>
<h1 class="jumbotron-heading text-center"><?php echo $jidelnicek['nazev'] ;?></h1>
<p class="lead text-muted text-center"><?php echo $jidelnicek['popis'];  ?></p>
<table class="table">
    <thead class="thead-light">
    <tr>
        <th colspan="5" class="text-center">Doporučená Jídla</th>
    </tr>
    <tr>
        <th> Název</th>
        <th> Popis</th>
        <th> Obsah cukru</th>
        <th> Obsah sacharidů</th>
        <th> Obsah bílkovin</th>
    </tr>
    </thead>
    <?php
    foreach ($jidla as $jidlo) {
        echo "<tr>";
        echo "<th>" . htmlspecialchars($jidlo['nazev'] ). "</th><th>" . htmlspecialchars($jidlo['popis']) . "</th><th>" . htmlspecialchars($jidlo['cukry']) . "g</th><th>" . htmlspecialchars($jidlo['sacharidy']) . "g</th><th>" . htmlspecialchars($jidlo['bilkoviny']) . "g</th>";
        echo "</tr>";
    }
    ?>

</table>
    <a href="jidelnicek.php" class="btn btn-outline-primary">Zpět</a>

















<?php

require_once "inc/footer.php";
?>