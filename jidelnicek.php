<?php
require_once 'inc/user.php'; //pripojeni k databayi a incicialiyace session
require_once "inc/header.php";
#nacteni jidelnicku
$query = $db->prepare('Select * from jidelnicek;');
$query->execute();
$jidelnicky= $query->fetchALL(PDO::FETCH_ASSOC);

#vybrani a ulezeni daneho jidelnicku
if(!empty($_POST)){
$queryVybrani = $db->prepare('UPDATE uzivatele SET jidelnicek_id=:idjidelnicek WHERE id=:iduser;');
$queryVybrani->execute([':idjidelnicek'=>$_POST['idjidelnicek'],
                            ':iduser'=>$_SESSION['user_id']]);
$_SESSION['user_jidelnicek']= $_POST['idjidelnicek'];
}
?>
<table class="table">
    <thead class="thead-light">
    <th>Název</th>
    <th>Popis</th>
    <th>Denní dávka <br>cukrů </th>
    <th> Denní dávka <br>sacharidů</th>
    <th>Denní dávka <br>bílkovin</th>
    <th>Doporučená jídla</th>
    <th></th>
    </thead>
    <?php
    foreach ($jidelnicky as $jidelnicek){
        echo "<tr>";
        //vypsani hodnot jidel
        echo "<th>".htmlspecialchars($jidelnicek['nazev'])."</th>"."<th>".htmlspecialchars($jidelnicek['popis'])."</th>"."<th>".htmlspecialchars($jidelnicek['cukry'])."g</th>"."<th>".htmlspecialchars($jidelnicek['sacharidy'])."g</th>"."<th>".htmlspecialchars($jidelnicek['bilkoviny'])."g</th>";
        //odkaz na doporucena jidla
        echo "<th>";?>
        <form method="get" action="doporucenajidla.php">
            <input type="hidden" name="idjidelnicek" value="<?php echo $jidelnicek['id']; ?>"/>
            <button type="submit" class="btn btn-outline-secondary">Jídla </button>
        </form>
        <?php echo "</th>";
        //vybrani jidelnicku zvoleny jidelicek je odlisen jinou barvou
        $queryzvolen= $db->prepare('Select jidelnicek_id from uzivatele where id=:iduser');
        $queryzvolen->execute([':iduser'=>$_SESSION['user_id']]);
        $zvoleny= $queryzvolen->fetch(PDO::FETCH_ASSOC);
        echo "<th>";
        if ($zvoleny['jidelnicek_id']==$jidelnicek['id'])
        {
            ?>
            <form method="post">
                <input type="hidden" name="idjidelnicek" value="<?php echo htmlspecialchars( $jidelnicek['id']); ?>"/>
                <button type="submit" class="btn btn-outline-warning">Vybrán</button>
            </form>
            <?php
        }
        else{
            ?>
            <form method="post">
                <input type="hidden" name="idjidelnicek" value="<?php echo htmlspecialchars($jidelnicek['id']); ?>"/>
                <button type="submit" class="btn btn-outline-success">Vybrat</button>
            </form>
            <?php
        }
            echo "</th>";
        echo "</tr>";
    }
    ?>
</table>
















<?php
require_once "inc/footer.php";
?>


