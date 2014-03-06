<?php
require_once 'lib/all.php';
class AssignmentController extends Controller
{
	public function actionIndex($courseId)
	{
		$model = Course::model()->find('id=?', array($courseId));
		$this->render('index', array('model'=>$model));
	}
	
	public function actionDocument($id)
	{
		$assignment = Assignment::model()->find('id=?', array($id));
		$this->redirect("https://spreadsheets.google.com/ccc?key=".$assignment->docId);
	}
	
	public function actionCreate($courseId)
	{
		$model=new Assignment;
		$model->courseId = $courseId;
		
		if(isset($_POST['Assignment']))
		{
			$model->attributes=$_POST['Assignment'];
			$model->authorId = Yii::app()->user->getID();
			if($model->validate())
			{
				$model->save();
				$course = $model->course;
				$emails = array();
				foreach($course->registrations as $registration) {
					if($registration->role->description == 'Student') {
						$aia = new AccountsInAssignment();
						$aia->accountId = $registration->account->id;
						$aia->courseId = $course->id;
						$aia->assignmentId = $model->id;
						$aia->save();
						
						$emails[] = $registration->account->email;
					}
				}
				$GLOBALS['accessToken'] = get_account_token(Yii::app()->user->getId());
				// json_header();
				$ss = Spreadsheet::single($model->docId);
				$feed = $ss->batchCopy($model->title, $emails);
				echo $ss->batchShare($emails, $feed);
				exit();
				$this->redirect('index?courseId='.$model->courseId.'&id='.$model->id);
			}
		}
		$course = Course::model()->find('id=?', array($courseId));
		$this->render('create',array('model'=>$model, 'course'=>$course));
	}
}