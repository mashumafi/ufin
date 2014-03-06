<?php
require_once 'lib/all.php';

class OAuthIdentity implements IUserIdentity
{
	private $_id;
	private $_name;
	
	function authenticate() {
		$token = upgradeToken($_GET['code']);
		$session = oauthSession($token);
		if($model = Oauth::model()->findByAttributes(array('email'=>$session['email']))) {
			$this->_id = $model->accountId;
			$this->_name = $model->email;
			$account = $model->account;
			$account->accessToken = $session['access_token'];
			$account->refreshToken = $session['refresh_token'];
			$account->expires = time() + $session['expires_in'];
			$account->email = $session['email'];
			$account->save();
			return $model != null;
		}
		return false;
	}
	
	function getIsAuthenticated() {
		return $this->_id > 0;
	}
	
	function getPersistentStates() {
	}
	
	function getId() {
		return $this->_id;
	}
	
	function getName() {
		return $this->_name;
	}
}
?>