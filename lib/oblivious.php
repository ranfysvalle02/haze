<?php
namespace Oblivious;
class Oblivious
{
	protected $name='oblivious';
	const VERSION = '0.0.1';
	protected $serversalt='';
	
	protected $mode='';
	
	/*Internal Copies of $_POST,$_GET,$_COOKIE*/
	protected $post = array();
	protected $get = array();
	protected $cookie = array();
	
	/*Base Path*///'data/'.$this->name
	//Used in: prepareCrypto(), prepareServerSalt()
	protected $base_path = '';
	/*File Path*///dirname(__FILE__).'/../data/'.$this->name.'/';
	//Used in: dataid2path() {{file paths}}
	protected $path = array();
	
	/*Default Meta Tags*/
	protected $meta_tags = array(
			'expire',
			'burnafterreading',
			'opendiscussion',
			'encrypted',
			'krypi',
			'unencrypted',
			'containsimage'
	);
	protected $categories = array(
		
	);
	
	protected $structuredepth = 10; //use for file-system storage
	protected $crypto_key = array();
	
	protected $crypto_method = 'aes-256-cbc';
	
	private function generateRandomSalt(){
		if (function_exists("mcrypt_create_iv")){
			$this->serversalt=bin2hex(mcrypt_create_iv(256, MCRYPT_DEV_URANDOM));
		}
		else
			for($i=0;$i<16;$i++) { $this->serversalt.=base_convert(mt_rand(),10,16); }
	}
private function _createCategory($category){
		
		$this->path[$category] = dirname(__FILE__).'/../data/'.$this->name.'/'.$category.'/';

		$keyfile = $this->path[$category].'key.php';
		
		if (!is_file($keyfile)){
			// Generate a 256-bit encryption key
			// This should be stored somewhere instead of recreating it each time
			$encryption_key = openssl_random_pseudo_bytes(32);
			// Generate an initialization vector
			// This *MUST* be available for decryption as well
			
			$this->crypto_key[$category] = $encryption_key;
			if (!is_dir('data'))
			{
				mkdir(dirname(__FILE__).'/../data',0705);
				file_put_contents(dirname(__FILE__).'/../data/.htaccess',"Allow from none\nDeny from all\n", LOCK_EX);
			}
			if (!is_dir($this->base_path)) {
				mkdir($this->base_path,0705);
			}
			if (!is_dir($this->path[$category])) {
				mkdir($this->path[$category],0705);
			}
			file_put_contents($keyfile,'<?php /* |'.$this->crypto_key[$category].'| */ ?>',LOCK_EX);
				
		}else{
			$items=explode('|',file_get_contents($keyfile));
			$this->crypto_key[$category] = $items[1];
		}
	}
	private function _createInviteCategory(){
		$this->path['invites'] = dirname(__FILE__).'/../data/'.$this->name.'/invites/';
		
	
		$keyfile = $this->path['invites'].'key.php';
		if (!is_file($keyfile)){
			// Generate a 256-bit encryption key
			// This should be stored somewhere instead of recreating it each time
			$encryption_key = openssl_random_pseudo_bytes(32);
			// Generate an initialization vector
			// This *MUST* be available for decryption as well
			$this->crypto_key['invites'] = $encryption_key;
			if (!is_dir('data'))
			{
				mkdir(dirname(__FILE__).'/../data',0705);
				file_put_contents(dirname(__FILE__).'/../data/.htaccess',"Allow from none\nDeny from all\n", LOCK_EX);
			}
			if (!is_dir($this->base_path)) {
				mkdir($this->base_path,0705);
			}
			if (!is_dir($this->path['invites'])) {
				mkdir($this->path['invites'],0705);
			}
			file_put_contents($keyfile,'<?php /* |'.$this->crypto_key['invites'].'| */ ?>',LOCK_EX);
	
		}else{
			$items=explode('|',file_get_contents($keyfile));
			$this->crypto_key['invites'] = $items[1];
		}
	}
	public function createCategory($newCategory){
		$base_path = realpath( dirname(__FILE__).'/../data/oblivious/' );
		$catfile = $base_path.'/categories.php';
		if (!is_file($catfile)){
			$new_data = '<?php return ' . var_export(array(), true) . ';';
			file_put_contents($catfile,$new_data);
		}
		if(!ISSET($this->crypto_key[$newCategory])){
			$this->_createCategory($newCategory);
			$categories = include $catfile;
			if(!in_array($newCategory,$categories)){
				$categories[]= $newCategory;
			}
			file_put_contents($catfile, '<?php return ' . var_export($categories, true) . ';');
			return array('msg'=>'Successfully created -'.$newCategory);
		}else{
			//category already exists - and we have a key
			return array('msg'=>'Category already exists -'.$newCategory);
		}
	}
	public function removeCategory($newCategory){
		$base_path = realpath( dirname(__FILE__).'/../data/oblivious/' );
		$catfile = $base_path.'/categories.php';
		if (!is_file($catfile)){
			return array('msg'=>'No Category to remove');
		}
		$processed = array();
		$categories = include $catfile;
		if(in_array($newCategory,$categories)){
			for($i=0;$i<count($categories);$i++){
				if($categories[$i] != $newCategory){
					$processed[] = $categories[$i];
				}
			}
			file_put_contents($catfile, '<?php return ' . var_export($processed, true) . ';');
			return true;
		}else{
			return false;
		}
	}
	private function prepareCrypto(){
		$base_path = realpath( dirname(__FILE__).'/../data/oblivious/' );
		$catfile = $base_path.'/categories.php';
		$categories = include $catfile;
		//file_put_contents('config.php', '<?php return ' . var_export($config, true) . ';');
		
		foreach($categories as $i=>$category){
			$this->_createCategory($category);
		}
		$this->_createInviteCategory();
		
	}
	private function prepareServerSalt(){
		//Will require 'WRITE' Permissions
		//0775
		$this->base_path = 'data/'.$this->name;
		$saltfile = $this->base_path.'/salt.php';
		if (!is_file($saltfile)){
			
			$this->generateRandomSalt();
			if (!is_dir('data'))
			{
				mkdir(dirname(__FILE__).'/../data',0705);
				file_put_contents(dirname(__FILE__).'/../data/.htaccess',"Allow from none\nDeny from all\n", LOCK_EX);
			}
			if (!is_dir($this->base_path)) {
				mkdir($this->base_path,0705);
			}
			file_put_contents($saltfile,'<?php /* |'.$this->serversalt.'| */ ?>',LOCK_EX);
				
		}else{
			$items=explode('|',file_get_contents($saltfile));
			$this->serversalt = $items[1];
		}
	}
	private function serverCheck(){
		$server_ready = false;
		//if (version_compare(PHP_VERSION, '5.2.6') < 0) die('Oblivious requires php 5.2.6 or above to work. Sorry.');
		
		// In case stupid admin has left magic_quotes enabled in php.ini:
		if (get_magic_quotes_gpc())
		{
			function stripslashes_deep($value) { $value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value); return $value; }
			$this->post = array_map('stripslashes_deep', $_POST);
			$this->get = array_map('stripslashes_deep', $_GET);
			$this->cookie = array_map('stripslashes_deep', $_COOKIE);
		}else{
			$this->post = $_POST;
			$this->get = $_GET;
			$this->cookie = $_COOKIE;
		}
		$server_ready = true;
		
