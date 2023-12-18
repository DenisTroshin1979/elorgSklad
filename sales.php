<?php
require "login.php";
require "utils.php";

$intSaleBuyerID=0;
$strSaleDateFrom=date("Y-m-d");
$strSaleDateTo=date("Y-m-d"); 
$intSaleStorekeeperID=0;
$strSaleDocName="";
$strSaleFilter="";

$selSaleID=0;
$selSaleDate="";
$selSaleBuyer="";
$selSaleDoc="";

$bFirstCond=true;        
$bCheckboxSaleChecked=false;
$bBuyer=false;
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
      
        // продавец в combobox
        if (isset($_POST['comboBuyers']))
        {
            $intSaleBuyerID=get_post($conn, 'comboBuyers');
            if ($intSaleBuyerID!=0) $bBuyer=true;
        }
        else 
        {    
            $intSaleBuyerID=0;        
            $bBuyer=false;
        }
        // период поставки
        if (isset($_POST['checkboxSale']))
        {
            $bCheckboxSaleChecked=get_post($conn, 'checkboxSale');
        }
        if (isset($_POST['dateSaleDateFrom']))
        {
            $strSaleDateFrom=get_post($conn, 'dateSaleDateFrom');
            // проверить корректность даты
            list($year, $month, $day) = explode("-", $strSaleDateFrom);
            if (checkdate($month, $day, $year))    
               $bDateFrom=true;
            else $bDateFrom=false;
        }
        else $bDateFrom=false;
        if (isset($_POST['dateSaleDateTo']))
        {
            $strSaleDateTo=get_post($conn, 'dateSaleDateTo');
            // проверить корректность даты
            list($year, $month, $day) = explode("-", $strSaleDateTo);
            if (checkdate($month, $day, $year))    
                $bDateTo=true;
            else $bDateTo=false;
        }
        else $bDateTo=false;
        // кладовщик
        if (isset($_POST['comboSaleStorekeepers']))
        {
            $intSaleStorekeeperID=get_post($conn, 'comboSaleStorekeepers');
            if ($intSaleStorekeeperID!=0) $bStorekeeper=true;
        }
        else 
        {    
            $intSaleStorekeeperID=0;        
            $bStorekeeper=false;                    
        }
        // наименование документа
        if (isset($_POST['editSaleDocName']))
        {
            $strSaleDocName=trim(get_post($conn, 'editSaleDocName'));
            if ($strSaleDocName!="") $bDocName=true;
        }
        else 
        {    
            $strSaleDocName="";        
            $bDocName=false;                    
        }
                
        $strSaleFilter="";
                
        if ($bBuyer)
        {
            $strSaleFilter = $strSaleFilter . "sales.sale_buyer_id='" . $intSaleBuyerID . "'";
            $bFirstCond=false;
        }
                
        if ($bDateFrom && $bDateTo)
        {    
            if ($strSaleDateTo >= $strSaleDateFrom)                     
            {
                if ($bFirstCond)
                    $bFirstCond=false;
                else
                    $strSaleFilter = $strSaleFilter . " AND ";
                $strSaleFilter = $strSaleFilter . "sales.sale_date >= '" . $strSaleDateFrom . "'";

                if ($bFirstCond)
                    $bFirstCond=false;
                else
                    $strSaleFilter = $strSaleFilter . " AND ";
                $strSaleFilter = $strSaleFilter . "sales.sale_date <= '" . $strSaleDateTo . "'";
            }
            else 
            {
                echo "<font color='red'>Период продажи задан неверно!</font><br><br>"; 
                $bCheckboxSaleChecked=false; 
                $strSaleDateFrom=date("Y-m-d"); 
                $strSaleDateTo=date("Y-m-d");

            }
        }

        if ($bStorekeeper)
        {
            if ($bFirstCond)
                $bFirstCond=false;
            else 
                    $strSaleFilter = $strSaleFilter . " AND ";
            $strSaleFilter = $strSaleFilter . "users.user_id='" . $intSaleStorekeeperID . "'";

        }

        if ($bDocName)
        {
            if ($bFirstCond)
                    $bFirstCond=false;
            else
                $strSaleFilter = $strSaleFilter . " AND ";
            $strSaleFilter = $strSaleFilter . "sales.sale_doc LIKE '%" . $strSaleDocName . "%'";
        }

        // закончить оформление фильтра записей 
        if (!$bFirstCond)
            $strSaleFilter = " WHERE " . $strSaleFilter;  
    }
    else /*Кнопка Сброс*/            
    {
        // reset filter to default values
        $strSaleFilter="";   
        $intSaleBuyerID=0;
        $bCheckboxSaleChecked=false;
        $strSaleDateFrom=date("Y-m-d");
        $strSaleDateTo=date("Y-m-d");        
        $intSaleStorekeeperID=0;
        $strSaleDocName="";
    }
    $_SESSION['strSaleFilter']=$strSaleFilter;
    $_SESSION['intSaleBuyerID'] = $intSaleBuyerID;
    $_SESSION['bCheckboxSaleChecked'] = $bCheckboxSaleChecked;
    $_SESSION['strSaleDateFrom'] = $strSaleDateFrom;
    $_SESSION['strSaleDateTo'] = $strSaleDateTo;
    $_SESSION['intSaleStorekeeperID'] = $intSaleStorekeeperID;            
    $_SESSION['strSaleDocName'] =$strSaleDocName;
    // reset selected item
    $selSaleID=0;
    $_SESSION['sale_id']=$selSaleID;
    $selSaleDate="";
    $_SESSION['sale_date']=$selSaleDate;
    $selSaleBuyer="";
    $_SESSION['buyer_name']=$selSaleBuyer;
    $selSaleDoc="";
    $_SESSION['sale_doc']=$selSaleDoc;
    
}
else if (isset($_GET['sale_id']))
{
    $selSaleID=get_get($conn, 'sale_id');
    $_SESSION['sale_id']=$selSaleID;
    $selSaleDate=get_get($conn, 'sale_date');
    $_SESSION['sale_date']=$selSaleDate;
    $selSaleBuyer=get_get($conn, 'buyer_name');
    $_SESSION['buyer_name']=$selSaleBuyer;
    $selSaleDoc=get_get($conn, 'sale_doc');
    $_SESSION['sale_doc']=$selSaleDoc;

    if (isset($_SESSION['strSaleFilter'])) $strSaleFilter=$_SESSION['strSaleFilter'];
    if (isset($_SESSION['intSaleBuyerID'])) $intSaleBuyerID=$_SESSION['intSaleBuyerID'];
    if (isset($_SESSION['bCheckboxSaleChecked'])) $bCheckboxSaleChecked=$_SESSION['bCheckboxSaleChecked'];
    if (isset($_SESSION['strSaleDateFrom'])) $strSaleDateFrom=$_SESSION['strSaleDateFrom'];
    if (isset($_SESSION['strSaleDateTo'] )) $strSaleDateTo=$_SESSION['strSaleDateTo'];
    if (isset($_SESSION['intSaleStorekeeperID'] )) $intSaleStorekeeperID=$_SESSION['intSaleStorekeeperID'];            
    if (isset($_SESSION['strSaleDocName'] )) $strSaleDocName=$_SESSION['strSaleDocName'];                        
}
else
{   
    if (isset($_SESSION['sale_id'])) $selSaleID=$_SESSION['sale_id'];
    if (isset($_SESSION['sale_date'])) $selSaleDate=$_SESSION['sale_date'];
    if (isset($_SESSION['buyer_name'])) $selSaleBuyer=$_SESSION['buyer_name'];
    if (isset($_SESSION['sale_doc'])) $selSaleDoc=$_SESSION['sale_doc'];
    
    if (isset($_SESSION['strSaleFilter'])) $strSaleFilter=$_SESSION['strSaleFilter']; 
    if (isset($_SESSION['intSaleBuyerID'])) $intSaleBuyerID=$_SESSION['intSaleBuyerID']; 
    if (isset($_SESSION['bCheckboxSaleChecked'])) $bCheckboxSaleChecked=$_SESSION['bCheckboxSaleChecked'];
    if (isset($_SESSION['strSaleDateFrom'])) $strSaleDateFrom=$_SESSION['strSaleDateFrom'];
    if (isset($_SESSION['strSaleDateTo'] )) $strSaleDateTo=$_SESSION['strSaleDateTo'];
    if (isset($_SESSION['intSaleStorekeeperID'] )) $intSaleStorekeeperID=$_SESSION['intSaleStorekeeperID'];            
    if (isset($_SESSION['strSaleDocName'] )) $strSaleDocName=$_SESSION['strSaleDocName'];                        
}

