<?php
require_once "login.php";
require_once "utils.php";

$intNewSaleBuyerID=0;
$intNewSaleStorekeeperID=0;
$strNewSaleDate=date("Y-m-d");
$strNewSaleDocName="";
$intNewSaleMatID=0;
$strQuantity="";
$strPrice="";
$strFileName="newsale.csv";
$arrMatList=array();

if (!$loggedin)
{
    echo "Необходимо авторизоваться.";
    die();
}
if (isset($_POST['btnAdd']))
{
    if ($_POST['btnAdd']=='add')    // кнопка Добавить
    {
            // загрузка списка материалов из файла newsale.csv
            LoadMaterialsFromFile($strFileName, $arrMatList);

            GetPostData();
            
            // добавление в список очередного материала
            if ( AddMaterialToList($conn, $arrMatList, $intNewSaleMatID, $strQuantity, $strPrice, 3) )
            // сохранение списка в файл newsale.csv
            SaveMaterialsToFile($arrMatList, $strFileName);
     
            // сохранить сессию
            SaveNewSaleSession();
    }
}
else if (isset ($_POST['btnSave'])) // Кнопка OK
{
        if ($_POST['btnSave']=='save')
        {
            // загрузка списка материалов из файла newsale.csv
            LoadMaterialsFromFile($strFileName, $arrMatList);

            GetPostData();
            
            if (isset($_SESSION['idCurrentStorekeeper'])) 
                $intNewSaleStorekeeperID = $_SESSION['idCurrentStorekeeper'];
            else $intNewSaleStorekeeperID=0;
            
            // добавить информацию о продаже в базу данных
            if ( NewSale_Add($conn, $strNewSaleDate, $strNewSaleDocName, $intNewSaleBuyerID, $intNewSaleStorekeeperID, $arrMatList) )
            {        
                // сбросить значения информации о продаже
                $strNewSaleDate=date("Y-m-d");
                $_SESSION['strNewSaleDate']=$strNewSaleDate;            
                $strNewSaleDocName="";
                $_SESSION['strNewSaleDocName']=$strNewSaleDocName;                        
                $intNewSaleBuyerID=0;
                $_SESSION['intNewSaleBuyerID']=$intNewSaleBuyerID;         

                // сбросить значения информации о последнем выбранном материале
                $intNewSaleMatID=0;
                $_SESSION['intNewSaleMatID']= $intNewSaleMatID;
                 $strQuantity="";
                $_SESSION['strNewSaleMatQuantity']=$strQuantity;            
                $strPrice="";
                $_SESSION['strNewSaleMatPrice']=$strPrice;                        

                $arrMatList=array();
                
                if (file_exists($strFileName))
                    unlink($strFileName);
                
                echo "Продажа успешно сохранена.<br>";
                header("Location: sales.php");
                die();
            }
            else 
            {
                SaveNewSaleSession();
            }
        }    
}
else if (isset($_POST['newbuyer']))
{
    GetPostData();
    SaveNewSaleSession();
    
    header("Location: newbuyer.php");
    die();
}
else if (isset ($_POST['btnDeleteAll'])) // кнопка "Удалить все"
{
    GetPostData();
    SaveNewSaleSession();

    $arrMatList=array();

    if (file_exists($strFileName))
        unlink($strFileName);

    RestoreNewSaleSession();
    
     // сбросить значения информации о последнем выбранном материале
    $intNewSaleMatID=0;
    $_SESSION['intNewSaleMatID']= $intNewSaleMatID;
    $strQuantity="";
    $_SESSION['strNewSaleMatQuantity']=$strQuantity;            
    $strPrice="";
    $_SESSION['strNewSaleMatPrice']=$strPrice;       
}
else if (isset($_GET['delete'])) // ссылка "Удалить" (возле каждого материала из списка продажи)
{
    // загрузка списка материалов из файла newsale.csv
    LoadMaterialsFromFile($strFileName, $arrMatList);    

    $DeleteMatID=get_get($conn, 'delete');
    DeleteMaterialFromList($arrMatList, $DeleteMatID);
    SaveMaterialsToFile($arrMatList, $strFileName);    
    
    RestoreNewSaleSession();
}
else if (isset($_GET['get_mat_ost']))
{
    //  RestoreNewSaleSession();
    
    $get_mat_ost=get_get($conn, 'get_mat_ost');

    $s=UpdatePriceCombo($conn, "comboPrices", "comboPrices", $strPrice, $get_mat_ost);
    $ost=GetOstatok($conn, $get_mat_ost);

    if ($ost==-1)
    {
        http_response_code(404);
        echo json_encode(array("message" => "Материал не найден."), JSON_UNESCAPED_UNICODE);
    }    
    else 
    {
        http_response_code(200);
        echo json_encode(array("ostatok" => $ost, "prices" => $s)) ;
    }
    
    die();
    
}
else
{   
    // восстановить сессию
    RestoreNewSaleSession();
    // загрузка списка материалов из файла newsale.csv
    LoadMaterialsFromFile($strFileName, $arrMatList);
}
echo <<< _END
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="jquery/jquery-3.6.0.js"></script>
<script type="text/javascript" src="bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- глобальная переменная протокол и имя хоста -->
<script>
    g_host=window.location.protocol + "//" + window.location.host + "/";
