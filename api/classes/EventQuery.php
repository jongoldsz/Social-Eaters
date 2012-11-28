<?php

	class EventQuery extends Query
	{
		public function runCommand($Command)
		{
			if(parent::verifyCommandType($Command)) // Verify if command has matching command type
			{
				switch($Command->getFunction()) // select function and pass parameters
				{
					case "Join":
					     return $this->joinEvent($Command->getParameters());
					break;
					case "View":
					     return $this->viewEvents($Command->getParameters());
					break;
					case "Create":
					     return $this->createEvent($Command->getParameters());
					break;
					case "Delete":
 					     return $this->deleteEvent($Command->getParameters());
                    break;
					case "Update":
						 return $this->updateEvent($Command->getParameters());
					break;
					case "ViewAttendees":
						return $this->viewAttendees($Command->getParameters());
					break;
					case "ViewAttending":
						return $this->viewAttending($Command->getParameters());
					break;
					case "Leave":
						return $this->leaveEvent($Command->getParameters());
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
		private function createEvent($parameters)
                {
			// check if proper parameters are set
                        if(isset($parameters->event_name) && isset($parameters->location) && isset($parameters->address1) && isset($parameters->address2) && isset($parameters->date) && isset($parameters->time))
			{
				include('db_connect.php');
				
				/* Find user's table and id */
                        	$table_id = $parameters->fb_id % 10;
                        	$find_self = $dbconn->prepare("SELECT * FROM users".$table_id." WHERE fb_id=:fb_id");
                        	$find_self->execute( array(":fb_id"=>$parameters->fb_id));
                        	$self = $find_self->fetchAll();
                        	$self = $self[0];
                        	$create_event = $dbconn->prepare("INSERT INTO events (creator_table,creator_id,event_name,location,address1,address2,date,time) VALUES (:creator_table,:creator_id,:event_name,:location,:address1,:address2,:date,:time)");
                        	if($create_event->execute( array(":creator_table"=>$table_id,":creator_id"=>$self['id'],":event_name"=>$parameters->event_name,":location"=>$parameters->location,":address1"=>$parameters->address1,":address2"=>$parameters->address2,":date"=>$parameters->date,":time"=>$parameters->time)))
				{
					return new Response(1,'Created event successfully.','{"message": "event created"}');
				}
				else
				{
					return new Response(0,'Failed to create event.','{"message": "event creation failed"}');
				}
			}
			else
			{
				return new Response(0,'Missing parameters for creating an event.','{"message": "missing parameters"}');
			}
		}
		private function deleteEvent($parameters)
                {
			// check if proper parameters are set
                        if(isset($parameters->event_id))
                        {
                                include('db_connect.php');

                                /* Find user's table and id */
                                $table_id = $parameters->fb_id % 10;
                                $find_self = $dbconn->prepare("SELECT * FROM users".$table_id." WHERE fb_id=:fb_id");
                                $find_self->execute( array(":fb_id"=>$parameters->fb_id));
                                $self = $find_self->fetchAll();
                                $self = $self[0];
                                $create_event = $dbconn->prepare("DELETE FROM events WHERE id=:event_id AND creator_table=:creator_table AND creator_id=:creator_id");
                                if($create_event->execute( array(":event_id"=>$parameters->event_id,":creator_table"=>$table_id,":creator_id"=>$self['id'])))
                        	{
					$remove_attendees = $dbconn->prepare("DELETE FROM event_attendance WHERE event_id=:event_id");
					$remove_attendees->execute( array(":event_id"=>$parameters->event_id));
					return new Response(1,'Deleted event successfully.','{"message": "event deleted"}');
                        	}
				else
				{
					return new Response(0,'Failed to delete the specified event.','{"message": "event deletion failed"}');
				}
			}
                        else
                        {
				return new Response(0,'Missing parameters for deleting an event.','{"message": "missing parameters"}');
                        }
                }
		private function updateEvent($parameters)
                {
			// check if proper parameters are set
                        if(isset($parameters->event_id) && isset($parameters->event_name) && isset($parameters->location) && isset($parameters->address1) && isset($parameters->address2) && isset($parameters->date) && isset($parameters->time))
                        {
                                include('db_connect.php');

                                /* Find user's table and id */
                                $table_id = $parameters->fb_id % 10;
                                $find_self = $dbconn->prepare("SELECT * FROM users".$table_id." WHERE fb_id=:fb_id");
                                $find_self->execute( array(":fb_id"=>$parameters->fb_id));
                                $self = $find_self->fetchAll();
                                $self = $self[0];
                                $create_event = $dbconn->prepare("UPDATE events SET event_name=:event_name,location=:location,address1=:address1,address2=:address2,date=:date,time=:time WHERE id=:event_id AND creator_table=:creator_table AND creator_id=:creator_id");
                                if($create_event->execute( array(":event_name"=>$parameters->event_name,":location"=>$parameters->location,":address1"=>$parameters->address1,":address2"=>$parameters->address2,":date"=>$parameters->date,":time"=>$parameters->time,":event_id"=>$parameters->event_id,":creator_table"=>$table_id,":creator_id"=>$self['id'])))
                                {
                                        return new Response(1,'Updated event successfully.','{"message": "event updated"}');
                                }
                                else
                                {
                                        return new Response(0,'Failed to update the specified event.','{"message": "event update failed"}');
                                }
                        }
                        else
                        {
                                return new Response(0,'Missing parameters for updating an event.','{"message": "missing parameters"}');
                        }
                }
		private function joinEvent($parameters)
		{
			// check if proper parameters are set
			if(isset($parameters->event_id))
			{
				include('db_connect.php');

				/* Find user's table and id */
                                $table_id = $parameters->fb_id % 10;
                                $find_self = $dbconn->prepare("SELECT * FROM users".$table_id." WHERE fb_id=:fb_id");
                                $find_self->execute( array(":fb_id"=>$parameters->fb_id));
                                $self = $find_self->fetchAll();
                                $self = $self[0];

				// Check to see if user is already attending event
				$find_user_attendance = $dbconn->prepare("SELECT * FROM event_attendance WHERE event_id=:event_id AND user_table=:user_table AND user_id=:user_id");
				$find_user_attendance->execute( array(":event_id"=>$parameters->event_id,":user_table"=>$table_id,":user_id"=>$self['id']));
				$user_attendance = $find_user_attendance->fetchAll();
				
				if(count($user_attendance) == 0) // User needs to be added to event
				{
					$join_event = $dbconn->prepare("INSERT INTO event_attendance (event_id,user_table,user_id) VALUES (:event_id,:user_table,:user_id)");
        				$join_event->execute( array(":event_id"=>$parameters->event_id,":user_table"=>$table_id,":user_id"=>$self['id']));
					return new Response(1,'User successfully added to event.','{"message": "successfully added"}');
				}
				else // User is already attending the event
				{
					return new Response(0,'User already attending event.','{"message": "already attending"}');
				}
			}
			else
			{
				return new Response(0,'Missing parameters for adding user to event.','{"message": "missing parameters"}');
			}
		}
		private function viewEvents($parameters)
		{
			if(isset($parameters->by))
			{
				include('db_connect.php');
				
				$find_events;				

				if($parameters->by == 'self')
				{
					/* Find user's table and id */
					$table_id = $parameters->fb_id % 10;
					$find_self = $dbconn->prepare("SELECT * FROM users".$table_id." WHERE fb_id=:fb_id");
					$find_self->execute( array(":fb_id"=>$parameters->fb_id));
					$self = $find_self->fetchAll();
					$self = $self[0];

					/* Find corresponding events */
					$find_events = $dbconn->prepare("SELECT * FROM events WHERE creator_table=:creator_table AND creator_id=:creator_id ORDER BY date ASC");
					$find_events->execute( array(":creator_table"=>$table_id,":creator_id"=>$self['id']));
				}
				else if($parameters->by == 'friends')
				{
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

						$friend_ids = array();
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
								foreach($ids as $friend_id)
								{
									$friend_ids[] = array("id"=>$friend_id['id'],"table_id"=>$i);
								}
							}
						}

						if(count($friend_ids) > 0)
						{
							/* create sql for WHERE clause of PDO query */
                                                        $sql = '(creator_table=:creator_table0 AND creator_id=:creator_id0)';
                                                        for($j = 1; $j < count($friend_ids); $j++)
                                                        {
                                                                $sql .= ' OR (creator_table=:creator_table'.$j.' AND creator_id=:creator_id'.$j.')';
                                                        }

                                                        /* put hashed items into an array for PDO execute */
                                                        $exec = array();
                                                        for($j = 0; $j < count($friend_ids); $j++)
                                                        {
                                                                $exec[':creator_table'.$j] = $friend_ids[$j]['table_id'];
								$exec[':creator_id'.$j] = $friend_ids[$j]['id'];
                                                        }
							
							/* fetch all events by friends */
							$find_events = $dbconn->prepare("SELECT * FROM events WHERE ".$sql);
							$find_events->execute( $exec);
						}
						else
						{
							return new Response(0,'None of the user\'s friends use this service.','{"message": "no friends found"}');
						}
					}
					else
					{
						return new Response(0,'Missing parameters for viewing events.','{"message": "missing parameters"}');
					}
				}

				// Fill an event array with all corresponding events
        			$events = $find_events->fetchAll();
				$event_list = array();
        			for($i = 0; $i < count($events); $i++)
        			{
					$event = $events[$i];
                			$event_list[$i] = new event($event['id'],$event['event_name'],$event['creator_table'],$event['creator_id'],$event['location'],$event['address1'],$event['address2'],$event['time'],$event['date']);
        			}

				// Fill output with JSON of event array
				$output = '{ "events": [';
				for($i = 0; $i < count($event_list); $i++)
        			{
				      if($i > 0)
                      		      	    $output .= ',';
                		      $output .= $event_list[$i]->toJson();
        			}
        			$output .= '] }';
			
				return new Response(1,"Filled event list successfully",$output);
			}
			else
			{
				return new Response(0,'Missing parameters for viewing events.','{"message": "missing parameters"}');
			}
		}
		private function viewAttendees($parameters)
		{
			if(isset($parameters->event_id))
			{
				include('db_connect.php');
				
				$find_event;
				$find_attendees;				

				/* Find event id */
				$event_id = $parameters->event_id;
				
				/* Find attendees */
				$find_attendees = $dbconn->prepare("SELECT user_table, user_id FROM event_attendance WHERE event_id=:event_id");
				$find_attendees->execute( array(":event_id"=>$event_id) );
				$attendees = $find_attendees->fetchAll();
				
				// Fill output with JSON of event array
				$output = '{"attendees":[';
				for($i = 0; $i < count($attendees); $i++)
        			{
				      if($i > 0)
						$output .= ',';
					$output .= '{ "user_table": "' . $attendees[$i]['user_table'] . '", "user_id": "' . $attendees[$i]['user_id'] . '"}';
        			}
        			$output .= ']}';
				
				return new Response(1,"Collected attendees list successfully",$output);
			}
			else
			{
				return new Response(0,'Missing parameters for viewing attendees.','{"message": "missing parameters"}');
			}
		}
		private function viewAttending($parameters)
		{
			include('db_connect.php');
			
			$find_events;			

			/* Find user id and table id */
			$fb_id = $parameters->fb_id;
			$table_id = $parameters->fb_id % 10;
			
			$find_user_id = $dbconn->prepare("SELECT * FROM users".$table_id." WHERE fb_id=:fb_id");
			$find_user_id->execute( array(":fb_id"=>$fb_id) );
			$user_id = $find_user_id->fetchAll();
			$user_id = $user_id[0]['id'];
			
			
			/* Find event ids of attending */
			$find_events = $dbconn->prepare("SELECT * FROM event_attendance WHERE user_table=:user_table AND user_id=:user_id");
			$find_events->execute( array(":user_table"=>$table_id,":user_id"=>$user_id) );
			$events_ids = $find_events->fetchAll();
			
			if(count($events_ids) > 0)
			{
				/* create sql for WHERE clause of PDO query */
				$sql = '(id='.$events_ids[0]['event_id'].')';
				for($j = 1; $j < count($events_ids); $j++)
				{
						$sql .= ' OR (id='.$events_ids[$j]['event_id'].')';
				}
				
				/* fetch all events by friends */
				$find_events = $dbconn->prepare("SELECT * FROM events WHERE ".$sql);
				$find_events->execute();
			} else {
				return new Response(0,'No events were found.','{"message": "no events found"}');
			}
			// Fill an event array with all corresponding events
			$events = $find_events->fetchAll();
			
			$event_list = array();
				for($i = 0; $i < count($events); $i++)
				{
				$event = $events[$i];
						$event_list[$i] = new event($event['id'],$event['event_name'],$event['creator_table'],$event['creator_id'],$event['location'],$event['address1'],$event['address2'],$event['time'],$event['date']);
				}

			// Fill output with JSON of event array
			$output = '{ "events": [';
			for($i = 0; $i < count($event_list); $i++)
				{
				  if($i > 0)
					$output .= ',';
					$output .= $event_list[$i]->toJson();
				}
				$output .= '] }';
			
			return new Response(1,"Collected attendees list successfully",$output);
		}
		private function leaveEvent($parameters)
		{
			// check if proper parameters are set
			if(isset($parameters->event_id))
			{
				include('db_connect.php');

				/* Find user's table and id */
				$table_id = $parameters->fb_id % 10;
				$find_self = $dbconn->prepare("SELECT * FROM users".$table_id." WHERE fb_id=:fb_id");
				$find_self->execute( array(":fb_id"=>$parameters->fb_id));
				$self = $find_self->fetchAll();
				$self = $self[0];

				// Check to see if user is attending event
				$find_user_attendance = $dbconn->prepare("SELECT * FROM event_attendance WHERE event_id=:event_id AND user_table=:user_table AND user_id=:user_id");
				$find_user_attendance->execute( array(":event_id"=>$parameters->event_id,":user_table"=>$table_id,":user_id"=>$self['id']));
				$user_attendance = $find_user_attendance->fetchAll();
				
				if(count($user_attendance) != 0) // User can be removed from event
				{
					$leave_event = $dbconn->prepare("DELETE FROM event_attendance WHERE event_id=:event_id AND user_table=:user_table AND user_id=:user_id");
        			$leave_event->execute( array(":event_id"=>$parameters->event_id,":user_table"=>$table_id,":user_id"=>$self['id']) );
					return new Response(1,'User successfully left event.','{"message": "successfully removed"}');
				}
				else // User is not attending the event
				{
					return new Response(0,'User not attending event.','{"message": "not attending"}');
				}
			}
			else
			{
				return new Response(0,'Missing parameters for leaving event.','{"message": "missing parameters"}');
			}
		}
		
	}

?>