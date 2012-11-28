<?php

	class QueryList
	{
		public function __construct()
		{
			$this->queries = array();
		}
		public function getQueryWithType($CommandType)
		{
			foreach ($this->queries as $Query)
			{
				if($Query->getCommandType()->isEqual($CommandType))
				{
					return $Query;
				}
			}
			return NULL;
		}
		public function addQuery($Query)
		{
			// Only add Query if it's command type is not in the queries array
			if($this->getQueryWithType($Query->getCommandType()) == NULL)
			{
				$this->queries[] = $Query;
				return 1;
			}
			else
			{
				return 0;
			}
		}
	}

?>