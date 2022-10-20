// ============================
// = FIRE EVENTS ON DOM READY =
// ============================
$(document).ready(function(){
	
	$('div.feedback').slideDown();
	
	$('a.delete').click(function(e){
		e.preventDefault();
		if(confirm("Bent u zeker dat u dit item wil verwijderen?")){
			window.location.href = $(this).attr('href');
		}
	});
	
	
	
	$('input.meta').each(function(){
		if($(this).attr('rel')!=undefined && ($(this).val()=='' || $(this).val()==$(this).attr('rel'))){
			$(this).val($(this).attr('rel'));
			$(this).addClass('relDescription');
		}
	});
	
	
	$('tr').each(function() {
		$(this).dblclick(function(e){
			if($(this).find('a.details').attr('href'))	window.location.href = $(this).find('a.details').attr('href');
		});
	})
// =====================================
// = SHOW DESCRIPTION IN REL IF NEEDED =
// =====================================
	$('input.meta').focus(function(){
		if($(this).val() == $(this).attr('rel')){
			$(this).val('');
			$(this).removeClass('relDescription');
		}
	}).focusout(function(){
		if($(this).val()==''){
			$(this).val($(this).attr('rel'));
			$(this).addClass('relDescription');
		}
	});

// ==============================
// = FORMAT NUMERIC TEXT INTPUT =
// ==============================
	$('input.numeric').focusout(function(){
		var crrValue = $(this).val().replace(",", ".");
		$(this).val(crrValue);
	});

	$('input.date').datepicker({dateFormat: 'yy-mm-dd', firstDay:1});



// ===========================================
// = REMOVE DEFAULT VALUES BEFORE SUBMITTING =
// ===========================================
	$('a#btnSubmit').click(function(e){
		
		e.preventDefault();
	
		//	Remove default values
		$('input').each(function(){
			if($(this).attr('rel')==$(this).val()){
				$(this).val(' ');
			}
		});
		
		//	Set postback value for full submit
		$('#myPlaceholder').attr('name', 'postback');
		
		//	Submit the form
		$('form#' + $(this).attr('rel')).submit();
		
	});


// =============================================
// = PREVENT SUBMITTING FORM ON PRESSING ENTER =
// =============================================
$('input').keypress(function(e){
    if ( e.which == 13 ) return false;
});


// ==============================
// = BUILD DYNAMIC SEARCH FIELD =
// ==============================
	$('a#btnAddField').click(function(e){
		e.preventDefault();
		
		if($('select#ddlAddFields').val()!=0){
			$('div#frmSearch').show();
			var sField = $('select#ddlAddFields').val();
			var sHTML = $('table#dummyTable tr#dummy_' + sField).html();
			
			var sCrrFields = $('input#arr_fields').val();
			var arrCrrFields = sCrrFields.split('|');
			
			if(arrCrrFields.indexOf(sField)!=-1){
				sField += "1";
			}
			
			sHTML = sHTML.replace('datepicker', 'date');
			sHTML = sHTML.replace(/__SET_FIELDNAME__/g, sField);		//	replace globally
			$('div#frmSearch table').append("<tr>" + sHTML + "</tr>");
			
			var sCrrFields = $('input#arr_fields').val();
			$('input#arr_fields').val(sCrrFields + sField + "|");

			//	See if it can be added mulitple times
			if($('table#dummyTable tr#dummy_' + sField).attr('rel')!='multiple'){
				$('select#ddlAddFields option:selected').remove();
			}
			
			
			$('input.date').datepicker({dateFormat: 'yy-mm-dd', firstDay:1});
		}
	});
	
	
	// ===============
	// = EDIT LOGINS =
	// ===============
	$('a.edit').click(function(e){
		e.preventDefault();
		
		//	Hide all other dummy rows
		$('tr').show();
		$('tr#editDummy').hide();
		$('tr.dummy').remove();
		
		var oRow = $(this).parent().parent();
		var iLoginID = oRow.attr('rel');
		var sEditRow = $('tr#editDummy').html();
		oRow.hide();
		oRow.after("<tr class='logins dummy' id='edit_" + iLoginID + "'>" + sEditRow + "</tr>");
		
		$('#update_login_id').val(iLoginID);
		$("tr#edit_" + iLoginID + " select.update_login_type").val($('#login_' + iLoginID + " td.type").attr('rel'));
		$("tr#edit_" + iLoginID + " input.update_login_host").val($('#login_' + iLoginID + " td.host").html());
		$("tr#edit_" + iLoginID + " input.update_login_user").val($('#login_' + iLoginID + " td.user").html());
		$("tr#edit_" + iLoginID + " input.update_login_pass").val($('#login_' + iLoginID + " td.pass").html());
		$("tr#edit_" + iLoginID + " input.update_login_remarks").val($('#login_' + iLoginID + " td.remarks").html());
		
		//	no submit on enter
		$('input').keypress(function(e){
		    if ( e.which == 13 ) return false;
		});
		
		$('a.cancel').click(function(e){
			e.preventDefault();
			$(this).parent().parent().remove();
			oRow.show();
		});
	})
	
});



$(window).load(function(){
	
	
	$('input.meta').each(function(){
		if($(this).attr('rel')!=undefined && $(this).val()==''){
			$(this).val($(this).attr('rel'));
			$(this).addClass('relDescription');
		}
	});
	
});