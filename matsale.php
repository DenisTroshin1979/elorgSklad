<?php
        // filter_input ???
        $bDateFrom=false;
        $bDateTo=false;        
        $bBuyer=false;
        $day=1;
        $month=1;
        $year=1970;
        $strDateDisabled="disabled";
        $strCheckBoxChecked="";

        if (isset($_POST['filter4']))
        {
            if ($_POST['filter4']=='filter')
            {
                $bFirstCond = true;

                // поставщик в combobox
                if (isset($_POST['comboBuyers']))
                {
                    $intBuyerID=get_post($conn, 'comboBuyers');
                    if ($intBuyerID!=0) $bBuyer=true;
                }
                else 
                {    
                    $intBuyerID=0;        
                    $bBuyer=false;                    
                }
                // период продажи
                if (isset($_POST['checkboxMatSale']))
                {
                    $bCheckboxMatSaleChecked=get_post($conn, 'checkboxMatSale');
                }
                if (isset($_POST['dateMatSaleDateFrom']))
                {
                    $strMatSaleDateFrom=get_post($conn, 'dateMatSaleDateFrom');
                    // проверить корректность даты
                    list($year, $month, $day) = explode("-", $strMatSaleDateFrom);
                    if (checkdate($month, $day, $year))    
                        $bDateFrom=true;
                    else $bDateFrom=false;
                }
                else $bDateFrom=false;
                if (isset($_POST['dateMatSaleDateTo']))
                {
                    $strMatSaleDateTo=get_post($conn, 'dateMatSaleDateTo');
                    // проверить корректность даты
                    list($year, $month, $day) = explode("-", $strMatSaleDateTo);
                    if (checkdate($month, $day, $year))    
                        $bDateTo=true;
                    else $bDateTo=false;
                }
                else $bDateTo=false;
                
                $strFilter4="";
                
                if ($bBuyer)
                {
                        $strFilter4 = $strFilter4 . "buyers.buyer_id='" . $intBuyerID . "'";
                        $bFirstCond=false;
                }
                
                if ($bDateFrom && $bDateTo)
                {  
                    if ($strMatSaleDateTo >= $strMatSaleDateFrom)    
                    {
                        if ($bDateFrom)
                        {
                                if ($bFirstCond)
                                        $bFirstCond=false;
                                else
                                        $strFilter4 = $strFilter4 . " AND ";
                                $strFilter4 = $strFilter4 . "sales.sale_date >= '" . $strMatSaleDateFrom . "'";
                        }  
                        if ($bDateTo)
                        {
                                if ($bFirstCond)
                                        $bFirstCond=false;
                                else
                                        $strFilter4 = $strFilter4 . " AND ";
                                $strFilter4 = $strFilter4 . "sales.sale_date <= '" . $strMatSaleDateTo . "'";
                        }  
                    }
                     else 
                     {
                         echo "<font color='red'>Период продажи задан неверно!</font><br><br>";
                        $bCheckboxMatSaleChecked=false; 
                        $strMatSaleDateFrom=date("Y-m-d"); 
                        $strMatSaleDateTo=date("Y-m-d");
                     }   
                } 

                // закончить оформление фильтра записей 
                if (!$bFirstCond)
                    $strFilter4 = " AND " . $strFilter4;
            }
            else /*Кнопка Сброс*/            
            {
                $strMatSaleDateFrom=date("Y-m-d");
                $strMatSaleDateTo=date("Y-m-d");        
                $intBuyerID=0;
                $strFilter4="";                
                $bCheckboxMatSaleChecked=false;                
            }
            $_SESSION['strFilter4']=$strFilter4;
            $_SESSION['intBuyerID'] = $intBuyerID;
            $_SESSION['strMatSaleDateFrom'] = $strMatSaleDateFrom;
            $_SESSION['strMatSaleDateTo'] = $strMatSaleDateTo;
            $_SESSION['bCheckboxMatSaleChecked']=$bCheckboxMatSaleChecked;            
        }
        else 
        {
            if (isset($_SESSION['strFilter4'])) $strFilter4=$_SESSION['strFilter4'];
            if (isset($_SESSION['intBuyerID'])) $intBuyerID=$_SESSION['intBuyerID'];
            if (isset($_SESSION['bCheckboxMatSaleChecked'])) $bCheckboxMatSaleChecked=$_SESSION['bCheckboxMatSaleChecked'];
            if (isset($_SESSION['strMatSaleDateFrom'])) $strMatSaleDateFrom=$_SESSION['strMatSaleDateFrom'];
            if (isset($_SESSION['strMatSaleDateTo'] )) $strMatSaleDateTo=$_SESSION['strMatSaleDateTo'];
        }

        $query="SELECT sales_d.sale_d_id, sales.sale_date, 
                        sales.sale_date, sales.sale_doc, buyers.buyer_name, 
                        sales_d.sale_d_quantity, sales_d.sale_d_price
                        FROM buyers INNER JOIN (sales INNER JOIN sales_d 
                        ON sales.sale_id=sales_d.sale_d_id) 
                        ON buyers.buyer_id=sales.sale_buyer_id
                        WHERE sales_d.sale_d_mat_id=$selMatID $strFilter4 ORDER BY sales_d.sale_d_id ASC;";
                $datasetMatSale=$conn->query($query);
                if(!$datasetMatSale) die($conn->connect_error);

                if ( ($datasetMatSale->num_rows>0) || ( ($datasetMatSale->num_rows==0) && ($strFilter4!="")) )
                {    
                    if ($bCheckboxMatSaleChecked)
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
                    <form name="MatSaleFilterForm" action="materials.php" method="post" enctype="application/x-www-form-urlencoded" accept-charset="utf-8">
                        <input name="checkboxMatSale" type="checkbox" id="idcheckboxMatSale" tabindex="1" value='$bCheckboxMatSpisanChecked' $strCheckBoxChecked onclick="checkboxMatSale_onClick()">
                        <label for="checkboxMatSale">Период</label>&nbsp;
                        <label for="dateMatSaleDateFrom">с</label> 
                        <input name="dateMatSaleDateFrom" id="iddateMatSaleDateFrom" value='$strMatSaleDateFrom' type="date" tabindex="2" $strDateDisabled> 
                        <label for="dateMatSaleDateTo">по</label> 
                        <input name="dateMatSaleDateTo" id="iddateMatSaleDateTo" value='$strMatSaleDateTo' type="date" tabindex="3" $strDateDisabled> 
                        <label for="comboBuyers">Покупатели</label>
                    _END;

                    // список покупателей загрузить в combobox
                    $query="SELECT buyer_id, buyer_name FROM buyers;";
                    $result=$conn->query($query);
                    if(!$result) die($conn->connect_error);                    

                    $rows=$result->num_rows;

                    echo '<select name="comboBuyers" id="idBuyersCombo" tabindex="4">';                        
                    if ($intBuyerID==0) echo '<option selected value="0">Все покупатели</option>';
                        else echo '<option value="0">Все покупатели</option>';
                    for ($i=0; $i<$rows; ++$i)    
                    {
                            $result->data_seek($i);
                            $row=$result->fetch_array(MYSQLI_ASSOC);
                            if ($row['buyer_id']==$intBuyerID)
                                echo "<option selected value=$row[buyer_id]>$row[buyer_name]</option>";
                            else    
                                echo "<option value=$row[buyer_id]>$row[buyer_name]</option>";
                    }

                    echo "</select>";
                    echo "<button name=\"filter4\" type=\"submit\" value=\"filter\">Фильтр</button>&nbsp;<button name=\"filter4\" type=\"submit\" value=\"nofilter\">Сброс</button>";
                    echo "</form>";

                    $result->close();
                
                    echo "<table class=\"table w-auto small table-sm table-striped table-bordered table-hover table-responsive\">";
                    echo "<thead><tr>
                          <th scope=\"col\">Код</th>
                          <th scope=\"col\">Дата</th>
                          <th scope=\"col\">Документ</th>
                          <th scope=\"col\">Покупатель</th>
                          <th scope=\"col\">Количество</th>
                          <th scope=\"col\">Цена</th>
                          </tr></thead>";
                    echo "<tbody>";
                    for($j=0; $j<$datasetMatSale->num_rows; ++$j)
                    {
                              echo "<tr>";
                              $datasetMatSale->data_seek($j);
                              $row=$datasetMatSale->fetch_array(MYSQLI_ASSOC);
                              echo "<td>$row[sale_d_id]</td>
                                   <td>$row[sale_date]</td>
                                   <td>$row[sale_doc]</td>
                                   <td>$row[buyer_name]</td>
                                   <td>$row[sale_d_quantity]</td>
                                   <td>$row[sale_d_price]</td>";
                              echo "</tr>";
                    }
                    echo "</tbody></table>";
                }    
                echo "<p>";
                echo "Найдено записей: $datasetMatSale->num_rows"; 
                $datasetMatSale->close();
?>

