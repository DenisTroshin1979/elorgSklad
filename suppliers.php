<?php
    require_once "login.php";
    require_once "utils.php";

    define("MODE_VIEWSUPPLIERS", 0);
    define("MODE_NEWSUPPLIER", 1);
    define("MODE_EDITSUPPLIER", 2);

    $intSupplierID=0;
    $intSuplrListMode=MODE_VIEWSUPPLIERS;
    $strSupplierName="";
    $strSupplierINN="";
    $strSupplierAddr="";
    $strSupplierPhone="";
    
    if (!$loggedin)
    {
        echo "Необходимо авторизоваться.";
        die();
    }
    
   
    // нажатие на кнопку "Новый поставщик"
    if (isset($_POST['btnNewSupplier']))
    {
        $intSuplrListMode=MODE_NEWSUPPLIER;
        $_SESSION['intSuplrListMode']=$intSuplrListMode;
        
    }
    // нажатие на кнопку "Внести изменения" или "Отмена" в режиме редактирования поставщика
    else if ( isset($_POST['Save']) )
    {
        if ($_POST['Save']=='Save')
        {
            if (isset($_POST['editSupplierID']))
                $intSupplierID=get_post($conn, 'editSupplierID'); // hidden field in form
            else 
                $intSupplierID=0;

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
            
            SuppliersList_SaveChanges();
            
            $intSuplrListMode=MODE_VIEWSUPPLIERS;
            $_SESSION['intSuplrListMode']=$intSuplrListMode;            
        }
        // или нажата кнопка Отмена
        else if ($_POST['Save']=='Cancel')
        {
            $intSuplrListMode=MODE_VIEWSUPPLIERS;
            $_SESSION['intSuplrListMode']=$intSuplrListMode;            
        }
    }
    // нажатие на кнопку "Сохранить поставщика" или "Отмена" в режиме добавления поставщика
    else if ( isset($_POST['Add']) )
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

            SuppliersList_Add();
            
            $intSuplrListMode=MODE_VIEWSUPPLIERS;      
            $_SESSION['intSuplrListMode']=$intSuplrListMode;            
        }
        // или нажата кнопка Отмена
        else if ($_POST['Add']=='Cancel')
        {
            $intSuplrListMode=MODE_VIEWSUPPLIERS;
            $_SESSION['intSuplrListMode']=$intSuplrListMode;            
        }
        
    }
    // переход по ссылке из списка поставщиков
    else if (isset($_GET['supplier_id']) )
    {
        $intSupplierID=get_get($conn, 'supplier_id');
        
        // загрузить данные о поставщике из БД по ID
        if ($intSupplierID!=0)
        {
            $si=getSupplierByID($conn, $intSupplierID);
            $strSupplierName=$si->name;
            $strSupplierINN=$si->inn;            
            $strSupplierAddr=$si->addr;            
            $strSupplierPhone=$si->phone;            
            $intSuplrListMode=MODE_EDITSUPPLIER;
            $_SESSION['intSuplrListMode']=$intSuplrListMode;
        }
    }
    else 
    {
        //if (isset($_SESSION['intSuplrListMode'])) $intSuplrListMode=$_SESSION['intSuplrListMode'];
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
            <div class="row">   <!-- row 1-->
                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6"> <!-- r2 c1-->
    _END; 
        
    require 'menu.php';
    
    echo <<< _END
    <h1>Поставщики</h1><br>
    </div> <!-- end col 1-->
    </div> <!--  end row 1-->
    _END;
    
    echo <<< _END
    <div class="row">    <!-- row 2 -->
    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">    <!-- r2 c2-->
    _END;
    
    
    echo <<< _END
    <form name="SuppliersListForm" action="suppliers.php" method="post" enctype="application/x-www-form-urlencoded" accept-charset="utf-8">
    <button name="btnNewSupplier" type="submit" tabindex="1">Новый поставщик</button><br>
    </form><br>
    _END;                              
    
    $query="SELECT supplier_id, supplier_name, supplier_inn, supplier_addr, supplier_phone FROM suppliers;";
    
    $datasetSuppliers=$conn->query($query);
    if(!$datasetSuppliers) die($conn->connect_error);

    if ($datasetSuppliers->num_rows>0)  
    {
        echo  "<table class=\"table w-auto small table-sm table-striped table-bordered table-hover table-responsive\">";
        echo "<thead><tr>
              <th scope=\"col\">Код</th>
              <th scope=\"col\">Наименование</th>
              <th scope=\"col\">ИНН</th>
              <th scope=\"col\">Адрес</th>
              <th scope=\"col\">Телефон</th>              
              </tr></thead>";
        echo "<tbody>";
        for($j=0; $j<$datasetSuppliers->num_rows; ++$j)
        {
              echo "<tr>";
              $datasetSuppliers->data_seek($j);
              $row=$datasetSuppliers->fetch_array(MYSQLI_ASSOC);

              echo "<td>$row[supplier_id]</td>
                   <td><a href=\"suppliers.php?supplier_id=$row[supplier_id]\">$row[supplier_name]</a></td>
                   <td>$row[supplier_inn]</td>
                   <td>$row[supplier_addr]</td>                       
                   <td>$row[supplier_phone]</td>";
              echo "</tr>";
        }
        echo "</tbody></table>";
    }
    echo "<p>";
    echo "Найдено записей: $datasetSuppliers->num_rows"; 
    $datasetSuppliers->close();
    
    echo <<< _END
    </div> <!-- end col 1 row 2-->
    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4"> <!-- r2 c2-->
    _END;

    if ($intSuplrListMode==MODE_NEWSUPPLIER)
    {
        echo <<< _END
        <form name="SupplierAddForm" action="suppliers.php" method="post" enctype="application/x-www-form-urlencoded" accept-charset="utf-8"> 
        
        <fieldset class="border rounded-3 p-3">
        <legend class="float-none w-auto px-3">Добавление нового поставщика</legend>

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
        <button class="btn btn-primary form-control" name="Add" type="submit" value="Cancel" tabindex="6">Отмена</button>&nbsp;
        </form>
        _END;
    }
    else if ($intSuplrListMode==MODE_EDITSUPPLIER)
    {
        echo <<< _END
        <form name="SupplierEditForm" action="suppliers.php" method="post" enctype="application/x-www-form-urlencoded" accept-charset="utf-8"> 
        
        <fieldset class="border rounded-3 p-3">
        <legend class="float-none w-auto px-3">Редактирование поставщика</legend>

        <input type="hidden" name="editSupplierID" value="$intSupplierID">
                
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
        <button class="btn btn-primary form-control" name="Save" type="submit" value="Save" tabindex="5">Внести изменения</button>&nbsp;
        <button class="btn btn-primary form-control" name="Save" type="submit" value="Cancel" tabindex="6">Отмена</button>&nbsp;
        </form>
        _END;
        
    }
    
    echo "</div>";  // end r2 c2

    echo "</div>";  // end row 2
        
    echo <<< _END
    </div> <!-- end container-->
    </body>
    </html>
    _END;
    //--------------------------------------------------------------------------
    function SuppliersList_SaveChanges()
    {
        global $conn;
        global $intSupplierID;
        global $strSupplierName;
        global $strSupplierINN;
        global $strSupplierAddr;       
        global $strSupplierPhone;

        if ($strSupplierName=="")
	{ echo("Не задано Наименование поставщика.");  return false; }
	if ($strSupplierINN=="")
	{  echo("Не задан ИНН поставщика.");  return false; }
	
        if ( (mb_strlen($strSupplierINN)!=10) && (mb_strlen($strSupplierINN)!=12)  )
	{  echo("ИНН для юридического лица - 10 символов, для индивидуальных предпринимателей - 12 символов.");  return false; }

	// Поля Телефон и Адрес не являются необходимыми при заполнении формы
        // ...
        
	// По ИНН проверить уникальность поставщика
        if ($id=findSupplierByINN($conn, $strSupplierINN)!=0) 
        {
            if ($id!=$intSupplierID) // попытка поменять ИНН на ИНН используемый другим поставщиком
            { echo("Заданный ИНН использовать нельзя.");  return false;}
        }

        // сформируем SQL-запрос на Изменение записи в таблицу suppliers
        // поля: name, inn, addr, phone
	$q="UPDATE suppliers SET supplier_name='$strSupplierName',supplier_inn='$strSupplierINN',supplier_addr='$strSupplierAddr',supplier_phone='$strSupplierPhone'" .
	   " WHERE supplier_id='$intSupplierID';";
        
        $result=$conn->query($q);
        if (!$result)
        { echo "Сбой записи"; return false;}
        else
        {    
            echo "Изменен поставщик номер $intSupplierID.";
            return true;
        }   
    }
    //--------------------------------------------------------------------------
    function SuppliersList_Add()
    {
        global $conn;
        global $strSupplierName;
        global $strSupplierINN;
        global $strSupplierAddr;       
        global $strSupplierPhone;

        if ($strSupplierName=="")
	{ echo("Не задано Наименование поставщика.");  return false; }
	if ($strSupplierINN=="")
	{  echo("Не задан ИНН поставщика.");  return false; }
	
        if ( (mb_strlen($strSupplierINN)!=10) && (mb_strlen($strSupplierINN)!=12)  )
	{  echo("ИНН для юридического лица - 10 символов, для индивидуальных предпринимателей - 12 символов.");  return false; }

	// Поля Телефон и Адрес не являются необходимыми при заполнении формы
        // ...
        
	// По ИНН проверить уникальность поставщика
        if (findSupplierByINN($conn, $strSupplierINN)!=0) 
        { echo("Поставщик с таким ИНН уже существует.");  return false;}

        // сформируем SQL-запрос на добавление записи в таблицу Поставщики
        // поля: name, inn, addr, phone
	$q="INSERT INTO suppliers (supplier_name, supplier_inn, supplier_addr, supplier_phone) VALUES('$strSupplierName','$strSupplierINN','$strSupplierAddr','$strSupplierPhone');";
        
        $result=$conn->query($q);
        if (!$result)
        { echo "Сбой записи"; return false;}
        else
        {    
            echo "Добавлен поставщик номер $conn->insert_id.";
            return true;
        }   
    }
?>
