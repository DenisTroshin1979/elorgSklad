<?php
require_once 'login.php';
require_once 'utils.php';

if (!$loggedin)
{
    echo "Необходимо авторизоваться.";
    die();
}

// filter_input ???
$selMatID=0;
$selMatName="";
$selMatQuantity=0;
$selMatUnitName="";
$intMatType=0;
$strMatName="";
$strMatQuantityFrom="";
$strMatQuantityTo="";
$strFilter="";     
$bName=false;
$bQuantityFrom=false;
$bQuantityTo=false;
$bType=0;

$bCheckboxMatPostupChecked=false;
$intSupplierID=0;
$strMatPostupDateFrom=date("Y-m-d");
$strMatPostupDateTo=date("Y-m-d"); 
$strFilter2="";

$bCheckboxMatSpisanChecked=false;
$intObjID=0;
$strMatSpisanDateFrom=date("Y-m-d");
$strMatSpisanDateTo=date("Y-m-d");
$strFilter3="";

$bCheckboxMatSaleChecked=false;
$intBuyerID=0;
$strMatSaleDateFrom=date("Y-m-d");
$strMatSaleDateTo=date("Y-m-d");
$strFilter4="";

        
if (isset($_POST['filter']))
{
    if ($_POST['filter']=='filter')
    {
        $bFirstCond = true;
        // наименование материала
        if (isset($_POST['editMatName']))
        {
            $strMatName= trim(get_post($conn, 'editMatName'));
            if ($strMatName!='') $bName=true;
        }
        else 
        {
            $strMatName='';
            $bName=false;
        }
        // количество от..до
        if (isset($_POST['editQuantityFrom']))
        {
            $strMatQuantityFrom=trim(get_post($conn, 'editQuantityFrom'));
            if (($strMatQuantityFrom!='') && is_numeric($strMatQuantityFrom)) $bQuantityFrom=true;
        }
        else
        {    
            $strMatQuantityFrom='';
            $bQuantityFrom=false;
        }
        if (isset($_POST['editQuantityTo']))
        {
            $strMatQuantityTo=trim(get_post($conn, 'editQuantityTo'));
            if (($strMatQuantityTo!='') && is_numeric($strMatQuantityTo)) $bQuantityTo=true;
        }
        else 
        {
            $strMatQuantityTo='';
            $bQuantityTo=false;
        }
        // тип материала
        if (isset($_POST['comboMatType']))
        {
            $intMatType=get_post($conn, 'comboMatType');
            if ($intMatType!=0) $bType=true;
        }
        else 
        {    
            $intMatType=0;        
            $bType=false;                    
        }

        if ($bQuantityFrom && $bQuantityTo)
        {
                if ($strMatQuantityFrom > $strMatQuantityTo)
                {        
                    $bQuantityFrom=false;
                    $bQuantityTo=false; 
                    echo "Значение поля фильтра От не может быть больше значения поля До<br>"; 
                }
        }

        $strFilter="";
        if ($bName)
        {
                $strFilter = $strFilter . "materials.mat_name LIKE '%" . $strMatName . "%'";
                $bFirstCond=false;
        }
        if ($bQuantityFrom)
        {
                if ($bFirstCond)
                        $bFirstCond=false;
                else
                        $strFilter = $strFilter . " AND ";
                $strFilter = $strFilter . "materials.mat_quantity >= '" . $strMatQuantityFrom . "'";
        }
        if ($bQuantityTo)
        {
                if ($bFirstCond)
                        $bFirstCond=false;
                else
                        $strFilter = $strFilter . " AND ";
                $strFilter = $strFilter . "materials.mat_quantity <= '" . $strMatQuantityTo . "'";
        }
        if ($bType)
        {
                if ($bFirstCond)
                        $bFirstCond=false;
                else
                        $strFilter = $strFilter . " AND ";
                $strFilter= $strFilter . "materials.mat_type='" . $intMatType . "'";
        }
        // закончить оформление фильтра записей 
        if (!$bFirstCond)
            $strFilter = " WHERE " . $strFilter;
    }
    else /*Кнопка Сброс*/            
    {
        $strMatName="";
        $strMatQuantityFrom="";
        $strMatQuantityTo="";
        $intMatType=0;
        $strFilter="";

        ResetMatOperFilters();
    }
    $_SESSION['intMatType']=$intMatType;
    $_SESSION['strMatName']=$strMatName;
    $_SESSION['strMatQuantityFrom']=$strMatQuantityFrom;
    $_SESSION['strMatQuantityTo']=$strMatQuantityTo;
    $_SESSION['strFilter']=$strFilter;
    $selMatID=0;
    $_SESSION['mat_id']=0;
    $selMatName="";
    $_SESSION['mat_name']="";
    $selMatQuantity=0;
    $_SESSION['mat_quantity']=0;
    $selMatUnitName="";
    $_SESSION['mat_unitname']="";
}
else if (isset($_GET['mat_id']))
{
    $selMatID=get_get($conn, 'mat_id');
    $_SESSION['mat_id']=$selMatID;
    $selMatName=get_get($conn, 'mat_name');
    $_SESSION['mat_name']=$selMatName;
    $selMatQuantity=get_get($conn, 'mat_quantity');
    $_SESSION['mat_quantity']=$selMatQuantity;
    $selMatUnitName=get_get($conn, 'mat_unitname');
    $_SESSION['mat_unitname']=$selMatUnitName;

    if (isset($_SESSION['intMatType'])) $intMatType=$_SESSION['intMatType'];
    if (isset($_SESSION['strMatName'])) $strMatName=$_SESSION['strMatName'];
    if (isset($_SESSION['strMatQuantityFrom'])) $strMatQuantityFrom=$_SESSION['strMatQuantityFrom'];
    if (isset($_SESSION['strMatQuantityTo'])) $strMatQuantityTo=$_SESSION['strMatQuantityTo'];
    if (isset($_SESSION['strFilter'])) $strFilter=$_SESSION['strFilter'];

    ResetMatOperFilters();
}
else 
{
    if (isset($_SESSION['mat_id'])) $selMatID=$_SESSION['mat_id'];
    if (isset($_SESSION['mat_name'])) $selMatName=$_SESSION['mat_name'];
    if (isset($_SESSION['mat_quantity'])) $selMatQuantity=$_SESSION['mat_quantity'];
    if (isset($_SESSION['mat_unitname'])) $selMatUnitName=$_SESSION['mat_unitname'];

    if (isset($_SESSION['intMatType'])) $intMatType=$_SESSION['intMatType'];
    if (isset($_SESSION['strMatName'])) $strMatName=$_SESSION['strMatName'];
    if (isset($_SESSION['strMatQuantityFrom'])) $strMatQuantityFrom=$_SESSION['strMatQuantityFrom'];
    if (isset($_SESSION['strMatQuantityTo'])) $strMatQuantityTo=$_SESSION['strMatQuantityTo'];
    if (isset($_SESSION['strFilter'])) $strFilter=$_SESSION['strFilter'];
 }
       
