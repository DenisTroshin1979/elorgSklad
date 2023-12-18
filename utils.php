<?php
function IsAdmin($conn)
{
    if (isset($_SESSION['idCurrentStorekeeper']))
    {
        $id = $_SESSION['idCurrentStorekeeper'];
    
        $sk = getStorekeeperByID($conn, $id);
        if ($sk)
        {    
            if ($sk->login == "admin")
                return true;
            else 
                return false;
        }
        else 
            return false;
    }
    return false;
}
//-----------------------------------------------------------------------------
function get_post($conn, $var)
{
    return $conn->real_escape_string($_POST[$var]);
}
//-----------------------------------------------------------------------------
function get_get($conn, $var)
{
    return $conn->real_escape_string($_GET[$var]);
}
//-----------------------------------------------------------------------------
// загрузка списка поставщиков в комбобокс
function UpdateSuppliersCombo($conn, $strComboName, $comboID, $strFirstItem, $intComboTabIndex, $intSelSupplierID)
{
    echo "<select name='$strComboName' id='$comboID' tabindex='$intComboTabIndex'>";                        
    if ($intSelSupplierID==0) echo "<option selected value=\"0\">$strFirstItem</option>";
        else echo "<option value=\"0\">$strFirstItem</option>";

    // список поставщиков загрузить в combobox
    $query="SELECT supplier_id, supplier_name FROM suppliers;";
    $result=$conn->query($query);
    if(!$result) die($conn->connect_error);                    

    for ($i=0; $i<$result->num_rows; ++$i)    
    {
        $result->data_seek($i);
        $row=$result->fetch_array(MYSQLI_ASSOC);
        if ($row['supplier_id']==$intSelSupplierID)
            echo "<option selected value=$row[supplier_id]>$row[supplier_name]</option>";
        else    
            echo "<option value=$row[supplier_id]>$row[supplier_name]</option>";
    }
    echo "</select>";
    $result->close();
}
//-----------------------------------------------------------------------------
// загрузка списка объектов в комбобокс
function UpdateObjectsCombo($conn, $strComboName, $comboID, $strFirstItem, $intComboTabIndex, $intSelObjID)
{
    echo "<select name='$strComboName' id='$comboID' tabindex='$intComboTabIndex'>";                        
    if ($intSelObjID==0) echo "<option selected value=\"0\">$strFirstItem</option>";
        else echo "<option value=\"0\">$strFirstItem</option>";

    // список объектов загрузить в combobox
    $query="SELECT ob_id, ob_name FROM objects;";
    $result=$conn->query($query);
    if(!$result) die($conn->connect_error);                    

    for ($i=0; $i<$result->num_rows; ++$i)    
    {
        $result->data_seek($i);
        $row=$result->fetch_array(MYSQLI_ASSOC);
        if ($row['ob_id']==$intSelObjID)
            echo "<option selected value=$row[ob_id]>$row[ob_name]</option>";
        else    
            echo "<option value=$row[ob_id]>$row[ob_name]</option>";
    }
    echo "</select>";
    $result->close();
//-----------------------------------------------------------------------------
}
// загрузка списка покупателей в комбобокс
function UpdateBuyersCombo($conn, $strComboName, $comboID, $strFirstItem, $intComboTabIndex, $intSelBuyerID)
{
    echo "<select name='$strComboName' id='$comboID' tabindex='$intComboTabIndex'>";                        
    if ($intSelBuyerID==0) echo "<option selected value=\"0\">$strFirstItem</option>";
        else echo "<option value=\"0\">$strFirstItem</option>";

    // список покупателей загрузить в combobox
    $query="SELECT buyer_id, buyer_name FROM buyers;";
    $result=$conn->query($query);
    if(!$result) die($conn->connect_error);                    

    for ($i=0; $i<$result->num_rows; ++$i)    
    {
        $result->data_seek($i);
        $row=$result->fetch_array(MYSQLI_ASSOC);
        if ($row['buyer_id']==$intSelBuyerID)
            echo "<option selected value=$row[buyer_id]>$row[buyer_name]</option>";
        else    
            echo "<option value=$row[buyer_id]>$row[buyer_name]</option>";
    }
    echo "</select>";
    $result->close();
}
//-----------------------------------------------------------------------------
// загрузка списка кладовщиков в комбобокс
function UpdateStorekeepersCombo($conn, $strComboName, $comboID, $strFirstItem, $intComboTabIndex, $intSelStorekeeperID)
{
    echo "<select name='$strComboName' id='$comboID' tabindex='$intComboTabIndex'>";                        
    if ($intSelStorekeeperID==0) echo "<option selected value=\"0\">$strFirstItem</option>";
        else echo "<option value=\"0\">$strFirstItem</option>";

    // список кладовщиков загрузить в combobox
    $query="SELECT user_id, user_surname FROM users;";
    $result=$conn->query($query);
    if(!$result) die($conn->connect_error);                    

    for ($i=0; $i<$result->num_rows; ++$i)    
    {
        $result->data_seek($i);
        $row=$result->fetch_array(MYSQLI_ASSOC);
        if ($row['user_id']==$intSelStorekeeperID)
        echo "<option selected value=$row[user_id]>$row[user_surname]</option>";
        else    
            echo "<option value=$row[user_id]>$row[user_surname]</option>";
    }
    echo "</select>";
    $result->close();
}
//-----------------------------------------------------------------------------
// загрузка списка кладовщиков в комбобокс на странице авторизации
function UpdateLoginStorekeepersCombo($conn, $strComboName, $comboID, $strFirstItem, $intComboTabIndex, $intSelStorekeeperID)
{
    echo "<select class='form-control' name='$strComboName' id='$comboID' tabindex='$intComboTabIndex'>";                        
    if ($intSelStorekeeperID==0) echo "<option selected value=\"0\">$strFirstItem</option>";
        else echo "<option value=\"0\">$strFirstItem</option>";

    // список кладовщиков загрузить в combobox
    $query="SELECT user_id, user_surname, user_fname, user_midname FROM users;";
    $result=$conn->query($query);
    if(!$result) die($conn->connect_error);                    

    for ($i=0; $i<$result->num_rows; ++$i)    
    {
        $result->data_seek($i);
        $row=$result->fetch_array(MYSQLI_ASSOC);
        if ($row['user_id']==$intSelStorekeeperID)
        echo "<option selected value=$row[user_id]>$row[user_surname] $row[user_fname] $row[user_midname]</option>";
        else    
            echo "<option value=$row[user_id]>$row[user_surname] $row[user_fname] $row[user_midname]</option>";
    }
    echo "</select>";
    $result->close();
}
//-----------------------------------------------------------------------------
function UpdateMaterialsCombo($conn, $strComboName, $comboID, $strFirstItem, $intComboTabIndex, $intSelMatID)
{
    echo "<select name='$strComboName' id='$comboID' tabindex='$intComboTabIndex'>";                        
    if ($intSelMatID==0) echo "<option selected value=\"0\">$strFirstItem</option>";
        else echo "<option value=\"0\">$strFirstItem</option>";

    // список материалов загрузить в combobox
    $query="SELECT mat_id, mat_name FROM materials;";
    $result=$conn->query($query);
    if(!$result) die($conn->connect_error);                    

    for ($i=0; $i<$result->num_rows; ++$i)    
    {
        $result->data_seek($i);
        $row=$result->fetch_array(MYSQLI_ASSOC);
        if ($row['mat_id']==$intSelMatID)
        echo "<option selected value=$row[mat_id]>$row[mat_name]</option>";
        else    
            echo "<option value=$row[mat_id]>$row[mat_name]</option>";
    }
    echo "</select>";
    $result->close();
}        
//-----------------------------------------------------------------------------
// fill the list of prices for selected material
function UpdatePriceCombo($conn, $strComboName, $comboID, $strPrice, $intSelMatID)
{
    $s="";
    
    // список цен для заданного материала загрузить в combobox
    $query="SELECT postup_d_mat_id, postup_d_price FROM postup_d WHERE postup_d_mat_id=$intSelMatID;";
    $result=$conn->query($query);
    if(!$result) return $s;                    
    
    $s .= "&nbsp;<label>Цена</label>";
    $s .= "&nbsp;<input name=\"editPrice\" id=\"editPrice\" value=\"$strPrice\" maxlength=\"11\" size=\"12\" tabindex=\"7\" list=\"$comboID\">"; 
    
    $s .= "<datalist name=\"$strComboName\" id=\"$comboID\">";
     
    for ($i=0; $i<$result->num_rows; ++$i)    
    {
        $result->data_seek($i);
        $row=$result->fetch_array(MYSQLI_ASSOC);
        $s .= "<option>$row[postup_d_price]</option>";
    }
    $s .= "</datalist>";
    
    $result->close();
    return $s;
}        
//-----------------------------------------------------------------------------
// get material info by id
function getMaterialByID($conn, $id)
{
    $query="SELECT materials.mat_id, materials.mat_name, matunits.matunit_name," 
           . "materials.mat_quantity, mattypes.mattype_name"
           . " FROM materials join matunits on  materials.mat_unit=matunits.matunit_id"
           . " join mattypes on materials.mat_type=mattypes.mattype_id WHERE materials.mat_id='$id';";

    $result=$conn->query($query);
    if (!$result) { return 0; }
    if ($result->num_rows==0) return 0;
    
    $result->data_seek(0);
    $row=$result->fetch_array(MYSQLI_ASSOC);
    $name=$row['mat_name'];
    $unit=$row['matunit_name'];
    $quantity=$row['mat_quantity'];    
    $type=$row['mattype_name'];

    $result->close();
    $mi=new MaterialInfo($name, $unit, $quantity, $type);
    return $mi;
}
class MaterialInfo
{
    public $name;
    public $unit;
    public $quantity;
    public $type;
    
