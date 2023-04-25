/**
* Javascript File
* @file /js/search.js
* included in /siteadmin
*/

$(function(){
	$(".addCustomer").on('click', function(){
		location.href = "/customer/add_new";
	});
//Customer Search ajax
	$("#custSearch").focus().on('keyup', function(){
		var txt = $(this).val();
		ajaxSearch(txt, $(".searchResults"));
	}).on('keypress', function(e){
		if(e.keyCode == 13){//Enter Key
			e.preventDefault();
			//navigate to first customer in list
			var h = $(".searchResults li a:first").attr('href');
			if(h !== undefined){
				$(".searchResults li a:first").css({"color":"#00F", "text-decoration":"underline"});
				location.href = h;
			}
		}
	});
	
});

function ajaxSearch(txt, container){
		var url = "/siteadmin/customer_search";
		//only search if string length is more than 2
		if(txt.length > 1){
			$.post(url, {srch:txt}, function(data){
				container.html(data);
			});
		}else{
			container.html('');
		}
	
}
