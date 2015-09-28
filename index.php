<?php
require 'sub_modules/slimphp/Slim/Slim.php';
require 'lib/oblivious.php';
error_reporting(E_ALL);
$oblivious_settings=array(
		'app_name'=>'haze',
		'mode' => 'development',
		'meta_tags'=>array('isinvite','nickname','syntaxcoloring')
);
$oblivious = new \Oblivious\Oblivious(array($oblivious_settings));


\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim(array(
    'cookies.encrypt' => true,
		'mode' => 'development',
		'templates.path' => './html/oblivious'
));
// Only invoked if mode is "development"
$app->configureMode('development', function () use ($app,$oblivious) {
	$app->config(array(
			'log.enable' => true,
			'debug' => true
	));
});

//We can inject the $app variable into the callback function with the use keyword:
$app->get('/', function () use ($app,$oblivious) {
	$view_data = array( 'js_path'=>'/html/oblivious/js/home.js',
			 'nav_breadcrumb'=>'',
			 'path_from_index' => '/html/oblivious/',
			'nav_path'=>'/');
	$app->render('html_top.php', $view_data);
	
	$app->render('oblivious.php', $view_data);
	$app->render('html_bottom.php', $view_data);
	
});
$app->get('/view/add/', function () use ($app,$oblivious) {
	$view_data = array( 'js_path'=>'/html/oblivious/js/add.js', 'nav_breadcrumb'=>':add', 'path_from_index' => '/html/oblivious/','nav_path'=>'/');
	$app->render('html_top.php', $view_data);
	$app->render('add-entry.php', $view_data);
	$app->render('html_bottom.php', $view_data);
	
});
$app->get('/view/settings/', function () use ($app,$oblivious) {
	$view_data =  array( 'js_path'=>'/html/oblivious/js/settings.js', 'nav_breadcrumb'=>':settings', 'path_from_index' => '/html/oblivious/','nav_path'=>'/');
	$app->render('html_top.php',$view_data);
	
	$app->render('settings.php', $view_data);
	
	$app->render('html_bottom.php', $view_data);
	
});
	$app->get('/view/settings/admin/', function () use ($app,$oblivious) {
// 		$view_data =  array( 'js_path'=>'/html/oblivious/js/settings.js', 'nav_breadcrumb'=>':settings', 'path_from_index' => '/html/oblivious/','nav_path'=>'/');
// 		$app->render('html_top.php',$view_data);
	
// 		$app->render('settings-admin.php', $view_data);
	
// 		$app->render('html_bottom.php', $view_data);
	
	});
$app->post('/api/create/entry/',function() use ($app,$oblivious){
	if(ISSET($_POST['data'])){
		echo json_encode( $oblivious->createEntry() );
	}
	else{
		echo "Fail";
	}
});
$app->post('/api/get/entry/',function() use ($app,$oblivious){
	if(ISSET($_POST['entry_id']) && ISSET($_POST['category'])){
		echo json_encode( $oblivious->getEntry($_POST['entry_id'], $_POST['category']) );
	}
	else{
		echo "Fail";
	}
});
	$app->post('/api/get/entry/meta/',function() use ($app,$oblivious){
		if(ISSET($_POST['entry_id']) && ISSET($_POST['category'])){
			echo json_encode( $oblivious->getEntry($_POST['entry_id'], $_POST['category']), true );
		}
		else{
			echo "Fail";
		}
	});
$app->post('/api/remove/entry/',function() use ($app,$oblivious){
	if(ISSET($_POST['entry_id']) && ISSET($_POST['delete_token']) && ISSET($_POST['category'])){
		
		echo json_encode( $oblivious->deleteEntry($_POST['entry_id'],$_POST['delete_token'],$_POST['category']) );
	}
	else{
		echo "Fail";
	}
});
$app->get('/api/list/categories/',function() use ($app,$oblivious){
	
	echo json_encode( $oblivious->getCategories() );
	
});
$app->get('/api/add/categories/:category/',function($category) use ($app,$oblivious){

	echo json_encode( $oblivious->createCategory($category) );

});
$app->get('/api/remove/categories/:category/',function($category) use ($app,$oblivious){

	echo json_encode( $oblivious->removeCategory($category) );

});
$app->get('/api/list/entries/',function() use ($app,$oblivious){
	
	echo json_encode( $oblivious->listEntries() ); //json_encode
	
});

$app->get('/api/list/entries/:category/',function($category) use ($app,$oblivious){

	echo json_encode( $oblivious->listEntries($category) ); //json_encode

});
$app->get('/api/get/publickeys/:category/',function($category) use ($app,$oblivious){
	$res = $oblivious->getCategoryPublicKey($category); //json_encode
	echo json_encode( array('Key'=>$res) );
});
$app->error(function (\Exception $e) use ($app) {
	print_r($e);echo "yeah";
});
$app->run();
