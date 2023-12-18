<?php
        // filter_input ???
        $bDateFrom=false;
        $bDateTo=false;        
        $bSupplier=false;
        $day=1;
        $month=1;
        $year=1970;
        $strDateDisabled="disabled";
        $strCheckBoxChecked="";
                
        if (isset($_POST['filter2']))
        {
            if ($_POST['filter2']=='filter')
            {
                $bFirstCond = true;

                // поставщик в combobox
                if (isset($_POST['comboSuppliers']))
                {
                    $intSupplierID=get_post($conn, 'comboSuppliers');
                    if ($intSupplierID!=0) $bSupplier=true;
                }
                else 
                {    
                    $intSupplierID=0;        
                    $bSupplier=false;                    
                }
                // период поставки
                if (isset($_POST['checkboxMatPostup']))
                {
                    $bCheckboxMatPostupChecked=get_post($conn, 'checkboxMatPostup');
                }
                if (isset($_POST['dateMatPostupDateFrom']))
                {
                    $strMatPostupDateFrom=get_post($conn, 'dateMatPostupDateFrom');
                    // проверить корректность даты
                    list($year, $month, $day) = explode("-", $strMatPostupDateFrom);
                    if (checkdate($month, $day, $year))    
                        $bDateFrom=true;
                    else $bDateFrom=false;
                }
                else $bDateFrom=false;
                if (isset($_POST['dateMatPostupDateTo']))
                {
                    $strMatPostupDateTo=get_post($conn, 'dateMatPostupDateTo');
                    // проверить корректность даты
                    list($year, $month, $day) = explode("-", $strMatPostupDateTo);
                    if (checkdate($month, $day, $year))    
                        $bDateTo=true;
                    else $bDateTo=false;
                }
                else $bDateTo=false;
                
                $strFilter2="";
                
                if ($bSupplier)
                {
                        $strFilter2 = $strFilter2 . "suppliers.supplier_id='" . $intSupplierID . "'";
                        $bFirstCond=false;
                }
                
                if ($bDateFrom && $bDateTo)
                {    
                    if ($strMatPostupDateTo >= $strMatPostupDateFrom)                     
                    {
                        if ($bFirstCond)
                            $bFirstCond=false;
                        else
                            $strFilter2 = $strFilter2 . " AND ";
                        $strFilter2 = $strFilter2 . "postup.postup_date >= '" . $strMatPostupDateFrom . "'";

                        if ($bFirstCond)
                            $bFirstCond=false;
                        else
                            $strFilter2 = $strFilter2 . " AND ";
                        $strFilter2 = $strFilter2 . "postup.postup_date <= '" . $strMatPostupDateTo . "'";
                    }
                    else 
                    {
                        echo "<font color='red'>Период поставки задан неверно!</font><br><br>"; 
                        $bCheckboxMatPostupChecked=false; 
                        $strMatPostupDateFrom=date("Y-m-d"); 
                        $strMatPostupDateTo=date("Y-m-d");
                        
                    }
                }

                // закончить оформление фильтра записей 
                if (!$bFirstCond)
                    $strFilter2 = " AND " . $strFilter2;
            }
            else /*Кнопка Сброс*/            
            {
                $strMatPostupDateFrom=date("Y-m-d");
                $strMatPostupDateTo=date("Y-m-d");        
                $intSupplierID=0;
                $strFilter2="";   
                $bCheckboxMatPostupChecked=false;
            }
            $_SESSION['strFilter2']=$strFilter2;
            $_SESSION['intSupplierID'] = $intSupplierID;
            $_SESSION['strMatPostupDateFrom'] = $strMatPostupDateFrom;
            $_SESSION['strMatPostupDateTo'] = $strMatPostupDateTo;
            $_SESSION['bCheckboxMatPostupChecked']=$bCheckboxMatPostupChecked;
        }
        else
        {
            if (isset($_SESSION['strFilter2'])) $strFilter2=$_SESSION['strFilter2'];
            if (isset($_SESSION['intSupplierID'])) $intSupplierID=$_SESSION['intSupplierID'];
            if (isset($_SESSION['bCheckboxMatPostupChecked'])) $bCheckboxMatPostupChecked=$_SESSION['bCheckboxMatPostupChecked'];
            if (isset($_SESSION['strMatPostupDateFrom'])) $strMatPostupDateFrom=$_SESSION['strMatPostupDateFrom'];
            if (isset($_SESSION['strMatPostupDateTo'] )) $strMatPostupDateTo=$_SESSION['strMatPostupDateTo'];
        }
        
               
                $query="SELECT postup_d.postup_d_id, postup.postup_date, 
                        postup.postup_doc, suppliers.supplier_name, 
                        postup_d.postup_d_quantity, postup_d.postup_d_price
                        FROM suppliers INNER JOIN (postup INNER JOIN postup_d 
                        ON postup.postup_id=postup_d.postup_d_id) 
                        ON suppliers.supplier_id=postup.postup_suplr_id
                        WHERE postup_d.postup_d_mat_id=$selMatID $strFilter2 ORDER BY postup_d.postup_d_id ASC;";
                $datasetMatPostup=$conn->query($query); // инициализация $datasetMatPostup
                if(!$datasetMatPostup) die($conn->connect_error);

                if ( ($datasetMatPostup->num_rows>0) || ( ($datasetMatPostup->num_rows==0) && ($strFilter2!="")) )
                {    
                    if ($bCheckboxMatPostupChecked)
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
                    <form name="MatPostupFilterForm" action="materials.php" method="post" enctype="application/x-www-form-urlencoded" accept-charset="utf-8">
                      <input name="checkboxMatPostup" type="checkbox" id="idcheckboxMatPostup" tabindex="1" value='$bCheckboxMatPostupChecked' $strCheckBoxChecked onclick="checkboxMatPostup_onClick()">
                      <label for="checkboxMatPostup">Период</label>&nbsp;
                      <label for="dateMatPostupDateFrom">с</label> 
                      <input name="dateMatPostupDateFrom" id="iddateMatPostupDateFrom" value='$strMatPostupDateFrom' type="date" tabindex="2" $strDateDisabled> 
                      <label for="dateMatPostupDateTo">по</label> 
                      <input name="dateMatPostupDateTo" id="iddateMatPostupDateTo" value='$strMatPostupDateTo' type="date" tabindex="3" $strDateDisabled> 
                      <label for="comboSuppliers">Поставщики</label>
                    _END;

                        
                    echo '<select name="comboSuppliers" id="idSuppliersCombo" tabindex="4">';                        
                    if ($intSupplierID==0) echo '<option selected value="0">Все поставщики</option>';
                        else echo '<option value="0">Все поставщики</option>';

                     // список поставщиков загрузить в combobox
                    $query="SELECT supplier_id, supplier_name FROM suppliers;";
                    $result=$conn->query($query);
                    if(!$result) die($conn->connect_error);                    

                    for ($i=0; $i<$result->num_rows; ++$i)    
                    {
                          $result->data_seek($i);
                          $row=$result->fetch_array(MYSQLI_ASSOC);
                          if ($row['supplier_id']==$intSupplierID)
                              echo "<option selected value=$row[supplier_id]>$row[supplier_name]</option>";
                          else    
                              echo "<option value=$row[supplier_id]>$row[supplier_name]</option>";
                    }

                    echo "</select>";
                    echo "<button name=\"filter2\" type=\"submit\" value=\"filter\">Фильтр</button>&nbsp;<button name=\"filter2\" type=\"submit\" value=\"nofilter\">Сброс</button>";
                    echo "</form>";

                    $result->close();

                    echo  "<table class=\"table w-auto small table-sm table-striped table-bordered table-hover table-responsive\">";
                    echo "<thead><tr>
                          <th scope=\"col\">Код</th>
                          <th scope=\"col\">Дата</th>
                          <th scope=\"col\">Документ</th>
                          <th scope=\"col\">Поставщик</th>
                          <th scope=\"col\">Количество</th>
                          <th scope=\"col\">Цена</th>
                          </tr></thead>";
                    echo "<tbody>";
                    for($j=0; $j<$datasetMatPostup->num_rows; ++$j)
                      {
                          echo "<tr>";
                          $datasetMatPostup->data_seek($j);
                          $row=$datasetMatPostup->fetch_array(MYSQLI_ASSOC);
                          echo "<td>$row[postup_d_id]</td>
                               <td>$row[postup_date]</td>
                               <td>$row[postup_doc]</td>
                               <td>$row[supplier_name]</td>
                               <td>$row[postup_d_quantity]</td>
                               <td>$row[postup_d_price]</td>";
                          echo "</tr>";
                      }
                    echo "</tbody></table>";
                }
                echo "<p>";
                echo "Найдено записей: $datasetMatPostup->num_rows"; 
                $datasetMatPostup->close();

?>

