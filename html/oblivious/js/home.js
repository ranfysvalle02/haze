console.log('home page');
$.blockUI.defaults.message = "";
//execute callback when the page is ready:
$( document ).ready(function() {
  // Handler for .ready() called.
	var oblivious_data = {
			entries:[],
			categories:[],
			subnav:{
				'categories':true,	
				'content':[],
				'active':'',
				'travel':null,
			}
	};
	oblivious_data.subnav.travel = function(){
		var cat = $(this).val();
		$.blockUI({ onBlock:function(){
			$.getJSON('/oblivious/api/list/entries/'+cat+'/',function(data){
				oblivious_data.entries = data.Entries;
				console.log('data.Entries',data.Entries);
				oblivious_data.subnav.active = cat;
				$.unblockUI();
			});
		}
		} );
			
	};
	oblivious_viewentry_data = {
		'entries':[],
		'category':'',
		'contents':[],
		'_entryid':''
	};
	
	rivets.bind($('#oblivious_entrylist'), {
		oblivious_data: oblivious_data
	});
	rivets.bind($('#subnav-home'), {
		oblivious_data:oblivious_data
	});
	rivets.bind($('#oblivious_viewentry'),{
		oblivious_viewentry_data: oblivious_viewentry_data
	});
	
	$.getJSON('/oblivious/api/list/categories/',function(data){
			oblivious_data.categories = data.Categories;
			oblivious_data.subnav.content = data.Categories;
			oblivious_data.subnav.active = '(choose a category to begin...)';
	});
	$('body').on('click', '.oblivious-entry-button', function() {
	    // do something
		var entryID = $(this).attr('eid');
		console.log('entryID',entryID);
		$("#oblivious_entrylist").hide();
		$("#oblivious_viewentry").show();
		//oblivious_viewentry_data
		
		$.each(oblivious_data.entries,function(i,entry){
			if(entry.entryid == entryID){
				oblivious_viewentry_data.category = entry.category;
				oblivious_viewentry_data.entries = [entry];
				oblivious_viewentry_data._entryid = entry.entryid;
				
				$("#view-entry-meta").val( JSON.stringify(entry.meta,null,2) )
				$.blockUI({ onBlock:function(){
					oblivious.getEntry(entry.entryid,entry.category,function(){
						console.log('this @ getEntry',this);
						var data = this;
						if(data[0].meta.krypi == "1"){
							var pwd = prompt("Please enter password to decrypt");
							if(pwd){
								var eContents = oblivious._processGetEntry(data,entry.entryid,pwd);
								oblivious_viewentry_data.contents = eContents;
							}else{
								
								$("#return-to-entries").click();
							}
					    }else{
					    	var eContents = oblivious._processGetEntry(data,entry.entryid,'');
					    	oblivious_viewentry_data.contents = eContents;
					    	console.log(oblivious_viewentry_data.contents);
					    }
						$.unblockUI();
					});
				} });
					
				return false;
			}
		});
	});
$('body').on('click','#loadblackbook-button',function(){
	console.log('loading blackbook...');

	
	var Entries =[];
	var localMeta = oblivious.blackbookGet('meta');
	$.each(localMeta,function(i,obj){
		var tmpEntry = {
				'category':'',
				'entryid':'',
				'meta':''
		};
		tmpEntry.entryid = obj.key;
		tmpEntry.category = obj.value.category;
		tmpEntry.meta = obj.value;
		Entries.push(tmpEntry);
	});
	oblivious_data.entries = Entries;
	oblivious_data.subnav.active = 'Blackbook';
	
	
})
$('body').on('click','#return-to-entries',function(){
		
		$("#oblivious_entrylist").show();
		$("#oblivious_viewentry").hide();
	});
$('body').on('click','.invite-button',function(){
	//clear fields
	$("#invite-pwd").val('');
	var entryID = $(this).attr('eid');
	var cat = $(this).attr('ecat');
	oblivious_viewentry_data._entryid = entryID;
	oblivious_viewentry_data.category = cat;
	console.log('did this work?',cat);
});
$('body').on('click','.add-comment-button',function(){
	//clear fields
	$("#addcommentform")[0].reset();
});
$('body').on('click','#send-invite-button',function(){
	var entryid = oblivious_viewentry_data._entryid;
	var entrycategory = oblivious_viewentry_data.category;
	
	if($("#invite-pwd").val() != ''){
			oblivious._generateInvite(entryid,entrycategory);
			//clear fields
			$("#invite-pwd").val('');
	}
	
});
	var commentviewdata = {
			'newcomment':{
				'text':'',
				'img':'',
			}
	};
	oblivious._onchange('addimage-input',function(){
		var imgData = String(this);
		commentviewdata.newcomment.img = imgData;
	});
	
	$(document).on('click', '#send-comment-button', function () {
		console.log('commentviewdata',commentviewdata);
		console.log('oblivious_viewentry_data',oblivious_viewentry_data);
		commentviewdata.newcomment.text = $("#comment-text").val();
		if(commentviewdata.newcomment.text == "" && commentviewdata.newcomment.img == ""){
			console.log('no comment entered');
		}else{
			//oblivious.addEntryComment(entryID,category,comment,nickname)
			//oblivious_viewentry_data._entryid
			//oblivious_viewentry_data.category
			//commentviewdata.newcomment.text
			//commentviewdata.newcomment.img
			var d ={ comment:String(commentviewdata.newcomment.text),
		             imgdata:'',
		             userkey:'',
		             containsimage:0,
		             nickname:''
			     };
			if(commentviewdata.newcomment.img != ''){
				d.imgdata = commentviewdata.newcomment.img;
				d.containsimage = 1;
			}
			console.log('d',d);
			$.blockUI({ onBlock:function(){
				oblivious.addEntryComment(oblivious_viewentry_data._entryid, 
						oblivious_viewentry_data.category,
						d,function(){
					
							console.log('this@addentrycomment',String(this));
							oblivious.getEntry(oblivious_viewentry_data._entryid,oblivious_viewentry_data.category,function(){
								
								var data = this;
								if(data[0].meta.krypi == "1"){
									var pwd = prompt("Please enter password to decrypt");
									if(pwd){
										var eContents = oblivious._processGetEntry(data,oblivious_viewentry_data._entryid,pwd);
										oblivious_viewentry_data.contents = eContents;
									}else{
										
										$("#return-to-entries").click();
									}
							    }else{
							    	var eContents = oblivious._processGetEntry(data,oblivious_viewentry_data._entryid,'');
							    	oblivious_viewentry_data.contents = eContents;
							    	console.log(oblivious_viewentry_data.contents);
							    }
								
								$.unblockUI();
							});
						}
						);
			}});
				
			
		}
	});

	$(document).on('cancellation', '.remodal', function () {
	  console.log('Cancel button is clicked');
	});
});
