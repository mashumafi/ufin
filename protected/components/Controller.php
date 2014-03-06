<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/column1';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();
	
	protected function beforeAction($action) {
		if(Yii::app()->user->isGuest) {
			if($this->getId() !== 'site' || $this->action->getId() !== 'index') {
				$this->redirect('/');
			}
			if($_GET['code']) {
				$identity = new OAuthIdentity();
				if($identity->authenticate()) {
					Yii::app()->user->login($identity);
					// clear the token from the browser
					$this->redirect('index');
				} else {
					// TODO: Tell them they do not have access
				}
			}
		}
		return parent::beforeAction($action);
	}
	
	public function getAccount() {
		return Yii::app()->user->isGuest ? null : Account::model()->find('id=?', array(Yii::app()->user->getId()));
	}
	
	private $_role;
	
	public function role() {
		if($account = $this->getAccount()) {
			return ($this->_role = $account->role->description);
		}
	}
	
	public function roleInCourse($courseId) {
		$registrations = Registration::model()->find('accountId=? && courseId=?', array(Yii::app()->user->getId(), $courseId));
		return $registrations->role->description;
	}
	
	public function isInRole($role) {
		if($this->_role) {
			return $this->_role == $role;
		} else {
			$account = null;
			if($account = $this->getAccount()) {
				return ($this->_role = $account->role->description) == $role;
			}
		}
		return false;
	}
	
	public function isInRoles($roles) {
		foreach($roles as $role) {
			if($this->isInRole($role)) {
				return true;
			}
		}
		return false;
	}
	
	public function requireRole($role) {
		if(!$this->isInRole($role)) {
			Yii::app()->end();
		}
	}
	
	public function requireRoles($roles=array()) {
		foreach($roles as $role) {
			if($this->isInRole($role)) {
				return;
			}
		}
		Yii::app()->end();
	}
	
	public function isInRoleForCourse($courseId, $role) {
		$registrations = Registration::model()->find('accountId=? && courseId=?', array(Yii::app()->user->getId(), $courseId));
		return $registrations->role->description == $role;
	}
	
	public function isInRolesForCourse($courseId, $roles) {
		foreach($roles as $role) {
			if($this->isInRoleForCourse($courseId, $role)) {
				return true;
			}
		}
		return false;
	}
	
	public function requireRoleForCourse($courseId, $role) {
		if(!$this->isInRoleForCourse($courseId, $role)) {
			Yii::app()->end();
		}
	}
	
	public function requireRolesForCourse($courseId, $roles) {
		foreach($roles as $role) {
			if($this->isInRoleForCourse($courseId, $role)) {
				return;
			}
		}
		Yii::app()->end();
	}
}