<? require_once 'lib/all.php'; ?>
<div>
<?php
	if(Yii::app()->user->isGuest) {
		$GLOBALS['scope'] = 'ALL';
		echo CHtml::div(CHtml::link('Instructor Login', auth_url()));
		$GLOBALS['scope'] = 'EMAIL';
		echo CHtml::div(CHtml::link('Student Login', auth_url()));
	} else {
		if($this->isInRole('Administrator')) {
			echo CHtml::div(CHtml::link('Manage Accounts', '/admin'));
			echo "<hr/>";
			echo CHtml::div(CHtml::link('View Courses', '/course'));
			echo CHtml::div(CHtml::link('Create Course', '/course/create'));
		} else if($this->isInRole('Instructor')) {
			echo CHtml::div(CHtml::link('Create Instructor', '/instructor/create'));
			echo CHtml::div(CHtml::link('Create Assistant', '/assistant/create'));
			echo CHtml::div(CHtml::link('Create Student', '/student/create'));
			echo "<hr/>";
			echo CHtml::div(CHtml::link('View Courses', '/course'));
			echo CHtml::div(CHtml::link('Create Course', '/course/create'));
		} else if($this->isInRole('Assistant')) {
			echo CHtml::div(CHtml::link('Create Student', '/student/create'));
			echo "<hr/>";
			echo CHtml::div(CHtml::link('View Courses', '/course'));
		} else if($this->isInRole('Student')) {
			echo CHtml::div(CHtml::link('View Courses', '/course'));
		} else {
			echo "You are not a valid user for this application.";
		}
	}
?>
</div>