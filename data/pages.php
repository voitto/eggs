<?php

class Pages extends MulletMapper {
	
	function __construct() {
		$this->key( 'title', String );
		$this->key( 'body', String );
		$this->validates_uniqueness_of( 'title' );
	  $this->many( 'comments' );
	}
	
	function index() {

	  $conn = new Mullet(USR,PWD);

	  $coll = $conn->user->pages;
	  
	  $pages = array();

		$cursor = $coll->find();

		while( $cursor->hasNext() ) {
			$page = $cursor->getNext();
       $pages[] = array(
        'title' => $page->title,
        'key' => $page->keyname
      );
		}

    return array('ok'=>true,'pages'=>$pages);
    
	}
	
	function show($key) {
    
  	$conn = new Mullet(USR,PWD);

	  $coll = $conn->user->pages;

	  $pages = array();

		$cursor = $coll->find(array(
		  'keyname' => $key,
		));

	  $page = $cursor->getNext();

		return array('ok'=>true,'content'=>urldecode(stripslashes($page->content)),'key'=>$page->keyname);
		
	}
	
	function create() {
	  
	  if (isset($_POST['title'])) {

    	$conn = new Mullet(USR,PWD);

  	  $coll = $conn->user->pages;

  	  $result = $coll->insert(array(
  		  'title' => $_POST['title'],
  		));

  	  if ($result)
  			return array(
  				'ok'=>true
  			);
  			
      return array('ok'=>false);
      
	  }

    return array('ok'=>true);

	}
	
	function edit($key) {

  	$conn = new Mullet(USR,PWD);

	  $coll = $conn->user->pages;

	  $pages = array();

		$cursor = $coll->find(array(
		  'keyname' => $key,
		));

	  $page = $cursor->getNext();

    if (isset($_POST['title'])) {

  		$result = $coll->update( 
        array( 
    		  'keyname' => $key,
  	    ),
        array( 
          'title' => $_POST['title'],
          'content' => $_POST['content']
        )
      );

  	  if ($result)
  			return array(
  				'ok'=>true
  			);
			
      return array('ok'=>false);
    
    }

    return array(
      'ok'=>true,
      'title'=>$page->title,
      'content'=>$page->content,
      'key'=>$page->keyname
    );
    
	}
	
	function destroy() {
		echo 'destroy';
	}
	
}

