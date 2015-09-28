console.log('settings page');
//http://fabian-valle.com/oblivious/api/remove/categories/dimelo
//http://fabian-valle.com/oblivious/api/add/categories/dimelo
//execute callback when the page is ready:
$( document ).ready(function() {
// Handler for .ready() called.
	
	var oblivious_viewdata = {
		'categories':[],
		'removeCat':null
	};
	oblivious_viewdata.removeCat = function(){
		var cat = $(this).text();
		$.getJSON('/oblivious/api/remove/categories/'+cat+'/',function(data){
			$.getJSON('/oblivious/api/list/categories/',function(d){
				oblivious_viewdata.categories = d.Categories;
			});
		});
	};
	rivets.bind($('#oblivious_categorylist'), {
		viewdata: oblivious_viewdata
	});
	$.getJSON('/oblivious/api/list/categories/',function(data){
		oblivious_viewdata.categories = data.Categories;
	});
	
	$("#submit-category-button").on("click",function(){
		var cat = $("#new-category").val();
		console.log('cat',cat);
		$("#new-category").val('');
		$.getJSON('/oblivious/api/add/categories/'+cat+'/',function(data){
			$.getJSON('/oblivious/api/list/categories/',function(d){
				oblivious_viewdata.categories = d.Categories;
			});
		});
	});
	$("#clear-blackbook-button").on("click",function(){
		
			oblivious.blackbookClear();
		
	});
	$("#remove-category-button").on("click",function(){
		var cat = $("#current-category").val();
		if(cat == '(choose a category)'){
			console.log('no category selected for deletion');
			return false;
		}
		$("#current-category").val('');
		$.getJSON('/oblivious/api/remove/categories/'+cat+'/',function(data){
			$.getJSON('/oblivious/api/list/categories/',function(d){
				oblivious_viewdata.categories = d.Categories;
			});
		});
	});
});