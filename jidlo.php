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
<div>
    <table class="table">
        <thead class="thead-light">
        <tr>
            <th colspan="3" class="text-center">Doporučená Jídla</th>
        </tr>
        <tr>
            <th> Název</th>
            <th> Popis</th>
            <th> Stav</th>
        </tr>
        </thead>
        <?php
        #ziskani a vypsani doporucenych jidel
        $queryDopo = $db->prepare('SELECT jidlo_id FROM jidlakjidelnicku WHERE jidelnicek_id=:idjidelnicek;');
        $queryDopo->execute([':idjidelnicek'=>$zvoleny['jidelnicek_id']]);
        $dopojidla = $queryDopo->fetchAll(PDO::FETCH_ASSOC);
        $prikaz = 'SELECT * FROM jidlo WHERE id=-1';#vzdy neplatna podminka, ale ve for dikz tomu muzu zacit orem vzdy;
        foreach ($dopojidla as $dopojidlo){
         $prikaz = $prikaz.' or id='.$dopojidlo['jidlo_id'];
        }
        $prikaz = $prikaz. ';';
        $queryJidla = $db->prepare($prikaz);
        $queryJidla->execute();
        $jidla = $queryJidla->fetchAll(PDO::FETCH_ASSOC);
        foreach ($jidla as $jidlo){
           ?>
            <tr>
                <th> <?php echo $jidlo['nazev']; ?></th>
                <th>
                    <button type="button" class="btn btn-outline-info" href="popisjidla.php">Info</button>
                </th>
                <th>

                        <?php
                        $pomoc=0;#pomocna promenna
                        foreach ($dnesnijidla as $porovnani){
                           if($porovnani['id']==$jidlo['id']){
                                $pomoc=1;
                           }
                        }
                        if($pomoc==1){
                            ?>
                            <form method="post">
                                <input type="hidden" name="idjidlo" value="<?php echo $jidlo['id']; ?>"/>
                                <button type="submit" class="btn btn-outline-warning">Snědeno</button>
                            </form>
                            <?
                        }
                        else
                        {
                            ?>
                            <form method="post">
                                <input type="hidden" name="idjidlo" value="<?php echo $jidlo['id']; ?>"/>
                                <button type="submit" class="btn btn-outline-success">Vybrat</button>
                            </form>
                            <?php

                        }
                        ?>

                </th>
            </tr>

            <?php
        }
        ?>
    </table>

</div>


















<?php

require_once "inc/footer.php";