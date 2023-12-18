<?php
require_once "login.php";
require_once "utils.php";

$strSupplierName="";
$strSupplierINN="";
$strSupplierAddr="";
$strSupplierPhone="";


if (!$loggedin)
{
    echo "Необходимо авторизоваться.";
    die();
}

// нажатие на кнопку "Сохранить поставщика" 
if ( isset($_POST['Add']) )
{
    if ($_POST['Add']=='Add')
    {
        if (isset($_POST['editSupplierName']))
            $strSupplierName=trim(get_post($conn, 'editSupplierName'));
        else 
            $strSupplierName="";
        if (isset($_POST['editSupplierINN']))
            $strSupplierINN=trim(get_post($conn, 'editSupplierINN'));
        else 
            $strSupplierINN="";
        if (isset($_POST['editSupplierAddr']))
            $strSupplierAddr=trim(get_post($conn, 'editSupplierAddr'));
        else 
            $strSupplierAddr="";
        if (isset($_POST['editSupplierPhone']))
            $strSupplierPhone=trim(get_post($conn, 'editSupplierPhone'));
        else 
            $strSupplierPhone="";

        if ( $intNewPostupSupplierID=SuppliersList_Add($conn, $strSupplierName, $strSupplierINN, $strSupplierAddr, $strSupplierPhone) )
        {
                $_SESSION['intNewPostupSupplierID']=$intNewPostupSupplierID;         
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
<h1>Новый поставщик</h1><br>
</div> <!-- end col 1-->
</div> <!--  end row 1-->
_END;

echo <<< _END
<div class="row">    <!-- row 2 -->
<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">    <!-- r2 c1-->
_END;
  
echo <<< _END
<form name="SupplierAddForm" action="newsupplier.php" method="post" enctype="application/x-www-form-urlencoded" accept-charset="utf-8"> 

<fieldset class="border rounded-3 p-3">

<label class="form-label" for="editSupplierName">Наименование</label>
<input class="form-control" type="text" name="editSupplierName" value='$strSupplierName' id="editSupplierName" maxlength="100" size="30" tabindex="1"><br> 

<label class="form-label" for="editSupplierINN">ИНН</label>
<input class="form-control" type="text" name="editSupplierINN" value='$strSupplierINN' id="editSupplierINN" maxlength="12" size="12" tabindex="2"><br> 

<label class="form-label" for="editSupplierAddr">Адрес</label>
<input class="form-control" type="text" name="editSupplierAddr" value='$strSupplierAddr' id="editSupplierAddr" maxlength="100" size="30" tabindex="3"><br> 

<label class="form-label" for="editSupplierPhone">Телефон</label>
<input class="form-control" type="text" name="editSupplierPhone" value='$strSupplierPhone' id="editSupplierPhone" maxlength="20" size="20" tabindex="4"><br> 

<br>
</fieldset>
<br>
<button class="btn btn-primary form-control" name="Add" type="submit" value="Add" tabindex="5">Сохранить поставщика</button>&nbsp;
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
//--------------------------------------------------------------------------
function SuppliersList_Add($conn, $strSupplierName, $strSupplierINN, $strSupplierAddr, $strSupplierPhone)
{
    if ($strSupplierName=="")
    { echo("Не задано Наименование поставщика.");  return 0; }
    if ($strSupplierINN=="")
    {  echo("Не задан ИНН поставщика.");  return 0; }

    if ( (mb_strlen($strSupplierINN)!=10) && (mb_strlen($strSupplierINN)!=12)  )
    {  echo("ИНН для юридического лица - 10 символов, для индивидуальных предпринимателей - 12 символов.");  return 0; }

    // Поля Телефон и Адрес не являются необходимыми при заполнении формы
    // ...

    // По ИНН проверить уникальность поставщика
    if (findSupplierByINN($conn, $strSupplierINN)!=0) 
    { echo("Поставщик с таким ИНН уже существует.");  return 0;}

    // сформируем SQL-запрос на добавление записи в таблицу suppliers
    $q="INSERT INTO suppliers (supplier_name, supplier_inn, supplier_addr, supplier_phone) VALUES('$strSupplierName','$strSupplierINN','$strSupplierAddr','$strSupplierPhone');";

    $result=$conn->query($q);
    if (!$result)
    { echo "Сбой записи"; return 0;}
    else
        return $conn->insert_id;
}

?>