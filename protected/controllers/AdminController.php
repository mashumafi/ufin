<?php
class AdminController extends Controller
{
	public function actionIndex()
	{
		$this->requireRole('Administrator');
		$accounts = Account::model()->findAll();
		$model = array();
		foreach($accounts as $account) {
			$model[] = array('id'=>$account->id, 'email'=>$account->email, 'firstName'=>$account->firstName, 'lastName'=>$account->lastName, 'role'=>$account->role->description);
		}
		$this->render('index',array('model'=>$model));
	}
	
	public function actionCreate()
	{
		$this->requireRole('Administrator');
		$account=new Account;

		if(isset($_POST['Account']))
		{
			$account->attributes=$_POST['Account'];
			if(Oauth::model()->find('email=?', array($account->email)) === null)
			{
				if($account->validate())
				{
					$account->save();
					$oauth = new OAuth();
					$oauth->email = $account->email;
					$oauth->accountId = $account->id;
					if($oauth->validate()) {
						$oauth->save();
						$this->redirect('/account');
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
	
	public function actionRead($id)
	{
		$this->requireRole('Administrator');
		$this->render('read',array('model'=>$model));
	}
	
	public function actionUpdate($id)
	{
		$this->requireRole('Administrator');
		$account=Account::model()->find('id', array($id));

		if(isset($_POST['Account']))
		{
			$account->attributes=$_POST['Account'];
			if($account->validate())
			{
				$account->save();
				$this->redirect('/account');
			}
		}
		$this->render('update',array('account'=>$account));
	}
	
	public function actionDelete($id)
	{
		$this->requireRole('Administrator');
		$this->render('delete',array('model'=>$model));
	}
	
	public function actionLink($id)
	{
		$this->requireRole('Administrator');
		$this->render('link',array('model'=>$model));
	}
	
	public function actionUnlink($id)
	{
		$this->requireRole('Administrator');
		$this->render('unlink',array('model'=>$model));
	}
}
?>