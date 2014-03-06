<?php
$this->breadcrumbs=array(
	'Course'=>array('/course'),
	"{$model->course->name}"=>array('/course/view?id='.$model->courseId),
	'Members'=>array('/course/members?id='.$model->courseId),
	'Remove'
);
?>
<?php
$form = $this->beginWidget('bootstrap.widgets.BootActiveForm', array(
    'id'=>'horizontalForm',
    'type'=>'horizontal',
)); ?>
 
<fieldset>
 
    <legend>Remove Member</legend>
	
	<?php echo $form->errorSummary($model); ?>
 
	<?php echo $form->uneditableRow($model->course, 'name'); ?>
    <?php echo $form->uneditableRow($model->account->oauths[0], 'email'); ?>
 
</fieldset>
 
<div class="form-actions">
    <?php $this->widget('bootstrap.widgets.BootButton', array('buttonType'=>'submit', 'type'=>'primary', 'icon'=>'ok white', 'label'=>'Remove')); ?>
</div>
 
<?php $this->endWidget(); ?>