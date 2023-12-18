<?php
require "login.php";
require "utils.php";

$intSpisanObjID=0;
$strSpisanDateFrom=date("Y-m-d");
$strSpisanDateTo=date("Y-m-d"); 
$intSpisanStorekeeperID=0;
$strSpisanDocName="";
$strSpisanFilter="";

$selSpisanID=0;
$selSpisanDate="";
$selSpisanObject="";
$selSpisanDoc="";

$bFirstCond=true;        
$bCheckboxSpisanChecked=false;
$bObj=false;
$bDateFrom=false;
$bDateTo=false;        
$bStorekeeper=false;
$bDocName=false;
$day=1;
$month=1;
$year=1970;
$strDateDisabled="disabled";
$strCheckBoxChecked="";
        
if (!$loggedin)
{
    echo "Необходимо авторизоваться.";
    die();
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

if (isset($_POST['filter']))
{
    if ($_POST['filter']=='filter')
    {
        $bFirstCond = true;
      
        // поставщик в combobox
        if (isset($_POST['comboObjects']))
        {
            $intSpisanObjID=get_post($conn, 'comboObjects');
            if ($intSpisanObjID!=0) $bObj=true;
        }
        else 
        {    
            $intSpisanObjID=0;        
            $bObj=false;
        }
        // период поставки
        if (isset($_POST['checkboxSpisan']))
        {
            $bCheckboxSpisanChecked=get_post($conn, 'checkboxSpisan');
        }
        if (isset($_POST['dateSpisanDateFrom']))
        {
            $strSpisanDateFrom=get_post($conn, 'dateSpisanDateFrom');
            // проверить корректность даты
            list($year, $month, $day) = explode("-", $strSpisanDateFrom);
            if (checkdate($month, $day, $year))    
               $bDateFrom=true;
            else $bDateFrom=false;
        }
        else $bDateFrom=false;
        if (isset($_POST['dateSpisanDateTo']))
        {
            $strSpisanDateTo=get_post($conn, 'dateSpisanDateTo');
            // проверить корректность даты
            list($year, $month, $day) = explode("-", $strSpisanDateTo);
            if (checkdate($month, $day, $year))    
                $bDateTo=true;
            else $bDateTo=false;
        }
        else $bDateTo=false;
        // кладовщик
        if (isset($_POST['comboSpisanStorekeepers']))
        {
            $intSpisanStorekeeperID=get_post($conn, 'comboSpisanStorekeepers');
            if ($intSpisanStorekeeperID!=0) $bStorekeeper=true;
        }
        else 
        {    
            $intSpisanStorekeeperID=0;        
            $bStorekeeper=false;                    
        }
        // наименование документа
        if (isset($_POST['editSpisanDocName']))
        {
            $strSpisanDocName=trim(get_post($conn, 'editSpisanDocName'));
            if ($strSpisanDocName!="") $bDocName=true;
        }
        else 
        {    
            $strSpisanDocName="";        
            $bDocName=false;                    
        }
                
        $strSpisanFilter="";
                
        if ($bObj)
        {
            $strSpisanFilter = $strSpisanFilter . "objects.ob_id='" . $intSpisanObjID . "'";
            $bFirstCond=false;
        }
                
        if ($bDateFrom && $bDateTo)
        {    
            if ($strSpisanDateTo >= $strSpisanDateFrom)                     
            {
                if ($bFirstCond)
                    $bFirstCond=false;
                else
                    $strSpisanFilter = $strSpisanFilter . " AND ";
                $strSpisanFilter = $strSpisanFilter . "spisan.spisan_date >= '" . $strSpisanDateFrom . "'";

                if ($bFirstCond)
                    $bFirstCond=false;
                else
                    $strSpisanFilter = $strSpisanFilter . " AND ";
                $strSpisanFilter = $strSpisanFilter . "spisan.spisan_date <= '" . $strSpisanDateTo . "'";
            }
            else 
            {
                echo "<font color='red'>Период списания задан неверно!</font><br><br>"; 
                $bCheckboxSpisanChecked=false; 
                $strSpisanDateFrom=date("Y-m-d"); 
                $strSpisanDateTo=date("Y-m-d");

            }
        }

        if ($bStorekeeper)
        {
            if ($bFirstCond)
                $bFirstCond=false;
            else 
                    $strSpisanFilter = $strSpisanFilter . " AND ";
            $strSpisanFilter = $strSpisanFilter . "users.user_id='" . $intSpisanStorekeeperID . "'";

        }

        if ($bDocName)
        {
            if ($bFirstCond)
                    $bFirstCond=false;
            else
                $strSpisanFilter = $strSpisanFilter . " AND ";
            $strSpisanFilter = $strSpisanFilter . "spisan.spisan_doc LIKE '%" . $strSpisanDocName . "%'";
        }

        // закончить оформление фильтра записей 
        if (!$bFirstCond)
            $strSpisanFilter = " WHERE " . $strSpisanFilter;  
    }
    else /*Кнопка Сброс*/            
    {
        // reset filter to default values
        $strSpisanFilter="";   
        $intSpisanObjID=0;
        $bCheckboxSpisanChecked=false;
        $strSpisanDateFrom=date("Y-m-d");
        $strSpisanDateTo=date("Y-m-d");        
        $intSpisanStorekeeperID=0;
        $strSpisanDocName="";
    }
    $_SESSION['strSpisanFilter']=$strSpisanFilter;
    $_SESSION['intSpisanObjID'] = $intSpisanObjID;
    $_SESSION['bCheckboxSpisanChecked'] = $bCheckboxSpisanChecked;
    $_SESSION['strSpisanDateFrom'] = $strSpisanDateFrom;
    $_SESSION['strSpisanDateTo'] = $strSpisanDateTo;
    $_SESSION['intSpisanStorekeeperID'] = $intSpisanStorekeeperID;            
    $_SESSION['strSpisanDocName'] =$strSpisanDocName;
    // reset selected item
    $selSpisanID=0;
    $_SESSION['spisan_id']=$selSpisanID;
    $selSpisanDate="";
    $_SESSION['spisan_date']=$selSpisanDate;
    $selSpisanObject="";
    $_SESSION['ob_name']=$selSpisanObject;
    $selSpisanDoc="";
    $_SESSION['spisan_doc']=$selSpisanDoc;
    
}
else if (isset($_GET['spisan_id']))
{
    $selSpisanID=get_get($conn, 'spisan_id');
    $_SESSION['spisan_id']=$selSpisanID;
    $selSpisanDate=get_get($conn, 'spisan_date');
    $_SESSION['spisan_date']=$selSpisanDate;
    $selSpisanObject=get_get($conn, 'ob_name');
    $_SESSION['ob_name']=$selSpisanObject;
    $selSpisanDoc=get_get($conn, 'spisan_doc');
    $_SESSION['spisan_doc']=$selSpisanDoc;

    if (isset($_SESSION['strSpisanFilter'])) $strSpisanFilter=$_SESSION['strSpisanFilter'];
    if (isset($_SESSION['intSpisanObjID'])) $intSpisanObjID=$_SESSION['intSpisanObjID'];
    if (isset($_SESSION['bCheckboxSpisanChecked'])) $bCheckboxSpisanChecked=$_SESSION['bCheckboxSpisanChecked'];
    if (isset($_SESSION['strSpisanDateFrom'])) $strSpisanDateFrom=$_SESSION['strSpisanDateFrom'];
    if (isset($_SESSION['strSpisanDateTo'] )) $strSpisanDateTo=$_SESSION['strSpisanDateTo'];
    if (isset($_SESSION['intSpisanStorekeeperID'] )) $intSpisanStorekeeperID=$_SESSION['intSpisanStorekeeperID'];            
    if (isset($_SESSION['strSpisanDocName'] )) $strSpisanDocName=$_SESSION['strSpisanDocName'];                        
}
else
{   
    if (isset($_SESSION['spisan_id'])) $selSpisanID=$_SESSION['spisan_id'];
    if (isset($_SESSION['spisan_date'])) $selSpisanDate=$_SESSION['spisan_date'];
    if (isset($_SESSION['ob_name'])) $selSpisanObject=$_SESSION['ob_name'];
    if (isset($_SESSION['spisan_doc'])) $selSpisanDoc=$_SESSION['spisan_doc'];
    
    if (isset($_SESSION['strSpisanFilter'])) $strSpisanFilter=$_SESSION['strSpisanFilter']; 
    if (isset($_SESSION['intSpisanObjID'])) $intSpisanObjID=$_SESSION['intSpisanObjID']; 
    if (isset($_SESSION['bCheckboxSpisanChecked'])) $bCheckboxSpisanChecked=$_SESSION['bCheckboxSpisanChecked'];
    if (isset($_SESSION['strSpisanDateFrom'])) $strSpisanDateFrom=$_SESSION['strSpisanDateFrom'];
    if (isset($_SESSION['strSpisanDateTo'] )) $strSpisanDateTo=$_SESSION['strSpisanDateTo'];
    if (isset($_SESSION['intSpisanStorekeeperID'] )) $intSpisanStorekeeperID=$_SESSION['intSpisanStorekeeperID'];            
    if (isset($_SESSION['strSpisanDocName'] )) $strSpisanDocName=$_SESSION['strSpisanDocName'];                        
}

echo "<h1>Списания</h1><br>";
        
$query="SELECT spisan.spisan_id, spisan.spisan_date, spisan.spisan_doc, 
    objects.ob_name, users.user_surname FROM objects 
    INNER JOIN (users INNER JOIN spisan ON spisan.spisan_user_id=users.user_id) 
    ON spisan.spisan_obj_id=objects.ob_id $strSpisanFilter ORDER BY spisan.spisan_id DESC;";			

$datasetSpisan=$conn->query($query); // инициализация $datasetSpisan
if(!$datasetSpisan) die($conn->connect_error); 

if ( ($datasetSpisan->num_rows>0) || ( ($datasetSpisan->num_rows==0) && ($strSpisanFilter!="")) )
{    
    if ($bCheckboxSpisanChecked)
    {    
        $strDateDisabled="";
        $strCheckBoxChecked="checked";
    }
    else
    {    
        $strDateDisabled="disabled";
        $strCheckBoxChecked="";                    
    }
                    
    echo <<< _END
    <form name="SpisanFilterForm" action="spisan.php" method="post" enctype="application/x-www-form-urlencoded" accept-charset="utf-8">
    <label for="comboObjects">Объект</label> 
    _END;
    
    UpdateObjectsCombo($conn, "comboObjects", "idObjectsCombo", "Все объекты", 1, $intSpisanObjID);

    echo <<< _END
    &nbsp;<input name="checkboxSpisan" type="checkbox" id="idcheckboxSpisan" tabindex="2" value='$bCheckboxSpisanChecked' $strCheckBoxChecked onclick="checkboxSpisan_onClick()">
    <label for="checkboxSpisan">Период</label>&nbsp;
    <label for="dateSpisanDateFrom">с</label> 
    <input name="dateSpisanDateFrom" id="iddateSpisanDateFrom" value='$strSpisanDateFrom' type="date" tabindex="3" $strDateDisabled> 
    <label for="dateSpisanDateTo">по</label> 
    <input name="dateSpisanDateTo" id="iddateSpisanDateTo" value='$strSpisanDateTo' type="date" tabindex="4" $strDateDisabled> 
    <br>
    <label for="comboSpisanStorekeepers">Кладовщик</label> 
    _END;
    
    UpdateStorekeepersCombo($conn, "comboSpisanStorekeepers", "idSpisanStorekeepersCombo", "Все кладовщики", 5, $intSpisanStorekeeperID);
    
    echo <<< _END
    <label for="editSpisanDocName">Документ</label>
    <input name="editSpisanDocName" value='$strSpisanDocName' type="text" maxlength="30" size="15" tabindex="6"> 
    <button name="filter" type="submit" value="filter" tabindex="7">Фильтр</button>&nbsp;<button name="filter" type="submit" value="nofilter" tabindex="8">Сброс</button>
    </form><br>
    _END;                              
    
    echo  "<table class=\"table w-auto small table-sm table-striped table-bordered table-hover table-responsive\">";
    echo "<thead><tr>
    <th scope=\"col\">Код</th>
    <th scope=\"col\">Дата</th>
    <th scope=\"col\">Документ</th>
    <th scope=\"col\">Объект</th>
    <th scope=\"col\">Кладовщик</th>
    </tr></thead>";
    echo "<tbody>";

    for($j=0; $j<$datasetSpisan->num_rows; ++$j)
    {
        echo "<tr>";
        $datasetSpisan->data_seek($j);
        $row=$datasetSpisan->fetch_array(MYSQLI_ASSOC);
        // заменить кавычки в названии объекта
        $convObj=htmlspecialchars($row['ob_name']);
        
        echo <<< _END
        <td>$row[spisan_id]</td>
        <td>$row[spisan_date]</td>
        <td><a href="spisan.php?spisan_id=$row[spisan_id]&spisan_date=$row[spisan_date]&ob_name=$convObj&spisan_doc=$row[spisan_doc]">$row[spisan_doc]</a></td>        
        <td>$convObj</td>
        <td>$row[user_surname]</td>
        </tr>
        _END;      
    }
    echo "</tbody></table>";
}
echo "<p>";
echo "Найдено записей: $datasetSpisan->num_rows"; 
$datasetSpisan->close();        

echo "</div>";

echo <<< _END
<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6"> 
_END;
if ($selSpisanID==0) echo "<b><i>Списание не выбрано</i></b>";
else
echo <<< _END
<b><i>Выбрано поступление:</i></b><br>Код: $selSpisanID<br>Дата:$selSpisanDate<br>Поставщик: $selSpisanObject<br> Документ: $selSpisanDoc
<p>  
_END;

echo "<hr>";

if ($selSpisanID!=0)
{    
    $query="SELECT materials.mat_name, matunits.matunit_name, 
            spisan_d.spisan_d_quantity, spisan_d.spisan_d_price 			
            FROM matunits INNER JOIN (spisan_d INNER JOIN materials 
            ON materials.mat_id=spisan_d.spisan_d_mat_id) 
            ON matunits.matunit_id=materials.mat_unit
            WHERE spisan_d.spisan_d_id='$selSpisanID';";

    $datasetMatVSpisan=$conn->query($query);
    if(!$datasetMatVSpisan) die($conn->connect_error);

    if ($datasetMatVSpisan->num_rows>0)  
    {
        echo  "<table class=\"table w-auto small table-sm table-striped table-bordered table-hover table-responsive\">";
        echo "<thead><tr>
              <th scope=\"col\">Наименование</th>
              <th scope=\"col\">Ед.изм</th>
              <th scope=\"col\">Количество</th>
              <th scope=\"col\">Цена</th>
              </tr></thead>";
        echo "<tbody>";
        for($j=0; $j<$datasetMatVSpisan->num_rows; ++$j)
        {
              echo "<tr>";
              $datasetMatVSpisan->data_seek($j);
              $row=$datasetMatVSpisan->fetch_array(MYSQLI_ASSOC);
              echo "<td>$row[mat_name]</td>
                   <td>$row[matunit_name]</td>
                   <td>$row[spisan_d_quantity]</td>
                   <td>$row[spisan_d_price]</td>";
              echo "</tr>";
        }
        echo "</tbody></table>";
    }
    echo "<p>";
    echo "Найдено записей: $datasetMatVSpisan->num_rows"; 
    $datasetMatVSpisan->close();
}
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

?>