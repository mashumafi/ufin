<?php

class CollectionController extends Controller
{
	public function actionAdd()
	{
		$this->render('add');
	}

	public function actionCreate()
	{
		$this->render('create');
	}

	public function actionCreateSubDir()
	{
		$this->render('createSubDir');
	}

	public function actionDelete()
	{
		$this->render('delete');
	}

	public function actionIndex()
	{
		$this->render('index');
	}

	public function actionRemove()
	{
		$this->render('remove');
	}
}