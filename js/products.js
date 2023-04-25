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

function addEvents(){
	convertDropdownsToText();
	saveProductDetails();
	deleteProduct();
	addProduct();
}

function saveProductDetails(){
	$(".productTable input, .productTable select").each(function(){
		var s = $(this);
		s.on('change', function(e){
			cont = s.parent().parent(),
			id = cont.find('.prd_id').text(),
			url = "/products/update_product",
			d = {'prd_id':id, 'field':s.attr('class'), 'value':s.val()};
			$.post(url, d, function(data){
				if(data){
					if(s.prop("tagName") === "SELECT"){
						s.parent().find('p').text(s.find("option[value='"+s.val()+"']").text());	
					}
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

function convertDropdownsToText(){
	$('.productTable td').each(function(){
		var td = $(this);
		selectToText(td.find('select'));
		td.find('p').on('click', function(){
			$(".productTable td select").hide();
			$(".productTable td p").show();
			$(this).hide();
			td.find('select').show();	
		});
		td.find('select').on('blur', function(){
			$(this).hide();
			$(this).siblings('p').show();
		}).on('keypress', function(e){
			if(e.keyCode == 13){//Enter Key
				e.preventDefault();
				selectNextCell($(this));
			}
		});
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

function deleteProduct(){
	$(".deleteProduct").on('click', function(){
		var url = "/products/delete_product",
			prd_id = $(this).parent().parent().find(".prd_id").text(),
			cat = $(this).parentsUntil('.productList').parent().find('.addNewProduct').attr('data-cat-id'),
			confirmMessage = "Are you sure you want to delete this product? This action cannot be undone.";
		if(confirm(confirmMessage)){
			$.post(url, {prd_id:prd_id, cat_id:cat}, function(data){
				if(!data){
					alert('System error: could not delete product');	
				}else{
					$(".addNewProduct[data-cat-id='"+cat+"']").siblings('.productTableWrapper').html(data);
					addEvents();
				}
			});
		}
	});
}

function addProduct(){
	$(".addNewProduct").on('click', function(){
		var url = "/products/add_product",
		btn = $(this),
		cat = btn.attr('data-cat-id');
			
		$.post(url, {cat_id:cat}, function(data){
			if(!data){
				alert('System Error: could not add product');	
			}else{
				btn.siblings('.productTableWrapper').html(data).find('tr:last .prd_name').focus();
				addEvents();
			}
		});
	});
}

