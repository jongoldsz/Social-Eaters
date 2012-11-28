<?php

	class CommandManager
	{
		public function __construct($QueryList)
		{
			$this->QueryList = $QueryList;
		}
		public function passCommandToQuery($Command)
		{
			// determine which query can run the command and run it
			if( ($Query = $this->QueryList->getQueryWithType($Command->getCommandType())) != NULL )
			{
				return $Query->runCommand($Command);
			}
			else
			{
				return new Response(0,"Can't find a Query with the specified CommandType.",'{"message": "Invalid CommandType"}');;
			}
		}
	}

?>