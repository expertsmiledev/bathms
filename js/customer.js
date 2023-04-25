/**
* Javascript File
* @file /js/customer.js
* included in /customer
*/

$(function(){
//initialise accordion widget
	$("#accordion").accordion({
		active:1,
		header:"h2",
		heightStyle:"content",
		collapsible:true,
		autoHeight:true
	});
//Open Customer Details accordion section & focus on Cust Code
	if(location.pathname == "/customer/add_new"){
		$("#accordion").accordion({active:0});
		$(".customerForm input[name='cus_code']").focus();
	};
//Add warning to Delete Customer button
	$("#deleteCustomer").on('click', function(e){
		var href = $(this).parent().attr('href');
		e.preventDefault();
		if(confirm('Are you sure you want to delete this customer?')){
			location.href = href;
		}
	});
//Save customer details
	var previous,
		cus_id = $(".customerForm").attr("id");
	$(".custDetails input:text").on('focus', function(){
		previous = $(this).val();
	}).on('change', function(){
		var fieldName = $(this).attr('name'),
			fieldValue = $(this).val();
		saveCustomerDetails(cus_id, fieldName, fieldValue, $(this), previous);
	});
	$(".custDetails textarea").on('focus', function(){
		previous = $(this).text();
	}).on('change', function(){
		var fieldName = $(this).attr('name'),
			fieldValue = $(this).text();
		saveCustomerDetails(cus_id, fieldName, fieldValue, $(this), previous);
	});
	$(".custDetails input:checkbox").on('click', function(){
		var fieldName = $(this).attr('name'),
			fieldValue = $(this).prop("checked");
			previous = !fieldValue;
		saveCustomerDetails(cus_id, fieldName, fieldValue?1:0, $(this), previous);
	//show/hide Retest Date dropdown on checkbox change
		if($(this).attr('name') == 'cus_hmsretest'){
			var r = $("select[name='cus_retestdate']");
			fieldValue?r.show():r.hide();
		}
	});
//Hide/Show Retest Date dropdown on page open
	if($("input[name='cus_hmsretest']").prop('checked')){
		$("select[name='cus_retestdate']").show();
	}else{
		$("select[name='cus_retestdate']").hide();
	}
	$("input[name='cus_hmsretest']").on('click', function(){
		$(".custDetails select").one('click', function(){
			previous = $(this).val();
		}).on("change", function(){
			var fieldName = $(this).attr('name'),
				fieldValue = $(this).val();
			saveCustomerDetails(cus_id, fieldName, fieldValue, $(this), previous);
		});
		if($(this).prop('checked')){
			$("select[name='cus_retestdate']").show();
		}else{
			$("select[name='cus_retestdate']").hide();
		}
	});
	addNewAsset();
	addNewLocation();
	addCustomerEvents();
	//Handle Errors
	if(loadPageVar('err')){
		switch (loadPageVar('err')){
			case 'delete_fail':
				alert("Server Error: Could not delete customer.");
				break;
			default:
		}
	}
});

function addCustomerEvents(){
	convertDropdownsToText();
	addCopyAssetEvent();
	addDeleteAssetEvent();
	addDeleteLocationEvent();
	addNewUserEvent();
	saveUserDetails();
	addDeleteUserEvent();
	addInspectionEvent();
	assetFilter();
	copyEndFittings();
}

function saveCustomerDetails(cus_id, name, value, el, previous){
	var url = '/customer/update_customer_details',
		dat = {cus_id:cus_id, name:name, value:value};
	$.post(url,dat, function(data){
		if(data == "_failed_"){
			alert("Data save failed");
			if(el.is(":checkbox")){
				el.prop("checked", previous);
			}else if(el.is("textarea")){
				el.text(previous);
			}else{
				el.val(previous);
			}
		}else{
			if(el.is(":checkbox")){
				el.prop("checked", data == 0?false:true);
			}else if(el.is("textarea")){
				el.text(data);
			}else{
				el.val(data);
			}
		}
	});
	
	
}

