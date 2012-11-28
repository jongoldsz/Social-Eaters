<?php

	class FriendsQuery extends Query
	{
		public function runCommand($Command)
		{
			if(parent::verifyCommandType($Command)) // Verify if command has matching command type
			{
				switch($Command->getFunction()) // select function and pass parameters
				{
					case "Find":
					     return $this->findFriends($Command->getParameters());
					break;
					default:
					     return new Response(0,'Specified function does not exist.','{"message": "invalid function"}');
					break;
				}
			}
			else
			{
				return new Response(0,'Command passed to Query has a non-matching CommandType.','{"message": "CommandType mismatch"}');
			}
		}
		private function findFriends($parameters)
		{
			include('db_connect.php');
			if(isset($parameters->friends_list))
			{
				/* parse friends list into an array */
				$friends = explode(',',$parameters->friends_list);
	
				/* initialize hash */
				$friends_hash = array();
				for($i = 0; $i < 10; $i++)
				{
					$friends_hash[$i] = array();
				}

				/* split friends array into an array hash */
				foreach ($friends as &$friend)
				{
					$friends_hash[$friend % 10][] = $friend;
				}

				$friend_list = array();
				/* get friend ids */
				for($i = 0; $i < 10; $i++) // iterate through hash
				{
					if(count($friends_hash[$i])) // do we have at least one friend in the hash at key $i?
					{
						/* create sql for WHERE clause of PDO query */
						$sql = 'fb_id=:fb_id0';
						for($j = 1; $j < count($friends_hash[$i]); $j++)
						{
							$sql .= ' OR fb_id=:fb_id'.$j;
						}

						/* put hashed items into an array for PDO execute */
						$exec = array();
						for($j = 0; $j < count($friends_hash[$i]); $j++)
                                                {
							$exec[':fb_id'.$j] = $friends_hash[$i][$j];
                                                }

						/* Query for all friends in table $i*/
						$friend_query = $dbconn->prepare("SELECT * FROM users".$i." WHERE ".$sql);
                                      		$friend_query->execute( $exec);
						$ids = $friend_query->fetchAll();

						/* put table id and friend's id into the friend_ids array */
						foreach($ids as $friend)
						{
							$friend_list[] = new Friend($i, $friend['id'],$friend['fb_id'],$friend['name']);
						}
					}
					else
					{
						return new Response(0,'None of the user\'s friends use this service.','{"message": "no friends found"}');
					}
				}
				$output = '{ "friends": [';
				for($x = 0; $x < count($friend_list); $x++)
				{
					if($x > 0)
					      $output .= ',';
					$output .= $friend_list[$x]->toJson();
				}
				$output .= ']}';
				
				//print_r($friend_list);
				return new Response(1,"Filled event list successfully",$output);
			}
			else
			{
				return new Response(0,'Missing parameters for viewing events.','{"message": "missing parameters"}');
			}
		}
	}

?>