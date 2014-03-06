<?php
$this->breadcrumbs=array(
	'Course'=>array('/course'),
	'Create',
);
?>
<?php
$form = $this->beginWidget('bootstrap.widgets.BootActiveForm', array(
    'id'=>'horizontalForm',
    'type'=>'horizontal',
)); ?>
 
<fieldset>
 
    <legend>Create Course</legend>
	
	<p class="note">Fields with <span class="required">*</span> are required.</p>
	<?php echo $form->errorSummary($model); ?>
 
    <?php echo $form->textFieldRow($model, 'name'); ?>
    <?php echo $form->textFieldRow($model, 'referenceNumber'); ?>
 
</fieldset>
 
<div class="form-actions">
    <?php $this->widget('bootstrap.widgets.BootButton', array('buttonType'=>'submit', 'type'=>'primary', 'icon'=>'ok white', 'label'=>'Create')); ?>
    <?php $this->widget('bootstrap.widgets.BootButton', array('buttonType'=>'reset', 'icon'=>'remove', 'label'=>'Reset')); ?>
</div>
 
<?php $this->endWidget(); ?>