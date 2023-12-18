<?php
require_once "login.php";
require_once "utils.php";

$strBuyerName="";
$strBuyerINN="";
$strBuyerAddr="";
$strBuyerPhone="";

if (!$loggedin)
{
    echo "Необходимо авторизоваться.";
    die();
}

// нажатие на кнопку "Сохранить покупателя" 
if ( isset($_POST['Add']) )
{
    if ($_POST['Add']=='Add')
    {
        if (isset($_POST['editBuyerName']))
            $strBuyerName=trim(get_post($conn, 'editBuyerName'));
        else 
            $strBuyerName="";
        if (isset($_POST['editBuyerINN']))
            $strBuyerINN=trim(get_post($conn, 'editBuyerINN'));
        else 
            $strBuyerINN="";
        if (isset($_POST['editBuyerAddr']))
            $strBuyerAddr=trim(get_post($conn, 'editBuyerAddr'));
        else 
            $strBuyerAddr="";
        if (isset($_POST['editBuyerPhone']))
            $strBuyerPhone=trim(get_post($conn, 'editBuyerPhone'));
        else 
            $strBuyerPhone="";

        if ( $intNewSaleBuyerID=BuyersList_Add($conn, $strBuyerName, $strBuyerINN, $strBuyerAddr, $strBuyerPhone) )
        {
                $_SESSION['intNewSaleBuyerID']=$intNewSaleBuyerID;         
                header("Location: newsale.php");
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
<h1>Новый покупатель</h1><br>
</div> <!-- end col 1-->
</div> <!--  end row 1-->
_END;

echo <<< _END
<div class="row">    <!-- row 2 -->
<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">    <!-- r2 c1-->
_END;
  
echo <<< _END
<form name="BuyerAddForm" action="newbuyer.php" method="post" enctype="application/x-www-form-urlencoded" accept-charset="utf-8"> 

<fieldset class="border rounded-3 p-3">

<label class="form-label" for="editBuyerName">Наименование</label>
<input class="form-control" type="text" name="editBuyerName" value='$strBuyerName' id="editBuyerName" maxlength="100" size="30" tabindex="1"><br> 

<label class="form-label" for="editBuyerINN">ИНН</label>
<input class="form-control" type="text" name="editBuyerINN" value='$strBuyerINN' id="editBuyerINN" maxlength="12" size="12" tabindex="2"><br> 

<label class="form-label" for="editBuyerAddr">Адрес</label>
<input class="form-control" type="text" name="editBuyerAddr" value='$strBuyerAddr' id="editBuyerAddr" maxlength="100" size="30" tabindex="3"><br> 

<label class="form-label" for="editBuyerPhone">Телефон</label>
<input class="form-control" type="text" name="editBuyerPhone" value='$strBuyerPhone' id="editBuyerPhone" maxlength="20" size="20" tabindex="4"><br> 

<br>
</fieldset>
<br>
<button class="btn btn-primary form-control" name="Add" type="submit" value="Add" tabindex="5">Сохранить покупателя</button>&nbsp;
</form>

<br><a href="newsale.php">Назад к Новая продажа</a><br>        
        
_END;

echo "</div>";  // end r2 c1
echo "</div>";  // end row 2

echo <<< _END
</div> <!-- end container-->
</body>
</html>
_END;
//--------------------------------------------------------------------------
function BuyersList_Add($conn, $strBuyerName, $strBuyerINN, $strBuyerAddr, $strBuyerPhone)
{
    if ($strBuyerName=="")
    { echo("Не задано Наименование покупателя.");  return 0; }
    if ($strBuyerINN=="")
    {  echo("Не задан ИНН покупателя.");  return 0; }

    if ( (mb_strlen($strBuyerINN)!=10) && (mb_strlen($strBuyerINN)!=12)  )
    {  echo("ИНН для юридического лица - 10 символов, для индивидуальных предпринимателей - 12 символов.");  return 0; }

    // Поля Телефон и Адрес не являются необходимыми при заполнении формы
    // ...

    // По ИНН проверить уникальность покупателя
    if (findBuyerByINN($conn, $strBuyerINN)!=0) 
    { echo("Покупатель с таким ИНН уже существует.");  return 0;}

    // сформируем SQL-запрос на добавление записи в таблицу buyers
    $q="INSERT INTO buyers (buyer_name, buyer_inn, buyer_addr, buyer_phone) VALUES('$strBuyerName','$strBuyerINN','$strBuyerAddr','$strBuyerPhone');";

    $result=$conn->query($q);
    if (!$result)
    { echo "Сбой записи"; return 0;}
    else
        return $conn->insert_id;
}

?>
