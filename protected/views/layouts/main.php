<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
	<link href="/css/smoothness/jqueryui.css" type="text/css" rel="stylesheet" />
	<script type="text/javascript" src="/js/jqueryui.js"></script>
</head>
<body>
	<div class="container">
		<!--<div style="text-align:center;">
			<img src="/images/Top.jpg" height="96"/>
		</div>-->
		<?php
			$isGuest = Yii::app()->user->isGuest;
			$this->widget('bootstrap.widgets.BootNavbar', array(
				'fixed'=>true,
				'brand'=>CHtml::encode(Yii::app()->name),
				'brandUrl'=>'/',
				'collapse'=>false, // requires bootstrap-responsive.css
				'items'=>array(
					array(
						'htmlOptions'=>array('class'=>'pull-right'),
						'class'=>'bootstrap.widgets.BootMenu',
						'items'=>array(
							array('label'=>Yii::app()->user->getName(), 'url'=>'#', 'visible'=>!$isGuest, 'items'=>array(
								array('label'=>'Details', 'icon'=>'user', 'url'=>'#'),
								array('label'=>'Logout', 'icon'=>'off', 'url'=>'/site/logout'),
							)),/*
							$this->widget('bootstrap.widgets.BootButtonGroup', array(
								'type'=>'primary',
								'size'=>'mini',
								'buttons'=>array(
									array('label'=>'Account', 'url'=>'#'),
									array('items'=>array(
										array('label'=>'Logout', 'url'=>'#'),
									)),
								),
							))*/
						),
					),
				),
			));
		?>
		<noscript>
			<div class="alert">
				Some features require javascript to be enabled. Please enable it to get the most out of your experience.
			</div>
		</noscript>
		<?php
			$this->widget('bootstrap.widgets.BootBreadcrumbs', array(
				'links'=>$this->breadcrumbs,
			)); 
		?>
		<div class="well">
			<?php echo $content; ?>
		</div>
		<footer style="text-align:center;">
			<!--Copyright &copy; <?php echo date('Y'); ?> by Matthew Murphy.<br/>
			All Rights Reserved.<br/>-->
			<?php echo Yii::powered(); ?>
		</footer>
	</div>
</body>
</html>
