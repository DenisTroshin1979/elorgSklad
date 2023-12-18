<?php
        // filter_input ???
        $bDateFrom=false;
        $bDateTo=false;        
        $bObj=false;
        $day=1;
        $month=1;
        $year=1970;
        $strDateDisabled="disabled";
        $strCheckBoxChecked="";
        
        if (isset($_POST['filter3']))
        {
            if ($_POST['filter3']=='filter')
            {
                $bFirstCond = true;

                // поставщик в combobox
                if (isset($_POST['comboObjects']))
                {
                    $intObjID=get_post($conn, 'comboObjects');
                    if ($intObjID!=0) $bObj=true;
                }
                else 
                {    
                    $intObjID=0;        
                    $bObj=false;                    
                }
                // период списания
                if (isset($_POST['checkboxMatSpisan']))
                {
                    $bCheckboxMatSpisanChecked=get_post($conn, 'checkboxMatSpisan');
                }                
                if (isset($_POST['dateMatSpisanDateFrom']))
                {
                    $strMatSpisanDateFrom=get_post($conn, 'dateMatSpisanDateFrom');
                    // проверить корректность даты
                    list($year, $month, $day) = explode("-", $strMatSpisanDateFrom);
                    if (checkdate($month, $day, $year))    
                        $bDateFrom=true;
                    else $bDateFrom=false;
                }
                else $bDateFrom=false;
                if (isset($_POST['dateMatSpisanDateTo']))
                {
                    $strMatSpisanDateTo=get_post($conn, 'dateMatSpisanDateTo');
                    // проверить корректность даты
                    list($year, $month, $day) = explode("-", $strMatSpisanDateTo);
                    if (checkdate($month, $day, $year))    
                        $bDateTo=true;
                    else $bDateTo=false;
                }
                else $bDateTo=false;
                
                $strFilter3="";
                
                if ($bObj)
                {
                        $strFilter3 = $strFilter3 . "objects.ob_id='" . $intObjID . "'";
                        $bFirstCond=false;
                }
                if ($bDateFrom && $bDateTo)
                {    
                    if ($strMatSpisanDateTo >= $strMatSpisanDateFrom)    
                    {
                        if ($bFirstCond)
                            $bFirstCond=false;
                        else
                            $strFilter3 = $strFilter3 . " AND ";
                        $strFilter3 = $strFilter3 . "spisan.spisan_date >= '" . $strMatSpisanDateFrom . "'";
                        
                        if ($bFirstCond)
                            $bFirstCond=false;
                        else
                            $strFilter3 = $strFilter3 . " AND ";
                        $strFilter3 = $strFilter3 . "spisan.spisan_date <= '" . $strMatSpisanDateTo . "'";
                     }
                    else 
                    {
                        echo "<font color='red'>Период списания задан неверно!</font><br><br>";
                        $bCheckboxMatSpisanChecked=false; 
                        $strMatSpisanDateFrom=date("Y-m-d"); 
                        $strMatSpisanDateTo=date("Y-m-d");
                    }                     
                }    
                // закончить оформление фильтра записей 
                if (!$bFirstCond)
                    $strFilter3 = " AND " . $strFilter3;
            }
            else /*Кнопка Сброс*/            
            {
                $strMatSpisanDateFrom=date("Y-m-d");
                $strMatSpisanDateTo=date("Y-m-d");        
                $intObjID=0;
                $strFilter3="";                
                $bCheckboxMatSpisanChecked=false;
            }
            $_SESSION['strFilter3']=$strFilter3;
            $_SESSION['intObjID'] = $intObjID;
            $_SESSION['strMatSpisanDateFrom'] = $strMatSpisanDateFrom;
            $_SESSION['strMatSpisanDateTo'] = $strMatSpisanDateTo;
            $_SESSION['bCheckboxMatSpisanChecked']=$bCheckboxMatSpisanChecked;            
        }
        else 
        {
            if (isset($_SESSION['strFilter3'])) $strFilter3=$_SESSION['strFilter3'];
            if (isset($_SESSION['intObjID'])) $intObjID=$_SESSION['intObjID'];
            if (isset($_SESSION['bCheckboxMatSpisanChecked'])) $bCheckboxMatSpisanChecked=$_SESSION['bCheckboxMatSpisanChecked'];
            if (isset($_SESSION['strMatSpisanDateFrom'])) $strMatSpisanDateFrom=$_SESSION['strMatSpisanDateFrom'];
            if (isset($_SESSION['strMatSpisanDateTo'] )) $strMatSpisanDateTo=$_SESSION['strMatSpisanDateTo'];
        }
    
        $query="SELECT spisan_d.spisan_d_id, spisan.spisan_date, 
                        spisan.spisan_doc, objects.ob_name, 
                        spisan_d.spisan_d_quantity, spisan_d.spisan_d_price
                        FROM objects INNER JOIN (spisan INNER JOIN spisan_d 
                        ON spisan.spisan_id=spisan_d.spisan_d_id) ON objects.ob_id=spisan.spisan_obj_id 
                        WHERE spisan_d.spisan_d_mat_id=$selMatID $strFilter3 ORDER BY spisan_d.spisan_d_id ASC;";
                $datasetMatSpisan=$conn->query($query); // инициализация $datasetMatSpisan
                if(!$datasetMatSpisan) die($conn->connect_error);

                if ( ($datasetMatSpisan->num_rows>0) || ( ($datasetMatSpisan->num_rows==0) && ($strFilter3!="")) )
                {    
                   if ($bCheckboxMatSpisanChecked)
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
                    <form name="MatSpisanFilterForm" action="materials.php" method="post" enctype="application/x-www-form-urlencoded" accept-charset="utf-8">
                        <input name="checkboxMatSpisan" type="checkbox" id="idcheckboxMatSpisan" tabindex="1" value='$bCheckboxMatSpisanChecked' $strCheckBoxChecked  onclick="checkboxMatSpisan_onClick()">
                        <label for="checkboxMatSpisan">Период</label>&nbsp;
                        <label for="dateMatSpisanDateFrom">с</label> 
                        <input name="dateMatSpisanDateFrom" id="iddateMatSpisanDateFrom" value='$strMatSpisanDateFrom' type="date" tabindex="2" $strDateDisabled> 
                        <label for="dateMatSpisanDateTo">по</label> 
                        <input name="dateMatSpisanDateTo" id="iddateMatSpisanDateTo" value='$strMatSpisanDateTo' type="date" tabindex="3" $strDateDisabled> 
                        <label for="comboObjects">Объекты</label>
                    _END;


                    // список объектов загрузить в combobox
                    $query="SELECT ob_id, ob_name FROM objects;";
                    $result=$conn->query($query);
                    if(!$result) die($conn->connect_error);                    

                    $rows=$result->num_rows;

                    echo '<select name="comboObjects" id="idObjectsCombo" tabindex="4">';                        
                    if ($intObjID==0) echo '<option selected value="0">Все объекты</option>';
                        else echo '<option value="0">Все объекты</option>';
                    for ($i=0; $i<$rows; ++$i)    
                    {
                            $result->data_seek($i);
                            $row=$result->fetch_array(MYSQLI_ASSOC);
                            if ($row['ob_id']==$intObjID)
                                echo "<option selected value=$row[ob_id]>$row[ob_name]</option>";
                            else    
                                echo "<option value=$row[ob_id]>$row[ob_name]</option>";
                    }

                    echo "</select>";
                    echo "<button name=\"filter3\" type=\"submit\" value=\"filter\">Фильтр</button>&nbsp;<button name=\"filter3\" type=\"submit\" value=\"nofilter\">Сброс</button>";
                    echo "</form>";

                    $result->close();
                
                    echo  "<table class=\"table w-auto small table-sm table-striped table-bordered table-hover table-responsive\">";
                    echo "<thead><tr>
                          <th scope=\"col\">Код</th>
                          <th scope=\"col\">Дата</th>
                          <th scope=\"col\">Документ</th>
                          <th scope=\"col\">Объект</th>
                          <th scope=\"col\">Количество</th>
                          <th scope=\"col\">Цена</th>
                          </tr></thead>";
                    echo "<tbody>";
                    for($j=0; $j<$datasetMatSpisan->num_rows; ++$j)
                    {
                        echo "<tr>";
                        $datasetMatSpisan->data_seek($j);
                        $row=$datasetMatSpisan->fetch_array(MYSQLI_ASSOC);
                        echo "<td>$row[spisan_d_id]</td>
                              <td>$row[spisan_date]</td>
                              <td>$row[spisan_doc]</td>
                              <td>$row[ob_name]</td>
                              <td>$row[spisan_d_quantity]</td>
                              <td>$row[spisan_d_price]</td>";
                        echo "</tr>";
                    }
                    echo "</tbody></table>";
                }
                echo "<p>";
                echo "Найдено записей: $datasetMatSpisan->num_rows"; 
                $datasetMatSpisan->close();
                    
?>

