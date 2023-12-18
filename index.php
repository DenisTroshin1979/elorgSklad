<?php
require_once "login.php";
require_once "utils.php";

if ($loggedin)
{
    header("Location: materials.php");
    die();
}    

$intLoginStorekeeperID=0;
$strCurrentStorekeeper="";
$login="";
$pass="";

// как задокументировать изменения в php файлах(что поменялось, когда, в каких файлах)

// ? filter_input заменяет обезвреживание с помощью real_escape_string (mysqli) ?

//$login=filter_input(INPUT_POST, 'LoginEdit', FILTER_DEFAULT);

if (isset($_POST['btnEnter']))
{
    if (!empty($_POST['LoginEdit']))
        $login=get_post($conn, 'LoginEdit');
    if (!empty($_POST['PasswordEdit']))  
        $pass=get_post($conn, 'PasswordEdit');
    if (!empty($_POST['comboLoginStorekeepers']))
        $intLoginStorekeeperID=get_post($conn, 'comboLoginStorekeepers');

    if ($intLoginStorekeeperID==0)
        echo "Кладовщик не выбран";
    else    
    {    
        $sk=getStorekeeperByID($conn, $intLoginStorekeeperID);

        if ( ($sk->login!=$login) || !password_verify($pass, $sk->pass) )
            echo "Логин и/или пароль введены неверно!";
        else
        {    
            //$_SESSION['strCurrentStorekeeper'] = $sk->surname . ' ' . $sk->fname . ' ' . $sk->midname;
            $_SESSION['idCurrentStorekeeper'] = $intLoginStorekeeperID;
            header("Location: materials.php");
            die();
        }
    }    
}
        
echo <<< _END
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- <script src="jquery/jquery-3.6.0.js"></script>  -->
        <script type="text/javascript" src="bootstrap/js/bootstrap.bundle.min.js"></script>
        <script type="text/javascript" src="login.js"></script>
        <script type="text/javascript" src="events.js"></script>
        <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">         
        <title>Управление складом электромонтажной организации</title>
    </head>
    <body>
            <div class="container">
            <div class="row">
                <div class="col-auto">
                
                <form name="LoginForm" action="index.php" method="post" enctype="application/x-www-form-urlencoded" accept-charset="utf-8"> <!-- onSubmit="onEnterBtnClick() -->
                    <h1 align="center">Управление складом<br>электромонтажной организации</h1><br>
                    <fieldset class="border rounded-3 p-3">
                    <legend class="float-none w-auto px-3">Авторизация</legend>

                    <label class="form-label" for="idcomboLoginStorekeepers">Кладовщик</label><br>
_END;

UpdateLoginStorekeepersCombo($conn, "comboLoginStorekeepers", "idcomboLoginStorekeepers", "Выберите кладовщика...", 1, $intLoginStorekeeperID);

echo <<< _END
                    <label class="form-label" for="idLoginEdit">Логин</label> <br> 
                    <input class="form-control" name="LoginEdit" type="text" title="Введите логин" id="idLoginEdit" maxlength="50" size="30" tabindex="2" required><br> 
                    <label class="form-label" for="idPasswordEdit">Пароль</label> <br> 
                    <input class="form-control" name="PasswordEdit" type="password" title="Введите пароль" id="idPasswordEdit" maxlength="50" size="30" tabindex="3"><br> 
                     <br>
                    </fieldset>
                    <br>
                    <button class="btn btn-primary form-control" name="btnEnter" type="submit" value="enter" tabindex="4">Вход</button>
                </form>
                    
               </div>
            </div> <!-- end row 1 -->
        </div> <!-- end container -->
    </body>
</html>
_END;
?>

