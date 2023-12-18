<?php
    require_once "login.php";
    require_once "utils.php";

    define("MODE_VIEWOBJECTS", 0);
    define("MODE_NEWOBJECT", 1);
    define("MODE_EDITOBJECT", 2);

    $intObjID=0;
    $intObjListMode=MODE_VIEWOBJECTS;
    $strObjName="";
    $strObjLocation="";
    
    if (!$loggedin)
    {
        echo "Необходимо авторизоваться.";
        die();
    }
    
   
    // нажатие на кнопку "Новый объект"
    if (isset($_POST['btnNewObj']))
    {
        $intObjListMode=MODE_NEWOBJECT;
        $_SESSION['intObjListMode']=$intObjListMode;
        
    }
    // нажатие на кнопку "Внести изменения" или "Отмена" в режиме редактирования объекта
    else if ( isset($_POST['Save']) )
    {
        if ($_POST['Save']=='Save')
        {
            if (isset($_POST['editObjID']))
                $intObjID=get_post($conn, 'editObjID'); // hidden field in form
            else 
                $intObjID=0;

            if (isset($_POST['editObjName']))
                $strObjName=trim(get_post($conn, 'editObjName'));
            else 
                $strObjName="";
            if (isset($_POST['editObjLocation']))
                $strObjLocation=trim(get_post($conn, 'editObjLocation'));
            else 
                $strObjLocation="";
            
            ObjList_SaveChanges();
            
            $intObjListMode=MODE_VIEWOBJECTS;
            $_SESSION['intObjListMode']=$intObjListMode;            
        }
        // или нажата кнопка Отмена
        else if ($_POST['Save']=='Cancel')
        {
            $intObjListMode=MODE_VIEWOBJECTS;
            $_SESSION['intObjListMode']=$intObjListMode;            
        }
    }
    // нажатие на кнопку "Сохранить объект" или "Отмена" в режиме добавления объекта
    else if ( isset($_POST['Add']) )
    {
        if ($_POST['Add']=='Add')
        {
            if (isset($_POST['editObjName']))
                $strObjName=trim(get_post($conn, 'editObjName'));
            else 
                $strObjName="";
            if (isset($_POST['editObjLocation']))
                $strObjLocation=trim(get_post($conn, 'editObjLocation'));
            else 
                $strObjLocation="";

            ObjList_Add();
            
            $intObjListMode=MODE_VIEWOBJECTS;      
            $_SESSION['intObjListMode']=$intObjListMode;            
        }
        // или нажата кнопка Отмена
        else if ($_POST['Add']=='Cancel')
        {
            $intObjListMode=MODE_VIEWOBJECTS;
            $_SESSION['intObjListMode']=$intObjListMode;            
        }
        
    }
    // переход по ссылке из списка объектов
    else if (isset($_GET['ob_id']) )
    {
        $intObjID=get_get($conn, 'ob_id');
        
        // загрузить данные об объекте из БД по ID
        if ($intObjID!=0)
        {
            $oi=getObjByID($conn, $intObjID);
            $strObjName=$oi->name;
            $strObjLocation=$oi->location;            
            $intObjListMode=MODE_EDITOBJECT;
            $_SESSION['intObjListMode']=$intObjListMode;
        }
    }
    else 
    {
        //if (isset($_SESSION['intObjListMode'])) $intObjListMode=$_SESSION['intObjListMode'];
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
    <h1>Объекты</h1><br>
    </div> <!-- end col 1-->
    </div> <!--  end row 1-->
    _END;
    
    echo <<< _END
    <div class="row">    <!-- row 2 -->
    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">    <!-- r2 c2-->
    _END;
    
    
    echo <<< _END
    <form name="ObjectsListForm" action="objects.php" method="post" enctype="application/x-www-form-urlencoded" accept-charset="utf-8">
    <button name="btnNewObj" type="submit" tabindex="1">Новый объект</button><br>
    </form><br>
    _END;                              
    
    $query="SELECT ob_id, ob_name, ob_location FROM objects;";
    
    $datasetObjects=$conn->query($query);
    if(!$datasetObjects) die($conn->connect_error);

    if ($datasetObjects->num_rows>0)  
    {
        echo  "<table class=\"table w-auto small table-sm table-striped table-bordered table-hover table-responsive\">";
        echo "<thead><tr>
              <th scope=\"col\">Код</th>
              <th scope=\"col\">Наименование</th>
              <th scope=\"col\">Местоположение</th>
              </tr></thead>";
        echo "<tbody>";
        for($j=0; $j<$datasetObjects->num_rows; ++$j)
        {
              echo "<tr>";
              $datasetObjects->data_seek($j);
              $row=$datasetObjects->fetch_array(MYSQLI_ASSOC);

              echo "<td>$row[ob_id]</td>
                   <td><a href=\"objects.php?ob_id=$row[ob_id]\">$row[ob_name]</a></td>
                   <td>$row[ob_location]</td>";
              echo "</tr>";
        }
        echo "</tbody></table>";
    }
    echo "<p>";
    echo "Найдено записей: $datasetObjects->num_rows"; 
    $datasetObjects->close();
    
    echo <<< _END
    </div> <!-- end col 1 row 2-->
    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4"> <!-- r2 c2-->
    _END;

    if ($intObjListMode==MODE_NEWOBJECT)
    {
        echo <<< _END
        <form name="ObjAddForm" action="objects.php" method="post" enctype="application/x-www-form-urlencoded" accept-charset="utf-8"> 
        
        <fieldset class="border rounded-3 p-3">
        <legend class="float-none w-auto px-3">Добавление нового объекта</legend>

        <label class="form-label" for="editObjName">Наименование</label>
        <input class="form-control" type="text" name="editObjName" value='$strObjName' id="editObjName" maxlength="100" size="30" tabindex="1"><br> 

        <label class="form-label" for="editObjLocation">Местоположение</label>
        <input class="form-control" type="text" name="editObjLocation" value='$strObjLocation' id="editObjLocation" maxlength="100" size="30" tabindex="2"><br> 
 
        <br>
        </fieldset>
        <br>
        <button class="btn btn-primary form-control" name="Add" type="submit" value="Add" tabindex="3">Сохранить объект</button>&nbsp;
        <button class="btn btn-primary form-control" name="Add" type="submit" value="Cancel" tabindex="4">Отмена</button>&nbsp;
        </form>
        _END;
    }
    else if ($intObjListMode==MODE_EDITOBJECT)
    {
        echo <<< _END
        <form name="ObjEditForm" action="objects.php" method="post" enctype="application/x-www-form-urlencoded" accept-charset="utf-8"> 
                
        <fieldset class="border rounded-3 p-3">
        <legend class="float-none w-auto px-3">Редактирование объекта</legend>
    
        <input type="hidden" name="editObjID" value="$intObjID">
        
        <label class="form-label" for="editObjName">Наименование</label>
        <input class="form-control" type="text" name="editObjName" value='$strObjName' id="editObjName" maxlength="100" size="30" tabindex="1"><br> 

        <label class="form-label" for="editObjLocation">Местоположение</label>
        <input class="form-control" type="text" name="editObjLocation" value='$strObjLocation' id="editObjLocation" maxlength="100" size="30" tabindex="2"><br> 
 
        <br>
        </fieldset>
        <br>
        <button class="btn btn-primary form-control" name="Save" type="submit" value="Save" tabindex="3">Внести изменения</button>&nbsp;
        <button class="btn btn-primary form-control" name="Save" type="submit" value="Cancel" tabindex="4">Отмена</button>&nbsp;
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
    function ObjList_SaveChanges()
    {
        global $conn;
        global $intObjID;
        global $strObjName;
        global $strObjLocation;       

        if ($strObjName=="")
	{ echo("Не задано Наименование объекта.");  return false; }
	if ($strObjLocation=="")
	{  echo("Не задано Местоположение объекта.");  return false; }
        
        // сформируем SQL-запрос на Изменение записи в таблицу objects
	$q="UPDATE objects SET ob_name='$strObjName',ob_location='$strObjLocation'" .
	   " WHERE ob_id='$intObjID';";
        
        $result=$conn->query($q);
        if (!$result)
        { echo "Сбой записи"; return false;}
        else
        {    
            echo "Изменен объект номер $intObjID.";
            return true;
        }   
    }
    //--------------------------------------------------------------------------
    function ObjList_Add()
    {
        global $conn;
        global $strObjName;
        global $strObjLocation;       

        if ($strObjName=="")
	{ echo("Не задано Наименование объекта.");  return false; }
	if ($strObjLocation=="")
	{  echo("Не задано Местоположение объекта.");  return false; }
        
        // сформируем SQL-запрос на добавление записи в таблицу objects
	$q="INSERT INTO objects (ob_name,ob_location) VALUES('$strObjName','$strObjLocation');";
      
        $result=$conn->query($q);
        if (!$result)
        { echo "Сбой записи"; return false;}
        else
        {    
            echo "Добавлен объект номер $conn->insert_id.";
            return true;
        }   
    }
?>
