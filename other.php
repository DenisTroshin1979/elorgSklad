<?php
    require_once "login.php";
    require_once "utils.php";

    $intOtherObjID=0; // индекс в комбобоксе Объекты на закладке Прочее
    $intOtherSupplierID=0; // индекс в комбобоксе Поставщики на закладке Прочее
    $intOtherBuyerID=0; // индекс в комбобоксе Покупатели на закладке Прочее
            
    if (!$loggedin)
    {
        echo "Необходимо авторизоваться.";
        die();
    }
    
    
    if (isset($_POST['filter1']))
    {
        if ($_POST['filter1']=='filter')
        {
            if (isset($_POST['comboObjects']))
            {
                $intOtherObjID=get_post($conn, 'comboObjects');
                $_SESSION['intOtherObjID']=$intOtherObjID;
            }

        }
    }
    else 
    {    
        if (isset($_SESSION['intOtherObjID'])) $intOtherObjID=$_SESSION['intOtherObjID'];
    }

    if (isset($_POST['filter2']))
    {
        if ($_POST['filter2']=='filter')
        {
            if (isset($_POST['comboSuppliers']))
            {
                $intOtherSupplierID=get_post($conn, 'comboSuppliers');
                $_SESSION['intOtherSupplierID']=$intOtherSupplierID;
            }

        }
    }
    else
    {
        if (isset($_SESSION['intOtherSupplierID'])) $intOtherSupplierID=$_SESSION['intOtherSupplierID'];        
    }
    
    if (isset($_POST['filter3']))        
    {
        if ($_POST['filter3']=='filter')
        {
            if (isset($_POST['comboBuyers']))
            {
                $intOtherBuyerID=get_post($conn, 'comboBuyers');
                $_SESSION['intOtherBuyerID']=$intOtherBuyerID;
            }

        }
    }
    else 
    {
        if (isset($_SESSION['intOtherBuyerID'])) $intOtherBuyerID=$_SESSION['intOtherBuyerID'];        
    }
    
    echo <<< _END
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script type="text/javascript" src="bootstrap/js/bootstrap.bundle.min.js"></script>
        <script type="text/javascript" src="login.js"></script>
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
        
    require 'menu.php';
    
    echo <<< _END
    <h1>Прочее</h1><br>
    </div> <!-- end col 1-->
    </div> <!--  end row 1-->
    _END;
    
    echo <<< _END
    <div class="row">    <!-- row 2 -->
    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">    <!-- row 2 col 2-->
    _END;
    
    // query materials by object
    echo <<< _END
    <form name="OtherFilterForm1" action="other.php" method="post" enctype="application/x-www-form-urlencoded" accept-charset="utf-8">
    <label for="comboObjects">Объекты</label> 
    _END;
    
    UpdateObjectsCombo($conn, "comboObjects", "idObjectsCombo", "Выберите объект...", 1, $intOtherObjID);
    
    echo <<< _END
    &nbsp;<button name="filter1" type="submit" value="filter" tabindex="2">Фильтр</button><br>
    <label>Материалы, списанные на объект</label>
    </form><br>
    _END;                              
    
    if ($intOtherObjID!=0)
    {    
        $query="SELECT materials.mat_name, matunits.matunit_name, SUM(spisan_d.spisan_d_quantity) AS sum_quantity1
        FROM matunits INNER JOIN (spisan INNER JOIN (materials INNER JOIN spisan_d ON materials.mat_id=spisan_d.spisan_d_mat_id) 
        ON spisan.spisan_id=spisan_d.spisan_d_id) ON matunits.matunit_id=materials.mat_unit
        WHERE spisan.spisan_obj_id='$intOtherObjID'
        GROUP BY materials.mat_name, matunits.matunit_name;";

        $datasetMatPerObject=$conn->query($query);
        if(!$datasetMatPerObject) die($conn->connect_error);

        if ($datasetMatPerObject->num_rows>0)  
        {
            echo  "<table class=\"table w-auto small table-sm table-striped table-bordered table-hover table-responsive\">";
            echo "<thead><tr>
                  <th scope=\"col\">Наименование</th>
                  <th scope=\"col\">Ед.изм</th>
                  <th scope=\"col\">Количество</th>
                  </tr></thead>";
            echo "<tbody>";
            for($j=0; $j<$datasetMatPerObject->num_rows; ++$j)
            {
                  echo "<tr>";
                  $datasetMatPerObject->data_seek($j);
                  $row=$datasetMatPerObject->fetch_array(MYSQLI_ASSOC);
                  echo "<td>$row[mat_name]</td>
                       <td>$row[matunit_name]</td>
                       <td>$row[sum_quantity1]</td>";
                  echo "</tr>";
            }
            echo "</tbody></table>";
        }
        echo "<p>";
        echo "Найдено записей: $datasetMatPerObject->num_rows"; 
        $datasetMatPerObject->close();
    }
    
    // query materials by supplier
    echo <<< _END
    </div> <!-- end col 1 row 2-->
    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4"> <!-- row 2 col 2-->
  
    <form name="OtherFilterForm2" action="other.php" method="post" enctype="application/x-www-form-urlencoded" accept-charset="utf-8">
    <label for="comboSuppliers">Поставщики</label> 
    _END;
    
    UpdateSuppliersCombo($conn, "comboSuppliers", "idSuppliersCombo", "Выберите поставщика...", 1, $intOtherSupplierID);
    
    echo <<< _END
    &nbsp;<button name="filter2" type="submit" value="filter" tabindex="2">Фильтр</button><br>
    <label>Материалы, поступившие от поставщика</label>
    </form><br>
    _END;                              
    
    if ($intOtherSupplierID!=0)
    {    
        $query="SELECT materials.mat_name, matunits.matunit_name, SUM(postup_d.postup_d_quantity) AS sum_quantity2
        FROM matunits INNER JOIN (postup INNER JOIN (materials INNER JOIN postup_d ON materials.mat_id=postup_d.postup_d_mat_id) 
        ON postup.postup_id=postup_d.postup_d_id) ON matunits.matunit_id=materials.mat_unit
        WHERE postup.postup_suplr_id='$intOtherSupplierID'
        GROUP BY materials.mat_name, matunits.matunit_name;";

        $datasetPerSupplier=$conn->query($query);
        if(!$datasetPerSupplier) die($conn->connect_error);

        if ($datasetPerSupplier->num_rows>0)  
        {
            echo  "<table class=\"table w-auto small table-sm table-striped table-bordered table-hover table-responsive\">";
            echo "<thead><tr>
                  <th scope=\"col\">Наименование</th>
                  <th scope=\"col\">Ед.изм</th>
                  <th scope=\"col\">Количество</th>
                  </tr></thead>";
            echo "<tbody>";
            for($j=0; $j<$datasetPerSupplier->num_rows; ++$j)
            {
                  echo "<tr>";
                  $datasetPerSupplier->data_seek($j);
                  $row=$datasetPerSupplier->fetch_array(MYSQLI_ASSOC);
                  echo "<td>$row[mat_name]</td>
                       <td>$row[matunit_name]</td>
                       <td>$row[sum_quantity2]</td>";
                  echo "</tr>";
            }
            echo "</tbody></table>";
        }
        echo "<p>";
        echo "Найдено записей: $datasetPerSupplier->num_rows"; 
        $datasetPerSupplier->close();
    }

    // query materials by buyer
    echo <<< _END
    </div>
    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4"> <!-- row 2 col 3 -->   
    <form name="OtherFilterForm3" action="other.php" method="post" enctype="application/x-www-form-urlencoded" accept-charset="utf-8">
    <label for="comboSuppliers">Покупатели</label> 
    _END;
    
    UpdateBuyersCombo($conn, "comboBuyers", "idBuyersCombo", "Выберите покупателя...", 1, $intOtherBuyerID);
    
    echo <<< _END
    &nbsp;<button name="filter3" type="submit" value="filter" tabindex="2">Фильтр</button><br> 
    <label>Материалы, проданные покупателю</label>
    </form><br>
    _END;                              

    if ($intOtherBuyerID!=0)
    {    
        $query="SELECT materials.mat_name, matunits.matunit_name, SUM(sales_d.sale_d_quantity) AS sum_quantity3
        FROM matunits INNER JOIN (sales INNER JOIN (materials INNER JOIN sales_d ON materials.mat_id=sales_d.sale_d_mat_id) 
        ON sales.sale_id=sales_d.sale_d_id) ON matunits.matunit_id=materials.mat_unit
        WHERE sales.sale_buyer_id='$intOtherBuyerID'
        GROUP BY materials.mat_name, matunits.matunit_name;";	  

        $datasetPerBuyer=$conn->query($query);
        if(!$datasetPerBuyer) die($conn->connect_error);

        if ($datasetPerBuyer->num_rows>0)  
        {
            echo  "<table class=\"table w-auto small table-sm table-striped table-bordered table-hover table-responsive\">";
            echo "<thead><tr>
                  <th scope=\"col\">Наименование</th>
                  <th scope=\"col\">Ед.изм</th>
                  <th scope=\"col\">Количество</th>
                  </tr></thead>";
            echo "<tbody>";
            for($j=0; $j<$datasetPerBuyer->num_rows; ++$j)
            {
                  echo "<tr>";
                  $datasetPerBuyer->data_seek($j);
                  $row=$datasetPerBuyer->fetch_array(MYSQLI_ASSOC);
                  echo "<td>$row[mat_name]</td>
                       <td>$row[matunit_name]</td>
                       <td>$row[sum_quantity3]</td>";
                  echo "</tr>";
            }
            echo "</tbody></table>";
        }
        echo "<p>";
        echo "Найдено записей: $datasetPerBuyer->num_rows"; 
        $datasetPerBuyer->close();
    }
    
    echo "</div>";
    echo "</div>";  // end row 2
        
    echo <<< _END
    </div> <!-- end container-->
    </body>
    </html>
    _END;

?>