echo <<< _END
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- <script src="jquery/jquery-3.6.0.js"></script>  -->
        <script type="text/javascript" src="bootstrap/js/bootstrap.bundle.min.js"></script>
        <script type="text/javascript" src="events.js"></script>        
        <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">         
        <title>Управление складом электромонтажной организации</title>
    </head>
    <body>
         
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
_END; 
        
require 'menu.php';
         
echo <<< _END
        <h1>Материалы</h1><br>
        <form name="MatFilterForm" action="materials.php" method="post" enctype="application/x-www-form-urlencoded" accept-charset="utf-8">
            <label for="editMatName">Наименование</label><br>
            <input name="editMatName" value='$strMatName' type="text" maxlength="100" size="30" tabindex="1"> 
            <label>Кол-во</label> 
            <label for="editQuantityFrom">от</label> 
            <input name="editQuantityFrom" value='$strMatQuantityFrom' type="text" maxlength="11" size="12" tabindex="2"> 
            <label for="editQuantityTo">до</label> 
            <input name="editQuantityTo" value='$strMatQuantityTo' type="text" maxlength="11" size="12" tabindex="3">                     
            <br>
            <label for="comboMatType">Тип</label> 
_END;            

            $query="SELECT mattype_id, mattype_name FROM mattypes;";
            $result=$conn->query($query);
            if(!$result) die($conn->connect_error);                    

            $rows=$result->num_rows;

            echo '<select name="comboMatType" id="idMatTypeCombo" tabindex="4">';                        
            if ($intMatType==0) echo '<option selected value="0">Все типы</option>';
                else echo '<option value="0">Все типы</option>';
            for ($i=0; $i<$rows; ++$i)    
            {
                $result->data_seek($i);
                $row=$result->fetch_array(MYSQLI_ASSOC);
                if ($row['mattype_id']==$intMatType)
                    echo "<option selected value=$row[mattype_id]>$row[mattype_name]</option>";
                else    
                    echo "<option value=$row[mattype_id]>$row[mattype_name]</option>";
            }

            echo '</select>';

            echo '<button name="filter" type="submit" value="filter">Фильтр</button>&nbsp;<button name="filter" type="submit" value="nofilter">Сброс</button>';
        echo '</form>';

            $result->close();     


            $query="SELECT materials.mat_id, materials.mat_name, matunits.matunit_name, 
                      materials.mat_quantity, mattypes.mattype_name 
                      FROM matunits INNER JOIN (mattypes INNER JOIN materials 
                      ON materials.mat_type=mattypes.mattype_id) 
                      ON materials.mat_unit=matunits.matunit_id $strFilter ORDER BY materials.mat_id ASC;"; 
              $datasetMaterials=$conn->query($query);
              if(!$datasetMaterials) die($conn->connect_error);

              echo "<p>";
              echo "<table class=\"table w-auto small table-sm table-striped table-bordered table-hover table-responsive\">";
              echo "<thead><tr>
                    <th scope=\"col\">Код</th>
                    <th scope=\"col\">Наименование</th>
                    <th scope=\"col\">Ед.изм.</th>
                    <th scope=\"col\">Остаток</th>
                    <th scope=\"col\">Тип</th>
                    </tr></thead>";
              echo "<tbody>";
              for($j=0; $j<$datasetMaterials->num_rows; ++$j)
              {
                  echo "<tr>";
                  $datasetMaterials->data_seek($j);
                  $row=$datasetMaterials->fetch_array(MYSQLI_ASSOC);
                  echo "<td>$row[mat_id]</td>
                        <td><a href=\"materials.php?mat_id=$row[mat_id]&mat_name=$row[mat_name]&mat_quantity=$row[mat_quantity]&mat_unitname=$row[matunit_name] \">$row[mat_name]</a></td> 
                        <td>$row[matunit_name]</td>
                        <td>$row[mat_quantity]</td>
                        <td>$row[mattype_name]</td>";
                  echo "</tr>";
              }

              echo "</tbody></table>";
              echo "Найдено записей: $datasetMaterials->num_rows <br><br>"; 
              $datasetMaterials->close();
        echo "</div>";
        // -------------------------------------------------------------
        echo <<< _END
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <div class="row">
        _END;
        if ($selMatID==0) echo "<b><i>Материал не выбран</i></b>";
        else
            echo <<< _END
            <b><i>Выбран материал:</i></b>($selMatID) $selMatName ($selMatQuantity $selMatUnitName)
            <p>  
            _END;      
        echo "</div>";
        echo "<hr>";
        if ($selMatID!=0)
        {    
            echo "<h3>Поступления материала</h3><br>";

            require 'matpostup.php';

            echo "<hr>";
            // -------------------------------------------------------------
            echo <<< _END

            <div class="row">
            <h3>Списания материала</h3><br>
            _END;

            require 'matspisan.php';

            echo "<hr>";                

            echo "</div>";
            // -------------------------------------------------------------                    
            echo <<< _END
            <div class="row">
            <h3>Продажи материала</h3><br>
            _END;

            require 'matsale.php';

            echo "</div>";
        }    
            echo <<< _END
            </div>
            </div>
            </div> 
            </body>
            </html>
            _END;
            
