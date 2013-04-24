// moola-control.js
//
// Erick Veil
//
// 2013-04-09
//
// javascript and jquery scripts to control various input modules in moola
//

// 0.1.0
// after page loads
$(function()
{
    addDatepickers();
    
    $("[field=ledger_date]").change(function(){
        //var msg=$(this).attr("value");
        //alert(msg);
    });
    
});

// 0.1.1
// date picker attributes need to be added on each reload of a widget
function addDatepickers()
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

    $("[field=ledger_date]").datepicker({
        defaultDate: $(this).attr("value"),    
        dateFormat: "yy-mm-dd",
        changeYear: false,
        onClose: function(selectedDate){
            var id=$(this).attr("id");
            ledgerDateChange(selectedDate,this);
        }
    });
}

// 0.1.1.1
// The ledger date is a "primary field". This means that when it is edited,
// instead of altering the entry, we preserve it, set it to deleted by edit,
// then insert a new entry into the table. This will allow restoring edited
// items to their downloaded state and also help prevent downloading
// duplicate entries, even after editing.
function ledgerDateChange(selectedDate,dom_obj)
{
    var id=$(dom_obj).attr("id");
    var old_date=$(dom_obj).attr("value");

    var entry=new Object;
    var entry.serial=$("[field=ledger_serial][id="+id+"]").val();
    var entry.amt=$("[field=ledger_amount][id="+id+"]").val();
    var entry.comment=$("[field=ledger_com][id="+id+"]").val();
    var entry.ptr=id;
    var entry.date=selectedDate;

    if (old_date == entry[date])
        return;

    addNewEntryToLedger(entry);
    deleteEntryFromLedger(ptr,"edit");
}

// 0.1.1.1.1
// used to insert a new entry into the downloads ledger table
// entry is an object. It should have members {ptr, date, serial, amt,
// comment}
function addNewEntryToLedger(entry)
{

}

// 0.1.1.1.2
// adds a value to the DEL column for the entry at PTR, indicating that it is
// "deleted" by virtue of its DEL value is not null
// pass the ptr for the entry and the type of deletion
function deleteEntryFromLedger(ptr,edit_type)
{

}

// 0.2.0
// pass the name of this function as a string to the php function,
// "drawDateRange()" This establishes that the date range selector is a
// sub-widget of the Ledger, and by selecting a range, and clicking the
// submission button, the ledger will be re-drawn with the new date range.
function redrawLedger(hook_id)
{
    var min_date=$("#min").val();
    var max_date=$("#max").val();

    var addy="widget-redraw.php?func=redrawLedger&min="+min_date+"&max="+max_date+"&hook="+hook_id;

    $.ajax({
        type:"GET",
        url:addy,
        cache:false
    }).done(function(html_str){
        $("#"+hook_id).html(html_str);
        addDatepickers();
    });
}

