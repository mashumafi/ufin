<?php

class SpreadsheetController extends GoogleController
{
	public function actionCopy()
	{
		require_once 'lib/all.php';
		$GLOBALS['accessToken'] = get_account_token(Yii::app()->user->getId());
		json_header();
		echo Spreadsheet::single(CHttpRequest::getParam('ssid'))->copy(CHttpRequest::getParam('title'))->to_json();
	}

	public function actionCreate()
	{
		require_once 'lib/all.php';
		$GLOBALS['accessToken'] = get_account_token(Yii::app()->user->getId());
		json_header();
		echo Spreadsheet::create(CHttpRequest::getParam('title'))->to_json();
	}

	public function actionDelete()
	{
		require_once 'lib/all.php';
		$GLOBALS['accessToken'] = get_account_token(Yii::app()->user->getId());
		json_header();
		echo Spreadsheet::single(CHttpRequest::getParam('ssid'))->delete()->to_json();
	}

	public function actionDeny()
	{
		require_once 'lib/all.php';
		$GLOBALS['accessToken'] = get_account_token(Yii::app()->user->getId());
		json_header();
		echo Spreadsheet::single(CHttpRequest::getParam('ssid'))->deny(CHttpRequest::getParam('email'))->to_json();
	}

	public function actionIndex()
	{
		require_once 'lib/all.php';
		$GLOBALS['accessToken'] = get_account_token(Yii::app()->user->getId());
		//json_header();
		echo Spreadsheet::feed(CHttpRequest::getParam('title'))->to_json();
	}

	public function actionShare()
	{
		require_once 'lib/all.php';
		$GLOBALS['accessToken'] = get_account_token(Yii::app()->user->getId());
		json_header();
		echo Spreadsheet::single(CHttpRequest::getParam('ssid'))->share(CHttpRequest::getParam('email'))->to_json();
	}

	public function actionShareLink()
	{
	}

	public function actionSingle()
	{
	}

	public function actionUpload()
	{
	}
}