function loadPageVar (sVar) {
  return decodeURI(window.location.search.replace(new RegExp("^(?:.*[&\\?]" + encodeURI(sVar).replace(/[\.\+\*]/g, "\\$&") + "(?:\\=([^&]*))?)?.*$", "i"), "$1"));
}
function assetFilter(){
	$(".assetFilter").keyup(function(){
		var rows =  $(".customerTable tbody tr"),
		allrows = rows,
		data = $(this).val().split(" ");
		//hide all the rows
		rows.hide();
		//Filter the jquery object to get results.
		$.each(data, function(i, v){
			rows = rows.has("input[value*='"+v+"'][class='ast_id'],input[value*='"+v+"'][class='ast_serial']");
		});
		if(data[0].length < 1){
			rows = allrows;
		}
		//show the rows that match.
		rows.show();
	});
}


function selectToText(e){
	var v = "<p>"+e.find("option:selected").text()+"</p>";
	e.parent().find('p').remove();
	e.hide();
	e.parent().append(v);
}

function selectNextCell(el){
	el.blur();
	var next = el.parent().next('td').children();
	var tags = new Array('INPUT','TEXTAREA');
	for(var i = 0; i < next.length; i++){
		var tag = $(next[i]).prop('tagName');
		if(tags.indexOf(tag) > -1){
			next[i].focus();
			break;
		}
	}
}
function convertDropdownsToText(){
	$('.editTable td').each(function(){
		var td = $(this),
			thisTable = td.parent().parent().parent().parent().attr('class');
		selectToText(td.find('select'));
		td.find('p').on('click', function(){
			$(".customerTable td select").hide();
			$(".customerTable td p").show();
			$(this).hide();
			td.find('select').show();	
		});
		td.find('select').on('blur', function(){
			$(this).hide();
			$(this).siblings('p').show();
		})
		td.find('input').on('focus', function(){
			$(".customerTable td select").hide();
			$(".customerTable td p").show();
		});
		//detect data change
		td.find('input, select, textarea').on('change', function(e){
			var s = $(this),
				cont = s.parent().parent(),
				t = s.parent().find('p'),
				cus_id = $("."+thisTable+" table").attr('id').substr(5),
				id, url, d;
			switch(thisTable){
				case 'assetTableWrapper':
					id = cont.find('.ast_id').attr('value'),
					url = "/asset/update_asset",
					d = {'ast_id':id, 'field':s.attr('class'), 'value':s.val()};
				break;
				case 'locationTableWrapper':
					idfield = cont.find('.deleteLocation').attr("id");
					id = idfield.substr(idfield.indexOf("_")+1);
					url = "/customer/update_location",
					d = {'loc_id':id, 'field':s.attr('class'), 'value':s.val()};
				break;
				case 'userTableWrapper':	
					id = cont.find('.usr_id').val(),
					url = "/customer/update_user",
					d = {'usr_id':id, 'field':s.attr('class'), 'value':s.val()};
				break;
			}
			//save changed data 
			$.post(url, d, function(data){
				if(data === 'duplicate'){
					alert('Asset ID already exists, please choose another');
					s.val(d.ast_id).focus();	
				}else{
					if(data){
						if(s.prop('tagName') == "SELECT"){
							t.text(s.find('option').filter(":selected").text());
						}else{
							t.text(s.val());
							//if changed item is the asset id, change related asset ids
							if(d.field === 'ast_id'){
								$("#inspect_"+d.ast_id).attr('id','inspect_'+d.value);	
								$("#copy_"+d.ast_id).attr('id','copy_'+d.value);
								$("#delete_"+d.ast_id).attr('id','delete_'+d.value);
							}
						}
						if(thisTable == 'locationTableWrapper'){
							refreshLocationDropdowns(cus_id);	
						}
					}
					$(".customerTable td select").hide();
					$(".customerTable td p").show();
					selectNextCell(s);
				}
			});
		}).on('keypress', function(e){
			if(e.keyCode == 13){//Enter Key
				e.preventDefault();
				selectNextCell($(this));
			}
		});
	});
}


