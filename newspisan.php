<?php
require_once "login.php";
require_once "utils.php";

$intNewSpisanObjID=0;
$intNewSpisanStorekeeperID=0;
$strNewSpisanDate=date("Y-m-d");
$strNewSpisanDocName="";
$intNewSpisanMatID=0;
$strQuantity="";
$strPrice="";
$strFileName="newspisan.csv";
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
            // загрузка списка материалов из файла newspisan.csv
            LoadMaterialsFromFile($strFileName, $arrMatList);

            GetPostData();
            
            // добавление в список очередного материала
            if ( AddMaterialToList($conn, $arrMatList, $intNewSpisanMatID, $strQuantity, $strPrice, 2) )
            // сохранение списка в файл newspisan.csv
            SaveMaterialsToFile($arrMatList, $strFileName);
     
            // сохранить сессию
            SaveNewSpisanSession();
    }
}
else if (isset ($_POST['btnSave'])) // Кнопка OK
{
        if ($_POST['btnSave']=='save')
        {
            // загрузка списка материалов из файла newspisan.csv
            LoadMaterialsFromFile($strFileName, $arrMatList);

            GetPostData();
            
            if (isset($_SESSION['idCurrentStorekeeper'])) 
                $intNewSpisanStorekeeperID = $_SESSION['idCurrentStorekeeper'];
            else $intNewSpisanStorekeeperID=0;
            
            // добавить информацию о списании в базу данных
            if ( NewSpisan_Add($conn, $strNewSpisanDate, $strNewSpisanDocName, $intNewSpisanObjID, $intNewSpisanStorekeeperID, $arrMatList) )
            {        
                // сбросить значения информации о списании
                $strNewSpisanDate=date("Y-m-d");
                $_SESSION['strNewSpisanDate']=$strNewSpisanDate;            
                $strNewSpisanDocName="";
                $_SESSION['strNewSpisanDocName']=$strNewSpisanDocName;                        
                $intNewSpisanObjID=0;
                $_SESSION['intNewSpisanObjID']=$intNewSpisanObjID;         

                // сбросить значения информации о последнем выбранном материале
                $intNewSpisanMatID=0;
                $_SESSION['intNewSpisanMatID']= $intNewSpisanMatID;
                 $strQuantity="";
                $_SESSION['strNewSpisanMatQuantity']=$strQuantity;            
                $strPrice="";
                $_SESSION['strNewSpisanMatPrice']=$strPrice;                        

                $arrMatList=array();
                
                if (file_exists($strFileName))
                    unlink($strFileName);
                
                echo "Списание успешно сохранено.<br>";
                header("Location: spisan.php");
                die();
            }
            else 
            {
                SaveNewSpisanSession();
            }
        }    
}
else if (isset($_POST['newobject']))
{
    GetPostData();
    SaveNewSpisanSession();
    
    header("Location: newobject.php");
    die();
}
else if (isset ($_POST['btnDeleteAll'])) // кнопка "Удалить все"
{
    GetPostData();
    SaveNewSpisanSession();

    $arrMatList=array();

    if (file_exists($strFileName))
        unlink($strFileName);

    RestoreNewSpisanSession();
    
     // сбросить значения информации о последнем выбранном материале
    $intNewSpisanMatID=0;
    $_SESSION['intNewSpisanMatID']= $intNewSpisanMatID;
    $strQuantity="";
    $_SESSION['strNewSpisanMatQuantity']=$strQuantity;            
    $strPrice="";
    $_SESSION['strNewSpisanMatPrice']=$strPrice;       
}
else if (isset($_GET['delete'])) // ссылка "Удалить" (возле каждого материала из списка списания)
{
    // загрузка списка материалов из файла newspisan.csv
    LoadMaterialsFromFile($strFileName, $arrMatList);    

    $DeleteMatID=get_get($conn, 'delete');
    DeleteMaterialFromList($arrMatList, $DeleteMatID);
    SaveMaterialsToFile($arrMatList, $strFileName);    
    
    RestoreNewSpisanSession();
}
else
{   
    // восстановить сессию
    RestoreNewSpisanSession();
    // загрузка списка материалов из файла newspisan.csv
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

echo "<h1>Новое списание</h1><br>";
// ------------------------------ ФОРМА ----------------------------------------
echo <<< _END
    <form name="NewSpisanForm" action="newspisan.php" method="post" enctype="application/x-www-form-urlencoded" accept-charset="utf-8">
    <fieldset class="border rounded-3 p-3">
    <label for="NewSpisanDate">Дата</label> 
    <input name="NewSpisanDate" id="NewSpisanDate" value='$strNewSpisanDate' type="date" tabindex="1">
    <label for="editDocName">Документ</label>
    <input name="editDocName" value='$strNewSpisanDocName' type="text" maxlength="30" size="20" tabindex="2"><br><br>
    <label for="comboObjects">Строительный объект</label> 
    _END;
    
    UpdateObjectsCombo($conn, "comboObjects", "comboObjects", "Выберите объект...", 3, $intNewSpisanObjID);
            
    echo <<< _END
    &nbsp;<button name="newobject" type="submit" value="new" tabindex="4">Новый объект</button><br><br>
    </fieldset>   
    <br>
    <fieldset class="border rounded-3 p-3">
    <label for="comboMaterials">Материал</label><br>
    _END;
    UpdateMaterialsCombo($conn, "comboMaterials", "comboMaterials", "Выберите материал...", 5, $intNewSpisanMatID);
        
    //$n=0;
    //if ($intNewSpisanMatID!=0)  { $mi=getMaterialByID($conn, $intNewSpisanMatID); $n=$mi->quantity;}
    
    //<br>
    //<label>В наличии на складе: $mi->quantity</label><br>
    echo <<< _END
    <br><br>
    <label for="editQuantity">Количество</label>
    &nbsp;<input name="editQuantity" id="editQuantity" value='$strQuantity' type="text" maxlength="11" size="12" tabindex="6"> 

    &nbsp;<label for="editPrice">Цена</label>
    &nbsp;<input name="editPrice" id="editPrice" value='$strPrice' type="text" maxlength="11" size="12" tabindex="7"> 
    <br><br>
  
    <button name="btnAdd" type="submit" value="add" tabindex="8">Добавить в списание</button>&nbsp;
    </fieldset><br><br>
    _END;
    
    ShowMaterialsListAsTable($conn, $arrMatList);

    echo <<< _END
    <button name="btnDeleteAll" type="submit" value="deleteall" tabindex="9">Очистить список материалов</button>&nbsp;             
    <button name="btnSave" type="submit" value="save" tabindex="10">Сохранить объект</button>
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
              <td><a href=\"newspisan.php?delete=$row[0]\">Удалить</a></td>";
        echo "</tr>";
    }
    unset($row);
    echo "</tbody></table>";
    return true;
}
// -----------------------------------------------------------------------------
// Добавление списания в базу данных
function NewSpisan_Add($conn, $strNewSpisanDate, $strNewSpisanDocName, $intNewSpisanObjID, $intNewSpisanStorekeeperID, $arrMatList)
{
    
    $num=count($arrMatList);
    
    if ($num==0) { echo "Не выбран ни один материал.";  return false; }
        
    // проверить корректность даты
    list($year, $month, $day) = explode("-", $strNewSpisanDate);
    if (!checkdate($month, $day, $year))    
        { echo "Неверная дата списания."; return false; }
    
    if ($strNewSpisanDocName=="")
	{ echo("Заполните поле Документ.");  return false; }

    if ($intNewSpisanObjID==0)
       { echo("Выберите Объект.");  return false; }

    if ($intNewSpisanStorekeeperID==0)
       { echo("Кладовщик не задан. Необходимо авторизоваться.");  return false; }
       
    
    // сформируем SQL-запрос на добавление записи в таблицу spisan
    // spisan_id, spisan_date, spisan_user_id, spisan_obj_id, spisan_doc
    $q="INSERT INTO spisan VALUES (NULL, '$strNewSpisanDate', '$intNewSpisanStorekeeperID', '$intNewSpisanObjID', '$strNewSpisanDocName');";
    
    $result=$conn->query($q);
    if (!$result)  {echo "Ошибка 001 сохранения в базу данных." ; return false; }

    $spisan_id=$conn->insert_id;
    
    foreach ($arrMatList as $row)
    {
        // сформируем SQL-запрос на добавление записи в таблицу spisan_d
        // spisan_d_id, spisan_d_mat_id, spisan_d_quantity, spisan_d_price
        $q="INSERT INTO spisan_d VALUES ($spisan_id, $row[0], '$row[1]', '$row[2]');";

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
function SaveNewSpisanSession()
{
    global $strNewSpisanDate, $strNewSpisanDocName, $intNewSpisanObjID;
    global $intNewSpisanMatID, $strQuantity, $strPrice;
    
    // информация о новом списании (дата, номер документа, код покупателя)
    $_SESSION['strNewSpisanDate']=$strNewSpisanDate;            
    $_SESSION['strNewSpisanDocName']=$strNewSpisanDocName;                        
    $_SESSION['intNewSpisanObjID']=$intNewSpisanObjID;  

    // информация по последнем добавленном в список материале (код материала, количество, цена)
    $_SESSION['intNewSpisanMatID']=$intNewSpisanMatID;
    $_SESSION['strNewSpisanMatQuantity']=$strQuantity;            
    $_SESSION['strNewSpisanMatPrice']=$strPrice;               
}
// ----------------------------------------------------------------------------
function RestoreNewSpisanSession()
{
    global $strNewSpisanDate, $strNewSpisanDocName, $intNewSpisanObjID;
    global $intNewSpisanMatID, $strQuantity, $strPrice;
    
    // восстановить информацию о новом списании
    if (isset($_SESSION['strNewSpisanDate'])) $strNewSpisanDate=$_SESSION['strNewSpisanDate'];
        else $strNewSpisanDate=date("Y-m-d");
    if (isset($_SESSION['strNewSpisanDocName'])) $strNewSpisanDocName=$_SESSION['strNewSpisanDocName'];
        else $strNewSpisanDocName="";
    if (isset($_SESSION['intNewSpisanObjID'])) $intNewSpisanObjID=$_SESSION['intNewSpisanObjID'];            
        else $intNewSpisanObjID=0;
        
    // восстановить информацию о последнем выбранном материале
    if (isset($_SESSION['intNewSpisanMatID'])) $intNewSpisanMatID=$_SESSION['intNewSpisanMatID'];
        else $intNewSpisanMatID=0;
    if (isset($_SESSION['strNewSpisanMatQuantity'])) $strQuantity=$_SESSION['strNewSpisanMatQuantity'];
        else $strQuantity="";
    if (isset($_SESSION['strNewSpisanMatPrice'])) $strPrice=$_SESSION['strNewSpisanMatPrice'];
        else $strPrice="";
}
// ----------------------------------------------------------------------------
function GetPostData()
{
    global $conn;
    global $strNewSpisanDate, $strNewSpisanDocName, $intNewSpisanObjID;
    global $intNewSpisanMatID, $strQuantity, $strPrice;

    // код материала
    if (isset($_POST['comboMaterials']))
        $intNewSpisanMatID=get_post($conn, 'comboMaterials');
    // количество материала
    if (isset($_POST['editQuantity']))
        $strQuantity=get_post($conn, 'editQuantity');
    // стоимость материала
    if (isset($_POST['editPrice']))
        $strPrice=get_post($conn, 'editPrice');

    // дата списания
    if (isset($_POST['NewSpisanDate']))
        $strNewSpisanDate=get_post($conn, 'NewSpisanDate');

    // номер документа
    if (isset($_POST['editDocName']))
        $strNewSpisanDocName=get_post($conn, 'editDocName');
    else 
        $strNewSpisanDocName="";        

    // объект в combobox
    if (isset($_POST['comboObjects']))
        $intNewSpisanObjID=get_post($conn, 'comboObjects');
    else 
        $intNewSpisanObjID=0;        

}
?>