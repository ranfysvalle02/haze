console.log('add page');

$.blockUI.defaults.message = "";
//add-image-button
//execute callback when the page is ready:
$( document ).ready(function() {
// Handler for .ready() called.
	// unblock when ajax activity stops 
	var oblivious_viewdata = {
		'clientsidecrypto':false,
		'categories':[],
		'togglecrypto':null,
		'setAddProperty':null,
		'newentry':{
			'text':'',
			'imgdata':'',
			'pwd':'',
			'expiration':'never',
			'category':'',
			'meta':{}
		}
	};
	
	oblivious_viewdata.togglecrypto = function(){
		if(oblivious_viewdata.clientsidecrypto == true){
			oblivious._disableClientCrypto();
			oblivious_viewdata.clientsidecrypto=false;
		}else{
			oblivious._enableClientCrypto();
			oblivious_viewdata.clientsidecrypto=true;
		}
	};
	oblivious_viewdata.setAddProperty = function(){
		var id = $(this).attr('id');
		switch(id){
		case "entry-expiration":
			oblivious_viewdata.newentry.expiration = $(this).val();
			break;
		case "entry-password":
			oblivious_viewdata.newentry.pwd = $(this).val();
			break;
		case "entry-text":
			oblivious_viewdata.newentry.text = $(this).val();
			break;
		case "addentry-category":
			oblivious_viewdata.newentry.category = $(this).val();
			break;
		}
	};
	rivets.bind($('#oblivious_addentry'), {
		viewdata: oblivious_viewdata
	});
	$.getJSON('/oblivious/api/list/categories/',function(data){
		oblivious_viewdata.categories = data.Categories;
	});
	
	oblivious._onchange('addimage-input',function(){
		var imgData = String(this);
		oblivious_viewdata.newentry.imgdata = imgData;
	});
	//oblivious.invite_onchange('inviteinput');
	$("#metadata_entry").on("change",function(){
		var tmpJSON = $(this).val();
		try{
			tmpJSON = JSON.parse(tmpJSON);
			for (var attrname in tmpJSON) { oblivious_viewdata.newentry.meta[attrname] = tmpJSON[attrname]; }
			$("#metadata_preview").val( JSON.stringify(tmpJSON,null,2) );
		}catch(e){
			console.log('not valid JSON');
		}
		
	});
	$("#submit-invite-button").on("click",function(){
			oblivious._processInvite('#invite-status');
			$("#inviteform")[0].reset();
	});
	$("#submit-entry-button").on("click",function(){
		$.blockUI({ 
			onBlock:function(){
				console.log('obliviousdata',oblivious_viewdata.newentry);
				var d ={ data:String(oblivious_viewdata.newentry.text),
			             expire:oblivious_viewdata.newentry.expiration,
			             opendiscussion: 1,
			             category:oblivious_viewdata.newentry.category,
			             userkey:'',
			             imgdata:'',
			             containsimage:0
				     };
				for (var attrname in oblivious_viewdata.newentry.meta) { d[attrname] = oblivious_viewdata.newentry.meta[attrname]; }

				if(oblivious_viewdata.newentry.pwd != ''){
					//krypi-password
					//(newEntry,category,userKey,img_data){
					d.userkey = oblivious_viewdata.newentry.pwd;
				}
				if(oblivious_viewdata.newentry.imgdata != ''){
					d.imgdata = oblivious_viewdata.newentry.imgdata;
					d.containsimage = 1;
				}
				console.log('d',d);
				if(d.imgdata || d.userkey){
					console.log('krypi entry');
					oblivious.addKrypiEntry(d,function(){
						$.unblockUI();
					});
				}else{
					console.log('regular entry');
					oblivious.addEntry(d,function(){
						$.unblockUI();
					});
				}
				$('#addentryform')[0].reset();
			}}); 
		
	});
	$("#addentryform").on('reset',function(){
		oblivious._disableClientCrypto();
		oblivious_viewdata.clientsidecrypto=false;
	});
	$('#addentryform')[0].reset();
	
	
	
});
