// moola-control.js
//
// Erick Veil
//
// 2013-04-09
//
// javascript and jquery scripts to control various input modules in moola
//

// Date Range Sub-widget handling. Allows a selectable calendar
// requires jquery-ui
$(function()
{
    // all of this work for something that will be native in html 5 -_-
    var def_min=$("#min").val();
    var def_max=$("#max").val();

    $("#min").datepicker({
        defaultDate: def_min,    
        dateFormat: "yy-mm-dd",
        changeYear: true,
        onClose: function(selectedDate){
            $("#max").datepicker("option","minDate",selectedDate);
        }
    });
    $("#max").datepicker({
        defaultDate: def_max,    
        dateFormat: "yy-mm-dd",
        changeYear: true,
        onClose: function(selectedDate){
            $("#min").datepicker("option","maxDate",selectedDate);
        }
    });
});

// pass the name of this function as a string to the php function,
// "drawDateRange()" This establishes that the date range selector is a
// sub-widget of the Ledger, and by selecting a range, and clicking the
// submission button, the ledger will be re-drawn with the new date range.
function redrawLedger()
{
    var min_date=$("#min").val();
    var max_date=$("#max").val();


    alert("starting request");
    var addy="widget-redraw.php?func=redrawLedger&min="+min_date+"&max="+max_date;
    alert(addy);

    $.ajax({
        type:"GET",
        url:addy,
        cache:false
    }).done(function(html_str){
        alert(html_str);
        $(".ledger_widget").html(html_str);
    });
}

