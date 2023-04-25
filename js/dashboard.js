/**
* Javascript File
* @file /js/dashboard.js
* included in /customer for role 3 customers
*/

$(function(){
//initialise accordion widget
	$("#accordion").accordion({
		active:1,
		header:"h2",
		heightStyle:"content",
		collapsible:true
	});
	addCustomerEvents();
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
		saveCustomerDetails(cus_id, fieldName, fieldValue?1:0, $(this), previous)
	});
//add new location
	$("#addNewLocation").on('click', function(){
		var url = "/customer/add_location",
			cus_id = $(".locationTableWrapper table").attr('id').substr(5);
			
		$.post(url, {cus_id:cus_id}, function(data){
			if(!data){
				alert('System Error: could not add location');	
			}else{
				$(".custLocations div").html(data);
				addCustomerEvents();
			}
		});
	});
});

function addCustomerEvents(){
	convertDropdownsToText();
	addDeleteLocationEvent();
	assetFilter();
}

function convertDropdownsToText(){
	$('.customerTable td').each(function(){
		var td = $(this);
		var thisTable = td.parent().parent().parent().parent().attr('class');
		selectToText(td.find('select'));
	});
}
function selectToText(e){
	var v = "<p>"+e.find("option:selected").text()+"</p>";
	e.parent().find('p').remove();
	e.hide();
	e.parent().append(v);
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
function addDeleteLocationEvent(){
	$(".deleteLocation").on('click', function(){
		var url = "/customer/delete_location",
			thisID = $(this).attr("id"),
			cus_id = $(".locationTableWrapper table").attr('id').substr(5),
			confirmMessage = "Are you sure you want to delete this location?";
			var loc_id = thisID.substr(thisID.indexOf("_")+1);
		if(confirm(confirmMessage)){
			if(checkAssetLocations(loc_id)){
				$.post(url, {loc_id:loc_id, cus_id:cus_id}, function(data){
					if(!data){
						alert('System error: could not delete row');	
					}else{
						$(".custLocations div").html(data);
						addCustomerEvents();
						refreshLocationDropdowns();
					}
				});
			}else{
				alert("Some assets are listed as being in this location. Cannot delete.");
			}
		}
	});
}

function checkAssetLocations(loc_id){
	url = "/customer/check_asset_locations";
	d = {loc_id:loc_id};
	$.post(url, d, function(data){
		return data;	
	});
}