    function __construct($name, $unit, $quantity, $type)
    {
        $this->name = $name;
        $this->unit = $unit;
        $this->quantity = $quantity;        
        $this->type = $type;
    }
}
//-----------------------------------------------------------------------------
function findMaterialByName($conn, $strName)
{
    $id=0;
    
    $query="SELECT mat_id FROM materials WHERE mat_name LIKE '$strName';";
    $result=$conn->query($query);
    if (!$result) return 0;

    if ($result->num_rows!=0)
    {
        $result->data_seek(0);
        $id=$result->fetch_assoc()['mat_id'];
    }
    else $id=0;
    $result->close();
    return $id;
}        
//-----------------------------------------------------------------------------
function UpdateMatUnitsCombo($conn, $strComboName, $comboID, $strFirstItem, $intComboTabIndex, $intMatUnitID)
{
    echo "<select class='form-control' name='$strComboName' id='$comboID' tabindex='$intComboTabIndex'>";                        
    if ($intMatUnitID==0) echo "<option selected value=\"0\">$strFirstItem</option>";
        else echo "<option value=\"0\">$strFirstItem</option>";

    // список единиц измерения загрузить в combobox
    $query="SELECT matunit_id, matunit_name FROM matunits;";
    $result=$conn->query($query);
    if(!$result) die($conn->connect_error);                    

    for ($i=0; $i<$result->num_rows; ++$i)    
    {
        $result->data_seek($i);
        $row=$result->fetch_array(MYSQLI_ASSOC);
        if ($row['matunit_id']==$intMatUnitID)
        echo "<option selected value=$row[matunit_id]>$row[matunit_name]</option>";
        else    
            echo "<option value=$row[matunit_id]>$row[matunit_name]</option>";
    }
    echo "</select>";
    $result->close();
}        
//-----------------------------------------------------------------------------
function UpdateMatTypesCombo($conn, $strComboName, $comboID, $strFirstItem, $intComboTabIndex, $intMatTypeID)
{
    echo "<select class='form-control' name='$strComboName' id='$comboID' tabindex='$intComboTabIndex'>";                        
    if ($intMatTypeID==0) echo "<option selected value=\"0\">$strFirstItem</option>";
        else echo "<option value=\"0\">$strFirstItem</option>";

    // список типов материалов загрузить в combobox
    $query="SELECT mattype_id, mattype_name FROM mattypes;";
    $result=$conn->query($query);
    if(!$result) die($conn->connect_error);                    

    for ($i=0; $i<$result->num_rows; ++$i)    
    {
        $result->data_seek($i);
        $row=$result->fetch_array(MYSQLI_ASSOC);
        if ($row['mattype_id']==$intMatTypeID)
        echo "<option selected value=$row[mattype_id]>$row[mattype_name]</option>";
        else    
            echo "<option value=$row[mattype_id]>$row[mattype_name]</option>";
    }
    echo "</select>";
    $result->close();
}        
//-----------------------------------------------------------------------------
// get supplier info by id
function getSupplierByID($conn, $id)
{
    $query="SELECT supplier_name, supplier_inn, supplier_addr, supplier_phone "
           . "FROM suppliers WHERE supplier_id='$id';";
    $result=$conn->query($query);
    if (!$result) die($conn->connect_error);
    
    $result->data_seek(0);
    $row=$result->fetch_array(MYSQLI_ASSOC);
    $name=$row['supplier_name'];
    $inn=$row['supplier_inn'];
    $addr=$row['supplier_addr'];    
    $phone=$row['supplier_phone'];

    $result->close();
    $si=new SupplierInfo($name, $inn, $addr, $phone);
    return $si;
}
class SupplierInfo
{
    public $name;
    public $inn;
    public $addr;
    public $phone;
    
