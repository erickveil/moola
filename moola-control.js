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
    $("[field=ledger_com]").change(function(){
        editField("COMMENTS",this);
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
    entry.serial=$("[field=ledger_serial][id="+id+"]").val();
    entry.amt=$("[field=ledger_amount][id="+id+"]").val();
    entry.comment=$("[field=ledger_com][id="+id+"]").val();
    entry.ptr=id;
    entry.date=selectedDate;
    entry.source="edit";

    var ledger_hook=$(dom_obj).parents(".hook");
    var hook_id=$(ledger_hook).attr("id");

    if (old_date == selectedDate)
        return;

    addNewEntryToLedger(entry,id,hook_id);
}

// 0.1.1.1.1
// used to insert a new entry into the downloads ledger table
// entry is an object. It should have members {ptr, date, serial, amt,
// comment}
function addNewEntryToLedger(entry,ptr,hook_id)
{
    var addy="queries.php?func=addEntry"+
        "&date="+entry.date+
        "&amt="+entry.amt+
        "&serial="+entry.serial+
        "&com="+entry.comment+
        "&src="+entry.source;
    //alert(addy);

    $.ajax({
        type:"GET",
        url:addy,
        cache:false
    }).done(function(return_val){
        deleteEntryFromLedger(ptr,"edit",hook_id);
    });
}

// 0.1.1.1.2
// adds a value to the DEL column for the entry at PTR, indicating that it is
// "deleted" by virtue of its DEL value is not null
// pass the ptr for the entry and the type of deletion
function deleteEntryFromLedger(ptr,edit_type,hook_id)
{
    var addy="queries.php?func=softDeleteEntry"+
        "&ptr="+ptr+
        "&type="+edit_type;
    //alert(addy);

    $.ajax({
        type:"GET",
        url:addy,
        cache:false
    }).done(function(return_val){
        redrawLedger(hook_id);
    });
}

// 1.1.2
// calls an update querry on the sql_field passed in the downloads table.
// the field_dom is the dom object ("this") of the changed field. It's id should
// match it's ptr entry in the downloads table.
function editField(sql_field, field_dom)
{
    var new_value=$(field_dom).val();
    var ptr=$(field_dom).attr("id");

    var addy="queries.php?func=editEntry&ptr="+ptr+"&field="+sql_field+"&data="+new_value;

alert(addy);
    $.ajax({
        type:"GET",
        url:addy,
        cache:false
    }).done(function(return_text){
        alert(return_text);
    });

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


