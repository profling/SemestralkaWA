<?php
require_once 'inc/user.php'; //pripojeni k databayi a incicialiyace session
require_once "inc/header.php";
#nacteni zvoleneho jidelnicku
$queryzvolen= $db->prepare('Select jidelnicek_id from uzivatele where id=:iduser');
$queryzvolen->execute([':iduser'=>$_SESSION['user_id']]);
$zvoleny= $queryzvolen->fetch(PDO::FETCH_ASSOC);
$queryjidelnicek = $db->prepare('Select * from jidelnicek where id=:idjidelnicek;');
$queryjidelnicek->execute([':idjidelnicek'=>$zvoleny['jidelnicek_id']]);
$jidelnicek= $queryjidelnicek->fetch(PDO::FETCH_ASSOC);


#zvoleni dne a ulezeni do promene
if(!empty($_POST)){
   $datum=$_POST['datum'];#v metode post jde vzdy info o datumu

    #ulozeni snedeneho jidla
    if(!empty($_POST['idjidlo'])&& $_POST['stav']=='vybrat'){
        $querypridat= $db->prepare('INSERT INTO den (uzivatele_id, jidlo_id, datum, jidelnicek) VALUES (:iduzivatele, :idjidla, :datum, :jidelnicek );');
        $querypridat->execute([':iduzivatele'=>$_SESSION['user_id'],
                                ':idjidla'=>$_POST['idjidlo'],
                                ':datum'=>$_POST['datum'],
                                ':jidelnicek'=>$zvoleny['jidelnicek_id']]);
    }
    if(!empty($_POST['idjidlo'])&& $_POST['stav']=='snedeno'){#smazani snedeneho jidla
        $querysmazat= $db->prepare('DELETE FROM den WHERE datum=:datum AND jidlo_id=:idjidlo ');
        $querysmazat->execute([':datum'=>$_POST['datum'],
                                ':idjidlo'=>$_POST['idjidlo']]);

    }
}else{#pri nacteni straanky
    $datum=date('Y-m-d');
}
#ulozeni snedeneho jidla


#nacteni dnesniho jidla a snedenych tech blbu
$querydnesek= $db->prepare('SELECT jidlo_id FROM den WHERE uzivatele_id=:iduzivatele and datum=:dneska;');
$querydnesek->execute([
    ':iduzivatele'=>$_SESSION['user_id'],
    ':dneska'=>$datum
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
        <div class="form-group">
            <form method="post" class="form-inline">
                <label for="datum">Datum: </label>
                <input type="date" name="datum" id="datum" required class="form-control" value="<?php echo $datum;?>" />
                <button type="submit" class="btn btn-outline-primary" >Zvolit</button>
            </form>
        </div>
<h3>
Statistika:
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
                    <form method="get" action="popisjidla.php">
                        <input type="hidden" name="idjidlo" value="<?php echo $jidlo['id']; ?>">
                        <button type="submit" class="btn btn-outline-info">Info</button>
                    </form>
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
                                <input type="hidden" name="datum" value="<?php echo $datum; ?>"/>
                                <input type="hidden" name="stav" value="snedeno"/>
                                <button type="submit" class="btn btn-outline-warning">Snědeno</button>

                            </form>
                            <?
                        }
                        else
                        {
                            ?>
                            <form method="post">
                                <input type="hidden" name="idjidlo" value="<?php echo $jidlo['id']; ?>"/>
                                <input type="hidden" name="datum" value="<?php echo $datum; ?>"/>
                                <input type="hidden" name="stav" value="vybrat"/>
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
<div>
    <table class="table">
        <thead class="thead-light">
        <tr>
            <th colspan="3" class="text-center">Vlastní Jídla</th>
        </tr>
        <tr>
            <th> Název</th>
            <th> Popis</th>
            <th> Stav</th>
        </tr>
        </thead>
        <?php #vybrani vlastnich jidel
            $queryvlastni = $db->prepare('SELECT * FROM jidlo WHERE vlastnik=:iduzivatele');
            $queryvlastni->execute([':iduzivatele'=>$_SESSION['user_id']]);
            $vlastni = $queryvlastni->fetchAll(PDO::FETCH_ASSOC);
        foreach ($vlastni as $jidlo){
            ?>
            <tr>
                <th> <?php echo $jidlo['nazev']; ?></th>
                <th>
                    <form method="get" action="popisjidla.php">
                        <input type="hidden" name="idjidlo" value="<?php echo $jidlo['id']; ?>">
                        <button type="submit" class="btn btn-outline-info">Info</button>
                    </form>
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
                            <input type="hidden" name="datum" value="<?php echo $datum; ?>"/>
                            <input type="hidden" name="stav" value="snedeno"/>
                            <button type="submit" class="btn btn-outline-warning">Snědeno</button>

                        </form>
                        <?
                    }
                    else
                    {
                        ?>
                        <form method="post">
                            <input type="hidden" name="idjidlo" value="<?php echo $jidlo['id']; ?>"/>
                            <input type="hidden" name="datum" value="<?php echo $datum; ?>"/>
                            <input type="hidden" name="stav" value="vybrat"/>
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
        <thead class="thead-light">
            <th colspan="3">
                <a type="button" class="btn btn-primary" href="pridatvlastni.php">Přidat vlastní</a>
            </th>

        <thead class="thead-light">
    </table>
</div>

















<?php

require_once "inc/footer.php";