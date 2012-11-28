<?php

	class CommandType // Class that encapsulates Command Types
	{
		public function __construct($type)
		{
			$this->type = $type;
		}
		public function getType()
		{
			return $this->type;
		}
		public function isEqual($CommandType)
		{
			return ($this->type == $CommandType->getType());
		}
	}

?>