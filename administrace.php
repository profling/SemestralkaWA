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
        echo "<th>".htmlspecialchars($jidelnicek['nazev'])."</th>"."<th>".htmlspecialchars($jidelnicek['popis'])."</th>"."<th>".htmlspecialchars($jidelnicek['cukry'])."g</th>"."<th>".htmlspecialchars($jidelnicek['sacharidy'])."g</th>"."<th>".htmlspecialchars($jidelnicek['bilkoviny'])."g</th>";
        //odkaz na doporucena jidla
        echo "<th>";?>
        <form method="get" action="zmenajidla.php">
            <input type="hidden" name="idjidelnicek" value="<?php echo htmlspecialchars($jidelnicek['id']); ?>"/>
            <button type="submit" class="btn btn-outline-secondary">Jídla </button>
        </form>
        <?php echo "</th>";
        echo "<th>";?>
        <form method="get" action="editjidelnicek.php">
            <input type="hidden" name="idjidelnicek" value="<?php echo htmlspecialchars($jidelnicek['id']); ?>"/>
            <button type="submit" class="btn btn-outline-primary">Upravit </button>
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
$queryJidla= $db->prepare('SELECT * FROM jidlo;');
$queryJidla->execute();
$jidla= $queryJidla->fetchAll(PDO::FETCH_ASSOC);
?>
<h3>Administrace Jídel</h3>
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
            <th></th>
        </tr>
        </thead>
        <?php
        foreach ($jidla as $jidlo) {
            echo "<tr>";
            echo "<th>" . htmlspecialchars($jidlo['nazev']) . "</th><th>" . htmlspecialchars($jidlo['popis'] ). "</th><th>" . htmlspecialchars($jidlo['cukry']) . "g</th><th>" . htmlspecialchars($jidlo['sacharidy'] ). "g</th><th>" . htmlspecialchars($jidlo['bilkoviny']) . "g</th>";
            echo "<th>";
            echo '<form  method="get" action="editjidlo.php">';
            echo '<input type="hidden" name="idjidla" value="'.htmlspecialchars($jidlo['id']).'"/>';
            echo '<button type="submit" class="btn btn-outline-primary">Upravit</button>';
            echo '</form>';
            echo "</th>";
            echo "</tr>";
        }
        ?>
        <thead class="thead-light">
        <th colspan="6">
            <a type="button" class="btn btn-primary" href="pridatjidlo.php">Přidat Jídlo</a>
        </th>

        </thead>
    </table>















<?php
require_once 'inc/footer.php';