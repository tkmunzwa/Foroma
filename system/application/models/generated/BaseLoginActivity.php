<?php

/**
 * BaseLoginActivity
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $user_id
 * @property string $event
 * @property timestamp $time
 * @property string $host
 * @property string $ipaddress
 * @property User $User
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7380 2010-03-15 21:07:50Z jwage $
 */
abstract class BaseLoginActivity extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('login_activity');
        $this->hasColumn('user_id', 'integer', 4, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => '4',
             ));
        $this->hasColumn('event', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('time', 'timestamp', null, array(
             'type' => 'timestamp',
             ));
        $this->hasColumn('host', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('ipaddress', 'string', 30, array(
             'type' => 'string',
             'length' => '30',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('User', array(
             'local' => 'user_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE'));
    }
}