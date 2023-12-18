jQuery(function($) 
{
    UpdatePriceCombo();
    
    $(document).on("change", "#comboMaterials",  function() 
    {
       UpdatePriceCombo();
       
    }); 
    
});
//-----------------------------------------------------------------------------
function UpdatePriceCombo()
{
   var id=$(document).find("#comboMaterials").val();
   if (id<=0) { $("#ostatok").text("Остаток: -"); $("#prices").html("&nbsp;"); }
   else
   {    
        $.getJSON(g_host + "elorgsklad/newsale.php?get_mat_ost=" + id, function(data)  
        {
            if (typeof data.ostatok !== "undefined")  
            { 
                var ostatok=data.ostatok;
                 $("#ostatok").text("Остаток: " + ostatok);               
            }
            else
                $("#ostatok").text("---");          

            if (typeof data.prices !== "undefined")
            {
                $("#prices").html(`${data.prices}`);
            }
            else
            {   $("#prices").html("&nbsp;"); }
        });
    }    
}