echo "<h1>Продажи</h1><br>";
        
$query="SELECT sales.sale_id, sales.sale_date, sales.sale_doc, buyers.buyer_name, 
        users.user_surname
        FROM buyers INNER JOIN (users INNER JOIN sales ON sales.sale_user_id=users.user_id) ON 
        sales.sale_buyer_id=buyers.buyer_id $strSaleFilter ORDER BY sales.sale_id DESC;";

$datasetSale=$conn->query($query); // инициализация $datasetSale
if(!$datasetSale) die($conn->connect_error); 

if ( ($datasetSale->num_rows>0) || ( ($datasetSale->num_rows==0) && ($strSaleFilter!="")) )
{    
    if ($bCheckboxSaleChecked)
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
    <form name="SaleFilterForm" action="sales.php" method="post" enctype="application/x-www-form-urlencoded" accept-charset="utf-8">
    <label for="comboBuyers">Покупатель</label> 
    _END;
    
    UpdateBuyersCombo($conn, "comboBuyers", "idBuyersCombo", "Все покупатели", 1, $intSaleBuyerID);

    echo <<< _END
    &nbsp;<input name="checkboxSale" type="checkbox" id="idcheckboxSale" tabindex="2" value='$bCheckboxSaleChecked' $strCheckBoxChecked onclick="checkboxSale_onClick()">
    <label for="checkboxSale">Период</label>&nbsp;
    <label for="dateSaleDateFrom">с</label> 
    <input name="dateSaleDateFrom" id="iddateSaleDateFrom" value='$strSaleDateFrom' type="date" tabindex="3" $strDateDisabled> 
    <label for="dateSaleDateTo">по</label> 
    <input name="dateSaleDateTo" id="iddateSaleDateTo" value='$strSaleDateTo' type="date" tabindex="4" $strDateDisabled> 
    <br>
    <label for="comboSaleStorekeepers">Кладовщик</label> 
    _END;
    
    UpdateStorekeepersCombo($conn, "comboSaleStorekeepers", "idSaleStorekeepersCombo", "Все кладовщики", 5, $intSaleStorekeeperID);
    
    echo <<< _END
    <label for="editSaleDocName">Документ</label>
    <input name="editSaleDocName" value='$strSaleDocName' type="text" maxlength="30" size="15" tabindex="6"> 
    <button name="filter" type="submit" value="filter" tabindex="7">Фильтр</button>&nbsp;<button name="filter" type="submit" value="nofilter" tabindex="8">Сброс</button>
    </form><br>
    _END;                              
    
    echo  "<table class=\"table w-auto small table-sm table-striped table-bordered table-hover table-responsive\">";
    echo "<thead><tr>
    <th scope=\"col\">Код</th>
    <th scope=\"col\">Дата</th>
    <th scope=\"col\">Документ</th>
    <th scope=\"col\">Покупатель</th>
    <th scope=\"col\">Кладовщик</th>
    </tr></thead>";
    echo "<tbody>";

    for($j=0; $j<$datasetSale->num_rows; ++$j)
    {
        echo "<tr>";
        $datasetSale->data_seek($j);
        $row=$datasetSale->fetch_array(MYSQLI_ASSOC);
        // заменить кавычки в названии покупателя
        $convBuyer=htmlspecialchars($row['buyer_name']);
        
        echo <<< _END
        <td>$row[sale_id]</td>
        <td>$row[sale_date]</td>
        <td><a href="sales.php?sale_id=$row[sale_id]&sale_date=$row[sale_date]&buyer_name=$convBuyer&sale_doc=$row[sale_doc]">$row[sale_doc]</a></td>        
        <td>$convBuyer</td>
        <td>$row[user_surname]</td>
        </tr>
        _END;      
    }
    echo "</tbody></table>";
}
echo "<p>";
echo "Найдено записей: $datasetSale->num_rows"; 
$datasetSale->close();        

