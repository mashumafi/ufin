<?php

class CourseController extends Controller
{
	public function actionIndex()
	{
		$model = Registration::model()->findAll('accountId=?', array(Yii::app()->user->getId()));
		$this->render('index', array('model'=>$model));
	}
	
	public function actionMembers($courseId)
	{
		$model = Course::model()->find('id=?', array($courseId));
		$this->render('members', array('model'=>$model));
	}
	
	public function actionAddAssistant($courseId)
	{
		$this->requireRoleForCourse($courseId, 'Instructor');
		
		$model=new Oauth;

		if(isset($_POST['Oauth']))
		{
			$model->attributes=$_POST['Oauth'];
			$model->accountId = 0;
			if($model->validate())
			{
				$accountId = Oauth::model()->find('email=?',array($model->email))->accountId;
				if($accountId) {
					if(Registration::model()->find('accountId=? && courseId=?', array($accountId, $courseId))) {
						$model->addError('email', 'There is already a member with that email, please remove them first.');
					} else {
						$registration  = new Registration();
						$registration->accountId = $accountId;
						$registration->courseId = $courseId;
						$registration->roleId = 3;
						$registration->registered = date("Y-m-d H:i:s");
						$registration->save();
						$this->redirect('members?courseId='.$courseId);
					}
				} else {
					$model->addError('email', 'There is no account with that email, please create one.');
				}
			}
		}
		$course = Course::model()->find('id=?', array($courseId));
		$this->render('addAssistant',array('model'=>$model, 'course'=>$course));
	}
	
	public function actionAddInstructor($courseId)
	{
		$this->requireRoleForCourse($courseId, 'Instructor');
		
		$model=new Oauth;

		if(isset($_POST['Oauth']))
		{
			$model->attributes=$_POST['Oauth'];
			$model->accountId = 0;
			if($model->validate())
			{
				$accountId = Oauth::model()->find('email=?',array($model->email))->accountId;
				if($accountId) {
					if(Registration::model()->find('accountId=? && courseId=?', array($accountId, $courseId))) {
						$model->addError('email', 'There is already a member with that email, please remove them first.');
					} else {
						$registration  = new Registration();
						$registration->accountId = $accountId;
						$registration->courseId = $courseId;
						$registration->roleId = 2;
						$registration->registered = date("Y-m-d H:i:s");
						$registration->save();
						$this->redirect('members?courseId='.$courseId);
					}
				} else {
					$model->addError('email', 'There is no account with that email, please create one.');
				}
			}
		}
		$course = Course::model()->find('id=?', array($courseId));
		$this->render('addInstructor',array('model'=>$model, 'course'=>$course));
	}
	
	public function actionAddStudent($courseId)
	{
		$this->requireRolesForCourse($courseId, array('Instructor', 'Assistant'));
		
		$model=new Oauth;

		if(isset($_POST['Oauth']))
		{
			$model->attributes=$_POST['Oauth'];
			$model->accountId = 0;
			if($model->validate())
			{
				$accountId = Oauth::model()->find('email=?',array($model->email))->accountId;
				if($accountId) {
					if(Registration::model()->find('accountId=? && courseId=?', array($accountId, $courseId))) {
						$model->addError('email', 'There is already a member with that email, please remove them first.');
					} else {
						$registration  = new Registration();
						$registration->accountId = $accountId;
						$registration->courseId = $courseId;
						$registration->roleId = 4;
						$registration->registered = date("Y-m-d H:i:s");
						$registration->save();
						$this->redirect('members?courseId='.$courseId);
					}
				} else {
					$model->addError('email', 'There is no account with that email, please create one.');
				}
			}
		}
		$course = Course::model()->find('id=?', array($courseId));
		$this->render('addStudent',array('model'=>$model, 'course'=>$course));
	}
	
	public function actionRemoveMember($courseId, $accountId) {
		$this->requireRoleForCourse($courseId, 'Instructor');
		$model= Registration::model()->find('courseId=? && accountId=?', array($courseId, $accountId));

		if(CHttpRequest::getIsPostRequest())
		{
			$model->delete();
			$this->redirect('/course/members?courseId='.$courseId);
		}
		
		$this->render('removeMember',array('model'=>$model));
	}
	
	public function actionView($id)
	{
		$model = Course::model()->find('id=?', array($id));
		$this->render('view', array('model'=>$model));
	}
	
	public function actionCreate()
	{
		$this->requireRoles(array('Administrator', 'Instructor'));
		$model=new Course;

		if(isset($_POST['Course']))
		{
			$model->attributes=$_POST['Course'];
			if($model->validate())
			{
				$model->save();
				$registration = new Registration();
				$registration->accountId = Yii::app()->user->getId();
				$registration->courseId = $model->id;
				$registration->roleId = 2;
				$registration->registered = date("Y-m-d H:i:s");
				if($registration->validate())
				{
					$registration->save();
				}
				$this->redirect('/course');
			}
		}
		$this->render('create',array('model'=>$model));
	}
	
	public function actionEdit($id)
	{
		$this->requireRoleForCourse($id, 'Instructor');
		$model= Course::model()->find('id=?', array($id));

		if(isset($_POST['Course']))
		{
			$model->attributes=$_POST['Course'];
			if($model->validate())
			{
				$model->save();
				$this->redirect('/course');
			}
		}
		$this->render('edit',array('model'=>$model));
	}
	
	public function actionDelete($id)
	{
		$this->requireRoleForCourse($id, 'Instructor');
		$model= Course::model()->find('id=?', array($id));

		if(CHttpRequest::getIsPostRequest())
		{
			$model->delete();
			$this->redirect('/course');
		}
		
		$this->render('delete',array('model'=>$model));
	}
}