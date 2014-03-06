<?php

class WorksheetController extends Controller
{
	public function actionCreate()
	{
		$this->render('create');
	}

	public function actionDelete()
	{
		$this->render('delete');
	}

	public function actionIndex()
	{
		$this->render('index');
	}

	public function actionRename()
	{
		$this->render('rename');
	}

	public function actionSingle()
	{
		$this->render('single');
	}
}