<?php
$this->breadcrumbs=array(
	'Course'=>array('/course'),
	$model->name,
);
?>
<h3>
<?php echo $model->name; ?> (<?php echo $model->referenceNumber; ?>)
</h3>
<?php echo CHtml::div(CHtml::link('Members', 'members?courseId='.$model->id)); ?>
<?php echo CHtml::div(CHtml::link('Assignments', '/assignment?courseId='.$model->id)); ?>