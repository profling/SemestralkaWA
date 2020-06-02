<?php
require_once 'inc/user.php'; //pripojeni k databayi a incicialiyace session
require_once "inc/header.php";
if($_SESSION['user_admin']!=1){// pokud neni admin nema tu co delat
    header('location:prehled.php');
}
#smazani a pridani
if(!empty($_POST)){
    if(!empty($_POST['idjidlasmazat'])){#odebrani z dopo jidel
        $queryodebrat = $db->prepare('DELETE FROM jidlakjidelnicku where jidelnicek_id=:idjidelnicek AND jidlo_id=:idjidlo;');
        $queryodebrat->execute([':idjidelnicek'=>$_GET['idjidelnicek'],
                                ':idjidlo'=>$_POST['idjidlasmazat']]);
    }
    if(!empty($_POST['idjidlapridat'])){#pridani k dopo jidel
        $querypridat = $db->prepare('INSERT INTO jidlakjidelnicku (jidelnicek_id, jidlo_id) VALUES (:idjidelnicek,:idjidlo)');
        $querypridat->execute([':idjidelnicek'=>$_GET['idjidelnicek'],
            ':idjidlo'=>$_POST['idjidlapridat']]);
    }
}


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
<h3 class="jumbotron-heading text-center"><?php echo $jidelnicek['nazev'] ;?></h3>
<table class="table">
    <thead class="thead-light">
    <tr>
        <th colspan="6" class="text-center">Doporučená Jídla</th>
    </tr>
    <tr>
        <th> Název</th>
        <th> Popis</th>
        <th> Obsah cukru</th>
        <th> Obsah sacharidů</th>
        <th> Obsah bílkovin</th>
        <th> Akce</th>
    </tr>
    </thead>
    <?php
    foreach ($jidla as $jidlo) {
        echo "<tr>";
        echo "<th>" . $jidlo['nazev'] . "</th><th>" . $jidlo['popis'] . "</th><th>" . $jidlo['cukry'] . "g</th><th>" . $jidlo['sacharidy'] . "g</th><th>" . $jidlo['bilkoviny'] . "g</th>";
        echo "<th>";
        echo '<form method="post">';
        echo '<input type="hidden" name="idjidlasmazat" value="'.$jidlo['id'].'"/>';
        echo '<button type="submit" class="btn btn-outline-danger">Odebrat</button>';
        echo '</form>';
        echo "</th>";
        echo "</tr>";
    }
    ?>

</table>
<?php
#vzhledani ostatnich jidel
$queryDopo = $db->prepare('SELECT jidlo_id FROM jidlakjidelnicku WHERE jidelnicek_id=:idjidelnicek;');
$queryDopo->execute([':idjidelnicek'=>$_GET['idjidelnicek']]);
$dopojidla = $queryDopo->fetchAll(PDO::FETCH_ASSOC);
#vyhledani popisu a infu o dopo jidel
$prikaz = 'SELECT * FROM jidlo WHERE id!=-1';#vzdy neplatna podminka, ale ve for dikz tomu muzu zacit orem vzdy;
foreach ($dopojidla as $dopojidlo){
    $prikaz = $prikaz.' and id!='.$dopojidlo['jidlo_id'];
}
$prikaz = $prikaz. ';';
$queryJidla = $db->prepare($prikaz);
$queryJidla->execute();
$jidla = $queryJidla->fetchAll(PDO::FETCH_ASSOC);
?>

<table class="table">
    <thead class="thead-light">
    <tr>
        <th colspan="6" class="text-center">Ostatní Jídla</th>
    </tr>
    <tr>
        <th> Název</th>
        <th> Popis</th>
        <th> Obsah cukru</th>
        <th> Obsah sacharidů</th>
        <th> Obsah bílkovin</th>
        <th> Akce</th>
    </tr>
    </thead>
    <?php
    foreach ($jidla as $jidlo) {
        echo "<tr>";
        echo "<th>" . htmlspecialchars($jidlo['nazev']) . "</th><th>" . htmlspecialchars($jidlo['popis']) . "</th><th>" . htmlspecialchars($jidlo['cukry']) . "g</th><th>" . htmlspecialchars($jidlo['sacharidy']) . "g</th><th>" . htmlspecialchars($jidlo['bilkoviny']) . "g</th>";
        echo "<th>";
        echo '<form method="post">';
        echo '<input type="hidden" name="idjidlapridat" value="'.$jidlo['id'].'"/>';
        echo '<button type="submit" class="btn btn-outline-primary">Pridat</button>';
        echo '</form>';
        echo "</th>";
        echo "</tr>";
    }
    ?>

</table>
