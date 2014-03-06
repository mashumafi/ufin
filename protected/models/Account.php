<?php

/**
 * This is the model class for table "account".
 *
 * The followings are the available columns in table 'account':
 * @property integer $id
 * @property integer $roleId
 * @property string $firstName
 * @property string $lastName
 * @property string $accessToken
 * @property string $refreshToken
 * @property string $expires
 *
 * The followings are the available model relations:
 * @property Role $role
 * @property AccountsInAssignment[] $accountsInAssignments
 * @property Assignment[] $assignments
 * @property Oauth[] $oauths
 * @property Registration[] $registrations
 * @property Status[] $statuses
 */
class Account extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Account the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'account';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('firstName, lastName', 'required'),
			array('roleId', 'numerical', 'integerOnly'=>true),
			array('firstName, lastName', 'length', 'max'=>20),
			array('accessToken, refreshToken', 'length', 'max'=>60),
			array('expires', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, roleId, firstName, lastName, accessToken, refreshToken, expires', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'role' => array(self::BELONGS_TO, 'Role', 'roleId'),
			'accountsInAssignments' => array(self::HAS_MANY, 'AccountsInAssignment', 'accountId'),
			'assignments' => array(self::HAS_MANY, 'Assignment', 'authorId'),
			'oauths' => array(self::HAS_MANY, 'Oauth', 'accountId'),
			'registrations' => array(self::HAS_MANY, 'Registration', 'accountId'),
			'statuses' => array(self::HAS_MANY, 'Status', 'accountId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'roleId' => 'Role',
			'firstName' => 'First Name',
			'lastName' => 'Last Name',
			'accessToken' => 'Access Token',
			'refreshToken' => 'Refresh Token',
			'expires' => 'Expires',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('roleId',$this->roleId);
		$criteria->compare('firstName',$this->firstName,true);
		$criteria->compare('lastName',$this->lastName,true);
		$criteria->compare('accessToken',$this->accessToken,true);
		$criteria->compare('refreshToken',$this->refreshToken,true);
		$criteria->compare('expires',$this->expires,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}