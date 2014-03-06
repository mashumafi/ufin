<?php
$this->breadcrumbs=array(
	'Create Instructor'
); ?>
<?php
$form = $this->beginWidget('bootstrap.widgets.BootActiveForm', array(
    'id'=>'horizontalForm',
    'type'=>'horizontal',
)); ?>
 
<fieldset>
 
    <legend>Create Instructor</legend>
	
	<p class="note">Fields with <span class="required">*</span> are required.</p>
	<?php echo $form->errorSummary($account); ?>
 
    <?php echo $form->textFieldRow($account, 'email'); ?>
    <?php echo $form->textFieldRow($account, 'firstName'); ?>
    <?php echo $form->textFieldRow($account, 'lastName'); ?>
 
</fieldset>
 
<div class="form-actions">
    <?php $this->widget('bootstrap.widgets.BootButton', array('buttonType'=>'submit', 'type'=>'primary', 'icon'=>'ok white', 'label'=>'Submit')); ?>
    <?php $this->widget('bootstrap.widgets.BootButton', array('buttonType'=>'reset', 'icon'=>'remove', 'label'=>'Reset')); ?>
</div>
 
<?php $this->endWidget(); ?>