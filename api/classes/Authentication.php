<?php

	class Authentication // Class that encapsulates authentication credentials
	{
		public function __construct($fb_id,$name)
		{
			$this->fb_id = $fb_id;
			$this->name = $name;
		}
		public function getFbId()
		{
			return $this->fb_id;
		}
		public function getName()
		{
			return $this->name;
		}
		public function isEqual($auth)
		{
			return ($this->getFbId() == $auth->getFbId());
		}
	}

?>