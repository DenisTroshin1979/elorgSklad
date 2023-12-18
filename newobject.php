<?php
require_once "login.php";
require_once "utils.php";

$strObjName="";
$strObjLocation="";


if (!$loggedin)
{
    echo "Необходимо авторизоваться.";
    die();
}

// нажатие на кнопку "Сохранить строительный объект" 
if ( isset($_POST['Add']) )
{
    if ($_POST['Add']=='Add')
    {
        if (isset($_POST['editObjName']))
            $strObjName=trim(get_post($conn, 'editObjName'));
        else 
            $strObjName="";
        if (isset($_POST['editObjLocation']))
            $strObjLocation=trim(get_post($conn, 'editObjLocation'));
        else 
            $strObjLocation="";
        
        if ( $intNewSpisanObjID=ObjList_Add($conn, $strObjName, $strObjLocation) )
        {
                $_SESSION['intNewSpisanObjID']=$intNewSpisanObjID;         
                header("Location: newspisan.php");
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
    <script type="text/javascript" src="bootstrap/js/bootstrap.bundle.min.js"></script>
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">         
    <title>Управление складом электромонтажной организации</title>
</head>
<body>
_END;

echo <<< _END
<div class="container-fluid">
        <div class="row">   <!-- row 1-->
            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6"> <!-- r1 c1-->
_END; 

require 'menu.php';

echo <<< _END
<h1>Новый строительный объект</h1><br>
</div> <!-- end col 1-->
</div> <!--  end row 1-->
_END;

echo <<< _END
<div class="row">    <!-- row 2 -->
<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">    <!-- r2 c1-->
_END;
  
echo <<< _END
<form name="ObjectAddForm" action="newobject.php" method="post" enctype="application/x-www-form-urlencoded" accept-charset="utf-8"> 

<fieldset class="border rounded-3 p-3">

<label class="form-label" for="editObjName">Наименование</label>
<input class="form-control" type="text" name="editObjName" value='$strObjName' id="editObjName" maxlength="100" size="30" tabindex="1"><br> 

<label class="form-label" for="editObjLocation">Адрес</label>
<input class="form-control" type="text" name="editObjLocation" value='$strObjLocation' id="editObjLocation" maxlength="100" size="30" tabindex="3"><br> 

<br>
</fieldset>
<br>
<button class="btn btn-primary form-control" name="Add" type="submit" value="Add" tabindex="3">Сохранить строительный объект</button>&nbsp;
</form>

<br><a href="newspisan.php">Назад к Новое списание</a><br>        
        
_END;

echo "</div>";  // end r2 c1
echo "</div>";  // end row 2

echo <<< _END
</div> <!-- end container-->
</body>
</html>
_END;
//--------------------------------------------------------------------------
function ObjList_Add($conn, $strObjName, $strObjLocation)
{
    if ($strObjName=="")
    { echo("Не задано Наименование объекта.");  return false; }
    if ($strObjLocation=="")
    {  echo("Не задано Местоположение объекта.");  return false; }

    // сформируем SQL-запрос на добавление записи в таблицу objects
    $q="INSERT INTO objects (ob_name,ob_location) VALUES('$strObjName','$strObjLocation');";

    $result=$conn->query($q);
    if (!$result)
    { echo "Сбой записи"; return 0;}
    else
        return $conn->insert_id;
}
?>