function ResetMatOperFilters()
{                
    $strMatPostupDateFrom=date("Y-m-d");
    $strMatPostupDateTo=date("Y-m-d");        
    $intSupplierID=0;
    $strFilter2="";   
    $bCheckboxMatPostupChecked=false;
    
    $_SESSION['strFilter2']=$strFilter2;
    $_SESSION['intSupplierID'] = $intSupplierID;
    $_SESSION['strMatPostupDateFrom'] = $strMatPostupDateFrom;
    $_SESSION['strMatPostupDateTo'] = $strMatPostupDateTo;
    $_SESSION['bCheckboxMatPostupChecked']=$bCheckboxMatPostupChecked;
    
    $strMatSpisanDateFrom=date("Y-m-d");
    $strMatSpisanDateTo=date("Y-m-d");        
    $intObjID=0;
    $strFilter3="";                
    $bCheckboxMatSpisanChecked=false;    
                
    $_SESSION['strFilter3']=$strFilter3;
    $_SESSION['intObjID'] = $intObjID;
    $_SESSION['strMatSpisanDateFrom'] = $strMatSpisanDateFrom;
    $_SESSION['strMatSpisanDateTo'] = $strMatSpisanDateTo;
    $_SESSION['bCheckboxMatSpisanChecked']=$bCheckboxMatSpisanChecked;            

    $strMatSaleDateFrom=date("Y-m-d");
    $strMatSaleDateTo=date("Y-m-d");        
    $intBuyerID=0;
    $strFilter4="";                
    $bCheckboxMatSaleChecked=false;     

    $_SESSION['strFilter4']=$strFilter4;
    $_SESSION['intBuyerID'] = $intBuyerID;
    $_SESSION['strMatSaleDateFrom'] = $strMatSaleDateFrom;
    $_SESSION['strMatSaleDateTo'] = $strMatSaleDateTo;
    $_SESSION['bCheckboxMatSaleChecked']=$bCheckboxMatSaleChecked;            
}                
?>