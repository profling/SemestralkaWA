<?php
require_once 'inc/user.php'; //pripojeni k databayi a incicialiyace session
require_once "inc/header.php";
if($_SESSION['user_admin']!=1){// pokud neni admin nema tu co delat
    header('location:prehled.php');
}
#nactenijidelnicku
$queryJidelnicek = $db->prepare('SELECT * FROM jidelnicek WHERE id=:idjidelnicek;');
$queryJidelnicek->execute([':idjidelnicek'=>$_GET['idjidelnicek']]);
$jidelnicek = $queryJidelnicek->fetch(PDO::FETCH_ASSOC);

#editace jidelnicku
if(!empty($_POST)){
    #smazani
    if(!empty($_POST['idjidelnicek'])){
        $querySmazat = $db->prepare('DELETE FROM jidelnicek WHERE id=:idjidelnicek;');
        $querySmazat->execute([':idjidelnicek'=>$_POST['idjidelnicek']]);
        header('location:administrace.php');
    }
    $errors = [];#kontrola jestli jsou data validni
    if ($_POST['cukry'] < 0) {
        $errors['cukry'] = 'Hodnota pole Cukry musí být větší nebo rovna 0.';
    }
    if ($_POST['bilkoviny'] < 0) {
        $errors['bilkoviny'] = 'Hodnota pole Bílkoviny musí být větší nebo rovna 0.';
    }
    if ($_POST['sacharidy'] < 0) {
        $errors['sacharidy'] = 'Hodnota pole Sacharidy musí být větší nebo rovna 0.';
    }
    if(empty($errors)){
        $queryzmena = $db->prepare('UPDATE jidelnicek SET nazev=:nazev, popis=:popis, cukry=:cukry, bilkoviny=:bilkoviny, sacharidy=:sacharidy WHERE id=:idjidelnicek; ');
        $queryzmena->execute([':nazev'=>$_POST['nazev'],
                                ':popis'=>$_POST['popis'],
                                ':cukry'=>$_POST['cukry'],
                                ':bilkoviny'=>$_POST['bilkoviny'],
                                ':sacharidy'=>$_POST['sacharidy'],
                                ':idjidelnicek'=>$_GET['idjidelnicek']]);
        header('location:administrace.php');
    }else{
        $jidelnicek['nazev']=$_POST['nazev'];
        $jidelnicek['popis']=$_POST['popis'];
        $jidelnicek['cukry']=$_POST['cukry'];
        $jidelnicek['bilkoviny']=$_POST['bilkoviny'];
        $jidelnicek['sacharidy']=$_POST['sacharidy'];
    }
}
?>
<h3>Úprava jídelníčku</h3>
<form id="edit" method="post"></form>
<form id="smazat" method="post"></form>
<input type="hidden" name="idjidelnicek" form="smazat" value="<?php echo $_GET['idjidelnicek']; ?>">
    <div class="form-group">
        <label for="nazev">Název:</label>
        <input type="text"form="edit" required id="nazev" name="nazev" class="form-control" value="<?php echo htmlspecialchars($jidelnicek['nazev']);?>"/>
    </div>
    <div class="form-group">
        <label for="popis">Popis:</label>
        <textarea type="text"form="edit" required id="popis" name="popis" class="form-control"><?php echo htmlspecialchars($jidelnicek['popis']);?></textarea>
    </div>
    <div class="form-inline">
        <div class="form-group">
            <label for="cukry" class="mb-2 mr-sm-2 mb-sm-0"> Cukry:</label>
            <input type="number"form="edit" required id="cukry" name="cukry" class="form-control mb-2 mr-sm-2 mb-sm-0 <?php echo (!empty($errors['cukry'])?'is-invalid':''); ?>" value="<?php echo htmlspecialchars($jidelnicek['cukry']);?>"/>
            <?php
            if (!empty($errors['cukry'])){
                echo '<div class="invalid-feedback" >'.$errors['cukry'].'</div>';
            }
            ?>
        </div>
        <div class="form-group">
            <label for="sacharidy" class="mb-2 mr-sm-2 mb-sm-0"> Sacharidy:</label>
            <input type="number"form="edit" required id="sacharidy" name="sacharidy" class="form-control mb-2 mr-sm-2 mb-sm-0 <?php echo (!empty($errors['sacharidy'])?'is-invalid':''); ?>" value="<?php echo htmlspecialchars($jidelnicek['sacharidy']);?>"/>
            <?php
            if (!empty($errors['sacharidy'])){
                echo '<div class="invalid-feedback">'.$errors['sacharidy'].'</div>';
            }
            ?>
        </div>
        <div class="form-group">
            <label for="bilkoviny" class="mb-2 mr-sm-2 mb-sm-0 "> Bílkoviny:</label>
            <input type="number"form="edit" required id="bilkoviny" name="bilkoviny" class="form-control mb-2 mr-sm-2 mb-sm-0 <?php echo (!empty($errors['bilkoviny'])?'is-invalid':''); ?>" value="<?php echo htmlspecialchars($jidelnicek['bilkoviny']);?>"/>
            <?php
            if (!empty($errors['bilkoviny'])){
                echo '<div class="invalid-feedback">'.$errors['bilkoviny'].'</div>';
            }
            ?>
        </div>
    </div>


    <br>
    <div class="form-group">
        <button type="submit"form="edit" class="btn btn-primary">Uložit</button>
        <button type="submit"form="smazat" class="btn btn-danger">Smazat</button>
        <a href="administrace.php" type="button" class="btn btn-outline-secondary">Zpět</a>
    </div>


