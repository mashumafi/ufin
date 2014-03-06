<?php

/**
 * This is the model class for table "assignment".
 *
 * The followings are the available columns in table 'assignment':
 * @property integer $id
 * @property integer $authorId
 * @property integer $courseId
 * @property string $title
 * @property string $docId
 * @property integer $maxGrade
 * @property string $issued
 * @property string $due
 *
 * The followings are the available model relations:
 * @property AccountsInAssignment[] $accountsInAssignments
 * @property AccountsInAssignment[] $accountsInAssignments1
 * @property Account $author
 * @property Course $course
 */
class Assignment extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Assignment the static model class
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
		return 'assignment';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('authorId, courseId, title, docId, maxGrade, issued, due', 'required'),
			array('authorId, courseId, maxGrade', 'numerical', 'integerOnly'=>true),
			array('title', 'length', 'max'=>20),
			array('docId', 'length', 'max'=>60),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, authorId, courseId, title, docId, maxGrade, issued, due', 'safe', 'on'=>'search'),
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
			'accountsInAssignments' => array(self::HAS_MANY, 'AccountsInAssignment', 'courseId'),
			'accountsInAssignments1' => array(self::HAS_MANY, 'AccountsInAssignment', 'assignmentId'),
			'author' => array(self::BELONGS_TO, 'Account', 'authorId'),
			'course' => array(self::BELONGS_TO, 'Course', 'courseId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'authorId' => 'Author',
			'courseId' => 'Course',
			'title' => 'Title',
			'docId' => 'Doc',
			'maxGrade' => 'Max Grade',
			'issued' => 'Issued',
			'due' => 'Due',
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
		$criteria->compare('authorId',$this->authorId);
		$criteria->compare('courseId',$this->courseId);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('docId',$this->docId,true);
		$criteria->compare('maxGrade',$this->maxGrade);
		$criteria->compare('issued',$this->issued,true);
		$criteria->compare('due',$this->due,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}