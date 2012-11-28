<?php

	class Query // Parent class for all queries
	{
		public function __construct($CommandType)
		{
			$this->CommandType = $CommandType;
		}
		public function getCommandType()
		{
			return $this->CommandType;
		}
		public function verifyCommandType($Command)
		{
			return $this->CommandType->isEqual($Command->getCommandType());
		}
	}

?>