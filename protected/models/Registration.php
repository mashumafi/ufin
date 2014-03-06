<?php

/**
 * This is the model class for table "registration".
 *
 * The followings are the available columns in table 'registration':
 * @property integer $accountId
 * @property integer $courseId
 * @property integer $roleId
 * @property string $registered
 *
 * The followings are the available model relations:
 * @property Account $account
 * @property Course $course
 * @property Role $role
 */
class Registration extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Registration the static model class
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
		return 'registration';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('accountId, courseId, roleId, registered', 'required'),
			array('accountId, courseId, roleId', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('accountId, courseId, roleId, registered', 'safe', 'on'=>'search'),
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
			'account' => array(self::BELONGS_TO, 'Account', 'accountId'),
			'course' => array(self::BELONGS_TO, 'Course', 'courseId'),
			'role' => array(self::BELONGS_TO, 'Role', 'roleId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'accountId' => 'Account',
			'courseId' => 'Course',
			'roleId' => 'Role',
			'registered' => 'Registered',
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

		$criteria->compare('accountId',$this->accountId);
		$criteria->compare('courseId',$this->courseId);
		$criteria->compare('roleId',$this->roleId);
		$criteria->compare('registered',$this->registered,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}