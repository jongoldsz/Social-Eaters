<?php

	class Response // class that encapsulates all response related data
	{
		public function __construct($success,$status,$output)
		{
			$this->success = $success;
			$this->status = $status;
			$this->output = $output;
		}
		public function getSuccess()
		{
			return $this->success;
		}
		public function getStatus()
		{
			return $this->status;
		}
		public function getOutput()
		{
			return $this->output;
		}
	}

?>