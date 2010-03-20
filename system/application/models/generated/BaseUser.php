<?php

/**
 * BaseUser
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $firstname
 * @property string $surname
 * @property string $emailaddress
 * @property string $language
 * @property boolean $locked_out
 * @property Doctrine_Collection $Groups
 * @property Language $Language
 * @property Doctrine_Collection $UserGroup
 * @property Doctrine_Collection $LoginActivity
 * @property Doctrine_Collection $Article
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7380 2010-03-15 21:07:50Z jwage $
 */
abstract class BaseUser extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('tblUsers');
        $this->hasColumn('id', 'integer', 4, array(
             'primary' => true,
             'autoincrement' => true,
             'type' => 'integer',
             'length' => '4',
             ));
        $this->hasColumn('username', 'string', 255, array(
             'type' => 'string',
             'unique' => true,
             'notnull' => true,
             'length' => '255',
             ));
        $this->hasColumn('password', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('firstname', 'string', 50, array(
             'type' => 'string',
             'length' => '50',
             ));
        $this->hasColumn('surname', 'string', 50, array(
             'type' => 'string',
             'length' => '50',
             ));
        $this->hasColumn('emailaddress', 'string', 255, array(
             'type' => 'string',
             'email' => true,
             'length' => '255',
             ));
        $this->hasColumn('language', 'string', 30, array(
             'type' => 'string',
             'length' => '30',
             ));
        $this->hasColumn('locked_out', 'boolean', null, array(
             'type' => 'boolean',
             'default' => false,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('Group as Groups', array(
             'refClass' => 'UserGroup',
             'local' => 'user_id',
             'foreign' => 'group_id'));

        $this->hasOne('Language', array(
             'local' => 'language',
             'foreign' => 'name'));

        $this->hasMany('UserGroup', array(
             'local' => 'id',
             'foreign' => 'user_id'));

        $this->hasMany('LoginActivity', array(
             'local' => 'id',
             'foreign' => 'user_id'));

        $this->hasMany('Article', array(
             'local' => 'id',
             'foreign' => 'author_id'));
    }
}