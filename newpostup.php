<?php
require_once "login.php";
require_once "utils.php";

$intNewPostupSupplierID=0;
$intNewPostupStorekeeperID=0;
$strNewPostupDate=date("Y-m-d");
$strNewPostupDocName="";
$intNewPostupMatID=0;
$strQuantity="";
$strPrice="";
$arrMatList=array();
$strFileName="newpostup.csv";

if (!$loggedin)
{
    echo "Необходимо авторизоваться.";
    die();
}

if (isset($_POST['btnAdd']))
{
    if ($_POST['btnAdd']=='add')    // кнопка Добавить
    {
            // загрузка списка материалов из файла newpostup.csv
            LoadMaterialsFromFile($strFileName, $arrMatList);

            GetPostData();
            
            // добавление в список очередного материала
            if ( AddMaterialToList($conn, $arrMatList, $intNewPostupMatID, $strQuantity, $strPrice, 1) )
            // сохранение списка в файл newpostup.csv
            SaveMaterialsToFile($arrMatList, $strFileName);
     
            // сохранить сессию
            SaveNewPostupSession();
    }
}
else if (isset ($_POST['btnSave'])) // Кнопка OK
{
        if ($_POST['btnSave']=='save')
        {
            // загрузка списка материалов из файла newpostup.csv
            LoadMaterialsFromFile($strFileName, $arrMatList);

            GetPostData();
            
            if (isset($_SESSION['idCurrentStorekeeper'])) 
                $intNewPostupStorekeeperID = $_SESSION['idCurrentStorekeeper'];
            else $intNewPostupStorekeeperID=0;
            
            // добавить информацию о поступлении в базу данных
            if ( NewPostup_Add($conn, $strNewPostupDate, $strNewPostupDocName, $intNewPostupSupplierID, $intNewPostupStorekeeperID, $arrMatList) )
            {        
                // сбросить значения информации о поступлении
                $strNewPostupDate=date("Y-m-d");
                $_SESSION['strNewPostupDate']=$strNewPostupDate;            
                $strNewPostupDocName="";
                $_SESSION['strNewPostupDocName']=$strNewPostupDocName;                        
                $intNewPostupSupplierID=0;
                $_SESSION['intNewPostupSupplierID']=$intNewPostupSupplierID;         

                // сбросить значения информации о последнем выбранном материале
                $intNewPostupMatID=0;
                $_SESSION['intNewPostupMatID']= $intNewPostupMatID;
                 $strQuantity="";
                $_SESSION['strNewPostupMatQuantity']=$strQuantity;            
                $strPrice="";
                $_SESSION['strNewPostupMatPrice']=$strPrice;                        

                $arrMatList=array();
                
                if (file_exists($strFileName))
                    unlink($strFileName);
                
                echo "Поступление успешно сохранено.<br>";
                header("Location: postup.php");
                die();
            }
            else 
            {
                SaveNewPostupSession();
            }
        }    
}
else if (isset($_POST['newsupplier']))
{
    GetPostData();
    SaveNewPostupSession();
    
    header("Location: newsupplier.php");
    die();
}
else if (isset($_POST['newmaterial']))
{
    GetPostData();
    SaveNewPostupSession();

    header("Location: newmaterial.php");
    die();
}
else if (isset ($_POST['btnDeleteAll'])) // кнопка "Удалить все"
{
    GetPostData();
    SaveNewPostupSession();

    $arrMatList=array();

    if (file_exists($strFileName))
        unlink($strFileName);

    RestoreNewPostupSession();
    
     // сбросить значения информации о последнем выбранном материале
    $intNewPostupMatID=0;
    $_SESSION['intNewPostupMatID']= $intNewPostupMatID;
    $strQuantity="";
    $_SESSION['strNewPostupMatQuantity']=$strQuantity;            
    $strPrice="";
    $_SESSION['strNewPostupMatPrice']=$strPrice;       
}
else if (isset($_GET['delete'])) // ссылка "Удалить" (возле каждого материала из списка поступления)
{
    // загрузка списка материалов из файла newpostup.csv
    LoadMaterialsFromFile($strFileName, $arrMatList);    

    $DeleteMatID=get_get($conn, 'delete');
    DeleteMaterialFromList($arrMatList, $DeleteMatID);
    SaveMaterialsToFile($arrMatList, $strFileName);    
    
    RestoreNewPostupSession();
}
else
{   
    // восстановить сессию
    RestoreNewPostupSession();
    // загрузка списка материалов из файла newpostup.csv
    LoadMaterialsFromFile($strFileName, $arrMatList);
}
echo <<< _END
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script type="text/javascript" src="bootstrap/js/bootstrap.bundle.min.js"></script>
<script type="text/javascript" src="events.js"></script>        
<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">         
<title>Управление складом электромонтажной организации</title>
</head>
<body>
_END;
    
