<?php
require_once 'inc/user.php'; //pripojeni k databayi a incicialiyace session
require_once "inc/header.php";
$data=[];#pokud se nepovede input tak se to v pise do toho formulare
if (!empty($_POST)) { #pridani vlastniho jidla
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
    if (empty($errors)) {#pridani
        $queryPridat= $db->prepare('INSERT INTO jidlo (nazev, popis, cukry, sacharidy, bilkoviny, vlastnik) VALUE (:nazev, :popis, :cukry, :sacharidy, :bilkoviny, :vlastnik)');
        $queryPridat->execute([':nazev'=>$_POST['nazev'],
                                ':popis'=>$_POST['popis'],
                                ':cukry'=>$_POST['cukry'],
                                ':sacharidy'=>$_POST['sacharidy'],
                                ':bilkoviny'=>$_POST['bilkoviny'],
                                ':vlastnik'=>$_SESSION['user_id']]);
        header('Location: jidlo.php');
    }else{
        $data['nazev']=$_POST['nazev'];
        $data['popis']=$_POST['popis'];
        $data['cukry']=$_POST['cukry'];
        $data['bilkoviny']=$_POST['bilkoviny'];
        $data['sacharidy']=$_POST['sacharidy'];

    }
}

?>
<h3>Přidání vlastního jídla</h3>
<form method="post">
    <div class="form-group">
        <label for="nazev">Název:</label>
        <input type="text" required id="nazev" name="nazev" class="form-control" value="<?php if(!empty($data)){echo htmlspecialchars($data['nazev']);}?>"/>
    </div>
    <div class="form-group">
        <label for="popis">Popis:</label>
        <textarea type="text" required id="popis" name="popis" class="form-control"><?php if(!empty($data)){echo htmlspecialchars($data['popis']);}?></textarea>
    </div>
    <div class="form-inline">
        <div class="form-group">
            <label for="cukry" class="mb-2 mr-sm-2 mb-sm-0"> Cukry:</label>
            <input type="number" required id="cukry" name="cukry" class="form-control mb-2 mr-sm-2 mb-sm-0 <?php echo (!empty($errors['cukry'])?'is-invalid':''); ?>" value="<?php if(!empty($data)){echo htmlspecialchars($data['cukry']);}?>"/>
            <?php
            if (!empty($errors['cukry'])){
                echo '<div class="invalid-feedback" >'.$errors['cukry'].'</div>';
            }
            ?>
        </div>
        <div class="form-group">
             <label for="sacharidy" class="mb-2 mr-sm-2 mb-sm-0"> Sacharidy:</label>
             <input type="number" required id="sacharidy" name="sacharidy" class="form-control mb-2 mr-sm-2 mb-sm-0 <?php echo (!empty($errors['sacharidy'])?'is-invalid':''); ?>" value="<?php if(!empty($data)){echo htmlspecialchars($data['sacharidy']);}?>"/>
            <?php
            if (!empty($errors['sacharidy'])){
                echo '<div class="invalid-feedback">'.$errors['sacharidy'].'</div>';
            }
            ?>
        </div>
        <div class="form-group">
            <label for="bilkoviny" class="mb-2 mr-sm-2 mb-sm-0 "> Bílkoviny:</label>
            <input type="number" required id="bilkoviny" name="bilkoviny" class="form-control mb-2 mr-sm-2 mb-sm-0 <?php echo (!empty($errors['bilkoviny'])?'is-invalid':''); ?>" value="<?php if(!empty($data)){echo htmlspecialchars($data['bilkoviny']);}?>"/>
            <?php
            if (!empty($errors['bilkoviny'])){
                echo '<div class="invalid-feedback">'.$errors['bilkoviny'].'</div>';
            }
            ?>
        </div>
    </div>


    <br>
    <div class="form-group">
        <button type="submit" class="btn btn-primary">Uložit</button>
        <a href="jidlo.php" type="button" class="btn btn-outline-secondary">Zpět</a>
    </div>
</form>















<?php
require_once "inc/footer.php";