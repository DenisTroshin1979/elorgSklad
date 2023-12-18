<?php
    require_once "login.php";
    require_once "utils.php";

    define("MODE_VIEWBUYERS", 0);
    define("MODE_NEWBUYER", 1);
    define("MODE_EDITBUYER", 2);

    $intBuyerID=0;
    $intBuyerListMode=MODE_VIEWBUYERS;
    $strBuyerName="";
    $strBuyerINN="";
    $strBuyerAddr="";
    $strBuyerPhone="";
    
    if (!$loggedin)
    {
        echo "Необходимо авторизоваться.";
        die();
    }
    
   
    // нажатие на кнопку "Новый покупатель"
    if (isset($_POST['btnNewBuyer']))
    {
        $intBuyerListMode=MODE_NEWBUYER;
        $_SESSION['intBuyerListMode']=$intBuyerListMode;
        
    }
    // нажатие на кнопку "Внести изменения" или "Отмена" в режиме редактирования покупателя
    else if ( isset($_POST['Save']) )
    {
        if ($_POST['Save']=='Save')
        {
            if (isset($_POST['editBuyerID']))
                $intBuyerID=get_post($conn, 'editBuyerID'); // hidden field in form
            else 
                $intBuyerID=0;

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
            
            BuyersList_SaveChanges();
            
            $intBuyerListMode=MODE_VIEWBUYERS;
            $_SESSION['intBuyerListMode']=$intBuyerListMode;            
        }
        // или нажата кнопка Отмена
        else if ($_POST['Save']=='Cancel')
        {
            $intBuyerListMode=MODE_VIEWBUYERS;
            $_SESSION['intBuyerListMode']=$intBuyerListMode;            
        }
    }
    // нажатие на кнопку "Сохранить покупателя" или "Отмена" в режиме добавления покупателя
    else if ( isset($_POST['Add']) )
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

            BuyersList_Add();
            
            $intBuyerListMode=MODE_VIEWBUYERS;      
            $_SESSION['intBuyerListMode']=$intBuyerListMode;            
        }
        // или нажата кнопка Отмена
        else if ($_POST['Add']=='Cancel')
        {
            $intBuyerListMode=MODE_VIEWBUYERS;
            $_SESSION['intBuyerListMode']=$intBuyerListMode;            
        }
        
    }
    // переход по ссылке из списка покупателей
    else if (isset($_GET['buyer_id']) )
    {
        $intBuyerID=get_get($conn, 'buyer_id');
        
        // загрузить данные о покупателе из БД по ID
        if ($intBuyerID!=0)
        {
            $bi=getBuyerByID($conn, $intBuyerID);
            $strBuyerName=$bi->name;
            $strBuyerINN=$bi->inn;            
            $strBuyerAddr=$bi->addr;            
            $strBuyerPhone=$bi->phone;            
            $intBuyerListMode=MODE_EDITBUYER;
            $_SESSION['intBuyerListMode']=$intBuyerListMode;
        }
    }
    else 
    {
        //if (isset($_SESSION['intBuyerListMode'])) $intBuyerListMode=$_SESSION['intBuyerListMode'];
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
    <h1>Покупатели</h1><br>
    </div> <!-- end col 1-->
    </div> <!--  end row 1-->
    _END;
    
    echo <<< _END
    <div class="row">    <!-- row 2 -->
    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">    <!-- r2 c2-->
    _END;
    
    
    echo <<< _END
    <form name="BuyerListForm" action="buyers.php" method="post" enctype="application/x-www-form-urlencoded" accept-charset="utf-8">
    <button name="btnNewBuyer" type="submit" tabindex="1">Новый покупатель</button><br>
    </form><br>
    _END;                              
    
    $query="SELECT buyer_id, buyer_name, buyer_inn, buyer_addr, buyer_phone FROM buyers;";
    
    $datasetBuyers=$conn->query($query);
    if(!$datasetBuyers) die($conn->connect_error);

    if ($datasetBuyers->num_rows>0)  
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
        for($j=0; $j<$datasetBuyers->num_rows; ++$j)
        {
              echo "<tr>";
              $datasetBuyers->data_seek($j);
              $row=$datasetBuyers->fetch_array(MYSQLI_ASSOC);

              echo "<td>$row[buyer_id]</td>
                   <td><a href=\"buyers.php?buyer_id=$row[buyer_id]\">$row[buyer_name]</a></td>
                   <td>$row[buyer_inn]</td>
                   <td>$row[buyer_addr]</td>                       
                   <td>$row[buyer_phone]</td>";
              echo "</tr>";
        }
        echo "</tbody></table>";
    }
    echo "<p>";
    echo "Найдено записей: $datasetBuyers->num_rows"; 
    $datasetBuyers->close();
    
    echo <<< _END
    </div> <!-- end col 1 row 2-->
    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4"> <!-- r2 c2-->
    _END;

    if ($intBuyerListMode==MODE_NEWBUYER)
    {
        echo <<< _END
        <form name="BuyerAddForm" action="buyers.php" method="post" enctype="application/x-www-form-urlencoded" accept-charset="utf-8"> 
        
        <fieldset class="border rounded-3 p-3">
        <legend class="float-none w-auto px-3">Добавление нового покупателя</legend>

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
        <button class="btn btn-primary form-control" name="Add" type="submit" value="Cancel" tabindex="6">Отмена</button>&nbsp;
        </form>
        _END;
    }
    else if ($intBuyerListMode==MODE_EDITBUYER)
    {
        echo <<< _END
        <form name="BuyerEditForm" action="buyers.php" method="post" enctype="application/x-www-form-urlencoded" accept-charset="utf-8"> 
        
        <fieldset class="border rounded-3 p-3">
        <legend class="float-none w-auto px-3">Редактирование покупателя</legend>

        <input type="hidden" name="editBuyerID" value="$intBuyerID">
                
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
    function BuyersList_SaveChanges()
    {
        global $conn;
        global $intBuyerID;
        global $strBuyerName;
        global $strBuyerINN;
        global $strBuyerAddr;       
        global $strBuyerPhone;

        if ($strBuyerName=="")
	{ echo("Не задано Наименование покупателя.");  return false; }
	if ($strBuyerINN=="")
	{  echo("Не задан ИНН покупателя.");  return false; }
	
        if ( (mb_strlen($strBuyerINN)!=10) && (mb_strlen($strBuyerINN)!=12)  )
	{  echo("ИНН для юридического лица - 10 символов, для индивидуальных предпринимателей - 12 символов.");  return false; }

	// Поля Телефон и Адрес не являются необходимыми при заполнении формы
        // ...
        
	// По ИНН проверить уникальность покупателя
        $id=findBuyerByINN($conn, $strBuyerINN);
        if ($id!=0) 
        {
            if ($id!=$intBuyerID) // попытка поменять ИНН на ИНН используемый другим покупателем
            { echo("Заданный ИНН использовать нельзя.");  return false;}
        }

        // сформируем SQL-запрос на Изменение записи в таблицу buyers
        // поля: name, inn, addr, phone
	$q="UPDATE buyers SET buyer_name='$strBuyerName',buyer_inn='$strBuyerINN',buyer_addr='$strBuyerAddr',buyer_phone='$strBuyerPhone'" .
	   " WHERE buyer_id='$intBuyerID';";
        
        $result=$conn->query($q);
        if (!$result)
        { echo "Сбой записи"; return false;}
        else
        {    
            echo "Изменен покупатель номер $intBuyerID.";
            return true;
        }   
    }
    //--------------------------------------------------------------------------
    function BuyersList_Add()
    {
        global $conn;
        global $strBuyerName;
        global $strBuyerINN;
        global $strBuyerAddr;       
        global $strBuyerPhone;

        if ($strBuyerName=="")
	{ echo("Не задано Наименование покупателя.");  return false; }
	if ($strBuyerINN=="")
	{  echo("Не задан ИНН покупателя.");  return false; }
	
        if ( (mb_strlen($strBuyerINN)!=10) && (mb_strlen($strBuyerINN)!=12)  )
	{  echo("ИНН для юридического лица - 10 символов, для индивидуальных предпринимателей - 12 символов.");  return false; }

	// Поля Телефон и Адрес не являются необходимыми при заполнении формы
        // ...
        
	// По ИНН проверить уникальность покупателя
        if (findBuyerByINN($conn, $strBuyerINN)!=0) 
        { echo("Покупатель с таким ИНН уже существует.");  return false;}

        // сформируем SQL-запрос на добавление записи в таблицу buyers
        // поля: name, inn, addr, phone
	$q="INSERT INTO buyers (buyer_name, buyer_inn, buyer_addr, buyer_phone) VALUES('$strBuyerName','$strBuyerINN','$strBuyerAddr','$strBuyerPhone');";
        
        $result=$conn->query($q);
        if (!$result)
        { echo "Сбой записи"; return false;}
        else
        {    
            echo "Добавлен покупатель номер $conn->insert_id.";
            return true;
        }   
    }
?>
