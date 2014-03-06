<?php
$this->breadcrumbs=array(
	'Course'=>array('/course'),
	"$course->name"=>array('/course/view?id='.$course->id),
	"Members"=>array('/course/members?courseId='.$course->id),
	'Add Student',
);
?>
<?php
$form = $this->beginWidget('bootstrap.widgets.BootActiveForm', array(
    'id'=>'horizontalForm',
    'type'=>'horizontal',
)); ?>
 
<fieldset>
 
    <legend>Add Student</legend>
	
	<p class="note">Fields with <span class="required">*</span> are required.</p>
	<?php echo $form->errorSummary($model); ?>
 
    <?php echo $form->textFieldRow($model, 'email', array('hint'=>'You must add them at ' . CHtml::link('Create Student', '/student/create?returnUrl='.urlencode('/course/addStudent?courseId='.$course->id)) . ' first.')); ?>
</fieldset>
 
<div class="form-actions">
    <?php $this->widget('bootstrap.widgets.BootButton', array('buttonType'=>'submit', 'type'=>'primary', 'icon'=>'ok white', 'label'=>'Submit')); ?>
    <?php $this->widget('bootstrap.widgets.BootButton', array('buttonType'=>'reset', 'icon'=>'remove', 'label'=>'Reset')); ?>
</div>
 
<?php $this->endWidget(); ?>