<?php
$this->breadcrumbs=array(
	'Course'=>array('/course'),
	"$model->name"=>array('/course/view?id='.$model->id),
	"Assignment"
);?>
<?php
if($this->isInRolesForCourse($model->id, array('Instructor', 'Assistant'))) {
?>
<a href="/assignment/create?courseId=<?php echo $model->id; ?>">Create</a>
<?php
$assignments = array();
foreach($model->assignments as $assignment) {
	$assignments[] = array('id'=>$assignment->id,'title'=>$assignment->title, 'maxGrade'=>$assignment->maxGrade, 'issued'=>$assignment->issued, 'due'=>$assignment->due);
}
$gridDataProvider = new CArrayDataProvider($assignments);
$this->widget('bootstrap.widgets.BootGridView', array(
	'type'=>'striped bordered condensed',
	'dataProvider'=>$gridDataProvider,
	'template'=>"{items}",
	'columns'=>array(
		array('header'=>'Title', 'value'=>'CHtml::link($data[title], "/assignment/document?id=".$data[id], array("target"=>"_blank"))', 'type'=>'raw'),
		array('name'=>'maxGrade', 'header'=>'Max Grade'),
		array('name'=>'issued', 'header'=>'Issued'),
		array('name'=>'due', 'header'=>'Due'),
		array(
			'class'=>'bootstrap.widgets.BootButtonColumn',
			'buttons'=>array(
				'view'=>array(
					'icon'=>'book',
					'label'=>'Document',
					'url'=>'"/assignment/document?id=".$data[id]',
					'options'=>array("target"=>"_blank")
				),
				'update'=>array(
					'visible'=>'false'
				),
				'delete'=>array(
					'visible'=>'false'
				),
			)
		),
	),
));
} else if($this->isInRoleForCourse($model->id, 'Student')) {
?>
<table>
<tr><th>Title</th><th>Grade</th><th>Issued</th><th>Due</th><th>Actions</th></tr>
	<?php foreach(AccountsInAssignment::model()->findAll('accountId=? && course_id=?', array(Yii::app()->user->getID(), $model->id)) as $aia) {?>
	<tr>
		<td>
			<?php echo CHtml::link($aia->assignment->title, 'assignment/view?id='.$aia->doc_id, array('target'=>'_blank')); ?>
		</td>
		<td>
			<?php echo $aia->grade; ?> / <?php echo $aia->assignment->max_grade; ?>
		</td>
		<td>
			<?php echo $aia->assignment->issued; ?>
		</td>
		<td>
			<?php echo $aia->assignment->due; ?>
		</td>
		<td>
			<?php echo CHtml::link('Restart'); ?> <?php echo CHtml::link('Submit'); ?> 
		</td>
	</tr>
<?php
	}
}
?>
</table>