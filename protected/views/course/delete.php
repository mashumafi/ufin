<?php
$this->breadcrumbs=array(
	'Course'=>array('/course'),
	'Delete',
);
?>
<?php
$form = $this->beginWidget('bootstrap.widgets.BootActiveForm', array(
    'id'=>'horizontalForm',
    'type'=>'horizontal',
)); ?>
 
<fieldset>
 
    <legend>Delete Course</legend>
	
	<?php echo $form->errorSummary($model); ?>
 
    <?php echo $form->uneditableRow($model, 'name'); ?>
    <?php echo $form->uneditableRow($model, 'referenceNumber'); ?>
 
</fieldset>
 
<div class="form-actions">
    <?php $this->widget('bootstrap.widgets.BootButton', array('buttonType'=>'submit', 'type'=>'primary', 'icon'=>'ok white', 'label'=>'Delete')); ?>
</div>
 
<?php $this->endWidget(); ?>