		return $server_ready;
	}
	/**
	 * Credits to: https://www.reddit.com/r/PHP/comments/276jko/is_openssl_random_pseudo_bytes_good_to_generate/
	 * 
	 * Generates a cryptographically secure random string from the alphabet ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_
	 *
	 * @param $len String length
	 * @throws Exception
	 * @return string
	 */
	private function secureRandomString($len) {
		if($len < 0) throw new Exception("Length must be non-negative");
		return strtr(substr(base64_encode(openssl_random_pseudo_bytes(ceil($len * 3 / 4))), 0, $len), '+/', '-_');
	}
	//Return crypto_iv's
	private function crypto_iv(){
		return openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->crypto_method));
	}
	public function getCategoryPublicKey($category){
		return bin2hex($this->crypto_key[$category]);
	}
	private function _encrypt($data,$key){
// 		$iv=$this->crypto_iv();
// 		$encrypted = openssl_encrypt($data, $this->crypto_method, $key, 0, $iv );
// 		$encrypted = $encrypted . ':' . $iv;
		//TODO:: fix server side crypto
		//Moving all crypto to client-side for now
		return $data;
		//return $encrypted;
	}
	private function _decrypt($data,$key){
		// To decrypt, separate the encrypted data from the initialization vector ($iv)
// 		$parts = explode(':', $data);
// 		// $parts[0] = encrypted data
// 		// $parts[1] = initialization vector
		
// 		//TRY/CATCH THIS MOFO
// 		//openssl_decrypt(): IV passed is only 12 bytes long, 
// 		//cipher expects an IV of precisely 16 bytes, padding with
// 		try{
// 			$decrypted = openssl_decrypt($parts[0], $this->crypto_method, $key, 0, $parts[1]);
				
// 		}catch(Exception $e){
// 			//TODO
// 		}
		//TODO::
		//fix server side crypto	
		return $data;
		//return $decrypted;
	}
	/* Convert paste id to storage path.
	 The idea is to creates subdirectories in order to limit the number of files per directory.
	 (A high number of files in a single directory can slow things down.)
	 eg. "f468483c313401e8" will be stored in "data/f4/68/f468483c313401e8"
	 High-trafic websites may want to deepen the directory structure (like Squid does).
	
	 eg. input 'e3570978f9e4aa90' --> output 'data/e3/57/'
	 */
	private function dataid2path($dataid,$category)
	{
		$path = $this->path[$category];
		$increment=2;
		for($i=0;$i<=$this->structuredepth;$i=$i+$increment){
			$path .= substr($dataid,$i,$increment).'/';
		}
		return $path;
	}

	/* Convert paste id to discussion storage path.
	 eg. 'e3570978f9e4aa90' --> 'data/e3/57/e3570978f9e4aa90.discussion/'
	 */
	private function dataid2discussionpath($dataid,$category)
	{
		return $this->dataid2path($dataid,$category).$dataid.'.discussion/';
	}
	
	private function setAppName($newName){
		$this->name = $newName;
	}
	private function setAppMode($newMode){
		$this->mode = $newMode;
	}
	
	
	private function checkDeleteToken($a,$b){
		// Constant time string comparison.
		// (Used to deter time attacks on hmac checking. See section 2.7 of https://defuse.ca/audits/zerobin.htm)
		
			$diff = strlen($a) ^ strlen($b);
			for($i = 0; $i < strlen($a) && $i < strlen($b); $i++)
			{
			$diff |= ord($a[$i]) ^ ord($b[$i]);
			}
			return $diff === 0;
			
	}
	private function _deleteEntry($pasteid,$category){
		// Delete the paste itself
		
		unlink($this->dataid2path($pasteid,$category).$pasteid);
		
		// Delete discussion if it exists.
		$discdir = $this->dataid2discussionpath($pasteid,$category);
		if (is_dir($discdir))
		{
			// Delete all files in discussion directory
			$dhandle = opendir($discdir);
			while (false !== ($filename = readdir($dhandle)))
			{
				if (is_file($discdir.$filename))  unlink($discdir.$filename);
			}
			closedir($dhandle);
		
			// Delete the discussion directory.
			rmdir($discdir);
		}
	}
	public function __construct(array $userSettings = array())
	{
		if($this->serverCheck()){
			$settings = $userSettings[0];
			if(ISSET($settings['app_name']))
				$this->setAppName($settings['app_name']);
			if(ISSET($settings['mode']))
				$this->setAppMode($settings['mode']);
			if(ISSET($settings['categories']))
				$this->categories = $this->getCategories();
				
			$this->prepareServerSalt();
			$this->prepareCrypto();
				
				
		}
	
	}
	public function deleteEntry($pasteid,$deletetoken,$category)
	{
		if (preg_match('/\A[a-f\d]{16}\z/',$pasteid))  // Is this a valid paste identifier ?
		{
			$filename = $this->dataid2path($pasteid,$category).$pasteid;
			if (!is_file($filename)) // Check that paste exists.
			{
				return array('','Paste does not exist, has expired or has been deleted.','');
			}
		}
		else
		{
			return array('','Invalid data','');
		}
		
		if (!$this->checkDeleteToken($deletetoken, hash_hmac('sha1', $pasteid , $this->serversalt ))) // Make sure token is valid.
		{
			return array('','Wrong deletion token. Paste was not deleted.','');
		}else{
			// Paste exists and deletion token is valid: Delete the paste.
			$this->_deleteEntry($pasteid,$category);
			return array('','','Paste was properly deleted.');
		}
		
	}
	public function getEntry($pasteid,$category,$admin=false){
		$filename='';
		if (preg_match('/\A[a-f\d]{16}\z/',$pasteid))  // Is this a valid paste identifier ?
		{
			$filename = $this->dataid2path($pasteid,$category).$pasteid;
			if (!is_file($filename)) // Check that paste exists.
			{
				return array('','Paste does not exist, has expired or has been deleted.','');
			}
		}
		else
		{
			return array('','Invalid data','');
		}
		
		// Get the paste itself.
		$raw_text=file_get_contents($filename);
		
		$raw_text=$this->_decrypt($raw_text,$this->crypto_key[$category]);
		$paste=json_decode($raw_text);
		if($admin){
			$messages = array($paste); // The paste itself is the first in the list of encrypted messages.
			unset($messages['data']);
						
			return $messages;
		}
		// See if paste has expired.
		if (isset($paste->meta->expire_date) && $paste->meta->expire_date<time())
		{
			$this->_deleteEntry($pasteid,$category);  // Delete the paste
			return array('','Paste does not exist, has expired or has been deleted.','');
		}
		
		
		// We kindly provide the remaining time before expiration (in seconds)
		if (property_exists($paste->meta, 'expire_date')) $paste->meta->remaining_time = $paste->meta->expire_date - time();
		
		$messages = array($paste); // The paste itself is the first in the list of encrypted messages.
		// If it's a discussion, get all comments.
		if (property_exists($paste->meta, 'opendiscussion') && $paste->meta->opendiscussion)
		{
			$comments=array();
			$datadir = $this->dataid2discussionpath($pasteid,$category);
			if (!is_dir($datadir)) mkdir($datadir,$mode=0705,$recursive=true);
			$dhandle = opendir($datadir);
			while (false !== ($filename = readdir($dhandle)))
			{
				if (is_file($datadir.$filename))
				{
					$raw_comment=file_get_contents($datadir.$filename);
					$raw_comment=$this->_decrypt($raw_comment,$this->crypto_key[$category]);
					$comment=json_decode($raw_comment);
					// Filename is in the form pasteid.commentid.parentid:
					// - pasteid is the paste this reply belongs to.
					// - commentid is the comment identifier itself.
					// - parentid is the comment this comment replies to (It can be pasteid)
					$items=explode('.',$filename);
					$comment->meta->commentid=$items[1]; // Add some meta information not contained in file.
					$comment->meta->parentid=$items[2];
					$comments[$comment->meta->postdate]=$comment; // Store in table
				}
			}
			closedir($dhandle);
			ksort($comments); // Sort comments by date, oldest first.
			$messages = array_merge($messages, $comments);
		}
		//$this->CIPHERDATA = json_encode($messages);
		//$this->CIPHERDATA = htmlspecialchars($this->CIPHERDATA,ENT_NOQUOTES);
		// If the paste was meant to be read only once, delete it.
		if (property_exists($paste->meta, 'burnafterreading') && $paste->meta->burnafterreading) $this->_deleteEntry($pasteid,$category);
		
		return $messages;
	}
	public function getCategories(){
		$base_path = realpath( dirname(__FILE__).'/../data/oblivious/' );
		$catfile = $base_path.'/categories.php';
		if (!is_file($catfile)){
			$new_data = '<?php return ' . var_export(array(), true) . ';';
			file_put_contents($catfile,$new_data);
		}
		$categories = include($catfile);
		return array('Categories'=>$categories);
	}
	public function createEntry(){
		// Read additional meta-information.
		$meta=array();
		if(!ISSET($this->post['data'])){
			return false;
		}
		$category='';
		if(ISSET($this->post['category'])){
			$category = $this->post['category'];
		}else{
			$category = '';
		}
		$data = $this->post['data'];
		$is_comment = (!empty($this->post['parentid']) && !empty($this->post['pasteid'])); // Is this post a comment ?
		$response = array();
		$allmeta = $this->post ;
		unset($allmeta['data']);
		$meta_tags = array_keys($allmeta);
		//TODO:: Server-Level Meta-Data Control; Settings.php
		//$this->meta_tags//
		for($i=0;$i<count($meta_tags);$i++){
			$meta_tag=$meta_tags[$i];
			if(!empty($this->post[$meta_tag])){
				switch($meta_tag){
					case "expire":
						$expire=$this->post[$meta_tag];
						if ($expire=='5min') $meta['expire_date']=time()+5*60;
						elseif ($expire=='10min') $meta['expire_date']=time()+10*60;
						elseif ($expire=='1hour') $meta['expire_date']=time()+60*60;
						elseif ($expire=='1day') $meta['expire_date']=time()+24*60*60;
						elseif ($expire=='1week') $meta['expire_date']=time()+7*24*60*60;
						elseif ($expire=='1month') $meta['expire_date']=time()+30*24*60*60; // Well this is not *exactly* one month, it's 30 days.
						elseif ($expire=='1year') $meta['expire_date']=time()+365*24*60*60;
						break;
					case "burnafterreading":
						$burnafterreading = $this->post[$meta_tag];
						if ($burnafterreading!='0' && $burnafterreading!='1') {
							//invalid value entered
							$response['error']=array('status'=>1,'message'=>'You are unlucky. Try again.');
						}
						if ($burnafterreading!='0') { $meta['burnafterreading']=true; }
						break;
					case "opendiscussion":
						$opendiscussion = $this->post['opendiscussion'];
						if ($opendiscussion!='0' && $opendiscussion!='1') { 
							//invalid value entered
							$response['error']=array('status'=>1,'message'=>'You are unlucky. Try again.');
													}
						if ($opendiscussion!='0') { $meta['opendiscussion']=true; }
						break;
					case "syntaxcoloring":
						$syntaxcoloring = $this->post['syntaxcoloring'];
						if ($syntaxcoloring!='0' && $syntaxcoloring!='1') { $error=true; }
						if ($syntaxcoloring!='0') { $meta['syntaxcoloring']=true; }
						break;
					default:
						$meta[$meta_tag] = $this->post[$meta_tag];
						break;
				}
			}
		}//end for loop
		if (isset($meta['burnafterreading'])) unset($meta['opendiscussion']);
		$meta['_hash']=hash('md5',$_SERVER['REMOTE_ADDR']);
		// Add post date to meta.
		$meta['postdate']=time();
		
		//$dataid = $this->secureRandomString(18);
		$dataid = substr(hash('md5',$this->secureRandomString(16)),0,16);
		
		$storage = array('data'=>$data);
		if (count($meta)>0) $storage['meta'] = $meta;  // Add meta-information only if necessary.
		
		if ($is_comment) // The user posts a comment.
		{
			$pasteid = $this->post['pasteid'];
			$parentid = $this->post['parentid'];
			if (!preg_match('/\A[a-f\d]{16}\z/',$pasteid)) { echo json_encode(array('status'=>1,'message'=>'Invalid data.')); exit; }
			if (!preg_match('/\A[a-f\d]{16}\z/',$parentid)) { echo json_encode(array('status'=>1,'message'=>'Invalid data.')); exit; }
			
			unset($storage['meta']['expire_date']); // Comment do not expire (it's the paste that expires)
			unset($storage['meta']['opendiscussion']);
			unset($storage['meta']['syntaxcoloring']);
		
			// Make sure paste exists.
			$storagedir = $this->dataid2path($pasteid,$category);
			if (!is_file($storagedir.$pasteid)) { echo json_encode(array('status'=>1,'message'=>'Invalid data.')); exit; }
		
			// Make sure the discussion is opened in this paste.
			$raw_text=file_get_contents($storagedir.$pasteid);
			$raw_text=$this->_decrypt($raw_text,$this->crypto_key[$category]);
			
			$paste=json_decode($raw_text);
			if (!$paste->meta->opendiscussion) { echo json_encode(array('status'=>1,'message'=>'Invalid data.')); exit; }
		
			$discdir = $this->dataid2discussionpath($pasteid,$category);
			$filename = $pasteid.'.'.$dataid.'.'.$parentid;
			if (!is_dir($discdir)) mkdir($discdir,$mode=0705,$recursive=true);
			if (is_file($discdir.$filename)) // Oups... improbable collision.
			{
				echo json_encode(array('status'=>1,'message'=>'You are unlucky. Try again.'));
				exit;
			}
			
			$new_data=json_encode($storage);
			$new_data=$this->_encrypt($new_data,$this->crypto_key[$category]);
			file_put_contents($discdir.$filename,$new_data, LOCK_EX);
			return array('status'=>0,'id'=>$dataid); // 0 = no error
			
		}
		else // a standard paste.
		{
			if(ISSET($allmeta['isinvite']) && $allmeta['isinvite']){
				$category = 'invites';
			}
			$storagedir = $this->dataid2path($dataid,$category);
			if (!is_dir($storagedir)) mkdir($storagedir,$mode=0705,$recursive=true);
			if (is_file($storagedir.$dataid)) // Oups... improbable collision.
			{
				return array('status'=>1,'message'=>'You are unlucky. Try again.');
				
			}
			// New paste
			$new_data=json_encode($storage);
			$new_data=$this->_encrypt($new_data,$this->crypto_key[$category]);
			file_put_contents($storagedir.$dataid,$new_data, LOCK_EX);
		
			// Generate the "delete" token.
			// The token is the hmac of the pasteid signed with the server salt.
			// The paste can be delete by calling http://myserver.com/haze/?pasteid=<pasteid>&deletetoken=<deletetoken>
			$deletetoken = hash_hmac('sha1', $dataid , $this->serversalt);
			
			return array('status'=>0,'id'=>$dataid,'deletetoken'=>$deletetoken,'category'=>$category); // 0 = no error

		}
	}
	private function _removeBasePath($path){
		$root_path = realpath( dirname(__FILE__).'/../data/oblivious/' );
		$path2return = str_replace($root_path,'',$path);
		return $path2return;
	}
	private function _getDirContents($dir, &$results = array()){
		$files = scandir($dir);
		
		foreach($files as $key => $value){
			$path = realpath($dir.DIRECTORY_SEPARATOR.$value);
			if(!is_dir($path)) {
				$file_data = pathinfo($path);
				if(ISSET($file_data['extension'])){
					switch($file_data['extension'])
					{
						case "php":
							break;
						default:
							break;
					}	
				}else{
					$results[] = $this->_removeBasePath($path);
				}
				
			} else if(is_dir($path) && $value != "." && $value != "..") {
				$this->_getDirContents($path, $results);
				//discussions are in folders - keep in mind
				//but whether its an opendiscussion should
				//be in the meta-data
				//skip folders
				//$results[] = $this->_removeBasePath($path);
			}
		}
		return $results;
	}
