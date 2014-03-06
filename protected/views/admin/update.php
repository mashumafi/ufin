<?php
$this->breadcrumbs=array(
	'Account'=>'/admin',
	'Update'
); ?>
<?php
$form = $this->beginWidget('bootstrap.widgets.BootActiveForm', array(
    'id'=>'horizontalForm',
    'type'=>'horizontal',
)); ?>
 
<fieldset>
 
    <legend>Update Account</legend>
	
	<p class="note">Fields with <span class="required">*</span> are required.</p>
	<?php echo $form->errorSummary($account); ?>
 
    <?php echo $form->uneditableRow($account, 'email'); ?>
    <?php echo $form->textFieldRow($account, 'firstName'); ?>
    <?php echo $form->textFieldRow($account, 'lastName'); ?>
	<?php echo $form->dropDownListRow($account, 'roleId', array('1'=>'Administrator', '2'=>'Instructor', '3'=>'Assistant', '4'=>'Student')); ?>
 
</fieldset>
 
<div class="form-actions">
    <?php $this->widget('bootstrap.widgets.BootButton', array('buttonType'=>'submit', 'type'=>'primary', 'icon'=>'ok white', 'label'=>'Submit')); ?>
    <?php $this->widget('bootstrap.widgets.BootButton', array('buttonType'=>'reset', 'icon'=>'remove', 'label'=>'Reset')); ?>
</div>
 
<?php $this->endWidget(); ?>