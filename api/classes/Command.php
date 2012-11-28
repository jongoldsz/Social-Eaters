<?php

	class Command // Class that encapsulates command data
	{
		public function __construct($CommandType,$fb_id,$function,$parameters)
		{
			$this->CommandType = $CommandType;
			$this->function = $function;
			$this->parameters = json_decode($parameters);
			$this->parameters->fb_id = $fb_id;
		}
		public function getCommandType()
		{
			return $this->CommandType;
		}
		public function getFunction()
		{
			return $this->function;
		}
		public function getParameters()
		{
			return $this->parameters;
		}
	}

?>