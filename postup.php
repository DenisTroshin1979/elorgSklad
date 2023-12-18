<?php
require_once "login.php";
require_once "utils.php";

$intPostupSupplierID=0;
$strPostupDateFrom=date("Y-m-d");
$strPostupDateTo=date("Y-m-d"); 
$intPostupStorekeeperID=0;
$strPostupDocName="";
$strPostupFilter="";

$selPostupID=0;
$selPostupDate="";
$selPostupSupplier="";
$selPostupDoc="";

$bFirstCond=true;        
$bCheckboxPostupChecked=false;
$bSupplier=false;
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
        if (isset($_POST['comboSuppliers']))
        {
            $intPostupSupplierID=get_post($conn, 'comboSuppliers');
            if ($intPostupSupplierID!=0) $bSupplier=true;
        }
        else 
        {    
            $intPostupSupplierID=0;        
            $bSupplier=false;
        }
        // период поставки
        if (isset($_POST['checkboxPostup']))
        {
            $bCheckboxPostupChecked=get_post($conn, 'checkboxPostup');
        }
        if (isset($_POST['datePostupDateFrom']))
        {
            $strPostupDateFrom=get_post($conn, 'datePostupDateFrom');
            // проверить корректность даты
            list($year, $month, $day) = explode("-", $strPostupDateFrom);
            if (checkdate($month, $day, $year))    
               $bDateFrom=true;
            else $bDateFrom=false;
        }
        else $bDateFrom=false;
        if (isset($_POST['datePostupDateTo']))
        {
            $strPostupDateTo=get_post($conn, 'datePostupDateTo');
            // проверить корректность даты
            list($year, $month, $day) = explode("-", $strPostupDateTo);
            if (checkdate($month, $day, $year))    
                $bDateTo=true;
            else $bDateTo=false;
        }
        else $bDateTo=false;
        // кладовщик
        if (isset($_POST['comboStorekeepers']))
        {
            $intPostupStorekeeperID=get_post($conn, 'comboStorekeepers');
            if ($intPostupStorekeeperID!=0) $bStorekeeper=true;
        }
        else 
        {    
            $intPostupStorekeeperID=0;        
            $bStorekeeper=false;                    
        }
        // наименование документа
        if (isset($_POST['editDocName']))
        {
            $strPostupDocName=trim(get_post($conn, 'editDocName'));
            if ($strPostupDocName!="") $bDocName=true;
        }
        else 
        {    
            $strPostupDocName="";        
            $bDocName=false;                    
        }
                
        $strPostupFilter="";
                
        if ($bSupplier)
        {
            $strPostupFilter = $strPostupFilter . "suppliers.supplier_id='" . $intPostupSupplierID . "'";
            $bFirstCond=false;
        }
                
        if ($bDateFrom && $bDateTo)
        {    
            if ($strPostupDateTo >= $strPostupDateFrom)                     
            {
                if ($bFirstCond)
                    $bFirstCond=false;
                else
                    $strPostupFilter = $strPostupFilter . " AND ";
                $strPostupFilter = $strPostupFilter . "postup.postup_date >= '" . $strPostupDateFrom . "'";

                if ($bFirstCond)
                    $bFirstCond=false;
                else
                    $strPostupFilter = $strPostupFilter . " AND ";
                $strPostupFilter = $strPostupFilter . "postup.postup_date <= '" . $strPostupDateTo . "'";
            }
            else 
            {
                echo "<font color='red'>Период поставки задан неверно!</font><br><br>"; 
                $bCheckboxPostupChecked=false; 
                $strPostupDateFrom=date("Y-m-d"); 
                $strPostupDateTo=date("Y-m-d");

            }
        }

        if ($bStorekeeper)
        {
            if ($bFirstCond)
                $bFirstCond=false;
            else 
                    $strPostupFilter = $strPostupFilter . " AND ";
            $strPostupFilter = $strPostupFilter . "users.user_id='" . $intPostupStorekeeperID . "'";

        }

        if ($bDocName)
        {
            if ($bFirstCond)
                    $bFirstCond=false;
            else
                $strPostupFilter = $strPostupFilter . " AND ";
            $strPostupFilter = $strPostupFilter . "postup.postup_doc LIKE '%" . $strPostupDocName . "%'";
        }

        // закончить оформление фильтра записей 
        if (!$bFirstCond)
            $strPostupFilter = " WHERE " . $strPostupFilter;  
    }
    else /*Кнопка Сброс*/            
    {
        $strPostupFilter="";   
        $intPostupSupplierID=0;
        $bCheckboxPostupChecked=false;
        $strPostupDateFrom=date("Y-m-d");
        $strPostupDateTo=date("Y-m-d");        
        $intPostupStorekeeperID=0;
        $strPostupDocName="";
    }
    
    $_SESSION['strPostupFilter']=$strPostupFilter;
    $_SESSION['intPostupSupplierID'] = $intPostupSupplierID;
    $_SESSION['bCheckboxPostupChecked'] = $bCheckboxPostupChecked;
    $_SESSION['strPostupDateFrom'] = $strPostupDateFrom;
    $_SESSION['strPostupDateTo'] = $strPostupDateTo;
    $_SESSION['intPostupStorekeeperID'] = $intPostupStorekeeperID;            
    $_SESSION['strPostupDocName'] =$strPostupDocName;
    //zero selected operation
    $_SESSION['postup_id']=$selPostupID=0;
    $_SESSION['postup_date']=$selPostupDate="";
    $_SESSION['supplier_name']=$selPostupSupplier="";
    $_SESSION['postup_doc']=$selPostupDoc="";
    
}
else if (isset($_GET['postup_id']))
{
    $selPostupID=get_get($conn, 'postup_id');
    $_SESSION['postup_id']=$selPostupID;
    $selPostupDate=get_get($conn, 'postup_date');
    $_SESSION['postup_date']=$selPostupDate;
    $selPostupSupplier=get_get($conn, 'supplier_name');
    $_SESSION['supplier_name']=$selPostupSupplier;
    $selPostupDoc=get_get($conn, 'postup_doc');
    $_SESSION['postup_doc']=$selPostupDoc;

    if (isset($_SESSION['strPostupFilter'])) $strPostupFilter=$_SESSION['strPostupFilter'];
    if (isset($_SESSION['intPostupSupplierID'])) $intPostupSupplierID=$_SESSION['intPostupSupplierID'];
    if (isset($_SESSION['bCheckboxPostupChecked'])) $bCheckboxPostupChecked=$_SESSION['bCheckboxPostupChecked'];
    if (isset($_SESSION['strPostupDateFrom'])) $strPostupDateFrom=$_SESSION['strPostupDateFrom'];
    if (isset($_SESSION['strPostupDateTo'] )) $strPostupDateTo=$_SESSION['strPostupDateTo'];
    if (isset($_SESSION['intPostupStorekeeperID'] )) $intPostupStorekeeperID=$_SESSION['intPostupStorekeeperID'];            
    if (isset($_SESSION['strPostupDocName'] )) $strPostupDocName=$_SESSION['strPostupDocName'];                        
}
else
{   
    if (isset($_SESSION['postup_id'])) $selPostupID=$_SESSION['postup_id'];
    if (isset($_SESSION['postup_date'])) $selPostupDate=$_SESSION['postup_date'];
    if (isset($_SESSION['supplier_name'])) $selPostupSupplier=$_SESSION['supplier_name'];
    if (isset($_SESSION['postup_doc'])) $selPostupDoc=$_SESSION['postup_doc'];
    
    if (isset($_SESSION['strPostupFilter'])) $strPostupFilter=$_SESSION['strPostupFilter']; 
    if (isset($_SESSION['intPostupSupplierID'])) $intPostupSupplierID=$_SESSION['intPostupSupplierID']; 
    if (isset($_SESSION['bCheckboxPostupChecked'])) $bCheckboxPostupChecked=$_SESSION['bCheckboxPostupChecked'];
    if (isset($_SESSION['strPostupDateFrom'])) $strPostupDateFrom=$_SESSION['strPostupDateFrom'];
    if (isset($_SESSION['strPostupDateTo'] )) $strPostupDateTo=$_SESSION['strPostupDateTo'];
    if (isset($_SESSION['intPostupStorekeeperID'] )) $intPostupStorekeeperID=$_SESSION['intPostupStorekeeperID'];            
    if (isset($_SESSION['strPostupDocName'] )) $strPostupDocName=$_SESSION['strPostupDocName'];                        
}

