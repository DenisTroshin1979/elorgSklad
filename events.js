function checkboxMatPostup_onClick()
{
    if (idcheckboxMatPostup.checked==true)
    {
        idcheckboxMatPostup.value="true";
        iddateMatPostupDateFrom.disabled=false;
        iddateMatPostupDateTo.disabled=false;
    }    
    else
    {
        idcheckboxMatPostup.value="false";
        iddateMatPostupDateFrom.disabled=true;
        iddateMatPostupDateTo.disabled=true;
    }
    return;
}
function checkboxMatSpisan_onClick()
{
    if (idcheckboxMatSpisan.checked==true)
    {
        idcheckboxMatSpisan.value="true";
        iddateMatSpisanDateFrom.disabled=false;
        iddateMatSpisanDateTo.disabled=false;
    }    
    else
    {
        idcheckboxMatSpisan.value="false";
        iddateMatSpisanDateFrom.disabled=true;
        iddateMatSpisanDateTo.disabled=true;
    }
    return;
}
function checkboxMatSale_onClick()
{
    if (idcheckboxMatSale.checked==true)
    {
        idcheckboxMatSale.value="true";
        iddateMatSaleDateFrom.disabled=false;
        iddateMatSaleDateTo.disabled=false;
    }    
    else
    {
        idcheckboxMatSale.value="false";
        iddateMatSaleDateFrom.disabled=true;
        iddateMatSaleDateTo.disabled=true;
    }
    return;
}

function checkboxPostup_onClick()
{
    if (idcheckboxPostup.checked==true)
    {
        idcheckboxPostup.value="true";
        iddatePostupDateFrom.disabled=false;
        iddatePostupDateTo.disabled=false;
    }    
    else
    {
        idcheckboxPostup.value="false";
        iddatePostupDateFrom.disabled=true;
        iddatePostupDateTo.disabled=true;
    }
    return;
}

function checkboxSpisan_onClick()
{
    if (idcheckboxSpisan.checked==true)
    {
        idcheckboxSpisan.value="true";
        iddateSpisanDateFrom.disabled=false;
        iddateSpisanDateTo.disabled=false;
    }    
    else
    {
        idcheckboxSpisan.value="false";
        iddateSpisanDateFrom.disabled=true;
        iddateSpisanDateTo.disabled=true;
    }
    return;
}

function checkboxSale_onClick()
{
    if (idcheckboxSale.checked==true)
    {
        idcheckboxSale.value="true";
        iddateSaleDateFrom.disabled=false;
        iddateSaleDateTo.disabled=false;
    }    
    else
    {
        idcheckboxSale.value="false";
        iddateSaleDateFrom.disabled=true;
        iddateSaleDateTo.disabled=true;
    }
    return;
}
// галочка "Изменение пароля"
function checkboxChangePass_onClick()
{
    if (checkboxChangePass.checked==true)
    {
        checkboxChangePass.value="true";
        editPass.disabled=false;
    }    
    else
    {
        checkboxChangePass.value="false";
        editPass.disabled=true;
    }
    return;
}

