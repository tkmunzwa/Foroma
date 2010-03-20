<?php

/**
 * BasePhone
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $number
 * @property integer $phonetype_id
 * @property PhoneType $PhoneType
 * @property Doctrine_Collection $Contact
 * @property Doctrine_Collection $Candidate
 * @property Doctrine_Collection $CandidatePhone
 * @property Doctrine_Collection $ContactPhone
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7380 2010-03-15 21:07:50Z jwage $
 */
abstract class BasePhone extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('phone');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             'length' => '4',
             ));
        $this->hasColumn('number', 'string', 50, array(
             'type' => 'string',
             'length' => '50',
             ));
        $this->hasColumn('phonetype_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('PhoneType', array(
             'local' => 'phonetype_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE'));

        $this->hasMany('Contact', array(
             'refClass' => 'ContactPhone',
             'local' => 'phone_id',
             'foreign' => 'contact_id'));

        $this->hasMany('Candidate', array(
             'refClass' => 'CandidatePhone',
             'local' => 'phone_id',
             'foreign' => 'candidate_id'));

        $this->hasMany('CandidatePhone', array(
             'local' => 'id',
             'foreign' => 'phone_id'));

        $this->hasMany('ContactPhone', array(
             'local' => 'id',
             'foreign' => 'phone_id'));
    }
}