echo "<h1>Поступления</h1><br>";
        
$query="SELECT postup.postup_id, postup.postup_date, 
        postup.postup_doc, suppliers.supplier_name, users.user_surname
        FROM suppliers INNER JOIN (users INNER JOIN postup ON postup.postup_user_id=users.user_id) 
        ON postup.postup_suplr_id=suppliers.supplier_id $strPostupFilter ORDER BY postup.postup_id DESC;"; 

$datasetPostup=$conn->query($query); // инициализация $datasetPostup
if(!$datasetPostup) die($conn->connect_error); 

if ( ($datasetPostup->num_rows>0) || ( ($datasetPostup->num_rows==0) && ($strPostupFilter!="")) )
{    
    if ($bCheckboxPostupChecked)
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
    <form name="PostupFilterForm" action="postup.php" method="post" enctype="application/x-www-form-urlencoded" accept-charset="utf-8">
    <label for="comboSuppliers">Поставщик</label> 
    _END;
    
    UpdateSuppliersCombo($conn, "comboSuppliers", "idSuppliersCombo", "Все поставщики", 1, $intPostupSupplierID);

    echo <<< _END
    &nbsp;<input name="checkboxPostup" type="checkbox" id="idcheckboxPostup" tabindex="2" value='$bCheckboxPostupChecked' $strCheckBoxChecked onclick="checkboxPostup_onClick()">
    <label for="checkboxPostup">Период</label>&nbsp;
    <label for="datePostupDateFrom">с</label> 
    <input name="datePostupDateFrom" id="iddatePostupDateFrom" value='$strPostupDateFrom' type="date" tabindex="3" $strDateDisabled> 
    <label for="datePostupDateTo">по</label> 
    <input name="datePostupDateTo" id="iddatePostupDateTo" value='$strPostupDateTo' type="date" tabindex="4" $strDateDisabled> 
    <br>
    <label for="comboStorekeepers">Кладовщик</label> 
    _END;
    
    UpdateStorekeepersCombo($conn, "comboStorekeepers", "idStorekeepersCombo", "Все кладовщики", 5, $intPostupStorekeeperID);
    
    echo <<< _END
    <label for="editDocName">Документ</label>
    <input name="editDocName" value='$strPostupDocName' type="text" maxlength="30" size="15" tabindex="6"> 
    <button name="filter" type="submit" value="filter" tabindex="7">Фильтр</button>&nbsp;<button name="filter" type="submit" value="nofilter" tabindex="8">Сброс</button>
    </form><br>
    _END;                              
    
    echo  "<table class=\"table w-auto small table-sm table-striped table-bordered table-hover table-responsive\">";
    echo "<thead><tr>
    <th scope=\"col\">Код</th>
    <th scope=\"col\">Дата</th>
    <th scope=\"col\">Документ</th>
    <th scope=\"col\">Поставщик</th>
    <th scope=\"col\">Кладовщик</th>
    </tr></thead>";
    echo "<tbody>";

    for($j=0; $j<$datasetPostup->num_rows; ++$j)
    {
        echo "<tr>";
        $datasetPostup->data_seek($j);
        $row=$datasetPostup->fetch_array(MYSQLI_ASSOC);
        // заменить кавычки в названии организации
        $convSupplier=htmlspecialchars($row['supplier_name']);
        
        echo <<< _END
        <td>$row[postup_id]</td>
        <td>$row[postup_date]</td>
        <td><a href="postup.php?postup_id=$row[postup_id]&postup_date=$row[postup_date]&supplier_name=$convSupplier&postup_doc=$row[postup_doc]">$row[postup_doc]</a></td>        
        <td>$convSupplier</td>
        <td>$row[user_surname]</td>
        </tr>
        _END;      
    }
    echo "</tbody></table>";
}
echo "<p>";
echo "Найдено записей: $datasetPostup->num_rows"; 
$datasetPostup->close();        