</script>

<script type="text/javascript" src="newsale.js"></script>        
<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">         
<title>Управление складом электромонтажной организации</title>
</head>
<body>
<div class="container-fluid">
<div class="row">
<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
_END; 
        
require "menu.php";

echo "<h1>Новая продажа</h1><br>";
// ------------------------------ ФОРМА ----------------------------------------
echo <<< _END
    <form name="NewSaleForm" action="newsale.php" method="post" enctype="application/x-www-form-urlencoded" accept-charset="utf-8">
    <fieldset class="border rounded-3 p-3">
    <label for="NewSaleDate">Дата</label> 
    <input name="NewSaleDate" id="NewSaleDate" value='$strNewSaleDate' type="date" tabindex="1">
    <label for="editDocName">Документ</label>
    <input name="editDocName" value='$strNewSaleDocName' type="text" maxlength="30" size="20" tabindex="2"><br><br>
    <label for="comboBuyers">Покупатель</label> 
    _END;
    
    UpdateBuyersCombo($conn, "comboBuyers", "comboBuyers", "Выберите покупателя...", 3, $intNewSaleBuyerID);
            
    echo <<< _END
    &nbsp;<button name="newbuyer" type="submit" value="new" tabindex="4">Новый покупатель</button><br><br>
    </fieldset>   
    <br>
    <fieldset class="border rounded-3 p-3">
    <label for="comboMaterials">Материал</label><br>
    _END;
    UpdateMaterialsCombo($conn, "comboMaterials", "comboMaterials", "Выберите материал...", 5, $intNewSaleMatID);
    
    // остаток материала будет вставлен через jQuery
    echo <<< _OSTATOK
    <br>    
    <label id="ostatok"></label>
    <br><br>
    _OSTATOK;
    
    echo <<< _END
    <label for="editQuantity">Количество</label>
    &nbsp;<input name="editQuantity" id="editQuantity" value='$strQuantity' type="text" maxlength="11" size="12" tabindex="6"> 

    <label id="prices">
    &nbsp;<label>Цена</label>&nbsp;<input name=\"editPrice\" id=\"editPrice\" value='$strPrice' maxlength=\"11\" size=\"12\" tabindex=\"7\" list=\"comboID\">         
    </label> <!-- список цен будет подгружен вызовом из jQuery-->
    _END;
    
    echo <<< _END
    <br><br>        
    <button name="btnAdd" type="submit" value="add" tabindex="8">Добавить в продажу</button>&nbsp;
    </fieldset><br><br>
    _END;
    
    ShowMaterialsListAsTable($conn, $arrMatList);

    echo <<< _END
    <button name="btnDeleteAll" type="submit" value="deleteall" tabindex="9">Очистить список материалов</button>&nbsp;             
    <button name="btnSave" type="submit" value="save" tabindex="10">Сохранить продажу</button>
    </form><br>
    _END;      
