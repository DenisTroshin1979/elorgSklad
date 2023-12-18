<?php
    require_once "login.php";
    require_once "utils.php";

    define("MODE_VIEWSTOREKEEPERS", 0);
    define("MODE_NEWSTOREKEEPER", 1);
    define("MODE_EDITSTOREKEEPER", 2);

    $intStorekeeperID=0;
    $intStorekeeperListMode=MODE_VIEWSTOREKEEPERS;
    $strSurname="";
    $strFName="";
    $strMidName="";
    $strPassport="";
    $strPhone="";
    $strLogin="";
    $strPass="";
    $bCheckboxChangePass=false;
            
    if (!$loggedin)
    {
        echo "Необходимо авторизоваться.";
        die();
    }
   
    // нажатие на кнопку "Новый кладовщик"
    if (isset($_POST['btnNewStorekeeper']))
    {
        $intStorekeeperListMode=MODE_NEWSTOREKEEPER;
        $_SESSION['intStorekeeperListMode']=$intStorekeeperListMode;
        
    }
    // нажатие на кнопку "Внести изменения" или "Отмена" в режиме редактирования кладовщика
    else if ( isset($_POST['Save']) )
    {
        if ($_POST['Save']=='Save')
        {
            if (isset($_POST['editStorekeeperID']))
                $intStorekeeperID=get_post($conn, 'editStorekeeperID'); // hidden field in form
            else 
                $intStorekeeperID=0;

            if (isset($_POST['editSurname']))
                $strSurname=trim(get_post($conn, 'editSurname'));
            else 
                $strSurname="";
            if (isset($_POST['editFName']))
                $strFName=trim(get_post($conn, 'editFName'));
            else 
                $strFName="";
            if (isset($_POST['editMidName']))
                $strMidName=trim(get_post($conn, 'editMidName'));
            else 
                $strMidName="";
            if (isset($_POST['editPassport']))
                $strPassport=trim(get_post($conn, 'editPassport'));
            else 
                $strPassport="";
            if (isset($_POST['editPhone']))
                $strPhone=trim(get_post($conn, 'editPhone'));
            else 
                $strPhone="";
            if (isset($_POST['editLogin']))
                $strLogin=trim(get_post($conn, 'editLogin'));
            else 
                $strLogin="";

            if (isset($_POST['checkboxChangePass']))
            {
                $bCheckboxChangePass=get_post($conn, 'checkboxChangePass');
                
                if ($bCheckboxChangePass)
                {
                    if (isset($_POST['editPass']))
                        $strPass=trim(get_post($conn, 'editPass'));
                    else 
                        $strPass="";
                }
           }
          
            StorekeepersList_SaveChanges();
            
            $intStorekeeperListMode=MODE_VIEWSTOREKEEPERS;
            $_SESSION['intStorekeeperListMode']=$intStorekeeperListMode;            
        }
        // или нажата кнопка Отмена
        else if ($_POST['Save']=='Cancel')
        {
            $intStorekeeperListMode=MODE_VIEWSTOREKEEPERS;
            $_SESSION['intStorekeeperListMode']=$intStorekeeperListMode;            
        }
    }
    // нажатие на кнопку "Сохранить кладовщика" или "Отмена" в режиме добавления кладовщика
    else if ( isset($_POST['Add']) )
    {
        if ($_POST['Add']=='Add')
        {
            if (isset($_POST['editSurname']))
                $strSurname=trim(get_post($conn, 'editSurname'));
            else 
                $strSurname="";
            if (isset($_POST['editFName']))
                $strFName=trim(get_post($conn, 'editFName'));
            else 
                $strFName="";
            if (isset($_POST['editMidName']))
                $strMidName=trim(get_post($conn, 'editMidName'));
            else 
                $strMidName="";
            if (isset($_POST['editPassport']))
                $strPassport=trim(get_post($conn, 'editPassport'));
            else 
                $strPassport="";
            if (isset($_POST['editPhone']))
                $strPhone=trim(get_post($conn, 'editPhone'));
            else 
                $strPhone="";
            if (isset($_POST['editLogin']))
                $strLogin=trim(get_post($conn, 'editLogin'));
            else 
                $strLogin="";
            if (isset($_POST['editPass']))
                $strPass=trim(get_post($conn, 'editPass'));
            else 
                $strPass="";

            
            StorekeepersList_Add();
            
            $intStorekeeperListMode=MODE_VIEWSTOREKEEPERS;      
            $_SESSION['intStorekeeperListMode']=$intStorekeeperListMode;            
        }
        // или нажата кнопка Отмена
        else if ($_POST['Add']=='Cancel')
        {
            $intStorekeeperListMode=MODE_VIEWSTOREKEEPERS;
            $_SESSION['intStorekeeperListMode']=$intStorekeeperListMode;            
        }
        
    }
    // переход по ссылке из списка кладовщиков
    else if (isset($_GET['user_id']) )
    {
        $intStorekeeperID=get_get($conn, 'user_id');
        
        // загрузить данные о кладовщике из БД по ID
        if ($intStorekeeperID!=0)
        {
            $ski=getStorekeeperByID($conn, $intStorekeeperID);
            $strSurname=$ski->surname;
            $strFName=$ski->fname;
            $strMidName=$ski->midname;
            $strPassport=$ski->passport;
            $strPhone=$ski->phone;
            $strLogin=$ski->login;
            $strPass=$ski->pass;

            $intStorekeeperListMode=MODE_EDITSTOREKEEPER;
            $_SESSION['intStorekeeperListMode']=$intStorekeeperListMode;
        }
    }
    else 
    {
        //if (isset($_SESSION['intStorekeeperListMode'])) $intStorekeeperListMode=$_SESSION['intStorekeeperListMode'];
    }
    
    echo <<< _END
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script type="text/javascript" src="bootstrap/js/bootstrap.bundle.min.js"></script>
        <script type="text/javascript" src="login.js"></script>
        <script type="text/javascript" src="events.js"></script>        
        <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">         
        <title>Управление складом электромонтажной организации</title>
    </head>
    <body>
    _END;
    
    echo <<< _END
    <div class="container-fluid">
            <div class="row">   <!-- row 1-->
                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6"> <!-- r2 c1-->
    _END; 
        
    require 'menu.php';
    
    if (!IsAdmin($conn))
    { echo("Только администратор может редактировать список кладовщиков."); die(); }    

    echo <<< _END
    <h1>Кладовщики</h1><br>
    </div> <!-- end col 1-->
    </div> <!--  end row 1-->
    _END;
    
    echo <<< _END
    <div class="row">    <!-- row 2 -->
    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">    <!-- r2 c2-->
    _END;
    
    
    echo <<< _END
    <form name="StorekeeperListForm" action="storekeepers.php" method="post" enctype="application/x-www-form-urlencoded" accept-charset="utf-8">
    <button name="btnNewStorekeeper" type="submit" tabindex="1">Новый кладовщик</button><br>
    </form><br>
    _END;                              
    
    $query="SELECT  user_id, user_surname, user_fname, user_midname, user_passport, user_phone, user_login FROM users;";
    
    $datasetStorekeepers=$conn->query($query);
    if(!$datasetStorekeepers) die($conn->connect_error);

    if ($datasetStorekeepers->num_rows>0)  
    {
        echo  "<table class=\"table w-auto small table-sm table-striped table-bordered table-hover table-responsive\">";
        echo "<thead><tr>
              <th scope=\"col\">Код</th>
              <th scope=\"col\">Фамилия</th>
              <th scope=\"col\">Имя</th>
              <th scope=\"col\">Отчество</th>
              <th scope=\"col\">Логин</th>
              <th scope=\"col\">Паспорт</th>              
              <th scope=\"col\">Телефон</th>              
              </tr></thead>";
        echo "<tbody>";
        for($j=0; $j<$datasetStorekeepers->num_rows; ++$j)
        {
              echo "<tr>";
              $datasetStorekeepers->data_seek($j);
              $row=$datasetStorekeepers->fetch_array(MYSQLI_ASSOC);

              echo "<td>$row[user_id]</td>
                   <td><a href=\"storekeepers.php?user_id=$row[user_id]\">$row[user_surname]</a></td>
                   <td>$row[user_fname]</td>
                   <td>$row[user_midname]</td>
                   <td>$row[user_login]</td>
                   <td>$row[user_passport]</td>                       
                   <td>$row[user_phone]</td>";
              echo "</tr>";
        }
        echo "</tbody></table>";
    }
    echo "<p>";
    echo "Найдено записей: $datasetStorekeepers->num_rows"; 
    $datasetStorekeepers->close();
    
    echo <<< _END
    </div> <!-- end col 1 row 2-->
    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4"> <!-- r2 c2-->
    _END;

    if ($intStorekeeperListMode==MODE_NEWSTOREKEEPER)
    {
        echo <<< _END
        <form name="StorekeeperAddForm" action="storekeepers.php" method="post" enctype="application/x-www-form-urlencoded" accept-charset="utf-8"> 
        
        <fieldset class="border rounded-3 p-3">
        <legend class="float-none w-auto px-3">Добавление нового кладовщика</legend>

        <label class="form-label" for="editSurname">Фамилия</label>
        <input class="form-control" type="text" name="editSurname" value='$strSurname' id="editSurname" maxlength="25" size="25" tabindex="1"><br> 

        <label class="form-label" for="editFName">Имя</label>
        <input class="form-control" type="text" name="editFName" value='$strFName' id="editFName" maxlength="25" size="25" tabindex="2"><br> 

        <label class="form-label" for="editMidName">Отчество</label>
        <input class="form-control" type="text" name="editMidName" value='$strMidName' id="editMidName" maxlength="25" size="25" tabindex="3"><br> 
        
        <label class="form-label" for="editPassport">Паспорт</label>
        <input class="form-control" type="text" name="editPassport" value='$strPassport' id="editPassport" maxlength="20" size="20" tabindex="4"><br> 
                
        <label class="form-label" for="editPhone">Телефон</label>
        <input class="form-control" type="text" name="editPhone" value='$strPhone' id="editPhone" maxlength="20" size="20" tabindex="5"><br> 

        <label class="form-label" for="editLogin">Логин</label>
        <input class="form-control" type="text" name="editLogin" value='$strLogin' id="editLogin" maxlength="20" size="20" tabindex="6><br> 

        <label class="form-label" for="editPass">Пароль</label>
        <input class="form-control" type="text" name="editPass" value='$strPass' id="editPass" maxlength="255" size="20" tabindex="7"><br> 
   
        <br>
        </fieldset>
        <br>
        <button class="btn btn-primary form-control" name="Add" type="submit" value="Add" tabindex="8">Сохранить кладовщика</button>&nbsp;
        <button class="btn btn-primary form-control" name="Add" type="submit" value="Cancel" tabindex="9">Отмена</button>&nbsp;
        </form>
        _END;
    }
    else if ($intStorekeeperListMode==MODE_EDITSTOREKEEPER)
    {
        if ($bCheckboxChangePass)
        {    
            $strPassDisabled="";
            $strCheckBoxChecked="checked";
        }
        else
        {    
            $strPassDisabled="disabled";
            $strCheckBoxChecked="";                    
        }
        echo <<< _END
        <form name="StorekeeperEditForm" action="storekeepers.php" method="post" enctype="application/x-www-form-urlencoded" accept-charset="utf-8"> 
        
        <fieldset class="border rounded-3 p-3">
        <legend class="float-none w-auto px-3">Редактирование кладовщика</legend>

        <input type="hidden" name="editStorekeeperID" value="$intStorekeeperID">
                
        <label class="form-label" for="editSurname">Фамилия</label>
        <input class="form-control" type="text" name="editSurname" value='$strSurname' id="editSurname" maxlength="25" size="25" tabindex="1"><br> 

        <label class="form-label" for="editFName">Имя</label>
        <input class="form-control" type="text" name="editFName" value='$strFName' id="editFName" maxlength="25" size="25" tabindex="2"><br> 

        <label class="form-label" for="editMidName">Отчество</label>
        <input class="form-control" type="text" name="editMidName" value='$strMidName' id="editMidName" maxlength="25" size="25" tabindex="3"><br> 
        
        <label class="form-label" for="editPassport">Паспорт</label>
        <input class="form-control" type="text" name="editPassport" value='$strPassport' id="editPassport" maxlength="20" size="20" tabindex="4"><br> 
                
        <label class="form-label" for="editPhone">Телефон</label>
        <input class="form-control" type="text" name="editPhone" value='$strPhone' id="editPhone" maxlength="20" size="20" tabindex="5"><br> 

        <label class="form-label" for="editLogin">Логин</label>
        <input class="form-control" type="text" name="editLogin" value='$strLogin' id="editLogin" maxlength="20" size="20" tabindex="6><br> 

        <label class="form-label" for="editPass">Пароль</label>
        <input class="form-control" type="text" name="editPass" id="editPass" $strPassDisabled maxlength="255" size="20" tabindex="7"><br> 
        <label class="form-label" for="checkboxChangePass">Для изменения пароля поставьте галочку</label>&nbsp;
        <input class="form-check-input" name="checkboxChangePass" type="checkbox" id="checkboxChangePass" tabindex="8" value='$bCheckboxChangePass' $strCheckBoxChecked onclick="checkboxChangePass_onClick()">
        <br>
        </fieldset>
        <br>
        <button class="btn btn-primary form-control" name="Save" type="submit" value="Save" tabindex="9">Сохранить изменения</button>&nbsp;
        <button class="btn btn-primary form-control" name="Save" type="submit" value="Cancel" tabindex="10">Отмена</button>&nbsp;

        </form>
        _END;
        
    }
    
    echo "</div>";  // end r2 c2

    echo "</div>";  // end row 2
        
    echo <<< _END
    </div> <!-- end container-->
    </body>
    </html>
    _END;
    //--------------------------------------------------------------------------
    function StorekeepersList_SaveChanges()
    {
        global $conn;
        global $intStorekeeperID;
        global $strSurname;
        global $strFName;
        global $strMidName;
        global $strPassport;
        global $strPhone;
        global $strLogin;
        global $strPass;
        global $bCheckboxChangePass;
        
        if ($strSurname=="")
	{ echo("Заполните поле Фамилия.");  return false; }
        if ($strFName=="")
	{ echo("Заполните поле Имя.");  return false; }
        if ($strLogin=="")
	{ echo("Заполните поле Логин.");  return false; }
        if ($strPassport=="")
	{ echo("Заполните поле Паспорт.");  return false; }
	

	// поля пароль, телефон, отчество не являются обязательными

        // проверить, есть ли заданный логин в таблице users
        $id=findStorekeeperByLogin($conn, $strLogin);
        if ($id!=0) 
        {
            if ($id!=$intStorekeeperID) // попытка поменять логин на логин, используемый другим кладовщиком
            { echo("Заданный логин использовать нельзя.");  return false;}
        }

        if ($bCheckboxChangePass) // пароль меняется
        {
            $strPassHash=password_hash($strPass, PASSWORD_DEFAULT);
        
            // сформируем SQL-запрос на Изменение записи в таблицу users
            $q="UPDATE users SET user_surname='$strSurname',user_fname='$strFName'," .
               "user_midname='$strMidName',user_passport='$strPassport'," .
               "user_phone='$strPhone',user_login='$strLogin',user_pass='$strPassHash'" .
               " WHERE user_id='$intStorekeeperID';";
        }
        else // запрос без пароля
            // сформируем SQL-запрос на Изменение записи в таблицу users
            $q="UPDATE users SET user_surname='$strSurname',user_fname='$strFName'," .
               "user_midname='$strMidName',user_passport='$strPassport'," .
               "user_phone='$strPhone',user_login='$strLogin'" .
               " WHERE user_id='$intStorekeeperID';";
            
        $result=$conn->query($q);
        if (!$result)
        { echo "Сбой записи"; return false;}
        else
        {    
            echo "Изменен кладовщик номер $intStorekeeperID.";
            return true;
        }   
    }
    //--------------------------------------------------------------------------
    function StoreKeepersList_Add()
    {
        global $conn;
        global $strSurname;
        global $strFName;
        global $strMidName;
        global $strPassport;
        global $strPhone;
        global $strLogin;
        global $strPass;

        if ($strSurname=="")
	{ echo("Заполните поле Фамилия.");  return false; }
        if ($strFName=="")
	{ echo("Заполните поле Имя.");  return false; }
        if ($strLogin=="")
	{ echo("Заполните поле Логин.");  return false; }
        if ($strPassport=="")
	{ echo("Заполните поле Паспорт.");  return false; }

	// поля пароль, телефон, отчество не являются обязательными

        // проверить, есть ли заданный логин в таблице users
        $id=findStorekeeperByLogin($conn, $strLogin);
        if ($id!=0) 
            { echo("Заданный логин использовать нельзя.");  return false;}
        
        $strPassHash=password_hash($strPass, PASSWORD_DEFAULT);
        
        // сформируем SQL-запрос на добавление записи в таблицу users
        // поля: 
	$q="INSERT INTO users (user_surname,user_fname,user_midname,user_passport,user_phone,user_login,user_pass)" .
           " VALUES('$strSurname','$strFName','$strMidName','$strPassport','$strPhone','$strLogin','$strPassHash');";
        
        $result=$conn->query($q);
        if (!$result)
        { echo "Сбой записи"; return false;}
        else
        {    
            echo "Добавлен кладовщик номер $conn->insert_id.";
            return true;
        }   
    }
?>