    function __construct($name, $inn, $addr, $phone)
    {
        $this->name = $name;
        $this->inn = $inn;
        $this->addr = $addr;        
        $this->phone = $phone;
    }
}
//-----------------------------------------------------------------------------
// retrieves supplier id by INN, if exists
function findSupplierByINN($conn, $inn)
{
    $id=0;
    
    $query="SELECT supplier_id FROM suppliers WHERE supplier_inn='$inn';";
    $result=$conn->query($query);
    if (!$result) return 0;

    if ($result->num_rows!=0)
    {
        $result->data_seek(0);
        $id=$result->fetch_assoc()['supplier_id'];
    }
    else $id=0;
    $result->close();
    return $id;
}        
//-----------------------------------------------------------------------------
// get buyer info by id
function getBuyerByID($conn, $id)
{
    $query="SELECT buyer_name, buyer_inn, buyer_addr, buyer_phone "
           . "FROM buyers WHERE buyer_id='$id';";
    $result=$conn->query($query);
    if (!$result) die($conn->connect_error);
    
    $result->data_seek(0);
    $row=$result->fetch_array(MYSQLI_ASSOC);
    $name=$row['buyer_name'];
    $inn=$row['buyer_inn'];
    $addr=$row['buyer_addr'];    
    $phone=$row['buyer_phone'];

    $result->close();
    $bi=new BuyerInfo($name, $inn, $addr, $phone);
    return $bi;
}
class BuyerInfo
{
    public $name;
    public $inn;
    public $addr;
    public $phone;
    
