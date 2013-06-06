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

    $("[field=ledger_serial]").change(function(){
        editField("SERIAL",this);
    });

    $("[field=ledger_amount]").change(function(){
        var new_amt=$(this).val();
        ledgerPrimaryFieldChange(new_amt,this,"AMOUNT");
    });

    // draw import dialog box
    $("#import").dialog({
        autoOpen:false,
        title:"Load CSV File",
        modal:true    
    });

    $("[button=import]").click(function(){
        var import_dialog=$("#import");
        fillImportDialog(import_dialog);
        $(import_dialog).dialog("open");
    });

    // handle import dialog onclick
    $("#load_csv").click(function(){

    });


});

/*
* 0.1.1
* date picker attributes need to be added on each reload of a widget
*/
function addDatepickers()
{
    var min_date_field=$("#min");
    var max_date_field=$("#max");

    // all of this work for something that will be native in html 5 -_-
    var def_min=$(min_date_field).val();
    var def_max=$(max_date_field).val();

    $(min_date_field).datepicker({
        defaultDate: def_min,    
        dateFormat: "yy-mm-dd",
        changeYear: true,
        onClose: function(selectedDate){
            $("#max").datepicker("option","minDate",selectedDate);
        }
    });

    $(max_date_field).datepicker({
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
            ledgerPrimaryFieldChange(selectedDate,this,"DATE");
        }
    });
}

// 0.1.1.1
// The ledger date is a "primary field". This means that when it is edited,
// instead of altering the entry, we preserve it, set it to deleted by edit,
// then insert a new entry into the table. This will allow restoring edited
// items to their downloaded state and also help prevent downloading
// duplicate entries, even after editing.
function ledgerPrimaryFieldChange(new_val,dom_obj,sql_field)
{
    var id=$(dom_obj).attr("id");
    var old_val=$(dom_obj).attr("value");

//alert(id+" = "+getSource(id));

    if(getSource(id)!="download")
    {
        // This is not a download entry, just edit the field
//alert(sql_field+"\n"+dom_obj);
        editField(sql_field, dom_obj);
        return;
    }

    var entry={};
    entry.serial=$("[field=ledger_serial][id="+id+"]").val();
    entry.amt=$("[field=ledger_amount][id="+id+"]").val();
    entry.comment=$("[field=ledger_com][id="+id+"]").val();
    entry.ptr=id;
    entry.date=$("[field=ledger_date][id="+id+"]").val();
    entry.source="edit";

    var ledger_hook=$(dom_obj).parents(".hook");
    var hook_id=$(ledger_hook).attr("id");

    if (old_val== new_val)
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
        var focus_selector="[id="+return_val+"][field=ledger_date]";
        deleteEntryFromLedger(ptr,"edit",hook_id,focus_selector);
    });
}

// 0.1.1.1.2
// adds a value to the DEL column for the entry at PTR, indicating that it is
// "deleted" by virtue of its DEL value is not null
// pass the ptr for the entry and the type of deletion
// focus_selector is the selectorof the element to restore focus to afterwords
function deleteEntryFromLedger(ptr,edit_type,hook_id,focus_selector)
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
        redrawLedger(hook_id,focus_selector);
    });
}

// 0.1.1.1.3
// gets the source type of the downloads table entry by its ptr.
// This is usualy used to determine if an entry's major field has been edited
// before.
// Te request is synchronous, so that you have to wait for the result before you
// know what to do with it.
function getSource(ptr)
{
    var addy="queries.php?func=getSource&ptr="+ptr;

    // return result asynchronously
    return $.ajax({
        type:"GET",
        url:addy,
        cache:false,
        async:false
    }).responseText;
}

// 0.1.2
function fillImportDialog(hook_obj)
{
    var addy="widget-redraw.php?func=fillImportDialog";

    $.ajax({
        type:"GET",
        url:addy,
        cache:false
    }).done(function(return_text){
        $(hook_obj).html(return_text);
    });
}

// 0.1.2.1
function warn(text)
{
    alert(text);
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

    $.ajax({
        type:"GET",
        url:addy,
        cache:false
    }).done(function(return_text){
    });
}

// 0.2.0
// pass the name of this function as a string to the php function,
// "drawDateRange()" This establishes that the date range selector is a
// sub-widget of the Ledger, and by selecting a range, and clicking the
// submission button, the ledger will be re-drawn with the new date range.
// focus selector is the item we want to move focus to when we are done.
function redrawLedger(hook_id,focus_selector)
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

        // what if the entry is out of the date range?
        // what if the selector does not exist?
        $(focus_selector).focus();
        $(focus_selector).blur();

        addDatepickers();
        //alert(focus_selector);
    });
}

// 0.3.0

