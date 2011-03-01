<?php

class Posts extends MulletMapper {
	
	function __construct() {
    /*
	      validates_presence_of   :firstname, :lastname
		attr_accessible :login, :email, :name, :password, :password_confirmation,
      :firstname, :lastname, :fullname, :is_admin
      */
	}
	
	function index() {
    json_emit(array('ok'=>true));
	}
	
	function show() {
		echo 'pageshow';
	}
	
	function create() {
    json_emit(array('ok'=>true,'foo'=>'yep'));
	}
	
	function edit() {
		echo 'edit';
	}
	
	function destroy() {
		echo 'destroy';
	}
	
}

/*
def self.is_indexable_by(accessing_user, parent = nil)
    accessing_user.is_admin?
  end

  def self.is_creatable_by(user, parent = nil)
    user == nil or user.is_admin?
  end

  def is_updatable_by(accessing_user, parent = nil)
    id == accessing_user.id or accessing_user.is_admin?
  end

  def is_deletable_by(accessing_user, parent = nil)
    false
  end

  def is_readable_by(user, parent = nil)
    id.eql?(user.id) or user.is_admin?
  end
  
  */
  
  
  