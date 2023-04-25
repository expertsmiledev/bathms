// JavaScript Document

$(function(){
//initialise accordion widget
	$("#accordion").accordion({
		active:false,
		header:"h2",
		heightStyle:"content",
		collapsible:true
	});
	
	addEvents();

});

function initialiseSortable(){
	$(".optionsAttributes ul, .optionsQuestions ul").sortable({ 
		appendTo: document.body,
        tolerance: 'pointer',
        cursor: 'pointer',
        connectWith: 'ul.sortable',
        update: function(event, ui) {
            if($(this).hasClass('sortable-delete')) {
				$(this).find('li').hide();
            }          
        }            
	});
}

function saveProductDetails(){
	$(".optionsProductTable td").each(function(){
		var td = $(this);
		var thisTable = td.parent().parent().parent().parent().attr('class');
		td.find('input').on('change', function(e){
			var s = $(this),
				cont = s.parent().parent(),
				t = s.parent().find('p'),
				id, url, d;
			id = cont.find('.prd_id').text(),
			url = "/options/update_product",
			d = {'prd_id':id, 'field':s.attr('class'), 'value':s.val()};
			$.post(url, d, function(data){
				if(data){
					t.text(s.val());
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
function deleteProductEvent(){
	$(".deleteProduct").on('click', function(){
		var url = "/options/delete_product",
			prd_id = $(this).parent().parent().find(".prd_id").text(),
			confirmMessage = "Are you sure you want to delete this product? This action cannot be undone.";
		if(confirm(confirmMessage)){
			$.post(url, {prd_id:prd_id}, function(data){
				if(!data){
					alert('System error: could not delete product');	
				}else{
					$(".productList>div").html(data);
					addEvents();
				}
			});
		}
	});
}

function addProductEvent(){
	$("#addNewProduct").on('click', function(){
		var url = "/options/add_product";
			
		$.post(url, {}, function(data){
			if(!data){
				alert('System Error: could not add product');	
			}else{
				$(".productList>div").html(data).find('tr:last .prd_name').focus();
				addEvents();
			}
		});
	});
}

function addAttribute(){
	$(".addNewAttribute").off('keypress').on('keypress', function(e){
		if(e.keyCode == 13){//Enter Key
			$(this).siblings('.addNewButton').trigger('click');
		}
	});
	$(".addNewButton").off('click').on('click', function(){
		var cont = $(this).parent();
		var input = cont.find('.addNewAttribute');
		if(input.val() != ""){
			cont.find('.saveThese').append("<li	class='ui-state-default'>"+input.val()+"</li>");
			input.val("");
		}
	});
}
function saveAttributes(){
	$(".saveAttributes").off('click').on('click', function(){
		var spinner = $(".spinner");
		spinner.show();
		var cont = $(this).parent(),
		saveData = cont.find('.saveThese li'),
		delData = cont.find('.sortable-delete li'),
		url = "/options/save_attributes",
		i,j,s = new Object(),
		d = new Object();
		for(i=0;i<saveData.length;i++){
			var saveId = $(saveData[i]).attr('id'),
			sid = "NULL";
			if(saveId !== undefined){
				sid = saveId.substr(4);
			}			
			s[i] = {
				id:sid,
				name:$(saveData[i]).text(),
				"sort":i
			}
		}
		save = JSON.stringify(s);
		for(j=0;j<delData.length;j++){
			var delId = $(delData[j]).attr('id'),
			did = "NULL";
			if(delId === undefined) break;	
			did = delId.substr(4);
			d[j] = {
				id:did,
				name:$(delData[j]).text(),
				"sort":j
			}
		}
		del = JSON.stringify(d);
		$.post(url, {save:save, del:del, table:cont.attr('id')}, function(data){
			spinner.hide();
			if(data == '0'){
				alert("Server Error. Could not save data.");
			}else{
				$(".assetAttributes").html(data);
				addEvents();
			}
		});
	});
}
function addQuestion(){
	$(".addNewQuestion").off('keypress').on('keypress', function(e){
		if(e.keyCode == 13){//Enter Key
			$(this).siblings('.addNewQuestionButton').trigger('click');
		}
	});
	$(".addNewQuestionButton").off('click').on('click', function(){
		var cont = $(this).parent();
		var input = cont.find('.addNewQuestion');
		if(input.val() != ""){
			cont.find('.saveThese').append("<li	class='ui-state-default'>"+input.val()+"</li>");
			input.val("");
		}
	});
}
function saveQuestions(){
	$(".saveQuestions").off('click').on('click', function(){
		var spinner = $(".spinner");
		spinner.show();
		var cont = $(this).parent(),
		saveData = cont.find('.saveThese li'),
		delData = cont.find('.sortable-delete li'),
		url = "/options/save_questions",
		i,j,s = new Object(),
		d = new Object();
		for(i=0;i<saveData.length;i++){
			var saveId = $(saveData[i]).attr('id'),
			sid = "NULL";
			if(saveId !== undefined){
				sid = saveId.substr(4);
			}			
			s[i] = {
				id:sid,
				question:$(saveData[i]).text(),
				"sort":i
			}
		}
		save = JSON.stringify(s);
		for(j=0;j<delData.length;j++){
			var delId = $(delData[j]).attr('id'),
			did = "NULL";
			if(delId === undefined) break;	
			did = delId.substr(4);
			d[j] = {
				id:did,
				question:$(delData[j]).text(),
				"sort":j
			}
		}
		del = JSON.stringify(d);
		$.post(url, {save:save, del:del}, function(data){
			spinner.hide();
			if(data == '0'){
				alert("Server Error. Could not save data.");
			}else{
				$(".inspectionQuestions").html(data);
				addEvents();
			}
		});
	});
}

function addUserButton(){
	var data;
	$("#addAdminUser").off('click').on('click', function(){
		addUser(1);
	});
	$("#addAssemblyUser").off('click').on('click', function(){
		addUser(2);
	});
	$(document).on('addUserData', function(e, role, data){
		if(role == 1){
			$(".adminUsers .userTableWrapper").html(data);
			addEvents();
			$(".adminUsers .editTable tr:last .usr_email").focus();
		}
		if(role == 2){
			$(".assemblyUsers .userTableWrapper").html(data);
			addEvents();
			$(".assemblyUsers .editTable tr:last .usrEmail").focus();
		}
	});
}

function addUser(r){
	var url = "/options/add_user";
	$.post(url,{role:r}, function(data){
		$(document).trigger('addUserData', [r, data]);
	});
}

function saveUserDetails(){
	$(".userTableWrapper input").on('change', function(){
		var url = "/options/update_user",
		input = $(this),
		email = input.parent().parent().find('.usr_email').attr('data-record-id'),
		field = input.attr('class'),
		value = input.val();
		if(field === 'usr_email'){
			input.parent().parent().find('.usr_email').attr('data-record-id', value)	
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

function deleteUser(){
	$(".deleteUser").off('click').on('click', function(){
		var url = "/options/delete_user",
		email = $(this).parent().parent().find(".usr_email").val(),
		name = $(this).parent().parent().find("input[class='usr_firstname']").val() + " " + $(this).parent().parent().find("input[class='usr_lastname']").val(),
		role, section,
		rDiv = $(this).parentsUntil('.ui-accordion-content').parent();
		if(rDiv.hasClass('adminUsers')){role = 1; section = ".adminUsers";}
		if(rDiv.hasClass('assemblyUsers')){role = 2; section = ".assemblyUsers";}
		if(confirm("Are you sure you want to delete "+name+" as a user?")){
			$.post(url,{email:email, role:role}, function(data){
				$(section+" .userTableWrapper").html(data);
				addEvents();
			});
		}
		
	});
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
function addEvents(){
	saveProductDetails();
	deleteProductEvent();
	addProductEvent();
	addAttribute();
	saveAttributes();
	addQuestion();
	saveQuestions();
	addUserButton();
	saveUserDetails();	
	deleteUser();
	initialiseSortable();
}