echo "</div>";

echo <<< _END
<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6"> 
_END;
if ($selSaleID==0) echo "<b><i>Продажа не выбрана</i></b>";
else
echo <<< _END
<b><i>Выбрана продажа:</i></b><br>Код: $selSaleID<br>Дата:$selSaleDate<br>Покупатель: $selSaleBuyer<br> Документ: $selSaleDoc
<p>  
_END;

echo "<hr>";

if ($selSaleID!=0)
{    
    $query="SELECT materials.mat_name, matunits.matunit_name, 
            sales_d.sale_d_quantity, sales_d.sale_d_price 			
            FROM matunits INNER JOIN (sales_d INNER JOIN materials 
            ON materials.mat_id=sales_d.sale_d_mat_id) 
            ON matunits.matunit_id=materials.mat_unit
            WHERE sales_d.sale_d_id='$selSaleID';";

    $datasetMatVSale=$conn->query($query);
    if(!$datasetMatVSale) die($conn->connect_error);

    if ($datasetMatVSale->num_rows>0)  
    {
        echo  "<table class=\"table w-auto small table-sm table-striped table-bordered table-hover table-responsive\">";
        echo "<thead><tr>
              <th scope=\"col\">Наименование</th>
              <th scope=\"col\">Ед.изм</th>
              <th scope=\"col\">Количество</th>
              <th scope=\"col\">Цена</th>
              </tr></thead>";
        echo "<tbody>";
        for($j=0; $j<$datasetMatVSale->num_rows; ++$j)
        {
              echo "<tr>";
              $datasetMatVSale->data_seek($j);
              $row=$datasetMatVSale->fetch_array(MYSQLI_ASSOC);
              echo "<td>$row[mat_name]</td>
                   <td>$row[matunit_name]</td>
                   <td>$row[sale_d_quantity]</td>
                   <td>$row[sale_d_price]</td>";
              echo "</tr>";
        }
        echo "</tbody></table>";
    }
    echo "<p>";
    echo "Найдено записей: $datasetMatVSale->num_rows"; 
    $datasetMatVSale->close();
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