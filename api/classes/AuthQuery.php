<?php

	class AuthQuery extends Query
	{
		public function runCommand($Command)
		{
			if(parent::verifyCommandType($Command)) // Verify if command has matching command type
			{
				return new Response(1,'Successfully authenticated.','{"auth": "success"}');
			}
			else
			{
				return new Response(0,'Command passed to Query has a non-matching CommandType.','{"message": "CommandType mismatch"}');
			}
		}
	}

?>