echo <<< _END
<div class="container-fluid">
<div class="row">
<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
_END; 
        
require "menu.php";

echo "<h1>Новое поступление</h1><br>";
// ------------------------------ ФОРМА ----------------------------------------
echo <<< _END
    <form name="NewPostupForm" action="newpostup.php" method="post" enctype="application/x-www-form-urlencoded" accept-charset="utf-8">
    <fieldset class="border rounded-3 p-3">
    <label for="NewPostupDate">Дата</label> 
    <input name="NewPostupDate" id="NewPostupDate" value='$strNewPostupDate' type="date" tabindex="1">
    <label for="editDocName">Документ</label>
    <input name="editDocName" value='$strNewPostupDocName' type="text" maxlength="30" size="20" tabindex="2"><br><br>
    <label for="comboSuppliers">Поставщик</label> 
    _END;
    
    UpdateSuppliersCombo($conn, "comboSuppliers", "comboSuppliers", "Выберите поставщика...", 3, $intNewPostupSupplierID);
            
    echo <<< _END
    &nbsp;<button name="newsupplier" type="submit" value="new" tabindex="4">Новый поставщик</button><br><br>
    </fieldset>   
    <br>
    <fieldset class="border rounded-3 p-3">
    <label for="comboMaterials">Материал</label><br>
    _END;
    UpdateMaterialsCombo($conn, "comboMaterials", "comboMaterials", "Выберите материал...", 5, $intNewPostupMatID);
        
    echo <<< _END
    &nbsp;<button name="newmaterial" type="submit" value="new" tabindex="6">Новый материал</button><br><br>
    
    <label for="editQuantity">Количество</label>
    &nbsp;<input name="editQuantity" id="editQuantity" value='$strQuantity' type="text" maxlength="11" size="12" tabindex="7"> 

    &nbsp;<label for="editPrice">Цена</label>
    &nbsp;<input name="editPrice" id="editPrice" value='$strPrice' type="text" maxlength="11" size="12" tabindex="8"> 
    <br><br>
  
    <button name="btnAdd" type="submit" value="add" tabindex="9">Добавить в поступление</button>&nbsp;
    </fieldset><br><br>
    _END;
    
    ShowMaterialsListAsTable($conn, $arrMatList);

    echo <<< _END
    <button name="btnDeleteAll" type="submit" value="deleteall" tabindex="10">Очистить список материалов</button>&nbsp;             
    <button name="btnSave" type="submit" value="save" tabindex="11">Сохранить поступление</button>
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
              <td><a href=\"newpostup.php?delete=$row[0]\">Удалить</a></td>";
        echo "</tr>";
    }
    unset($row);
    echo "</tbody></table>";
    return true;
}
// -----------------------------------------------------------------------------
// Добавление поступления в базу данных
function NewPostup_Add($conn, $strNewPostupDate, $strNewPostupDocName, $intNewPostupSupplierID, $intNewPostupStorekeeperID, $arrMatList)
{
    
    $num=count($arrMatList);
    
    if ($num==0) { echo "Не выбран ни один материал.";  return false; }
        
    // проверить корректность даты
    list($year, $month, $day) = explode("-", $strNewPostupDate);
    if (!checkdate($month, $day, $year))    
        { echo "Неверная дата поступления."; return false; }
    
    if ($strNewPostupDocName=="")
	{ echo("Заполните поле Документ.");  return false; }

    if ($intNewPostupSupplierID==0)
       { echo("Выберите Поставщика.");  return false; }

    if ($intNewPostupStorekeeperID==0)
       { echo("Кладовщик не задан. Необходимо авторизоваться.");  return false; }
       
    
    // сформируем SQL-запрос на добавление записи в таблицу postup
    // postup_id, postup_suplr_id, postup_date, postup_user_id, postup_doc
    $q="INSERT INTO postup VALUES (NULL, '$intNewPostupSupplierID', '$strNewPostupDate', '$intNewPostupStorekeeperID', '$strNewPostupDocName');";
    
    $result=$conn->query($q);
    if (!$result)  {echo "Ошибка 001 сохранения в базу данных." ; return false; }

    $postup_id=$conn->insert_id;
    
    foreach ($arrMatList as $row)
    {
        // сформируем SQL-запрос на добавление записи в таблицу postup_d
        // postup_d_id, postup_d_mat_id, postup_d_quantity, postup_d_price
        $q="INSERT INTO postup_d VALUES ($postup_id, $row[0], '$row[1]', '$row[2]');";

        $result=$conn->query($q);
        if (!$result)  {echo "Ошибка 002 сохранения в базу данных." ; return false; }

        // получить текущий остаток материала по заданному значению поля Код
        $mi=getMaterialByID($conn, $row[0]);
        // изменить остаток
        $quantity=$mi->quantity + $row[1];
        
        // сформируем SQL-запрос на Изменение поля quantity в таблице materials
        $q="UPDATE materials SET mat_quantity='$quantity' WHERE mat_id='$row[0]';";

        $result=$conn->query($q);
        if (!$result)  {echo "Ошибка 003 сохранения в базу данных." ; return false; }
    }   
    unset($row);
    return true;    
}
// ----------------------------------------------------------------------------
function SaveNewPostupSession()
{
    global $strNewPostupDate, $strNewPostupDocName, $intNewPostupSupplierID;
    global $intNewPostupMatID, $strQuantity, $strPrice;
    
    // информация о новом поступлении (дата, номер документа, код поставщика)
    $_SESSION['strNewPostupDate']=$strNewPostupDate;            
    $_SESSION['strNewPostupDocName']=$strNewPostupDocName;                        
    $_SESSION['intNewPostupSupplierID']=$intNewPostupSupplierID;  

    // информация по последнем добавленном в список материале (код материала, количество, цена)
    $_SESSION['intNewPostupMatID']=$intNewPostupMatID;
    $_SESSION['strNewPostupMatQuantity']=$strQuantity;            
    $_SESSION['strNewPostupMatPrice']=$strPrice;               
}
// ----------------------------------------------------------------------------
function RestoreNewPostupSession()
{
    global $strNewPostupDate, $strNewPostupDocName, $intNewPostupSupplierID;
    global $intNewPostupMatID, $strQuantity, $strPrice;
    
    // восстановить информацию о новом поступлении
    if (isset($_SESSION['strNewPostupDate'])) $strNewPostupDate=$_SESSION['strNewPostupDate'];
        else $strNewPostupDate=date("Y-m-d");
    if (isset($_SESSION['strNewPostupDocName'])) $strNewPostupDocName=$_SESSION['strNewPostupDocName'];
        else $strNewPostupDocName="";
    if (isset($_SESSION['intNewPostupSupplierID'])) $intNewPostupSupplierID=$_SESSION['intNewPostupSupplierID'];            
        else $intNewPostupSupplierID=0;
        
    // восстановить информацию о последнем выбранном материале
    if (isset($_SESSION['intNewPostupMatID'])) $intNewPostupMatID=$_SESSION['intNewPostupMatID'];
        else $intNewPostupMatID=0;
    if (isset($_SESSION['strNewPostupMatQuantity'])) $strQuantity=$_SESSION['strNewPostupMatQuantity'];
        else $strQuantity="";
    if (isset($_SESSION['strNewPostupMatPrice'])) $strPrice=$_SESSION['strNewPostupMatPrice'];
        else $strPrice="";
}
// ----------------------------------------------------------------------------
function GetPostData()
{
    global $conn;
    global $strNewPostupDate, $strNewPostupDocName, $intNewPostupSupplierID;
    global $intNewPostupMatID, $strQuantity, $strPrice;

    // код материала
    if (isset($_POST['comboMaterials']))
        $intNewPostupMatID=get_post($conn, 'comboMaterials');
    // количество материала
    if (isset($_POST['editQuantity']))
        $strQuantity=get_post($conn, 'editQuantity');
    // стоимость материала
    if (isset($_POST['editPrice']))
        $strPrice=get_post($conn, 'editPrice');

    // дата поступления
    if (isset($_POST['NewPostupDate']))
        $strNewPostupDate=get_post($conn, 'NewPostupDate');

    // номер документа
    if (isset($_POST['editDocName']))
        $strNewPostupDocName=get_post($conn, 'editDocName');
    else 
        $strNewPostupDocName="";        

    // поставщик в combobox
    if (isset($_POST['comboSuppliers']))
        $intNewPostupSupplierID=get_post($conn, 'comboSuppliers');
    else 
        $intNewPostupSupplierID=0;        

}
?>