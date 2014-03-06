<?php

class StudentController extends Controller
{
	public function actionCreate()
	{
		$this->requireRoles(array('Administrator', 'Instructor', 'Assistant'));
		$account=new Account;

		if(isset($_POST['Account']))
		{
			$account->attributes=$_POST['Account'];
			if(Oauth::model()->find('email=?', array($account->email)) === null)
			{
				if($account->validate())
				{
					$account->roleId = 4;
					$account->save();
					$oauth = new OAuth();
					$oauth->email = $account->email;
					$oauth->accountId = $account->id;
					if($oauth->validate()) {
						$oauth->save();
						$this->redirect(CHttpRequest::getQuery('returnUrl', '/'));
					} else {
						$account->delete();
					}
				}
			} else {
				$oauth->addError('email', 'That email is already registered');
			}
		}
		$this->render('create',array('oauth'=>$oauth, 'account'=>$account));
	}
}