function refreshLocationDropdowns(cus_id){
	var url = "/customer/ajax_create_cust_dropdown";
		table = 'loc_location',
		value = 'loc_id',
		text = 'loc_name';
	$.post(url, {cus_id:cus_id,table:table,value:value,text:text}, function(data){
		$(".assetTableWrapper .loc_id").each(function(){
			$(this).html(data);
		});
	});
}

function addInspectionEvent(){
	$(".inspectAsset").on('click', function(){
		var ast_id = $(this).attr("id").substr($(this).attr("id").indexOf("_")+1);
		if(inspectionValidation($(this)) === true){
			$("#inspectionDialog").dialog({
				buttons:[{
					text:"New Asset",
					click:function(){
						location.href = "/inspection/new_asset/"+ast_id;
					}
				},{
					text:"Service",
					click:function(){
						location.href = "/inspection/service/"+ast_id;
					}
				}]
			});
		}
	});
}

function inspectionValidation(el){
	var row = el.parent().parent(),
	colNames = {
		ast_id:"Asset ID",
		ast_serial:"Customer Serial No",
		loc_id:"Location",
		prd_id:"Product",
		ast_length:"Length",
		cpl_id_a:"Coupling A",
		cpa_id_a:"Addon A",
		atm_id_a:"Attach Method A",
		cpm_id_a:"Material A",
		cpl_id_b:"Coupling B",
		cpa_id_b:"Addon B",
		atm_id_b:"Attach Method B",
		cpm_id_b:"Material B",
		nmb_id:"Nominal Bore",
		ast_manufacturedate:"Manufacture Date"
	},
	valid = true,
	msg = "Please enter the following values:\n";
	row.find('select, input').each(function(){
		if($(this).val() === "-"){
			msg += colNames[$(this).attr('class')] + "\n";
			valid = false;
		}
	});
	if(!valid) alert(msg);
	return valid;
}

function addNewAsset(){
//add new asset
	$("#addNewAsset").on('click', function(){
		var url = "/customer/add_asset",
			cus_id = $(".assetTableWrapper table").attr('id').substr(5);
		$.post(url, {cus_id:cus_id}, function(data){
			if(!data){
				alert('System Error: could not add asset');	
			}else{
				$(".assetTableWrapper").html(data);
				addCustomerEvents();
			}
		});
	});
}

/**
*	Assigns click event to copy button. 
*	Adds a copy of the new asset to the database and refreshes the asset table
*/
function addCopyAssetEvent(){
	$(".copyAsset").on('click', function(){
		var url = "/customer/copy_asset",
			ast_id = $(this).parent().parent().find(".ast_id").val(),
			cus_id = $(".assetTableWrapper table").attr('id').substr(5);

		$.ajax({
			url: url,
			type: 'post',
			data: {ast_id:ast_id, cus_id:cus_id},
		})
		.done(function(data, status, error) {
			$(".assetTableWrapper").html(data);
			addCustomerEvents();
		})
		.fail(function(xhr, status, error) {
			console.log(xhr, status, error);
			alert('System Error: could not add asset');	
		});
		// 		$.post(url, {ast_id:ast_id, cus_id:cus_id}, function(data){
		// 	if(!data){
		// 		alert('System Error: could not add asset');	
		// 	}else{
		// 		$(".assetTableWrapper").html(data);
		// 		addCustomerEvents();
		// 	}
		// });
	});
}

