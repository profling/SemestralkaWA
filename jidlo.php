<?php
require_once "inc/header.php";
#nacteni zvoleneho jidelnicku
$queryzvolen= $db->prepare('Select jidelnicek_id from uzivatele where id=:iduser');
$queryzvolen->execute([':iduser'=>$_SESSION['user_id']]);
$zvoleny= $queryzvolen->fetch(PDO::FETCH_ASSOC);
$queryjidelnicek = $db->prepare('Select * from jidelnicek where id=:idjidelnicek;');
$queryjidelnicek->execute([':idjidelnicek'=>$zvoleny['jidelnicek_id']]);
$jidelnicek= $queryjidelnicek->fetch(PDO::FETCH_ASSOC);
#nacteni dnesniho jidla a snedenych tech blbu
$querydnesek= $db->prepare('SELECT jidlo_id FROM den WHERE uzivatele_id=:iduzivatele and datum=:dneska;');
$querydnesek->execute([
    ':iduzivatele'=>$_SESSION['user_id'],
    ':dneska'=>date('Y-m-d')
]);
#nacteni dneska uz snedenych jidel
$idjidla = $querydnesek->fetchAll(PDO::FETCH_ASSOC);
$prikaz = 'SELECT * FROM jidlo WHERE id= -1';// prvni podminka neni nikdz splnena, ale muyem dikz tomu zacit ve foru orem;
foreach ($idjidla as $idjidlo){
    $prikaz= $prikaz." or id=".$idjidlo['jidlo_id'];
}
$prikaz = $prikaz.";";
$querydnesnijidla = $db->prepare($prikaz);
$querydnesnijidla->execute();
$dnesnijidla=$querydnesnijidla->fetchAll(PDO::FETCH_ASSOC);
$cukry=0;
$sacharidy=0;
$bilkoviny=0;
foreach ($dnesnijidla as $dnesnijidlo){
    $cukry+=$dnesnijidlo['cukry'];
    $sacharidy+=$dnesnijidlo['sacharidy'];
    $bilkoviny+=$dnesnijidlo['bilkoviny'];
}
?>
    <div class="">
<h3>
Dnešní statistika:
    <small class="text-muted">
        <?php
        echo "cukry ".$cukry."/".$jidelnicek['cukry']."g, sacharidy ".$sacharidy."/".$jidelnicek['sacharidy']."g, bílkoviny ".$bilkoviny."/".$jidelnicek['bilkoviny']."g ";
        ?>
    </small>
</h3>
</div>


















<?php

require_once "inc/footer.php";