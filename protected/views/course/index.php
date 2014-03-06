<?php
$this->breadcrumbs=array(
	'Course'
);
if($this->isInRoles(array('Administrator', 'Instructor'))) {
	echo CHtml::div(CHtml::link('Create', '/course/create'));
	$courses = array();
	foreach($model as $registration) {
		$course = $registration->course;
		$courses[] = array('id'=>$course->id, 'name'=>$course->name, 'referenceNumber'=>$course->referenceNumber, isInstructor=>$this->isInRoleForCourse($course->id, "Instructor"));
	}
	$gridDataProvider = new CArrayDataProvider($courses);
	$this->widget('bootstrap.widgets.BootGridView', array(
		'type'=>'striped bordered condensed',
		'dataProvider'=>$gridDataProvider,
		'template'=>"{items}",
		'columns'=>array(
			array('value'=>'CHtml::link($data[name], "course/view?id=".$data[id])', 'header'=>'Name', 'type'=>'raw'),
			array('name'=>'referenceNumber', 'header'=>'Reference Number'),
			array(
				'class'=>'bootstrap.widgets.BootButtonColumn',
				'buttons'=>array(
					'view'=>array(
						'icon'=>'eye-open',
						'url'=>'"course/view?id=".$data[id]'
					),
					'update'=>array(
						'icon'=>'pencil',
						'label'=>'Edit',
						'url'=>'"course/edit?id=".$data[id]',
						'visible'=>'$data[isInstructor]'
					),
					'delete'=>array(
						'icon'=>'trash',
						'url'=>'"course/delete?id=".$data[id]'
					),
				)
			),
		),
	));
} else {
	foreach($model as $registration) {
		$course = $registration->course;
		echo CHtml::div(CHtml::link($course->name . " ($course->referenceNumber)", "course/view?id=". $course->id));
	}
}

?>
