<?php
//načteme připojení k databázi a inicializujeme session
session_start();
require_once 'inc/db.php';
require_once 'inc/facebook.php';

if (!empty($_SESSION['user_id'])){
    //uživatel už je přihlášený, nemá smysl, aby se registroval
    header('Location: prehled.php');
    exit();
}
#prihlasovani pomoci facebooku
//inicializujeme helper pro vytvoření odkazu
$fbHelper = $fb->getRedirectLoginHelper();

//nastavení parametrů pro vyžádání oprávnění a odkaz na přesměrování po přihlášení
$permissions = ['email'];
$callbackUrl = htmlspecialchars('https://eso.vse.cz/~polo03/SemestralkaWA/fb-callback.php');
//TODO nezapomeňte v předchozím řádku upravit adresu ke své vlastní aplikaci

//necháme helper sestavit adresu pro odeslání požadavku na přihlášení
$fbLoginUrl = $fbHelper->getLoginUrl($callbackUrl, $permissions);

$errors=[];
if (!empty($_POST)){
    #region zpracování formuláře
    #region kontrola jména
    $name=trim(@$_POST['name']);
    if (empty($name)){
        $errors['name']='Musíte zadat své jméno či přezdívku.';
    }
    #endregion kontrola jména

    #region kontrola emailu
    $email=trim(@$_POST['email']);
    if (!filter_var($email,FILTER_VALIDATE_EMAIL)){
        $errors['name']='Musíte zadat platnou e-mailovou adresu.';
    }else{
        //kontrola, jestli již není e-mail registrovaný
        $mailQuery=$db->prepare('SELECT * FROM uzivatele WHERE email=:email AND password!=NULL LIMIT 1;');
        $mailQuery->execute([
            ':email'=>$email
        ]);
        if ($mailQuery->rowCount()>0){
            $errors['name']='Uživatelský účet s touto e-mailovou adresou již existuje.';
        }
    }
    #endregion kontrola emailu

    #region kontrola hesla
    if (empty($_POST['password']) || (strlen($_POST['password'])<5)){
        $errors['password']='Musíte zadat heslo o délce alespoň 5 znaků.';
    }
    if ($_POST['password']!=$_POST['password2']){
        $errors['password2']='Zadaná hesla se neshodují.';
    }
    #endregion kontrola hesla

    if (empty($errors)){
        //zaregistrování uživatele
        $password=password_hash($_POST['password'],PASSWORD_DEFAULT);
        #testjestliuzneni prihlasen pomoci fb
        $queryfb= $db->prepare('SELECT * from uzivatele WHERE email=:email');
        $queryfb->execute([':email'=>$_POST['email']]);
        if($queryfb->rowCount()>0){#uzivatel ma prihlaseni pomoci fb
            $uzivatel= $queryfb->fetch(PDO::FETCH_ASSOC);
            $query = $db->prepare('UPDATE uzivatele SET password=:password WHERE facebook_id=:facebookId;');
            $query->execute([':password'=>$password,
                                ':facebookId'=>$uzivatel['facebook_id'] ] );
        }else { #uzivatel nema prihlaseni pomoci fb
            $query=$db->prepare('INSERT INTO uzivatele (name, email, password, active) VALUES (:name, :email, :password, 1);');
            $query->execute([
                ':name'=>$name,
                ':email'=>$email,
                ':password'=>$password
            ]);
        }


        //uživatele rovnou přihlásíme
        $_SESSION['user_id']=$uzivatel['id'];
        $_SESSION['user_name']=$uzivatel['name'];

        //přesměrování na homepage
        header('Location: prehled.php');
        exit();
    }

    #endregion zpracování formuláře
}


//vložíme do stránek patičku
$pageTitle='Registrace nového uživatele';
include 'inc/header.php';
?>

    <h2>Registrace nového uživatele</h2>

    <form method="post">
        <div class="form-group">
            <label for="name">Jméno či přezdívka:</label>
            <input type="text" name="name" id="name" required class="form-control <?php echo (!empty($errors['name'])?'is-invalid':''); ?>"
                   value="<?php echo htmlspecialchars(@$name);?>" />
            <?php
            echo (!empty($errors['name'])?'<div class="invalid-feedback">'.$errors['name'].'</div>':'');
            ?>
        </div>
        <div class="form-group">
            <label for="email">E-mail:</label>
            <input type="email" name="email" id="email" required class="form-control <?php echo (!empty($errors['email'])?'is-invalid':''); ?>"
                   value="<?php echo htmlspecialchars(@$email);?>" />
            <?php
            echo (!empty($errors['email'])?'<div class="invalid-feedback">'.$errors['email'].'</div>':'');
            ?>
        </div>
        <div class="form-group">
            <label for="password">Heslo:</label>
            <input type="password" name="password" id="password" required class="form-control <?php echo (!empty($errors['password'])?'is-invalid':''); ?>" />
            <?php
            echo (!empty($errors['password'])?'<div class="invalid-feedback">'.$errors['password'].'</div>':'');
            ?>
        </div>
        <div class="form-group">
            <label for="password2">Potvrzení hesla:</label>
            <input type="password" name="password2" id="password2" required class="form-control <?php echo (!empty($errors['password2'])?'is-invalid':''); ?>" />
            <?php
            echo (!empty($errors['password2'])?'<div class="invalid-feedback">'.$errors['password2'].'</div>':'');
            ?>
        </div>
        <button type="submit" class="btn btn-primary">registrovat se</button>
        <a href="<?php echo $fbLoginUrl; ?>" class="btn btn-outline-primary">registrovat se pomocí Facebooku</a>
        <a href="login.php" class="btn btn-light">přihlásit se</a>
        <a href="index.php" class="btn btn-light">zrušit</a>
    </form>

<?php
//vložíme do stránek patičku
include 'inc/footer.php';
