<?php

/**
 * User
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7380 2010-03-15 21:07:50Z jwage $
 */
class User extends BaseUser
{
	//FIXME: set bootstrap of Doctrine to overwrite accessor methods. this method is not called on $user->password = "pass";
	//alternatively, use magic methods so that setX() accessors are usable on all vars for consistency. It's not good to 
	//mix $u->setPassword($p) and $u->email = $em. mistakes are bound to happen
	public function setPassword($password){
		$this->password = md5($password);
	}
	
	public function authenticate($p){
		return ($this->password == md5($p));
	}
}