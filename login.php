<?php
//načteme připojení k databázi a inicializujeme session
session_start();
require_once 'inc/db.php';
require_once 'inc/facebook.php';
if (!empty($_SESSION['user_id'])){
    //uživatel už je přihlášený, nemá smysl, aby se přihlašoval znovu
    header('Location: prehled.php');
    exit();
}

$errors=false;
if (!empty($_POST)){
    #region zpracování formuláře
    $userQuery=$db->prepare('SELECT * FROM uzivatele WHERE email=:email LIMIT 1;');
    $userQuery->execute([
        ':email'=>trim($_POST['email'])
    ]);
    if ($user=$userQuery->fetch(PDO::FETCH_ASSOC)){

        if (password_verify($_POST['password'],$user['password'])){
            //heslo je platné => přihlásíme uživatele
            $_SESSION['user_id']=$user['id'];
            $_SESSION['user_name']=$user['name'];
            $_SESSION['user_admin']=$user['administrator'];
            $_SESSION['user_jidelnicek']=$user['jidelnicek_id'];
            header('Location: prehled.php');
            exit();
        }else{
            $errors=true;
        }

    }else{
        $errors=true;
    }
    #endregion zpracování formuláře
}

//vložíme do stránek patičku
$pageTitle='Přihlášení uživatele';
include 'inc/header.php';

#prihlasovani pomoci facebooku
//inicializujeme helper pro vytvoření odkazu
$fbHelper = $fb->getRedirectLoginHelper();

//nastavení parametrů pro vyžádání oprávnění a odkaz na přesměrování po přihlášení
$permissions = ['email'];
$callbackUrl = htmlspecialchars('https://eso.vse.cz/~polo03/SemestralkaWA/fb-callback.php');
//TODO nezapomeňte v předchozím řádku upravit adresu ke své vlastní aplikaci

//necháme helper sestavit adresu pro odeslání požadavku na přihlášení
$fbLoginUrl = $fbHelper->getLoginUrl($callbackUrl, $permissions);




?>

    <h2>Přihlášení uživatele</h2>

    <form method="post">
        <div class="form-group">
            <label for="email">E-mail:</label>
            <input type="email" name="email" id="email" required class="form-control <?php echo ($errors?'is-invalid':''); ?>" value="<?php echo htmlspecialchars(@$_POST['email'])?>"/>
            <?php
            echo ($errors?'<div class="invalid-feedback">Neplatná kombinace přihlašovacího e-mailu a hesla.</div>':'');
            ?>
        </div>
        <div class="form-group">
            <label for="password">Heslo:</label>
            <input type="password" name="password" id="password" required class="form-control <?php echo ($errors?'is-invalid':''); ?>" />
        </div>
        <button type="submit" class="btn btn-primary">přihlásit se</button>
        <a href="registration.php" class="btn btn-light">registrovat se</a>
        <a href="<?php echo $fbLoginUrl; ?>" class="btn btn-light">přihlásit se pomocí Facebooku</a>
        <a href="index.php" class="btn btn-light">zrušit</a>

    </form>

<?php
//vložíme do stránek patičku
include 'inc/footer.php';