    function __construct($name, $inn, $addr, $phone)
    {
        $this->name = $name;
        $this->inn = $inn;
        $this->addr = $addr;        
        $this->phone = $phone;
    }
}
//-----------------------------------------------------------------------------
// retrieves buyer id by INN, if exists
function findBuyerByINN($conn, $inn)
{
    $id=0;
    
    $query="SELECT buyer_id FROM buyers WHERE buyer_inn='$inn';";
    $result=$conn->query($query);
    if (!$result) return 0;

    if ($result->num_rows!=0)
    {
        $result->data_seek(0);
        $id=$result->fetch_assoc()['buyer_id'];
    }
    else $id=0;
    $result->close();
    return $id;
}        
//-----------------------------------------------------------------------------
// get object info by id
function getObjByID($conn, $id)
{
    $query="SELECT ob_name,ob_location "
           . "FROM objects WHERE ob_id='$id';";
    $result=$conn->query($query);
    if (!$result) die($conn->connect_error);
    
    $result->data_seek(0);
    $row=$result->fetch_array(MYSQLI_ASSOC);
    $name=$row['ob_name'];
    $location=$row['ob_location'];

    $result->close();
    $oi=new ObjInfo($name, $location);
    return $oi;
}
class ObjInfo
{
    public $name;
    public $location;
    
    function __construct($name, $location)
    {
        $this->name = $name;
        $this->location = $location;
    }
}
//-----------------------------------------------------------------------------
// get storekeeper info by id
function getStorekeeperByID($conn, $id)
{
    $query="SELECT user_surname, user_fname, user_midname, user_passport,
           user_phone, user_login, user_pass FROM users WHERE user_id='$id';";
    $result=$conn->query($query);
    if (!$result) die($conn->connect_error);
    
    $result->data_seek(0);
    $row=$result->fetch_array(MYSQLI_ASSOC);
    $surname=$row['user_surname'];
    $fname=$row['user_fname'];
    $midname=$row['user_midname'];        
    $passport=$row['user_passport'];        
    $phone=$row['user_phone'];
    $login=$row['user_login'];        
    $pass=$row['user_pass'];        

    $result->close();
    $ski=new StorekeeperInfo($surname, $fname, $midname, $passport, $phone, $login, $pass);
    return $ski;
}

