<?php

/**
 * This is the model class for table "accountsInAssignment".
 *
 * The followings are the available columns in table 'accountsInAssignment':
 * @property integer $accountId
 * @property integer $courseId
 * @property integer $assignmentId
 * @property string $docId
 * @property integer $grade
 * @property string $submitted
 *
 * The followings are the available model relations:
 * @property Account $account
 * @property Assignment $course
 * @property Assignment $assignment
 */
class AccountsInAssignment extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return AccountsInAssignment the static model class
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
		return 'accountsInAssignment';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('accountId, courseId, assignmentId, docId, grade', 'required'),
			array('accountId, courseId, assignmentId, grade', 'numerical', 'integerOnly'=>true),
			array('docId', 'length', 'max'=>60),
			array('submitted', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('accountId, courseId, assignmentId, docId, grade, submitted', 'safe', 'on'=>'search'),
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
			'course' => array(self::BELONGS_TO, 'Assignment', 'courseId'),
			'assignment' => array(self::BELONGS_TO, 'Assignment', 'assignmentId'),
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
			'assignmentId' => 'Assignment',
			'docId' => 'Doc',
			'grade' => 'Grade',
			'submitted' => 'Submitted',
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
		$criteria->compare('assignmentId',$this->assignmentId);
		$criteria->compare('docId',$this->docId,true);
		$criteria->compare('grade',$this->grade);
		$criteria->compare('submitted',$this->submitted,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}