<?php

class CellController extends Controller
{
	public function actionIndex()
	{
		$this->render('index');
	}

	public function actionUpdate()
	{
		$this->render('update');
	}
}