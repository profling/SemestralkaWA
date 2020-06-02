<?php
require_once 'inc/user.php'; //pripojeni k databayi a incicialiyace session
require_once "inc/header.php";
if($_SESSION['user_admin']!=1){// pokud neni admin nema tu co delat
    header('location:prehled.php');
}
?>
<h3>Administrace Jídelníčku</h3>
<?php

 $query = $db->prepare('Select * from jidelnicek;');
 $query->execute();
$jidelnicky= $query->fetchALL(PDO::FETCH_ASSOC);
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
        echo "<th>".$jidelnicek['nazev']."</th>"."<th>".$jidelnicek['popis']."</th>"."<th>".$jidelnicek['cukry']."g</th>"."<th>".$jidelnicek['sacharidy']."g</th>"."<th>".$jidelnicek['bilkoviny']."g</th>";
        //odkaz na doporucena jidla
        echo "<th>";?>
        <form method="get" action="zmenajidla.php">
            <input type="hidden" name="idjidelnicek" value="<?php echo $jidelnicek['id']; ?>"/>
            <button type="submit" class="btn btn-outline-secondary">Jídla </button>
        </form>
        <?php echo "</th>";
        echo "<th>";?>
        <form method="get" action="editjidelnicek.php">
            <input type="hidden" name="idjidelnicek" value="<?php echo $jidelnicek['id']; ?>"/>
            <button type="submit" class="btn btn-outline-secondary">Upravit </button>
        </form>
        <?php echo "</th>";
        echo "</tr>";
    }
    ?>
    <thead class="thead-light">
        <th colspan="7">
            <a type="button" class="btn btn-primary" href="pridatjidelnicek.php">Přidat Jídelníček</a>
        </th>

    </thead>
</table>


















<?php
require_once 'inc/footer.php';