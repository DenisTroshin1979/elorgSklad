<?php
require_once "login.php";
require_once "utils.php";

$strName="";
$intMatUnitID=0;
$intMatTypeID=0;


if (!$loggedin)
{
    echo "Необходимо авторизоваться.";
    die();
}


// нажатие на кнопку "Сохранить материал" 
if ( isset($_POST['Add']) )
{
    if ($_POST['Add']=='Add')
    {
        if (isset($_POST['editName']))
            $strName=trim(get_post($conn, 'editName'));
        else 
            $strName="";

        if (isset($_POST['comboMatUnits']))
            $intMatUnitID=get_post($conn, 'comboMatUnits');
        else 
            $intMatUnitID=0;

        if (isset($_POST['comboMatTypes']))
            $intMatTypeID=get_post($conn, 'comboMatTypes');
        else 
            $intMatTypeID=0;
        
        if ( $idMat = MaterialsList_Add($conn, $strName, $intMatUnitID, $intMatTypeID) )
        {
                $_SESSION['intNewPostupMatID']= $idMat;
                $_SESSION['strNewPostupMatQuantity'] = "";            
                $_SESSION['strNewPostupMatPrice'] = "";                        

                header("Location: newpostup.php");
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
<h1>Новый материал</h1><br>
</div> <!-- end col 1-->
</div> <!--  end row 1-->
_END;

echo <<< _END
<div class="row">    <!-- row 2 -->
<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">    <!-- r2 c1-->
_END;
  
echo <<< _END

<form name="MaterialAddForm" action="newmaterial.php" method="post" enctype="application/x-www-form-urlencoded" accept-charset="utf-8"> 

<fieldset class="border rounded-3 p-3">

<label class="form-label" for="editName">Наименование</label><br>
<input class="form-control" type="text" name="editName" value='$strName' id="editName" maxlength="100" size="30" tabindex="1"><br> 

<label for="comboMatUnits">Единица измерения</label><br> 
_END;
    
UpdateMatUnitsCombo($conn, "comboMatUnits", "comboMatUnits", "Выберите единицу измерения...", 2, $intMatUnitID);

echo <<< _END
<br><label class="form-label" for="comboMatTypes">Тип</label><br>
_END;

UpdateMatTypesCombo($conn, "comboMatTypes", "comboMatTypes", "Выберите тип...", 3, $intMatTypeID);

echo <<< _END
<br>
</fieldset>
<br>
<button class="btn btn-primary form-control" name="Add" type="submit" value="Add" tabindex="4">Сохранить материал</button>&nbsp;
</form>

<br><a href="newpostup.php">Назад к Новое поступление</a><br>

_END;

echo "</div>";  // end r2 c1
echo "</div>";  // end row 2

echo <<< _END
</div> <!-- end container-->
</body>
</html>
_END;
//-----------------------------------------------------------------------------
function MaterialsList_Add($conn, $strName, $intMatUnitID, $intMatTypeID)
{
    if ($strName=="")
    { echo("Не задано Наименование материала.");  return 0; }
    if ($intMatUnitID==0)
    {  echo("Не выбрана Единица измерения материала.");  return 0; }
    if ($intMatTypeID==0)
    {  echo("Не выбран Тип материала.");  return 0; }
    
    // проверить, есть ли материал с заданным именем в базе данных
    if ( findMaterialByName($conn, $strName) )
    {        
        echo "Материал с заданным именем уже существует.";
        return 0;
    }
   
    // сформируем SQL-запрос на добавление записи в таблицу materials
    $q="INSERT INTO materials (mat_name, mat_unit, mat_quantity, mat_type) VALUES('$strName', '$intMatUnitID',  0, '$intMatTypeID');";

    $result=$conn->query($q);
    if (!$result)
    { echo "Сбой записи"; return 0;}
    else
        return $conn->insert_id;
}
//-----------------------------------------------------------------------------
?>