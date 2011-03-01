<?php 


// Eggs, a small wiki based on:



// Structal, client-side mvc web framework

require 'structal/toro/toro.php';
require 'structal/json/json.php';
require 'structal/mullet/Mullet.php';
require 'structal/helper.php';
require 'structal/mustache/Mustache.php';

function authenticate_user($args) {
  $path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/';
  $parts = explode('/',$path_info);
	session_start();
	if (isset($_SESSION['face_userid']))
	  return member_of( $parts[2] );
	$_SESSION['return_to'] = 'http://tweetiepic.com/shared/'.$parts[2];
	$fblogin = 'http://tweetiepic.com/facebook';
	redirect_to( $fblogin );
}

function member_of( $group ) {
	$conn = new Mullet();
	$coll = $conn->user->profiles;
	$cursor = $coll->find(array(
	  'username' => $group
	));
	$user = $cursor->getNext();
	$conn = new Mullet(
		$user->datasource_username,
		$user->datasource_encrypted_password
	);
	$coll = $conn->user->perms;
	$cursor = $coll->find();
	$allowed = array();
	while( $cursor->hasNext() ) {
		$item = $cursor->getNext();
		$allowed[] =  $item->userid;
	}
  if (in_array($_SESSION['face_userid'],$allowed))
    return true;
  echo 'Sorry access to this information is restricted.';
  exit;
}

class IndexController extends ToroHandler {
	
  public function get() {

	  session_start();

	  $m = new Mustache;
		$resource = 'pages';
	  $tpl = 'templates/index.html';

		$routes = <<<EOD
      $.pathbinder.begin("/");
EOD;

		echo $m->render(file_get_contents($tpl), array(
		  'pathbinder' => $routes,
		  'resource' => $resource
		));

  }

  public function get_mobile() {
	  session_start();
  }

}

class ResourceController extends ToroHandler {
	
	function __construct() {
		//ToroHook::add( 'before_handler', 'authenticate_user' );
    //before_filter :login_required, :except => [ :new, :create ]
    //before_filter :has_permission?
	}
	
  public function get( $resource=false, $value=false, $action='index' ) {

	  $m = new Mustache;
	  $tpl = 'templates/index.html';
		$routes = <<<EOD
		  $.pathbinder.begin("/");
EOD;

		echo $m->render(file_get_contents($tpl), array(
		  'pathbinder' => $routes,
		  'resource' => $resource
		));
		
  }

  public function post( $resource=false, $value=false, $action='index' ) {
		$mapper = ucwords($resource);
		if (class_exists($mapper))
			$obj = new $mapper;
		if ($value == 'new')
		  $action = 'create';
		if (method_exists($obj,$action))
		  json_emit($obj->$action($value));
  }

  public function post_xhr( $resource=false, $value=false, $action='index' ) {
    $this->post($resource,$value,$action);
  }

}


class ChangesController extends ToroHandler {
	
	function __construct() {
		//ToroHook::add( 'before_handler', 'authenticate_user' );
	}
	
  public function get() {
    
    $m = new Mustache;
		$resource = 'pages';
	  $tpl = 'templates/changes.json';

/*
    $conn = new Mullet();
    $sources = array();
    $coll = $conn->user->profiles;
    $cursor = $coll->find();

    while( $cursor->hasNext() ) {

      $item = $cursor->getNext();

*/


    	$items = array(
    		'last' => false,
    		'id' => '',
    		'title' => 'page 1',
    		'link' => '',
    		'permalink' => '',
    		'pubdate' => '',
    		'body' => '',
        'has_enc' => false,
    		'enc_url' => '',
    		'enc_type' => '',
    		'enc_length' => 0
      );

/*
    }
*/

    $collections[] = array(
    	'last' => false,
    	'title' => $resource,
    	'feedurl' => '',
    	'url' => '',
    	'lastupdate' => '',
    	'items' => $items 
    );

    header('HTTP/1.1 200 OK');
    
    header('Content-Type: application/json');

    echo $m->render(file_get_contents($tpl), array(
    		'sources' => $collections
    ));
    
  }

  public function post() {
  }

  public function post_xhr() {
  }

}





$routes = array(array('/','IndexController'));

if ($handle = opendir('data'))
  while (false!==($file=readdir($handle)))
    if (strlen($file)>2) {
	    include 'data/'.$file;
	    $parts = explode('.',$file);
	    $routes[] = array('('.$parts[0].')','ResourceController');
	    $routes[] = array('changes','ChangesController');
	    $routes[] = array('('.$parts[0].')/(new)','ResourceController');
	    $routes[] = array('('.$parts[0].')/(index)','ResourceController');
	    $routes[] = array('('.$parts[0].')/([a-zA-Z0-9_]+)/(show)','ResourceController');
	    $routes[] = array('('.$parts[0].')/([a-zA-Z0-9_]+)/(edit)','ResourceController');
	    $routes[] = array('('.$parts[0].')/([a-zA-Z0-9_]+)/(destroy)','ResourceController');
    }

$site = new ToroApplication( $routes );

$site->serve();




