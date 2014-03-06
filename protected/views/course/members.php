<?php
$this->breadcrumbs=array(
	'Course'=>array('/course'),
	"$model->name"=>array('/course/view?id='.$model->id),
	'Members',
);
$instructor = $this->isInRoleForCourse($model->id, 'Instructor');
$assistant = $this->isInRoleForCourse($model->id, 'Assistant');
if($instructor) {
	echo CHtml::div(CHtml::link('Add Instructor', 'addInstructor?courseId='.$model->id));
	echo CHtml::div(CHtml::link('Add Assistant', 'addAssistant?courseId='.$model->id));
}
if($instructor || $assistant) {
	echo CHtml::div(CHtml::link('Add Student', 'addStudent?courseId='.$model->id));
}
?>
<?php
$members = array();
$isInstructor = $this->isInRoleForCourse($model->id, 'Instructor');
foreach($model->registrations as $registration) {
	$account = $registration->account;
	$members[] = array('id'=>$registration->accountId, 'email'=>$account->email, 'firstName'=>$account->firstName, 'lastName'=>$account->lastName, 'role'=>$registration->role->description);
}
$gridDataProvider = new CArrayDataProvider($members);
$this->widget('bootstrap.widgets.BootGridView', array(
    'type'=>'striped bordered condensed',
    'dataProvider'=>$gridDataProvider,
    'template'=>"{items}",
    'columns'=>array(
        array('name'=>'email', 'header'=>'Email'),
        array('name'=>'firstName', 'header'=>'First Name'),
        array('name'=>'lastName', 'header'=>'Last Name'),
        array('name'=>'role', 'header'=>'Role'),
        array(
            'class'=>'bootstrap.widgets.BootButtonColumn',
			'buttons'=>array(
				'view'=>array(
					'visible'=>'false'
				),
				'update'=>array(
					'visible'=>'false'
				),
				'delete'=>array(
					'icon'=>'remove',
					'label'=>'Remove',
					'url'=>'"/course/removeMember?courseId='.$registration->courseId.'&accountId=$data[id]"',
					'visible'=>'$data[id] !== Yii::app()->user->getId() && ' . $isInstructor
				),
			)
        ),
    ),
));
?>