class StorekeeperInfo
{
    public $surname;
    public $fname;
    public $midname;
    public $passport;
    public $phone;
    public $login;
    public $pass;    
    
    function __construct($sn, $fn, $mn, $ps, $ph, $l, $p)
    {
        $this->surname = $sn;
        $this->fname=$fn;
        $this->midname=$mn;
        $this->passport=$ps;
        $this->phone=$ph;
        $this->login=$l;
        $this->pass=$p;
    }
}
//------------------------------------------------------------------------------
// retrieves storekeeper id by login, if exists
function findStorekeeperByLogin($conn, $login)
{
    $id=0;
    
    //$login=mb_strtolower($login);
    
    $query="SELECT user_id FROM users WHERE user_login='$login';";
    $result=$conn->query($query);
    if (!$result) return 0;

    if ($result->num_rows!=0)
    {
        $result->data_seek(0);
        $id=$result->fetch_assoc()['user_id'];
    }
    else $id=0;
    $result->close();
    return $id;
}        
//------------------------------------------------------------------------------
// load materials list from csv file
function LoadMaterialsFromFile($strFileName, &$arrMatList) 
{
    
    if (!file_exists($strFileName))
        return false;
    
    $h=fopen($strFileName, "r");
    if (!$h) return false;
    
    while (($row = fgetcsv($h, 0, ";")) !== FALSE) 
        $arrMatList[]=$row;
        
    fclose($h);
    return true;
}
// -----------------------------------------------------------------------------
// save materials list to csv file
function SaveMaterialsToFile($arrMatList, $strFileName) 
{
    
    $h=fopen($strFileName, "w+");
    if (!$h) return false;
    
    foreach($arrMatList as $fields)
        fputcsv($h, $fields, ";");
    unset($fields);
    fclose($h);
    return true;
}   
// -----------------------------------------------------------------------------
// добавляет материал в список arrMatList
// op - операция (1 - поступление, 2 - списание, 3 - продажа)
function AddMaterialToList($conn, &$arrMatList, $intMatID, $strQuantity, $strPrice, $op)
{
    if ($intMatID==0) 
    { echo("Выберите материал."); return false; }
    
    if ($strQuantity=="")
        { echo("Заполните поле Количество.");  return false; }
    if ($strPrice=="")
        { echo("Заполните поле Цена.");  return false; }
        
    if (!is_numeric($strQuantity))
	{ echo("Поле Количество должно быть числом.");  return false; }
    if ($strQuantity<=0)    
        { echo("Поле Количество должно быть больше 0.");  return false; }

    if (!is_numeric($strPrice))
	{ echo("Поле Цена должно быть числом.");  return false; }
    if ($strPrice<=0)    
        { echo("Поле Цена должно быть больше 0.");  return false; }
        
    // проверить, есть ли заданный материал в списке
    $mi=getMaterialByID($conn, $intMatID);
    if (IsMaterialInList($arrMatList, $intMatID)) 
    {echo("Материал $mi->name уже добавлен в список."); return false;}
    if ( ($op==2) || ($op==3) ) // операция списания или продажи
    {    
        if ($strQuantity > $mi->quantity) 
        { 
            echo ("Количество материала не может быть больше остатка.");
            return false;
        }    
    }
    // добавить материал в список
    $arrMatList[]=array($intMatID, $strQuantity, $strPrice);
    return true;
}
// -----------------------------------------------------------------------------
// проверяет, есть ли материал с intMatID в списке arrMatList
function IsMaterialInList($arrMatList, $intMatID)
{
    foreach ($arrMatList as $fields) 
    {
        if ($fields[0]==$intMatID)
            return true;
    }
    unset($fields);
    return false;
}
// -----------------------------------------------------------------------------
// removes material from the list, if exists
function DeleteMaterialFromList(&$arrMatList, $intMatID)
{
    for ($i=0; $i<count($arrMatList); $i++)
    {
        if ($arrMatList[$i][0]==$intMatID)
        { unset($arrMatList[$i]); return true;}
    }
    return false;
}
// -----------------------------------------------------------------------------
?>