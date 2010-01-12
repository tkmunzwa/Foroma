<?php

/**
 * BaseGroupPermission
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $group_id
 * @property integer $permission_id
 * @property Group $Group
 * @property Permission $Permission
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 5845 2009-06-09 07:36:57Z jwage $
 */
abstract class BaseGroupPermission extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('group_permission');
        $this->hasColumn('group_id', 'integer', 4, array(
             'type' => 'integer',
             'primary' => true,
             'length' => '4',
             ));
        $this->hasColumn('permission_id', 'integer', 4, array(
             'type' => 'integer',
             'primary' => true,
             'length' => '4',
             ));
    }

    public function setUp()
    {
        $this->hasOne('Group', array(
             'local' => 'group_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE'));

        $this->hasOne('Permission', array(
             'local' => 'permission_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE'));
    }
}