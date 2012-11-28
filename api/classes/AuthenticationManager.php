<?php

	class AuthenticationManager
	{
		public function __construct()
		{

		}
		public function findMatchingCredentials($auth) // Find user with matching credentials
		{
			include('db_connect.php');
			$table_id = $auth->getFbId() % 10;
        		$find_matching_credentials = $dbconn->prepare("SELECT * FROM users".$table_id." WHERE fb_id=:fb_id");
        		$find_matching_credentials->execute( array(":fb_id"=>$auth->getFbId()));
			$credentials = $find_matching_credentials->fetchAll();
			if(count($credentials)) // found matching credentials
			{
				if($credentials[0]['name'] != $auth->getName()) // User has changed their name, update it in db
				{
					include('db_connect.php');
					$table_id = $auth->getFbId() % 10;
					$update_name = $dbconn->prepare("UPDATE users".$table_id." SET name=:name WHERE fb_id=:fb_id");
					$update_name->execute( array(':fb_id' => $auth->getFbId(),':name' => $auth->getName()));
				}
				return new Authentication($credentials[0]['fb_id'],$auth->getName());
			}
			else // didn't find matching credentials, create account and try again
			{
				$this->addAuthenticationCredential($auth);
				$find_matching_credentials->execute( array(":fb_id"=>$auth->getFbId()));
	                        $credentials = $find_matching_credentials->fetchAll();
                        	if(count($credentials)) // found matching credentials
                        	{
					if($credentials[0]['name'] != $auth->getName()) // User has changed their name, update it in db
                                	{
						include('db_connect.php');
                                 		$table_id = $auth->getFbId() % 10;
                                        	$update_name = $dbconn->prepare("UPDATE users".$table_id." SET name=:name WHERE fb_id=:fb_id");
                                        	$update_name->execute( array(':fb_id' => $auth->getFbId(),':name' => $auth->getName()));
                                	}
					return new Authentication($credentials[0]['fb_id'],$auth->getName());
				}
				else
				{
					return NULL;
                        	}
			}
		}
		public function addAuthenticationCredential($auth) // Add user with specified credentials
		{
			include('db_connect.php');
			$table_id = $auth->getFbId() % 10;
                        $add_auth = $dbconn->prepare("INSERT INTO users".$table_id." (name,fb_id) VALUES (:name,:fb_id)");
                        return $add_auth->execute( array(":name"=>$auth->getName(),":fb_id"=>$auth->getFbId()));
		}
	}

?>