private function _processDirContents($dircontents){
		///uncategorized/23/25/bd/49/f4/38/2325bd49f438c6bc
		$data = array();
		for($i=0; $i < count($dircontents); $i++){
			$tmp = array('entryid'=>'','category'=>'','contents'=>array());
			$curPath = $dircontents[$i];
			$str = explode("/",$curPath);
			$id = $str[count($str)-1];
			$cat = $str[1];
			if($cat=='invites'){
				//skip invites
				//continue;
			}
			$tmp['category'] = $cat;
			$tmp['entryid'] = $id;
			$e = $this->getEntry($id, $cat,true);
			if($e){
				$tmp['meta'] = $e[0]->meta;
				$data[] = $tmp;
			}
		}
		return $data;
	}
	private function _processDirCategories($dircontents){
		///uncategorized/23/25/bd/49/f4/38/2325bd49f438c6bc
		$data = array();
		print_r($dircontents);die();
		for($i=0; $i < count($dircontents); $i++){
			$curPath = $dircontents[$i];
			$str = explode("/",$curPath);
			$cat = $str[1];
			if(!in_array($cat,$data)){
				$data[] = $cat;
			}
		}
		return $data;
	}
	public function listEntries($category=''){
		$dir = dirname(__FILE__).'/../data';
		$dircontents = $this->_getDirContents($dir);
		$processed = array();
		$processed['Entries'] = $this->_processDirContents($dircontents);
		
		if($category != ''){
			$tmp = array();
			for($i=0; $i<count($processed['Entries']); $i++){
				$currEntry = $processed['Entries'][$i];
				if($currEntry['category'] == $category){
					$tmp[] = $currEntry;
				}
			}
			$processed['Entries'] = $tmp;
		}
		return $processed;
	}
}