sjcl.random.startCollectors();

var oblivious = (function () {
	var blackbook = function(){
		this.data_types = ['aliases','contacts','categories','entries','keys','tokens','meta'];
	};
	
	blackbook.prototype.set = function(data_type,newKey,newValue){
		if(jQuery.inArray(data_type, this.data_types) === -1)
			return false;
		var blackbook = this;
		var tmpCache = new Array();
		
	    var localCollection = blackbook.getFromStorage(data_type);
	   
	    if( localCollection.length == 0){
	    	console.log('no thing in storage for ' + data_type);
	    }else{
	    	$.each(localCollection,function(i,old){
	    		
	    		if(old.key == newKey){
	    			//skip
	    		}else{
	    			tmpCache.push(old);
	    		}
	    	});//end each
	    	
	    }
	    var newObj = {key:newKey,value:newValue};
	    
	    console.log('new object',newObj);
		tmpCache.push(newObj);
	    window.localStorage[data_type] = JSON.stringify(tmpCache);	
	    return true;
	};
	blackbook.prototype.isStorageSupported = function(){
		// Check browser support
	    if (typeof(Storage) != "undefined")
	    	return true;
	    else
	    	return false;
	};
	
	blackbook.prototype.getFromStorage = function(data_type){
		if(jQuery.inArray(data_type, this.data_types) === -1)
			return false;
		var blackbook = this;
		if(blackbook.isStorageSupported){
			//only runs if storage is supported
			var localCollection;
			switch(data_type){
			default:
				localCollection = localStorage.getItem(data_type);
				if(localCollection != null && localCollection.length > 0){
					localCollection = JSON.parse(localCollection);	
					return localCollection;
				}else{
					//if localStorage.getItem returns null, initialize it
					localStorage.setItem(data_type,JSON.stringify([]));
					return JSON.parse(localStorage.getItem(data_type));
				}
				break;
			}
		}
		
	};
	blackbook.prototype._getEntryKey = function(entryID){
		var localCollection = this.getFromStorage('keys');
		var key = false;
	    if( localCollection.length == 0){
	    	console.log('no keys in blackbook');
	    }else{
	    	$.each(localCollection,function(i,entry){
	    		if(entry.key == entryID){
	    			key = entry.value;
	    			return false;//exit $.each
	    		}
	    	});//end each
	    }
	    return key;
	};
	blackbook.prototype._getEntryCategory = function(entryID){
		var localCollection = this.getFromStorage('categories');
		var category = false;
	    if( localCollection.length == 0){
	    	console.log('no categories in blackbook');
	    }else{
	    	$.each(localCollection,function(i,entry){
	    		if(entry.key == entryID){
	    			category = entry.value;
	    			return false;//exit $.each
	    		}
	    	});//end each
	    }
	    return category;
	};
	var oblivious_module = {
			'entry_cache':{
				'message_text':'',
				'raw_image':'',
				'image_base64':''
			},
			'api_path':'', 
			'client-side-crypto':false,
			'blackbook': new blackbook()
	};//using an api_path will allow for more flexible folder structure
	
	function getKeyFromBlackbook(entryID){
		return oblivious_module.blackbook._getEntryKey(entryID);
	}
	var xmlhttp = null;
	function _serialize(obj,prefix){
		var str = [];
		  for(var p in obj) {
		    if (obj.hasOwnProperty(p)) {
		      var k = prefix ? prefix + "[" + p + "]" : p, v = obj[p];
		      str.push(typeof v == "object" ?
		        _serialize(v, k) :
		        encodeURIComponent(k) + "=" + encodeURIComponent(v));
		    }
		  }
		  return str.join("&");
	}
	function _requestPOST(url,data,cb){
		xmlhttp.open('POST', url, true);
		xmlhttp.onload = function(){
			if (xmlhttp.status >= 200 && xmlhttp.status < 400) {
				// Success!
			    var data = xmlhttp.responseText;
			    console.log('data from POST',data);
			    if(typeof cb !== 'undefined' && typeof cb === 'function')
			    	cb.call(data);
			}
		}
		xmlhttp.onerror = function() {
			// There was a connection error of some sort
		};
		
		xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
		
		xmlhttp.send(_serialize(data));
	}
	function _requestGET(url,cb){
		xmlhttp.open('GET',url,true);
		xmlhttp.onload = function(){
			if (xmlhttp.status >= 200 && xmlhttp.status < 400) {
				// Success!
			    var data = xmlhttp.responseText;
			    console.log('data from GET',data);
			    if(typeof cb !== 'undefined' && typeof cb === 'function')
			    	cb.call(data);
			}
		}
		xmlhttp.onerror = function() {
			// There was a connection error of some sort
		};

		xmlhttp.send();
	}
	function _request(requestType,url,data,cb){
		if(typeof cb === 'undefined'){
			cb = function(){
				var data='';
				try{
			        data=JSON.parse(this);
			    }catch(e){
			    	//not valid JSON
			    	console.log('oblivious expects a JSON response - not this',this);
			    }
				console.log('cb-data',data);
			};
		}
		if(xmlhttp != null){
			switch(requestType){
			case "POST":
			case "post":
				_requestPOST(url,data,cb);
				break;
			case "GET":
			case "get":
				_requestGET(url,cb);
				break;
			}
		}
	}
    function _init(){
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
    }

	function _onchange(elementID,cb){
		document.getElementById(elementID).onchange = function (e) {
			var imgURL = e.target.files[0];
			var img = _prepareImage(imgURL,cb);
			console.log('preparing image',img);
			
		};
	}
	function deleteEntry(entryID,deleteToken,category){
		if(typeof entryID === 'undefined' || typeof deleteToken === 'undefined'){
			return false;
		}
		var d = { 
				entry_id: entryID,
		        delete_token:  deleteToken,
		        category:category
		      };
		var url = "/api/remove/entry/";
		var reqObj = {
				url: url,
			     data: d
		};
		_request('POST',reqObj.url,reqObj.data);
	}
	function _isKrypified(cleartext){
        var isKrypi = false;
    	if( cleartext.indexOf('!#krypi#') >= 0){
    		isKrypi = true;
    	}
    	return isKrypi;
	}
	function _getKrypiCipher(cleartext){
		var krypiCipher;
        var isKrypi = _isKrypified(cleartext);
    	
        if(isKrypi){
        	krypiCipher = String( cleartext.split('!#krypi#')[1] );
        	krypiCipher = krypiCipher.replace('#krypi#!','');
        }    	
    	return krypiCipher;
	}
	function _isKrypiImage(cleartext){
		var isKrypiImg = false;
    	if( cleartext.indexOf('!#krypi_img#') >= 0){
    		isKrypiImg = true;
    	}
    	return isKrypiImg;
	}
	function _getImageCipher(cleartext){
		if(_isKrypiImage(cleartext)){
			var imgCipher;
			imgCipher = String( cleartext.split('!#krypi_img#')[1] );
    		imgCipher = String( imgCipher.split('#krypi_img#!')[0] );
    		return imgCipher;
		}
	}
	function getPublicKey(cat){
		var pkey=false;
		$.ajax({
	        async: false,
	        url: "/api/get/publickeys/"+cat,
	        dataType: "json",
	        data: {},
	        success: function(data){
	        	pkey = data.Key;
	        }
	    });
		return pkey;
	}
	function addEntry(entrydata,cb){
		console.log('entrydata',entrydata);
		var encrypted=0;var ranKey='';
		var krypi=0;
		
		if(_isKrypified(entrydata.data))
			entrydata.krypi=1;
		console.log('dimelo pina');
		if(oblivious_module['client-side-crypto']){
			ranKey = sjcl.codec.base64.fromBits(sjcl.random.randomWords(8, 0), 0);
			entrydata.data = String( _encrypt(ranKey,entrydata.data) );
			entrydata.encrypted=1;
			encrypted=1;
		}
//		var d ={ data:String(newEntry),
//	             expire:         "1month",
//	             burnafterreading: 0,
//	             isinvite:0,
//	             opendiscussion: 1,
//	             syntaxcoloring: 0,
//	             category:category,
//	             encrypted:encrypted,
//	             krypi:krypi
//		     };
		
		if(entrydata.expire == "burnafter"){
			entrydata.burnafterreading = 1;
			delete entrydata.opendiscussion;
			delete entrydata.expire;
		}
		//cleanup data
		delete entrydata.userkey;
		delete entrydata.imgdata;
		if(entrydata.encrypted != 1 && entrydata.krypi != 1){
			entrydata.unencrypted = 1;
			//get the server public key
			var publickey = getPublicKey(entrydata.category);
			console.log('pk',publickey);
			entrydata.data = String( _encrypt(publickey,entrydata.data) );
			entrydata.publickey=1;
		}
		
		
		var url = "/api/create/entry/";
		var reqObj = {
				url: url,
			     data: entrydata
		};
		console.log('reqObj',reqObj);
		_request('POST',reqObj.url,reqObj.data,function(){
			var data='';
			try{
		        data=JSON.parse(this);
		    }catch(e){
		    	//not valid JSON
		    	console.log('oblivious expects a JSON response - not this',this);
		    }
		    delete entrydata.data;
		    if(encrypted){
		    	//store it in the blackbook
		    	//blackbookSet('data_type','key','val');
		    	blackbookSet('entries',data.category,data.id);
		    	blackbookSet('keys',data.id,ranKey);
		    	blackbookSet('categories',data.id,data.category)
		    	blackbookSet('tokens',data.id,data.deletetoken);
		    	blackbookSet('meta',data.id,entrydata);
		    }
		    else if(entrydata.isinvite){
		    	
		    }else{
		    	blackbookSet('entries',data.category,data.id);
		    	blackbookSet('keys',data.id,false);
		    	blackbookSet('categories',data.id,data.category)
		    	blackbookSet('tokens',data.id,data.deletetoken);
		    	blackbookSet('meta',data.id,entrydata);
		    }
		    if(typeof cb !== 'undefined' && typeof cb === 'function')
		    	cb.call(data);
		});
	}
	function getEntryMeta(entryID,category,cb){
		if(typeof category === 'undefined')
			category = 'uncategorized';
		var d ={
				entry_id:entryID,
				category:category
		};
		var url = "/api/get/entry/meta/";
		var reqObj = {
				url: url,
			     data: d
		};
		_request('POST',reqObj.url,reqObj.data,function(){
			var data=this;
			try{
		        data=JSON.parse(data);
		    }catch(e){
		    	//not valid JSON
		    	console.log('oblivious expects a JSON response - not this',this);
		    }
		    
		    if(typeof cb !== 'undefined' && typeof cb === 'function')
		    	cb.call(data);
		});
	}
	function getEntry(entryID,category,cb){
		if(typeof category === 'undefined')
			category = 'uncategorized';
		var d ={
				entry_id:entryID,
				category:category
		};
		var url = "/api/get/entry/";
		var reqObj = {
				url: url,
			     data: d
		};
		_request('POST',reqObj.url,reqObj.data,function(){
			var data=this;
			try{
		        data=JSON.parse(data);
		    }catch(e){
		    	//not valid JSON
		    	console.log('oblivious expects a JSON response - not this',this);
		    }
		    
		    if(typeof cb !== 'undefined' && typeof cb === 'function')
		    	cb.call(data);
		});
	}
	function _generateInvite(entryID,category){
		console.log('entryID',entryID,'category',category);
		var result = _inviteMobile(entryID,category);
		console.log('result',result);
	}
	function _processGetEntry(data,entryID,userkey){
		var processedData = [];
		$.each(data,function(i,obj){
			var true_entry = {
				text:'',
				image:'',//'<a class="image" href="data:image/png;base64,'
				identicon:'',//'<a class="image" href="data:image/png;base64,'
				createdate:'',
				expiretime:'',
				
			};
			if(obj.meta.publickey){
				var publickey = getPublicKey(obj.meta.category);
				obj.data = _decrypt(publickey,obj.data);
			}
			if(obj.meta.postdate){
				var postdate = moment(obj.meta.postdate*1000).format("YYYY-MM-DD, HH:mm");
				true_entry.createdate = postdate;
			}
			if(obj.meta._hash){
				var identicon = new Identicon(obj.meta._hash, 420).toString();
				identicon = 'data:image/png;base64,' + identicon;
				true_entry.identicon = identicon;
			}
			if(obj.meta.remaining_time){
				var duration = moment.duration(obj.meta.remaining_time*1000, 'milliseconds');
		    	var hours = Math.floor(duration.asHours());
		    	var mins = Math.floor(duration.asMinutes()) - hours * 60;
		    	
		    	var expiration_text = ' Expires in ' + hours + ' hours, ' + mins + 'minutes.';
		    	true_entry.expiretime = expiration_text;
			}
			if(obj.meta.encrypted == "1"){
				//check the blackbook for the key
				console.log('entryID',entryID);
				var entryKey = oblivious_module.blackbook._getEntryKey(entryID);
				
				
				if(entryKey){
					var temp_entry = _decrypt(entryKey,obj.data);
					console.log('temp_entry',temp_entry);
					if(obj.meta.krypi == "1"){
						var pwd = userkey;
						var tmp_true_entry = _dekrypify(temp_entry,pwd);
						true_entry.text = tmp_true_entry.text;
						true_entry.image = tmp_true_entry.image;
						//krypify returns expected object
					}else{
						var tmp_true_entry = _dekrypify(temp_entry,'');
						true_entry.text = tmp_true_entry.text;
						true_entry.image = tmp_true_entry.image;
					}
					console.log('true_entry',true_entry);
				}else{
					console.log('no key found in blackbook');
					true_entry.text = "(no key found in blackbook)";
				}
			}else if(obj.meta.krypi == "1"){
				var pwd = userkey;
				var tmp_true_entry = _dekrypify(obj.data,pwd);
				true_entry.text = tmp_true_entry.text;
				true_entry.image = tmp_true_entry.image;
			}else{
				if(obj.meta.containsimage == "1"){
					var tmp_true_entry = _dekrypify(obj.data,'');
					true_entry.text = tmp_true_entry.text;
					true_entry.image = tmp_true_entry.image;
				}else{
					true_entry.text = obj.data;
				}
			}
			
			//is it a comment?
			if(typeof obj.meta.parentid !== 'undefined'){
				console.log('obj.data',obj.data);
				if(obj.meta.encrypted){
					var entryKey = oblivious_module.blackbook._getEntryKey(entryID);
					
					if(entryKey){
						var tmp_true_entry = _decrypt(entryKey,obj.data);
						console.log('tmp',tmp_true_entry,userkey);
						var isImg = _isKrypiImage(tmp_true_entry);
						if(isImg){
							console.log('we here');
							tmp_true_entry = _dekrypify(tmp_true_entry,'');
							true_entry.text = tmp_true_entry.text;
							true_entry.image = tmp_true_entry.image;
						}else{
							true_entry.text = tmp_true_entry;
						}
					}
				}else{
					var isImg = _isKrypiImage(obj.data);
					if(isImg){
						console.log('estamo aki?',userkey);
						var tmp_true_entry = _dekrypify(obj.data,userkey);
						true_entry.text = tmp_true_entry.text;
						true_entry.image = tmp_true_entry.image;
						console.log('wtf',true_entry);
					}else{
						true_entry.text = obj.data;
					}
				}
					
			}
			processedData.push(true_entry);
		});
		return processedData;
	}
	function addEntryComment(entryID,category,commentobj,cb){
		
		//no extra crypto enabled for comments at the time
		if(typeof commentobj.nickname === 'undefined'){
			commentobj.nickname = '(Anonymous)';
		}
		
		//before crypto - krypify
		if(commentobj.imgdata != ''){
			var tmpobj = {
					data:String(commentobj.comment),
		             imgdata:commentobj.imgdata,
		             userkey:''
			};
			var tmp = _krypify(tmpobj);
			commentobj.comment = tmp.data;
			
		}
		
		var entryKey = oblivious_module.blackbook._getEntryKey(entryID);
		var encrypted=0;
		if(entryKey){
			//entry uses client-side encryption
			//encrypt comments with the same key
			commentobj.comment = _encrypt(entryKey,commentobj.comment);
			encrypted=1;
		}
		var d = { data:commentobj.comment,
		        parentid: entryID,
		        pasteid:  entryID,
		        nickname: commentobj.nickname,
		        category:category,
		        encrypted:encrypted
		      };
		console.log('before post',d);
		var url = "/api/create/entry/";
		var reqObj = {
				url: url,
			     data: d
		};
		if(typeof cb !== 'undefined' && typeof cb === 'function')
			_request('POST',reqObj.url,reqObj.data,cb);
		else
			_request('POST',reqObj.url,reqObj.data);
		
	}
	function _prepareImage(url,cb){
		if(typeof loadImage === 'undefined'){
			console.log('missing loadImage dependency');
			return false;
		}
		var loadingImage = loadImage(
	        url,
	        function (img) {
				console.log('cb-img',img);
				var dataURL = img.toDataURL("image/png");
			    dataURL = dataURL.replace(/^data:image\/(png|jpg);base64,/, "");
			    console.log('dataURL',dataURL);
			    if(typeof cb !== 'undefined' && typeof cb === 'function'){
			    	cb.call(dataURL);
			    }
			    	
				
	        },
	        {maxWidth: 640,maxHeight:640, minWidth: 64,minHeight:64, canvas:true}
	    );
	    if (!loadingImage) {
	        // Alternative code ...
	    	console.log('loadImage failed');
	    }
	    return true;
	}
	function _compress(message){
	    return Base64.toBase64( RawDeflate.deflate( Base64.utob(message) ) );
	}
	function _decompress(data){
	    return Base64.btou( RawDeflate.inflate( Base64.fromBase64(data) ) );
	}
	function _encrypt(key, message){
		return sjcl.encrypt(key,_compress(message));
	}
	function _decrypt(key, data){
		return _decompress(sjcl.decrypt(key,data));
	}
	function addKrypiEntry(entryobj,cb){
		console.log('entryobj@addKryp',entryobj);
		var tmp = _krypify(entryobj);
		delete tmp['userkey'];
		entryobj = tmp;
		addEntry(entryobj,cb);
	}
	function _getRandomKey(){
		var randomkey = sjcl.codec.base64.fromBits(sjcl.random.randomWords(8, 0), 0);
		return randomkey;
	}
	function _dekrypify(rawEntry,userKey){
		var _dekrypObj={
				'text':'',
				'image':''
		};
		console.log('rawEntry',rawEntry);
		if(_isKrypified(rawEntry)){
			var krypiCipher = _getKrypiCipher(rawEntry);
			console.log('krypiCipher',krypiCipher);
			var krypiEntry = _decrypt(userKey,krypiCipher);
			console.log('krypiEntry',krypiEntry);
			if(_isKrypiImage(krypiEntry)){
				var krypiImgCipher = _getImageCipher(krypiEntry);
				console.log('krypiImgCipher',krypiImgCipher);
				var krypiImage = _decrypt(userKey,krypiImgCipher);
				_dekrypObj.image = 'data:image/png;base64,'  + krypiImage;
				console.log('krypiImg',krypiImage);
				krypiEntry = String( krypiEntry.replace(krypiImgCipher,''));
				krypiEntry = String(krypiEntry.replace("!#krypi_img##krypi_img#!",''));
				_dekrypObj.text = krypiEntry;
			}
			_dekrypObj.text = krypiEntry;
		}else if(_isKrypiImage(rawEntry)){
			var krypiImgCipher = _getImageCipher(rawEntry);
			console.log('not a cipher tho',krypiImgCipher);
			_dekrypObj.image = 'data:image/png;base64,'  + krypiImgCipher;
			rawEntry = String( rawEntry.replace(krypiImgCipher,''));
			rawEntry = String(rawEntry.replace("!#krypi_img##krypi_img#!",''));
			_dekrypObj.text = rawEntry;
		}else{
			console.log('dimelo pina');
			_dekrypObj.text = rawEntry;
		}
		console.log('_dekrypObj',_dekrypObj);
		return _dekrypObj;
	}
	function _krypify(entryobj){
		//embeds images & password-protects
		console.log('entryobj',entryobj);
		var tmpInfo;
		
		if(typeof entryobj.userkey !== 'undefined' && entryobj.userkey == ''){
			//_img_data
			if(typeof entryobj.imgdata !== 'undefined' && entryobj.imgdata != ''){
				entryobj.data = entryobj.data +"!#krypi_img#"+entryobj.imgdata+"#krypi_img#!";
				
				delete entryobj.imgdata;
			}
		}else if(typeof entryobj.userkey !== 'undefined' && entryobj.userkey != ''){
			//_img_data
			if(typeof entryobj.imgdata !== 'undefined' && entryobj.imgdata != ''){
				entryobj.data = entryobj.data +"!#krypi_img#"+_encrypt(entryobj.userkey,entryobj.imgdata)+"#krypi_img#!";
				
				delete entryobj.imgdata;
			}
			
			entryobj.data = "!#krypi#"+_encrypt(entryobj.userkey, entryobj.data)+"#krypi#!";
			delete entryobj.userkey;
		}else{
			console.log('entryobj looks like',entryobj,'not sure why we here');
		}
	    return entryobj;
	}
	function _enableClientCrypto(){
		oblivious_module['client-side-crypto'] = true;
		console.log('client-side crypto enabled');
	}
	function _disableClientCrypto(){
		oblivious_module['client-side-crypto'] = false;
		console.log('client-side crypto disabled');
	}
	function blackbookGet(data_type){
		return oblivious_module['blackbook'].getFromStorage(data_type);
	}
	function blackbookSet(data_type,key,value){
		return oblivious_module['blackbook'].set(data_type,key,value);
	}
	function blackbookClear(data_type){
		if(typeof data_type === 'undefined'){
			data_type='ALL';
			$.each(oblivious_module.blackbook.data_types, function(i,dt){
				if(window.localStorage[dt]){
			    	window.localStorage[dt] = JSON.stringify([]);
			    }
			});
		}else{
			if(window.localStorage[data_type]){
		    	window.localStorage[data_type] = JSON.stringify([]);
		    }
		}
		console.log('cleared ' + data_type)
	}
	function _inviteMobile(entryID,category){
		var entryCategory = oblivious_module.blackbook._getEntryCategory(entryID);
		var entryKey = oblivious_module.blackbook._getEntryKey(entryID);
		if(!entryCategory){
			entryCategory = category;
		}
		if(entryKey && entryCategory){
			var rawString = "#"+entryID+"#"+entryKey+"#"+entryCategory;
			var encodedString = rawString;
			var d ={ data:String(encodedString),
		             burnafterreading:1,
		             category:entryCategory,
		             userkey:'',
		             imgdata:'',
		             containsimage:0,
		             isinvite:1
			     };
			
			if($("#invite-pwd").val() != ''){
				var tmpPwd = $("#invite-pwd").val();
				encodedString = "#!k!#"+oblivious._encrypt(tmpPwd,encodedString);
				//avoids sending the unencrypted key
				d.krypi=1;
				d.data = encodedString;
			}
			
			console.log('regular entry');
			oblivious.addEntry(d,function(){
				console.log('this@afterinvite',this);
				var data = this;
				var inviteid = data.id;
				var invitecategory = entryCategory;
				var inviteString = inviteid + "#!" + invitecategory;
				var encodedInvite;
				
					encodedInvite = window.btoa(inviteString);
					
					console.log('encodedinvite',encodedInvite);

				$("#encodedinvite").val(encodedInvite);
			});
			
		}else if(entryCategory){
			var rawString = "#"+entryID+"#"+'(nokey)'+"#"+entryCategory;
			var encodedString = rawString;
			var d ={ data:String(encodedString),
		             burnafterreading:1,
		             category:entryCategory,
		             userkey:'',
		             imgdata:'',
		             containsimage:0,
		             isinvite:1
			     };
			
			if($("#invite-pwd").val() != ''){
				var tmpPwd = $("#invite-pwd").val();
				encodedString = "#!k!#"+oblivious._encrypt(tmpPwd,encodedString);
				//avoids sending the unencrypted key
				d.krypi=1;
				d.data = encodedString;
			}else{
				d.unencrypted=1;
			}
			
			console.log('regular entry');
			oblivious.addEntry(d,function(){
				console.log('this@afterinvite',this);
				var data = this;
				var inviteid = data.id;
				var invitecategory = entryCategory;
				var inviteString = inviteid + "#!" + invitecategory;
				var encodedInvite;
				
					encodedInvite = window.btoa(inviteString);
				console.log('encodedinvite',encodedInvite);
//				
				$("#encodedinvite").val(encodedInvite);
			});
		}
	}
	function _processInviteCode(rawCode){
		var decoded = window.atob(rawCode);
		var returnobj = {
				'entryid':'',
				'entrykey':'',
				'entrycategory':''
		};
		var s = decoded.split("#");
		if(s.length==4){
			var entryID = s[1];
			var entryKey = s[2];
			var entryCategory = s[3];
		}
		return returnobj;
		
	}
	function _processInvite(selector){
//		
		var encodedinvite = $("#encodedinvite").val();
		var decodedinvite = window.atob(encodedinvite);
		var str_parts = decodedinvite.split("#!");
		var inviteid  = str_parts[0];
		var invitecategory = str_parts[1];
		
		console.log('inviteid',inviteid);
		console.log('invitecategory',invitecategory);
		
		oblivious.getEntry(inviteid,'invites',function(){
			console.log('this@getentry',this);
			var invitedata = this[0];
			if(this[0].meta.krypi && this[0].meta.isinvite){
				//request password;

				var tmpData = String(invitedata.data).replace("#!k!#","");
				console.log('tmpData',tmpData);
				var pwd = prompt("Please enter password to decrypt");
				if(pwd){
					var eContents = oblivious._decrypt(pwd,tmpData);
					console.log('eContents',eContents);
					var decoded = eContents;
					console.log('decoded',decoded);
					var s = decoded.split("#");
					var invitestatus;
					
					if(s.length==4){
						var entryID = s[1];
						var entryKey = s[2];
						var entryCategory = s[3];
						console.log('entryID',entryID);
						console.log('entryKey',entryKey);
						console.log('entryCategory',entryCategory);
						var invitestatus;
						oblivious.getEntryMeta(entryID,entryCategory,function(){
							console.log('this@getEMeta',this);
							var alreadyhaskey = oblivious_module.blackbook._getEntryKey(entryID);
							console.log('already',alreadyhaskey);
							if(alreadyhaskey){
								invitestatus = "You already have access to this entry.";
							}else{
								invitestatus = "Successfully processed invite!";
								if(entryKey == '(nokey)'){
									entryKey = false;
									invitestatus = "Successfully processed invite! - no key required for entry.";
								}
								blackbookSet('entries',entryCategory,entryID);
						    	blackbookSet('keys',entryID,entryKey);
						    	blackbookSet('categories',entryID,entryCategory);
						    	blackbookSet('meta',entryID,this[0].meta);
							}
						});
				    	//blackbookSet('tokens',data.id,data.deletetoken);
					}else{
						invitestatus = "Could not process invite.";
					}
					$(selector).text(invitestatus);
					$(selector).show();
					setTimeout(function() { $(selector).hide(); }, 5000);
					
				}else{
					var eContents = window.atob(invitedata);
					console.log('eContents',eContents);
				}
				
			}else{
				console.log('unencrypted invite data',invitedata.data);
			}
		});
		//encodedString = "#!k!#"+oblivious._encrypt(tmpPwd,encodedString);
		//var rawString = "#"+entryID+"#"+entryKey+"#"+entryCategory;
		//var encodedString = btoa(rawString);
		
		//var inviteString = inviteid + "#!" + invitecategory;
//		var encodedInvite;
//		
//		encodedInvite = btoa(inviteString);
	}
	_init();
	return {
		_processInvite:_processInvite,
		_enableClientCrypto:_enableClientCrypto,
		_disableClientCrypto:_disableClientCrypto,
		_processGetEntry:_processGetEntry,
        _onchange: _onchange,
        getEntryMeta:getEntryMeta,
        _encrypt: _encrypt,
        _decrypt: _decrypt,
        deleteEntry: deleteEntry,
        addEntry: addEntry,
        getEntry: getEntry,
        addEntryComment: addEntryComment,
        addKrypiEntry:addKrypiEntry,
        _dekrypify:_dekrypify,
        blackbookGet:blackbookGet,
        blackbookSet:blackbookSet,
        blackbookClear:blackbookClear,
        _generateInvite:_generateInvite,
        getKeyFromBlackbook:getKeyFromBlackbook
    }; 
})();