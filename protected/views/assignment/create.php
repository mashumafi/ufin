<?php
$this->breadcrumbs=array(
	'Course'=>array('/course'),
	"$course->name"=>array('/course/view?id='.$course->id),
	"Assignment"=>array('index?courseId='.$course->id),
	'Create',
);
?>
<?php
$form = $this->beginWidget('bootstrap.widgets.BootActiveForm', array(
    'id'=>'horizontalForm',
    'type'=>'horizontal',
)); ?>
 
<fieldset>
 
    <legend>Create Assignment</legend>
	
	<p class="note">Fields with <span class="required">*</span> are required.</p>
	<?php
		echo $form->errorSummary($model);
 
		echo $form->hiddenField($model, 'docId');
		echo $form->textFieldRow($model, 'title', array('hint'=>'Click ' . CHtml::link('Here', '#', array('id'=>'createdoc')) . ' to create a document with this name or just go to your ' . CHtml::link('Documents', 'https://docs.google.com', array('target'=>'_blank'))));
		echo CHtml::div('', array('id'=>'progress', 'style'=>'display:none;'));
		echo $form->textFieldRow($model, 'maxGrade');
		echo $form->textFieldRow($model, 'issued');
		echo $form->textFieldRow($model, 'due');
	?>
</fieldset>
 
<div class="form-actions">
    <?php $this->widget('bootstrap.widgets.BootButton', array('buttonType'=>'submit', 'type'=>'primary', 'icon'=>'ok white', 'label'=>'Submit')); ?>
    <?php $this->widget('bootstrap.widgets.BootButton', array('buttonType'=>'reset', 'icon'=>'remove', 'label'=>'Reset')); ?>
</div>
 
<?php $this->endWidget(); ?>

<script type="text/javascript">
	$(document).ready(function() {
		$('#Assignment_issued').datepicker({dateFormat: 'yy-mm-dd'});
		$('#Assignment_due').datepicker({dateFormat: 'yy-mm-dd'});
		function onshow() {
			if($(this).attr("id") == $step2.attr("id")) {
				sscache = {};
				sslastXhr = null;
			} else if($(this).attr("id") == $step3.attr("id")) {
				wscache = {};
				wslastXhr = null;
			}
		}
		var sscache = {}, sslastXhr;
		var id = "#Assignment_title", $progress = $('#progress');
		$(id).autocomplete({
			minLength: 0,
			select: function(event, ui) {
				$(id).val(ui.item.title);
				$('#Assignment_docId').val(ui.item.id.split('/')[5]);
				return false;
			},
			source: function(request, response) {
				var term = request.term;
				if (term in sscache) {
					response(sscache[term]);
					return;
				}
				sslastXhr = $.post("/spreadsheet/index", {title: request.term}, function(data, status, xhr) {
					sscache[ term ] = data;
					if (xhr === sslastXhr) {
						response(data);
					}
				}, 'json');
			}
		}).focus(function() {
			$(this).trigger('keydown.autocomplete');
		}).data("autocomplete")._renderItem = function(ul, item) {
			return $( "<li></li>" )
				.data( "item.autocomplete", item)
				.append( "<a>" + item.title + "</a>" )
				.appendTo(ul);
		};
		$('#createdoc').click(function(evt) {
			$progress.html('').attr('class', 'controls');
			if($(id).val()) {
				$progress.html('<img src="http://ajaxload.info/cache/FF/FF/FF/00/00/00/1-1.gif" alt="loading"/>');
				$.post("/spreadsheet/create", {title: $(id).val()}, function(data, status, xhr) {
					if($(id).val() === data.title) {
						$('#Assignment_docId').val(data.id.split('%3A')[1]);
						$progress.html('Successfully created "' + data.title + '".').addClass('alert alert-success');
					}
				}, 'json');
			} else {
				$progress.html('You must provide a title.').addClass('controls alert alert-error');
			}
			$progress.css('display', 'block');
			evt.preventDefault();
		});
	});
</script>