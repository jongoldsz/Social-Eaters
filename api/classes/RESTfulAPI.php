<?php

	class RESTfulAPI
	{
		public function __construct($CommandManager,$AuthenticationManager)
		{
			$this->CommandManager = $CommandManager;
			$this->AuthenticationManager = $AuthenticationManager;
		}
		private function authenticate($Authentication) // Authenticate the user
		{
			return $this->AuthenticationManager->findMatchingCredentials($Authentication);
		}
		private function passCommand($Command) // pass command to proper query
		{
			return $this->CommandManager->passCommandToQuery($Command);
		}
		public function interpertPost($POST)
		{
			// ensure we have all necessary POSTed variables
			if(isset($POST['fb_id']) && isset($POST['name']) && isset($POST['command_type']) && isset($POST['function']) && isset($POST['parameters']))
			{
				$auth = new Authentication($POST['fb_id'],$POST['name']);
				if($this->authenticate($auth)) // Successful authentication, pass the command to the command manager
				{
					$CommandType = new CommandType($POST['command_type']);
					$Command = new Command($CommandType,$POST['fb_id'],$POST['function'],$POST['parameters']);
					return $this->passCommand($Command);
				}
				else // Failed authentication
				{
					return new Response(0,'Failed user authentication.','{"message": "failed authentication"}');
				}
			}
			else
			{
				return new Response(0,'Nothing posted.','{"message": "nothing posted"}');
			}
		}
	}

?>