// ----------------------------- КОНЕЦ ФОРМЫ -----------------------------------
echo "</div>";

echo <<< _END
<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6"> 
_END;

echo <<< _END
</div>
</div> <!-- end of row 1>

_END;
    
echo <<< _END
<div class="row">                                       <!-- row 2 -->
<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">    

</div>
<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">    
</div>
</div>                                                  <!-- end row 2 -->
_END;
        
echo <<< _END
</div> <!-- end of container -->
</body>
</html>
_END;
// -----------------------------------------------------------------------------
// show all materials from arrMatList in html table 
function ShowMaterialsListAsTable($conn, $arrMatList)
{
    
    if (count($arrMatList)==0)
        return false;
    
    echo  "<table class=\"table w-auto small table-sm table-striped table-bordered table-hover table-responsive\">";
    echo "<thead><tr>
          <th scope=\"col\">Наименование</th>
          <th scope=\"col\">Ед.изм</th>
          <th scope=\"col\">Количество</th>
          <th scope=\"col\">Цена</th>
          <th scope=\"col\"></th>          
          </tr></thead>";
    echo "<tbody>";

    foreach ($arrMatList as $row)
    {
        $mi=getMaterialByID($conn, $row[0]);        
        
        echo "<tr>";
        echo "<td>$mi->name</td>
              <td>$mi->unit</td>
              <td>$row[1]</td>
              <td>$row[2]</td>
              <td><a href=\"newsale.php?delete=$row[0]\">Удалить</a></td>";
        echo "</tr>";
    }
    unset($row);
    echo "</tbody></table>";
    return true;
}
// -----------------------------------------------------------------------------
// Добавление продажи в базу данных
function NewSale_Add($conn, $strNewSaleDate, $strNewSaleDocName, $intNewSaleBuyerID, $intNewSaleStorekeeperID, $arrMatList)
{
    
    $num=count($arrMatList);
    
    if ($num==0) { echo "Не выбран ни один материал.";  return false; }
        
    // проверить корректность даты
    list($year, $month, $day) = explode("-", $strNewSaleDate);
    if (!checkdate($month, $day, $year))    
        { echo "Неверная дата продажи."; return false; }
    
    if ($strNewSaleDocName=="")
	{ echo("Заполните поле Документ.");  return false; }

    if ($intNewSaleBuyerID==0)
       { echo("Выберите Покупателя.");  return false; }

    if ($intNewSaleStorekeeperID==0)
       { echo("Кладовщик не задан. Необходимо авторизоваться.");  return false; }
       
    
    // сформируем SQL-запрос на добавление записи в таблицу sale
    // sale_id, sale_date, sale_buyer_id, sale_user_id, sale_doc
    $q="INSERT INTO sales VALUES (NULL, '$strNewSaleDate', '$intNewSaleBuyerID', '$intNewSaleStorekeeperID', '$strNewSaleDocName');";
    
    $result=$conn->query($q);
    if (!$result)  {echo "Ошибка 001 сохранения в базу данных." ; return false; }

    $sale_id=$conn->insert_id;
    
    foreach ($arrMatList as $row)
    {
        // сформируем SQL-запрос на добавление записи в таблицу sales_d
        // sale_d_id, sale_d_mat_id, sale_d_quantity, sale_d_price
        $q="INSERT INTO sales_d VALUES ($sale_id, $row[0], '$row[1]', '$row[2]');";

        $result=$conn->query($q);
        if (!$result)  {echo "Ошибка 002 сохранения в базу данных." ; return false; }
       
        // получить текущий остаток материала по заданному значению поля Код
        $mi=getMaterialByID($conn, $row[0]);
        
        // изменить остаток
        $quantity=$mi->quantity - $row[1];
        
        // сформируем SQL-запрос на Изменение поля quantity в таблице materials
        $q="UPDATE materials SET mat_quantity='$quantity' WHERE mat_id='$row[0]';";

        $result=$conn->query($q);
        if (!$result)  {echo "Ошибка 003 сохранения в базу данных." ; return false; }
    }   
    unset($row);
    return true;    
}
// ----------------------------------------------------------------------------
function SaveNewSaleSession()
{
    global $strNewSaleDate, $strNewSaleDocName, $intNewSaleBuyerID;
    global $intNewSaleMatID, $strQuantity, $strPrice;
    
    // информация о новой продаже (дата, номер документа, код покупателя)
    $_SESSION['strNewSaleDate']=$strNewSaleDate;            
    $_SESSION['strNewSaleDocName']=$strNewSaleDocName;                        
    $_SESSION['intNewSaleBuyerID']=$intNewSaleBuyerID;  

    // информация по последнем добавленном в список материале (код материала, количество, цена)
    $_SESSION['intNewSaleMatID']=$intNewSaleMatID;
    $_SESSION['strNewSaleMatQuantity']=$strQuantity;            
    $_SESSION['strNewSaleMatPrice']=$strPrice;               
}
// ----------------------------------------------------------------------------
function RestoreNewSaleSession()
{
    global $strNewSaleDate, $strNewSaleDocName, $intNewSaleBuyerID;
    global $intNewSaleMatID, $strQuantity, $strPrice;
    
    // восстановить информацию о новой продаже
    if (isset($_SESSION['strNewSaleDate'])) $strNewSaleDate=$_SESSION['strNewSaleDate'];
        else $strNewSaleDate=date("Y-m-d");
    if (isset($_SESSION['strNewSaleDocName'])) $strNewSaleDocName=$_SESSION['strNewSaleDocName'];
        else $strNewSaleDocName="";
    if (isset($_SESSION['intNewSaleBuyerID'])) $intNewSaleBuyerID=$_SESSION['intNewSaleBuyerID'];            
        else $intNewSaleBuyerID=0;
        
    // восстановить информацию о последнем выбранном материале
    if (isset($_SESSION['intNewSaleMatID'])) $intNewSaleMatID=$_SESSION['intNewSaleMatID'];
        else $intNewSaleMatID=0;
    if (isset($_SESSION['strNewSaleMatQuantity'])) $strQuantity=$_SESSION['strNewSaleMatQuantity'];
        else $strQuantity="";
    if (isset($_SESSION['strNewSaleMatPrice'])) $strPrice=$_SESSION['strNewSaleMatPrice'];
        else $strPrice="";
}
// ----------------------------------------------------------------------------
function GetPostData()
{
    global $conn;
    global $strNewSaleDate, $strNewSaleDocName, $intNewSaleBuyerID;
    global $intNewSaleMatID, $strQuantity, $strPrice;

    // код материала
    if (isset($_POST['comboMaterials']))
        $intNewSaleMatID=get_post($conn, 'comboMaterials');
    // количество материала
    if (isset($_POST['editQuantity']))
        $strQuantity=get_post($conn, 'editQuantity');
    // стоимость материала
    if (isset($_POST['editPrice']))
        $strPrice=get_post($conn, 'editPrice');

    // дата поступления
    if (isset($_POST['NewSaleDate']))
        $strNewSaleDate=get_post($conn, 'NewSaleDate');

    // номер документа
    if (isset($_POST['editDocName']))
        $strNewSaleDocName=get_post($conn, 'editDocName');
    else 
        $strNewSaleDocName="";        

    // покупатель в combobox
    if (isset($_POST['comboBuyers']))
        $intNewSaleBuyerID=get_post($conn, 'comboBuyers');
    else 
        $intNewSaleBuyerID=0;        

}
// ----------------------------------------------------------------------------
function GetOstatok($conn, $id)
{
    if ($id!=0)  
    { 
        $mi=getMaterialByID($conn, $id); 
        if ($mi) return $mi->quantity;
        else return -1;
    }
    else return -1;
}
// ----------------------------------------------------------------------------
?>
