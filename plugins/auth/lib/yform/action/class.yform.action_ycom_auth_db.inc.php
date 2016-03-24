<?php

class rex_yform_action_ycom_auth_db extends rex_yform_action_abstract
{

	function execute()
	{
		
		$user = rex_ycom_auth::getUser();
		if (!rex::isBackend() && !$user) {

			echo "error - access denied - user not logged in";
			
		} else {

			switch($this->getElement(2)){

				case("logout"):
					rex_ycom_auth::unsetUser();
					unset($_COOKIE['ycomrex_auth']);
					break;

				case("delete"):
					rex_ycom_auth::deleteUser($user->getValue("id"));
					rex_ycom_auth::clearUserSession();
					break;

				case("update"):
				default:
					$sql = rex_sql::factory();
					if ($this->params["debug"]) {
						$sql->debugsql = TRUE;
					}

					$sql->setTable("rex_ycom_user");
					foreach($this->params["value_pool"]["sql"] as $key => $value) {
						
						$sql->setValue($key, $value);
					}
					$password = $sql->getValue('password');
					if ($password!=""){
						$salt_value = $user->getValue('salt');
						$hash_value = rex_ycom_auth::getHash($password,$salt_value);
						$sql->setValue('password_hash', $hash_value);
						$sql->setValue('password',"");
					}
					$sql->setWhere('id='.$user->getValue("id").'');
					$sql->update();
					break;
			}

		}

	}

	function getDescription()
	{
		return "action|com_auth_db|update(default)/delete/logout";
	}

}

?>