echo "</div>";

echo <<< _END
<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6"> 
_END;
if ($selPostupID==0) echo "<b><i>Поступление не выбрано</i></b>";
else
echo <<< _END
<b><i>Выбрано поступление:</i></b><br>Код: $selPostupID<br>Дата:$selPostupDate<br>Поставщик: $selPostupSupplier<br> Документ: $selPostupDoc
<p>  
_END;

echo "<hr>";

if ($selPostupID!=0)
{    
    $query="SELECT materials.mat_name, matunits.matunit_name, postup_d.postup_d_quantity, postup_d.postup_d_price
    FROM matunits INNER JOIN (postup_d INNER JOIN materials ON materials.mat_id=postup_d.postup_d_mat_id) ON matunits.matunit_id=materials.mat_unit
    WHERE postup_d.postup_d_id='$selPostupID';";

    $datasetMatVPostup=$conn->query($query);
    if(!$datasetMatVPostup) die($conn->connect_error);

    if ($datasetMatVPostup->num_rows>0)  
    {
        echo  "<table class=\"table w-auto small table-sm table-striped table-bordered table-hover table-responsive\">";
        echo "<thead><tr>
              <th scope=\"col\">Наименование</th>
              <th scope=\"col\">Ед.изм</th>
              <th scope=\"col\">Количество</th>
              <th scope=\"col\">Цена</th>
              </tr></thead>";
        echo "<tbody>";
        for($j=0; $j<$datasetMatVPostup->num_rows; ++$j)
        {
              echo "<tr>";
              $datasetMatVPostup->data_seek($j);
              $row=$datasetMatVPostup->fetch_array(MYSQLI_ASSOC);
              echo "<td>$row[mat_name]</td>
                   <td>$row[matunit_name]</td>
                   <td>$row[postup_d_quantity]</td>
                   <td>$row[postup_d_price]</td>";
              echo "</tr>";
        }
        echo "</tbody></table>";
    }
    echo "<p>";
    echo "Найдено записей: $datasetMatVPostup->num_rows"; 
    $datasetMatVPostup->close();
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