function addDeleteAssetEvent(){
	$(".deleteAsset").on('click', function(){
		var url = "/customer/delete_asset",
			ast_id = $(this).parent().parent().find(".ast_id").val(),
			cus_id = $(".assetTableWrapper table").attr('id').substr(5),
			confirmMessage = "Are you sure you want to delete this asset?";
		if(confirm(confirmMessage)){
			$.post(url, {ast_id:ast_id, cus_id:cus_id}, function(data){
				if(!data){
					alert('System error: could not delete row');	
				}else{
					$(".assetTableWrapper").html(data);
					addCustomerEvents();
				}
			});
		}
	});
}
function addNewLocation(){
//add new location
	$("#addNewLocation").on('click', function(){
		var url = "/customer/add_location",
			cus_id = $(".locationTableWrapper table").attr('id').substr(5);
			
		$.post(url, {cus_id:cus_id}, function(data){
			if(!data){
				alert('System Error: could not add location');	
			}else{
				$(".custLocations div").html(data);
				$(".loc_name:first").focus().on('blur', function(){
					var loc = $(this).parent().parent().find('img');
					if($(this).val().length < 1){
						if(confirm('You must add a name')){
							$(this).focus();
						}else{
							deleteLocation(loc);	
						}
					}
				});
				addCustomerEvents();
			}
		});
	});
}

function addDeleteLocationEvent(){
	$(".deleteLocation").on('click', function(){
		e = $(this);
		confirmMessage = "Are you sure you want to delete this location?";
		if(confirm(confirmMessage)){
			deleteLocation(e);
		}
	});
}

function deleteLocation(e){
	var url = "/customer/delete_location",
		thisID = e.attr("id"),
		cus_id = $(".locationTableWrapper table").attr('id').substr(5),
		loc_id = thisID.substr(thisID.indexOf("_")+1);
		
	$.post(url, {loc_id:loc_id, cus_id:cus_id}, function(data){
		if(data == "0"){
			alert('System error: could not delete location');	
		}else{
			if(data === 'exists'){
				alert("Some assets are listed as being in this location. Cannot delete.");
			}else{
				$(".custLocations div").html(data);
				addCustomerEvents();
				refreshLocationDropdowns(cus_id);
			}
		}
	});
}

function addNewUserEvent(){
//add new user
	$("#addNewUser").on('click', function(){
		var url = "/customer/add_user",
			cus_id = $(".userTableWrapper table").attr('id').substr(5);
			
		$.post(url, {cus_id:cus_id}, function(data){
			if(!data){
				alert('System Error: could not add user');	
			}else{
				$(".custUsers div").html(data);
				addCustomerEvents();
				$(".usr_email").filter(":last").focus();
			}
		});
	});
}

function saveUserDetails(){
	$(".userTableWrapper input").on('change', function(){
		var url = "/customer/update_user",
		input = $(this),
		email = input.parent().parent().find('.usr_email').attr('data-record-id'),
		cus_id = $(".userTableWrapper table").attr('id').substr(5),
		field = input.attr('class'),
		value = input.val();
console.log(value);		
		if(email === '' && !input.hasClass('usr_email')){
			email = input.parent().parent().find('.usr_email').val();
		}

		$.post(url,{email:email, field:field, value:value}, function(data){
			if(data === 'exists'){
				alert("A user with that email address already exists. Please try again.");
				input.val(email).focus().select();
			}
			if(data == 0){
				alert("Server error. Could not save data.");
			}
		});
		
	});
}

function addDeleteUserEvent(){
	$(".deleteUser").on('click', function(){
		var url = "/customer/delete_user",
			thisEmail = $(this).parent().parent().find(".usr_email").val();
			cus_id = $(".userTableWrapper table").attr('id').substr(5),
			confirmMessage = "Are you sure you want to delete this user?";
		if(confirm(confirmMessage)){
			$.post(url, {usr_email:thisEmail, cus_id:cus_id}, function(data){
				if(!data){
					alert('System error: could not delete row');	
				}else{
					$(".custUsers div").html(data);
					addCustomerEvents();
				}
			});
		}
	});
}

/**
*	copies end fitting details to other end when first selected
*/
function copyEndFittings(){
	$(".cpl_id_a, .cpa_id_a, .atm_id_a, .cpm_id_a").on('change', function(){
		var comp = "." + $(this).attr('class').replace(/_a/, "_b"),
		compEl = $(this).parent().parent().find(comp),
		value = $(this).val();
		if(compEl.val() === "-"){
			compEl.val(value);
			compEl.trigger('change');
		}
	});
}


